<?php
class Block extends CI_Model{
    protected $_fieldlist = 'cbp.id,cbp.pid,cbp.rootid,cbp.title,cbp.content,cbp.imgUrl,cbp.userid,cbp.receiveUserId,cbp.unionId,cbp.posttime,cbp.state,cbp.replycount,cbp.lastReplyTime,cbp.remark,cbp.isRead,cbp.type,cbp.category';
    /**
     * 表名
     * @var string
     */
    protected $_tableName;
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = TRUE;
    /**
     * [群福利]的默认板块-舶来的状态
     * @var int
     */
    const GROUP_AWARD_WELCOME_STATUS = 1;
    /**
     * 私聊标识
     * @var int
     */
    const PRIVATE_CHAT_STATUS = 1;
    /**
     * 系统消息标识
     * @var int
     */
    const SYSTEM_TYPE_STATUS = 2;
    /**
     * 消息类型配置
     * @var array
     */
    public static $message_type_config = array(
        self::PRIVATE_CHAT_STATUS,
        self::SYSTEM_TYPE_STATUS,
    );
    
    function __construct(){
    }

    function init($tablename){
        $this->_tableName = $tablename;
    }
    public function set_table_name($blockName){
        $this->_tableName = "{$blockName}blockpost";
    }

    public function getTypeList($userId = 0)
    {
        $query = $this->db->get('block');
        $ret = array();
        $today = date('Y-m-d');
        $dataUpdate = array();
        $this->load->model('UserUnion');
        foreach($query->result() as $row)
        {
            //检测更新日期是否当天
            if (isset($row->updateDay) && $row->updateDay != $today)
            {
                $row->todayPost = 0;
                $dataUpdate[] = $row->name;
            }
            $blockData = array('name'=>$row->name,'cnname'=>$row->cnname,'total'=>$row->totalPost, 'today'=>$row->todayPost);
            //闲聊板块
            if ($row->name == 'communication' && $userId > 0)
            {
                //获取当天新建的社团ID列表
                $todayCreateUnion = $this->UserUnion->getTodayCreateUnionIds($userId);
                if (count($todayCreateUnion) >= 1)
                {
                    $this->set_table_name($row->name);
                    list($postLists, $_) = $this->postsNew(1, 100, 0, $userId, $row->name);
                    $blockData['today'] = count($postLists);
                }
            }
            $ret[] = $blockData;
        }
        if (!empty($dataUpdate))
        {
            $blockname_list = "'" . join("','", $dataUpdate) . "'";
            $sql = "UPDATE `block` SET `todayPost`=0,`updateDay`=CURRENT_DATE WHERE `name` IN ({$blockname_list})";
            $this->db->query($sql);
        }
        return $ret;
    }
    /**
     * 私聊功能逻辑
     * @param $data
     * @param $postList
     */
    public function privateChatModel($userid, $data, $postList = array())
    {
        //未提交接收者的UID
        if (!isset($data['receiveUserId']) OR !is_numeric($data['receiveUserId']) OR $data['receiveUserId'] <= 0)
        {
            return array(FALSE, BLH_Utilities::genErrorMsg(-1, 'params [receiveUserId] is error,please retry'));
        }
        //未提交接收者的社团ID
        if (!isset($data['unionId']) OR !is_numeric($data['unionId']) OR $data['unionId'] <= 0)
        {
            return array(FALSE, BLH_Utilities::genErrorMsg(-2, 'params [unionId] is error,please retry'));
        }
        //不能跟自己发送私聊信息
        /*if ($userid == $data['receiveUserId'])
        {
            return array(FALSE, BLH_Utilities::genErrorMsg(-3, 'do not send message to self,please retry'));
        }*/
        $ret_create = FALSE;
        $ret_money = $ret_chat = TRUE;
        $sendUserUnionId = 0;
        #发起聊天者所在的社团ID，方便管理后台统计荔枝币流水
        if (isset($data['unionId']) && !empty($data['unionId']))
        {
            $sendUserUnionId = $data['unionId'];
        }
        //是否是回复消息/帖子
        $isReply = (isset($data['isReply']) && $data['isReply']) ? $data['isReply'] : FALSE;
        //私聊模块
        $this->load->model('UserChat');
        //回复
        if (TRUE == $isReply)
        {
            if (!empty($postList))
            {
                $rootData = (object)array();
                //回复时
                foreach($postList as $postValue)
                {
                    if(isset($postValue->id) && $postValue->id == $data['rootid'])
                    {
                        $rootData = &$postValue;
                        break;
                    }
                }
                if (empty($rootData))
                {
                    return array(FALSE, BLH_Utilities::genErrorMsg(-4, 'reply data is error,please retry'));
                }
                //原始消息/帖子的信息
                $sendUserId = $rootData->userid;
                //若是发帖者回复的，则使用回复者的用户ID作为查询的用户ID，否则视为别人回复发帖者
                $receiveUserid = ($userid == $sendUserId) ? $data['receiveUserId'] : $userid;
                //原始消息/帖子的信息
                $sendUserUnionId = $rootData->unionId;
                #获取该用户对某人发起的聊天记录(若在有效期内，则不重复扣钱，无需创建聊天记录)
                $userChatData = $this->UserChat->getUserChatBySendUid($sendUserId, $receiveUserid, $sendUserUnionId);
                if (empty($userChatData))
                {
                    //return array(FALSE, BLH_Utilities::genErrorMsg(-5, 'no private chat or chat has expired,please retry'));
                }
                $this->db->trans_start();
            }else{
                return array(FALSE, BLH_Utilities::genErrorMsg(-6, 'reply data is failed,please retry'));
            }
        }else{
            #获取该用户对某人发起的聊天记录(若在有效期内，则不重复扣钱，无需创建聊天记录)
            $userChatData = $this->UserChat->getUserChatBySendUid($userid, $data['receiveUserId'], $sendUserUnionId);
            //$this->db->trans_start();
            if (empty($userChatData))
            {
                //扣除相应的荔枝币
                $this->load->model('UserMoney');
                $chat_reduce_lizhi = $this->config->item('chat_reduce_lizhi', 'system_private_chat_config');
                $ret_money = $this->UserMoney->reduceMoney($userid, $chat_reduce_lizhi);
                if (!$ret_money)
                {
                    return array(FALSE, BLH_Utilities::genErrorMsg(-6, 'lizhi money is not enough,please retry'));
                }
                #新增聊天记录
                //if (isset($data['unionId'])) unset($data['unionId']);
                $chat_expries_timeout = $this->config->item('chat_expries_timeout', 'system_private_chat_config');
                //记录订单流水表
                $ret_chat = $ret_money && $this->UserChat->addChat($userid, $data['receiveUserId'], $sendUserUnionId, $chat_reduce_lizhi, $chat_expries_timeout);
            }
        }
        //私聊标识
        $data['type'] = self::PRIVATE_CHAT_STATUS;
        //发布/回复帖子
        $ret_chat && $ret_money && $ret_create = $this->create($data, $postList);
        //log_message('debug', 'TEST@@$ret_chat=>'.var_export($ret_chat,true).'@@$ret_money=>'.var_export($ret_money,true).'@@$ret_create=>'.var_export($ret_create,true).'@@$data=>'.var_export($data,true).'@@$postList=>'.var_export($postList,true));
        //$this->db->trans_complete();
        if ($ret_money && $ret_chat && is_array($ret_create) && !empty($ret_create['id']))
        {
            return array($ret_create, array());
        }
        return array(FALSE, BLH_Utilities::genErrorMsg(-7, 'seng message or reply message is failed,please retry'));
    }
    /**
     * 发布/回复帖子
     * @param array $data
     */
    public function create($data, $postList = array())
    {
        //是否是回复消息/帖子
        $isReply = (isset($data['isReply']) && $data['isReply']) ? $data['isReply'] : FALSE;
        //是否是私聊
        $privateChat = (isset($data['privateChat']) && $data['privateChat']) ? $data['privateChat'] : FALSE;
        if (isset($data['isReply'])) unset($data['isReply']);
        if (isset($data['privateChat'])) unset($data['privateChat']);
        if (isset($data['id'])) unset($data['id']);
        if (isset($data['posttime'])) unset($data['posttime']);
        if (isset($data['lastReplyTime'])) unset($data['lastReplyTime']);
        
        //社团名称
        $unionName = '';
        $data['state'] = 'new';
        $data['lastReplyTime'] = $data['posttime'] = date('Y-m-d H:i:s');
        if(!isset($data['rootid']) && isset($data['pid']))
        {
            $data['rootid'] = $data['pid'];
        }
        if (isset($data['content']))
        {
            $data['content'] = urldecode($data['content']);
        }
        //是否是私聊
        if ($privateChat)
        {
            //接收者的用户ID
            if (isset($data['receiveUserId']) && is_numeric($data['receiveUserId']))
            {
                $data['receiveUserId'] = (string)$data['receiveUserId'];
            }
        }
        //发布时
        if (FALSE == $isReply)
        {
            //用户所在社团的ID
            if (isset($data['unionId']) && is_numeric($data['unionId']))
            {
                $data['unionId'] = (int)$data['unionId'];
                if (isset($data['unionName']) && !empty($data['unionName']))
                {
                    $unionName = $data['unionName'];
                    unset($data['unionName']);
                }else{
                    //查询社团信息
                    $this->load->model('UnionManage');
                    $unionData = $this->UnionManage->getUnionById($data['unionId']);
                    if (!empty($unionData['unionName']))
                    {
                        $unionName = $unionData['unionName'];
                    }
                }
            }
        }
        else
        {
            //log_message('debug', 'TEST@@$data=>'.var_export($data,true).'@@$postList=>'.var_export($postList,true));
            //回复时
            if (isset($data['unionId']) && !empty($data['unionId']))
            {
                if (isset($data['unionName']) && !empty($data['unionName']))
                {
                    $unionName = $data['unionName'];
                    unset($data['unionName']);
                }else{
                    //查询社团信息
                    $this->load->model('UnionManage');
                    $unionData = $this->UnionManage->getUnionById($data['unionId']);
                    if (!empty($unionData['unionName']))
                    {
                        $unionName = $unionData['unionName'];
                    }
                }
            }else
            {
                if (!empty($postList))
                {
                    $rootData = (object)array();
                    //回复时
                    foreach($postList as $postValue)
                    {
                        if(isset($postValue->id) && $postValue->id == $data['rootid'])
                        {
                            $rootData = &$postValue;
                            break;
                        }
                    }
                    if (empty($rootData) OR !isset($rootData->unionId) OR $rootData->unionId <= 0)
                    {
                        return FALSE;
                    }
                    //原始消息/帖子的信息
                    $data['unionId'] = $rootData->unionId;
                    if (isset($data['unionName']) && !empty($data['unionName']))
                    {
                        $unionName = $data['unionName'];
                        unset($data['unionName']);
                    }else{
                        //查询社团信息
                        $this->load->model('UnionManage');
                        $unionData = $this->UnionManage->getUnionById($data['unionId']);
                        if (!empty($unionData['unionName']))
                        {
                            $unionName = $unionData['unionName'];
                        }
                    }
                }
            }
        }
        if(isset($data['imgUrl']) && is_array($data['imgUrl']))
        {
            $data['imgUrl'] = join(';', $data['imgUrl']);
        }
        //$this->db->trans_start();
        $ret = $this->db->insert($this->_tableName, $data);
        if($ret)
        {
            $insertId = $this->db->insert_id();
            $pid = isset($data['pid']) ? $data['pid'] : 0;
            $rid = isset($data['rootid']) ? $data['rootid'] : 0;
            if($pid || $rid)
            {
                $this->updateReplyCount(array($pid, $rid), $insertId);
            }
            $this->_updatePostCount();
            //$this->db->trans_complete();
            //开启缓存的处理
            if (self::$enableCache)
            {
                $key_lists_keys_1 = $key_lists_keys_2 = $key_lists_keys_3 = array();
                //清理缓存
                //$key_lists_1 = sprintf("%s|%s|%s|%s", __CLASS__, 'postNew', $data['userid'], '*');
                //$key_lists_keys_1 = $this->getCacheAdapter()->keys($key_lists_1);
                $key_lists_2 = sprintf("%s|%s|%s|%s", 'Userposts', 'itemlist', $data['userid'], '*');
                if ($key_lists_2)
                {
                    $key_lists_keys_2 = $this->getCacheAdapter()->keys($key_lists_2);
                }
                if (isset($data['receiveUserId']) && $data['receiveUserId'] > 0)
                {
                    $key_lists_3 = sprintf("%s|%s|%s|%s", 'Userposts', 'itemlist', $data['receiveUserId'], '*');
                    if ($key_lists_3)
                    {
                        $key_lists_keys_3 = $this->getCacheAdapter()->keys($key_lists_3);
                    }
                }
                $keys_all = array_merge($key_lists_keys_1, $key_lists_keys_2, $key_lists_keys_3);
                $this->deleteMultiCacheData($keys_all);
            }
            $addPostRet = array('id'=>$insertId, 'unionId'=>(isset($data['unionId']) ? $data['unionId']: 0), 'unionName'=>$unionName, 'imgUrl'=>(isset($data['imgUrl']) ? $data['imgUrl']: ''));
            return $addPostRet;
        }else{
            //$this->db->trans_complete();
        }
        return FALSE;
    }
    /**
     * 更新帖子的回复数
     * @param array $idlist
     * @param int $insertId
     */
    public function updateReplyCount($idlist, $insertId=0)
    {
        list($pid, $rootid) = $idlist;
        $idlist = array_unique($idlist);
        $idstring = join(',', $idlist);
        //把最新回复的帖子ID，更新到root的remark中
        if ($rootid > 0 && $insertId > 0)
        {
            $this->db->query("UPDATE {$this->_tableName} SET `remark`='{$insertId}' WHERE id='{$rootid}'");
        }
        //更新相关ID记录的回复次数
        $this->db->query("UPDATE {$this->_tableName} SET `replycount`=`replycount`+1 WHERE id in($idstring)");
        //更新相关ID记录的最新回复时间
        $sql="UPDATE {$this->_tableName} a, (SELECT rootid, MAX(posttime) AS mtime FROM {$this->_tableName} WHERE rootid IN($idstring) GROUP BY rootid) AS b SET a.lastReplyTime=b.mtime WHERE a.id=b.rootid AND a.id IN($idstring)";
        $this->db->query($sql);
    }
    
    /**
     * 查询帖子
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function postsNew($page = 1, $pagesize = 10, $timestamp = 0, $userId = 0, $blockname='')
    {
        //获取当前用户所加入的社团unionId列表
        $this->load->model('UserUnion');
        $userUnionIds = $this->UserUnion->getMyJoinUnionIdList($userId);
        if (empty($userUnionIds))
        {
            return array(array(), array());
        }
        //系统帐号ID列表
        $user_system_accounts = $this->config->item('system_accounts');
        $this->db->select('cbp.id,cbp.rootid,cbp.userid');
        $condition = '';
        //只获取[最新更新过]的用户列表
        if (is_numeric($timestamp) && $timestamp > 0)
        {
            $this->db->where('cbp.`posttime` >= ', date('Y-m-d H:i:s', $timestamp));
            $condition = ' AND ';
        }
        if ($blockname == 'welcome')
        {
            //最小的[舶来]账户的UID
            $min_welcome_uid = $this->config->item('min_welcome_uid', 'system_admin_config');
            //最大的[舶来]账户的UID
            $max_welcome_uid = $this->config->item('max_welcome_uid', 'system_admin_config');
            $this->db->ar_where[] = "{$condition} cbp.`userid` BETWEEN '{$min_welcome_uid}' AND '{$max_welcome_uid}'";
            $this->db->ar_where[] = "AND cbp.`unionId` IN ('".join("','", $userUnionIds)."')";
        }
        //当前用户只能查看所加入社团的消息
        $blockname != 'welcome' && $this->db->ar_where[] = "{$condition} cbp.`unionId` IN ('".join("','", $userUnionIds)."')";
        //私聊的消息
        //$blockname != 'welcome' && $this->db->ar_where[] = "OR (cbp.`receiveUserId`='{$userId}'))";
        //不显示舶来等用户信息
        $blockname != 'welcome' && $this->db->ar_where[] = "AND cbp.`userid` NOT IN ('".join("','", $user_system_accounts)."')";
        //不显示私聊信息
        //$blockname != 'welcome' && $this->db->ar_where[] = 'AND cbp.type !='.self::PRIVATE_CHAT_STATUS;
        $blockname != 'welcome' && $this->db->ar_where[] = 'AND cbp.`receiveUserId` = 0';
        $this->db->order_by('cbp.posttime' ,'DESC');
        $this->db->limit( $pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName . ' cbp');
        $result = $query->result_array();
        $ids = $userIds = $resultNew = array();
        if (!empty($result))
        {
            foreach($result as $index => $row)
            {
                $ids[$row['id']] = 1;
                if ($row['rootid'] > 0)
                {
                    $ids[$row['rootid']] = 1;
                }
                if (isset($row['userid']) && $row['userid'] > 0)
                {
                    $userIds[$row['userid']] = 1;
                }
            }
            if(count($ids) > 0)
            {
                //联表查询社团信息
                $this->db->select(array_merge(explode(',',$this->_fieldlist), array('tu.unionName', 'IFNULL(tua.status, 0) AS awardStatus')));
                $this->db->join('tbl_union tu', 'tu.unionId = cbp.unionId', 'LEFT');
                $this->db->join('tbl_user_award tua', 'tua.postId = cbp.id', 'LEFT');
                $this->db->where_in('cbp.id', array_keys($ids));
                //只显示公共消息/帖子
                //$this->db->where('cbp.receiveUserId =', 0);
                $this->db->order_by('cbp.posttime' ,'DESC');
                $pQuery = $this->db->get($this->_tableName . ' cbp');
                $resultNew = $pQuery->result_array();
                if (!empty($resultNew))
                {
                    foreach($resultNew as $index2 => $row2)
                    {
                        //应客户端要求，在这里也添加block字段
                        $resultNew[$index]['block'] = str_replace('blockpost', '', $this->_tableName);
                        /*if (!empty($resultNew[$index]))
                        {
                            $resultNew[$index] = $this->_tranNull2empty($row2);
                        }*/
                    }
                }
            }
        }
        $postNewList = array($resultNew, $userIds);
        return $postNewList;
    }
    /**
     * 查询帖子-New
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function postsListNew($page = 1, $pagesize = 10, $timestamp = 0, $userId = 0, $blockname='')
    {
        //获取当前用户所加入的社团unionId列表
        $this->load->model('UserUnion');
        $userUnionIds = $this->UserUnion->getMyJoinUnionIdList($userId);
        if (empty($userUnionIds))
        {
            return array(array(), array());
        }
        //系统帐号ID列表
        $user_system_accounts = $this->config->item('system_accounts');
        $this->db->select('cbp.id,cbp.rootid,cbp.userid');
        $condition = '';
        //只获取[最新更新过]的用户列表
        if (is_numeric($timestamp) && $timestamp > 0)
        {
            $this->db->where('cbp.`posttime` >= ', date('Y-m-d H:i:s', $timestamp));
            $condition = ' AND ';
        }
        if ($blockname == 'welcome')
        {
            //最小的[舶来]账户的UID
            $min_welcome_uid = $this->config->item('min_welcome_uid', 'system_admin_config');
            //最大的[舶来]账户的UID
            $max_welcome_uid = $this->config->item('max_welcome_uid', 'system_admin_config');
            $this->db->ar_where[] = "{$condition} cbp.`userid` BETWEEN '{$min_welcome_uid}' AND '{$max_welcome_uid}'";
            $this->db->ar_where[] = "AND cbp.`unionId` IN ('".join("','", $userUnionIds)."')";
        }
        //只查root的帖子
        $blockname != 'welcome' && $this->db->ar_where[] = "{$condition} cbp.`rootid` = 0";
        //当前用户只能查看所加入社团的消息
        $blockname != 'welcome' && $this->db->ar_where[] = "AND cbp.`unionId` IN ('".join("','", $userUnionIds)."')";
        //私聊的消息
        //$blockname != 'welcome' && $this->db->ar_where[] = "OR (cbp.`receiveUserId`='{$userId}'))";
        //不显示舶来等用户信息
        $blockname != 'welcome' && $this->db->ar_where[] = "AND cbp.`userid` NOT IN ('".join("','", $user_system_accounts)."')";
        //不显示私聊信息
        //$blockname != 'welcome' && $this->db->ar_where[] = 'AND cbp.type !='.self::PRIVATE_CHAT_STATUS;
        $blockname != 'welcome' && $this->db->ar_where[] = 'AND cbp.`receiveUserId` = 0';
        //不显示已删除的帖子
        $this->db->ar_where[] = 'AND cbp.`state` = \'new\'';
        //$this->db->join('tbl_union tu', 'tu.unionId = cbp.unionId', 'LEFT');
        //$this->db->join('tbl_user_award tua', 'tua.postId = cbp.id', 'LEFT');
        //按最新回复时间倒序
        $this->db->order_by('cbp.lastReplyTime' ,'DESC');
        $this->db->limit( $pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName . ' cbp');
        $resultRootList = $query->result_array('id');
        $response = array();
        if (!empty($resultRootList))
        {
            //获取root根帖子的ID
            $rootIds = array_keys($resultRootList);
            if(count($rootIds) > 0)
            {
                //联表查询社团信息
                $this->_fieldlist = str_replace(',', '|', $this->_fieldlist);
                $this->_fieldlist = str_replace('cbp.rootid', 'IF(`cbp`.`rootid` > 0, `cbp`.`rootid`, `cbp`.`id`) AS rootid', $this->_fieldlist);
                $this->db->select(array_merge(explode('|',$this->_fieldlist), array('tu.unionName', 'IFNULL(tua.status, 0) AS awardStatus')));
                $this->db->join('tbl_union tu', 'tu.unionId = cbp.unionId', 'LEFT');
                $this->db->join('tbl_user_award tua', 'tua.postId = cbp.id', 'LEFT');
                $this->db->where('cbp.state', 'new');
                $this->db->where_in('cbp.rootid', $rootIds);
                $this->db->or_where_in('cbp.id', $rootIds);
                //$this->db->order_by('cbp.posttime' ,'DESC');
                $pQuery = $this->db->get($this->_tableName . ' cbp');
                $resultReply = $pQuery->result_array('rootid', 'id', 'LIST');
                foreach ($resultRootList as $key_id => $value)
                {
                    //帖子ID
                    $postId = $value['id'];
                    $postOneItem = array();
                    //获取Root贴的内容
                    if (!empty($resultReply[$postId][$postId]))
                    {
                        $resultReply[$postId][$postId]['rootid'] = 0;
                        $postOneItem = $resultReply[$postId][$postId];
                        unset($resultReply[$postId][$postId]);
                        //回帖数量
                        $postOneItem['postsTotal'] = !empty($resultReply) && !empty($resultReply[$postId]) ? count($resultReply[$postId]) : 0;
                        $postOneItem['Replys'] = !empty($resultReply[$postId]) ? array_slice($resultReply[$postId], -2, 2) : array();
                        $response[] = $postOneItem;
                    }
                }
            }
        }
        return $response;
    }
    
    /**
     * 根据rootid获取所有回复的帖子集合
     * Add At 2014-08-06 12:15
     * @param $blockname
     * @param $rootid
     */
    public function postsRootList($userId = 0, $blockname='', $rootid=0, $is_admin = FALSE)
    {
        if (!$is_admin)
        {
            //获取当前用户所加入的社团unionId列表
            $this->load->model('UserUnion');
            $userUnionIds = $this->UserUnion->getMyJoinUnionIdList($userId);
            if (empty($userUnionIds))
            {
                return array(array(), array());
            }
        }
        //系统帐号ID列表
        $user_system_accounts = $this->config->item('system_accounts');
        $this->db->select('cbp.id,cbp.rootid,cbp.userid');
        $condition = '';
        if ($blockname == 'welcome')
        {
            //最小的[舶来]账户的UID
            $min_welcome_uid = $this->config->item('min_welcome_uid', 'system_admin_config');
            //最大的[舶来]账户的UID
            $max_welcome_uid = $this->config->item('max_welcome_uid', 'system_admin_config');
            $this->db->ar_where[] = "{$condition} cbp.`userid` BETWEEN '{$min_welcome_uid}' AND '{$max_welcome_uid}'";
            $this->db->ar_where[] = "AND cbp.`unionId` IN ('".join("','", $userUnionIds)."')";
        }
        //当前用户只能查看所加入社团的消息
        if ($blockname != 'welcome' && !$is_admin)
        {
            $this->db->ar_where[] = "{$condition} cbp.`unionId` IN ('".join("','", $userUnionIds)."')";
            $condition = ' AND ';
        }
        //根据rootid获取所有回复的帖子集合
        $blockname != 'welcome' && $this->db->ar_where[] = "{$condition} (cbp.`id` = '{$rootid}' OR cbp.`rootid` = '{$rootid}')";
        //私聊的消息
        //$blockname != 'welcome' && $this->db->ar_where[] = "OR (cbp.`receiveUserId`='{$userId}'))";
        //不显示舶来等用户信息
        $blockname != 'welcome' && $this->db->ar_where[] = "AND cbp.`userid` NOT IN ('".join("','", $user_system_accounts)."')";
        //不显示私聊信息
        //$blockname != 'welcome' && $this->db->ar_where[] = 'AND cbp.type !='.self::PRIVATE_CHAT_STATUS;
        $blockname != 'welcome' && $this->db->ar_where[] = 'AND cbp.`receiveUserId` = 0';
        //不显示已删除的帖子
        $this->db->ar_where[] = 'AND cbp.`state` = \'new\'';
        $this->db->order_by('cbp.posttime' ,'DESC');
        $query = $this->db->get($this->_tableName . ' cbp');
        $result = $query->result_array();
        $ids = $userIds = $resultNew = array();
        if (!empty($result))
        {
            foreach($result as $index => $row)
            {
                $ids[$row['id']] = 1;
                if ($row['rootid'] > 0)
                {
                    $ids[$row['rootid']] = 1;
                }
                if (isset($row['userid']) && $row['userid'] > 0)
                {
                    $userIds[$row['userid']] = 1;
                }
            }
            if(count($ids) > 0)
            {
                //联表查询社团信息
                $this->db->select(array_merge(explode(',',$this->_fieldlist), array('tu.unionName', 'IFNULL(tua.status, 0) AS awardStatus')));
                $this->db->join('tbl_union tu', 'tu.unionId = cbp.unionId', 'LEFT');
                $this->db->join('tbl_user_award tua', 'tua.postId = cbp.id', 'LEFT');
                $this->db->where_in('cbp.id', array_keys($ids));
                //只显示公共消息/帖子
                //$this->db->where('cbp.receiveUserId =', 0);
                $this->db->order_by('cbp.posttime' ,'DESC');
                $pQuery = $this->db->get($this->_tableName . ' cbp');
                $resultNew = $pQuery->result_array();
                if (!empty($resultNew))
                {
                    foreach($resultNew as $index2 => $row2)
                    {
                        //应客户端要求，在这里也添加block字段
                        $resultNew[$index]['block'] = str_replace('blockpost', '', $this->_tableName);
                        /*if (!empty($resultNew[$index]))
                        {
                            $resultNew[$index] = $this->_tranNull2empty($row2);
                        }*/
                    }
                }
            }
        }
        $postNewList = array($resultNew, $userIds);
        return $postNewList;
    }
    
    /**
     * 查询帖子
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function posts($page = 1, $pagesize = 10, $timestamp = 0){

        //$where = "`state`='new'";
        //$this->db->where($where, NULL, false);
    
        $this->db->select($this->_fieldlist);
        //只获取[最新更新过]的用户列表
        if (is_numeric($timestamp) && $timestamp > 0)
        {
            $this->db->where('cbp.posttime >= ', date('Y-m-d H:i:s', $timestamp));
        }
        $this->db->order_by('cbp.posttime' ,'DESC');
        $this->db->limit( $pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName . ' cbp');
        //echo $this->db->last_query(),"\n";
        $ids = $userIds = array();
        $result = $query->result_array();
        if (!empty($result))
        {
            foreach($result as $index => $row)
            {
                //应客户端要求，在这里也添加block字段
                $result[$index]['block'] = str_replace('blockpost', '', $this->_tableName);
                if ($row['rootid'] > 0)
                {
                    $ids[] = $row['rootid'];
                }
                if (isset($row['userid']))
                {
                    $userIds[$row['userid']] = 1;
                }
            }
        }
        if(count($ids) > 0)
        {
            $this->db->select($this->_fieldlist);
            $this->db->where_in('cbp.id', array_unique($ids));
            $pQuery = $this->db->get($this->_tableName . ' cbp');
            //echo $this->db->last_query(),"\n";
            $rootlist = $this->_obj2hash($pQuery->result(), 'id');
            if (!empty($rootlist))
            {
                $exists = $root_result = array();
                foreach($result as $index => $row)
                {
                    if (isset($exists[$row['id']]))
                    {
                        unset($result[$index]);
                        continue;
                    }
                    $exists[$row['id']] = 1;
                    if($row['rootid'] > 0 && isset($rootlist[$row['rootid']]))
                    {
                        //$result[$index]['root'] = $rootlist[$row->rootid];
                        $tmp_root = (array)$rootlist[$row['rootid']];
                        if (isset($exists[$tmp_root['id']]))
                        {
                            unset($result[$tmp_root['id']]);
                            continue;
                        }
                        $exists[$tmp_root['id']] = 1;
                        //应客户端要求，在这里也添加block字段
                        $tmp_root['block'] = str_replace('blockpost', '', $this->_tableName);
                        $root_result[] = $tmp_root;
                    }
                    if (!empty($result[$index]))
                    {
                        $result[$index] = $this->_tranNull2empty($result[$index]);
                    }
                }
                unset($exists);
                $result = array_merge($result, $root_result);
            }
        }
        return array($result, $userIds);
    }

    //return all list
    public function detailof($id, $page, $pagesize)
    {
        $this->db->select($this->_fieldlist . ',tu.unionId,tu.unionName');
        $where = "cbp.`state`='new' AND (cbp.`id`='{$id}' OR cbp.`rootid`='{$id}' OR cbp.`pid`='{$id}')";
        $this->db->join('tbl_union tu', 'tu.unionId = cbp.unionId', 'LEFT');
        $this->db->where($where, NULL, false);
        $this->db->order_by('cbp.rootid' ,'ASC');
        $this->db->order_by('cbp.posttime' ,'DESC');
        $this->db->limit( $pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName . ' cbp');
        //echo $this->db->last_query();
        return $query->result_array();
    }

    public function del($postid, $userid){
        $where ="`state`='new' AND `id`='{$postid}' AND ((`userid`='{$userid}' AND `receiveUserId`=0) OR (`userid`!='{$userid}' AND `receiveUserId`='{$userid}'))";
        $this->db->where($where ,NULL, false);
        $data['state']='delete';
        $this->db->update($this->_tableName, $data) ;
        //echo $this->db->last_query();
        $updateCount = $this->db->affected_rows();
        $this->_updatePostCount();
        return $updateCount > 0;
    }
    /**
     * 验证pid、rootid
     * @param int $pid
     * @param int $rootid
     * @param boolean $isReturnData
     */
    public function checkIds($pid, $rootid, $isReturnData = FALSE)
    {
        if ($pid==0 && $rootid==0)
        {
            return TRUE;
        }
        else if ($pid==0 && $rootid!=0)
        {
            return FALSE;
        }
        $this->db->select('cbp.id,cbp.pid,cbp.rootid,cbp.userid,cbp.receiveUserId,cbp.unionId,cbp.remark');
        $this->db->where_in('cbp.id', array($pid, $rootid));
        $query = $this->db->get($this->_tableName . ' cbp');
        if( $query->num_rows() < 1)
        {
            return FALSE;
        }
        $pidOK = $topOK = FALSE;
        $root_posts_data = $query->result();
        if (is_array($root_posts_data) && !empty($root_posts_data))
        {
            foreach($root_posts_data as $row)
            {
                if($row->id == $pid)
                {
                    $pidOK = TRUE;
                }
                if($row->id== $rootid)
                {
                    $topOK = TRUE;
                }
            }
        }
        $checkRet = $pidOK && ($rootid == 0 || $topOK);
        return $isReturnData ? array($checkRet, $root_posts_data) : $checkRet;
    }
    /**
     * 同步更新客户端接收消息的状态
     * @param string $blockName
     * @param int $id
     */
    public function rsyncMessageStatus($id, $userid)
    {
        //$this->db->where('isRead', 0);
        $where = "`isRead`=0 AND `id`='{$id}'";
        $this->db->where($where ,NULL, false);
        $this->db->update($this->_tableName, array('isRead'=>1));
        return (bool)$this->db->affected_rows();
    }
    
    /**
     * 获取【最新的招聘信息】列表
     */
    public function fetch_recruit_list($userId=0, $blockname, $page=1, $pagesize=10)
    {
        //计算[符合用户属性]的招聘信息条数
        $pageNum = max(1, ceil($pagesize * 0.7));
        //计算[系统]随机信息条数
        $systemNum = max(0, $pagesize - $pageNum);
        $user_property_list = $system_random_list = $system_recruit_list = $exist_id = $result = array();
        //获取用户当前的职位分类
        $this->load->model('Userinfo');
        $userData = $this->Userinfo->fetch_user_by_id($userId);
        if (isset($userData['category']) && !empty($userData['category']))
        {
            $this->db->select(array_merge(array('rbp.id','rbp.pid`','rbp.rootid','rbp.title','rbp.content','rbp.imgUrl','rbp.userid','rbp.receiveUserId','rbp.unionId','rbp.posttime','rbp.state','rbp.replycount','rbp.lastReplyTime','rbp.remark','rbp.isRead','rbp.type','rbp.category'), array('IF("p", "p", "p") AS strategyId')));//REPLACE(rbp.content, "<br />", "") AS content
            $this->db->where('rbp.state', 'new');
            $this->db->where('rbp.category', $userData['category']);
            $this->db->ar_where[] = sprintf("AND rbp.posttime >= DATE_ADD(now(), INTERVAL -%d DAY)", WEEK_DAY_NUM * 2);
            $this->db->order_by('rbp.posttime' ,'DESC');
            $this->db->limit($pageNum, ($page-1)*$pageNum);
            $query = $this->db->get($this->_tableName . ' rbp');
            $user_property_list = $query->result_array('id', '', 'ASC', FALSE);
        }
        //查出来的招聘信息数量少于查询条件规定的数量时的处理
        if (($user_property_cnt = count($user_property_list)) < $pageNum)
        {
            $systemNum = max(1, $pagesize - $user_property_cnt);
        }
        if ($systemNum > 0)
        {
            //随机获取系统的招聘信息
            $this->db->select(array_merge(array('rbp.id','rbp.pid`','rbp.rootid','rbp.title','rbp.content','rbp.imgUrl','rbp.userid','rbp.receiveUserId','rbp.unionId','rbp.posttime','rbp.state','rbp.replycount','rbp.lastReplyTime','rbp.remark','rbp.isRead','rbp.type','rbp.category'), array('IF("s", "s", "s") AS strategyId')));//REPLACE(rbp.content, "<br />", "") AS content
            $this->db->where('rbp.state', 'new');
            $this->db->ar_where[] = sprintf("AND rbp.posttime >= DATE_ADD(now(), INTERVAL -%d DAY)", WEEK_DAY_NUM * 2);
            !empty($user_property_list) && $this->db->where_not_in('rbp.id', array_keys($user_property_list));
            !empty($userData['category']) && $this->db->ar_where[] = sprintf('AND rbp.category != "%s"', $userData['category']);
            $this->db->order_by('rbp.posttime' ,'DESC');
            $this->db->limit($systemNum * 10);
            $query = $this->db->get($this->_tableName . ' rbp');
            $system_recruit_list = $query->result_array();
        }
        if (!empty($system_recruit_list))
        {
            if (count($system_recruit_list) <= $systemNum)
            {
                $result = array_merge($user_property_list, $system_recruit_list);
            }else{
                $current_num = min($systemNum, count($system_recruit_list));
                shuffle($system_recruit_list);
                $recruit_random_list = array_rand($system_recruit_list, $current_num);
                is_numeric($recruit_random_list) && $recruit_random_list = array($recruit_random_list);
                for ($i=0; $i<$current_num; $i++)
                {
                    if (isset($recruit_random_list[$i]) && !empty($system_recruit_list[$recruit_random_list[$i]]) && !isset($exist_id[$recruit_random_list[$i]]))
                    {
                        $system_random_list[] = $system_recruit_list[$recruit_random_list[$i]];
                        $exist_id[$recruit_random_list[$i]] = 1;
                    }
                }
                $result = array_merge($user_property_list, $system_random_list);
            }
        }else{
            $result =& $user_property_list;
        }
        !empty($result) && shuffle($result);
        return $result;
    }
    
    /**
     * 仅仅获取帖子信息
     * @param $id
     * @param $page
     * @param $pagesize
     */
    public function fetchSimpleById($id)
    {
        $this->db->select($this->_fieldlist);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $query = $this->db->get($this->_tableName . ' cbp');
        return $query->row_array();
    }
    //获取帖子详情
    public function fetchById($id, $userid = 0, $isReturnList = FALSE, $isReturnRoot = FALSE, $return='array')
    {
        $this->db->select($this->_fieldlist . ',tu.unionId,tu.unionName');
        $this->db->join('tbl_union tu', 'tu.unionId = cbp.unionId', 'LEFT');
        $this->db->where('cbp.id', $id);
        //$this->db->where('cbp.userid', $userid);
        $res = $this->db->get($this->_tableName . ' cbp');
        if($res->num_rows() == 1)
        {
            if ($isReturnList)
            {
                if ($isReturnRoot)
                {
                    $result = (array)$res->row();
                    if (!empty($result) && $result['userid'] > 0)
                    {
                        if ($result['rootid'] == 0)
                        {
                            return array($result);
                        }
                        $rootResult = $this->fetchById($result['rootid']);
                        return array($result, $rootResult);
                    }
                    return array();
                }
                else
                {
                    return $res->result_array();
                }
            }
            return $return == 'array' ? (array)$res->row() : $res->row();
        }
    }
    /**
     * 根据结果集里的rootid，分别去相应的blockpost表里去获取root内容
     * 执行之前，需要先执行下$this->set_table_name($blockName)
     * @param array $result
     */
    public function fetch_root_by_id($blockName='', &$result)
    {
        if (is_array($result) && !empty($result))
        {
            $ids = array();
            foreach ($result as $row)
            {
                if ($row['rootid'] > 0)
                {
                    $ids[$row['rootid']] = 1;
                }
            }
            if(count($ids) > 0)
            {
                $this->set_table_name($blockName);
                $this->db->select($this->_fieldlist);
                $this->db->where_in('cbp.id', array_values($ids));
                $pQuery = $this->db->get($this->_tableName . ' cbp');
                $rootlist = $this->_obj2hash($pQuery->result(), 'id');
                if (!empty($rootlist))
                {
                    $exists = $root_result = array();
                    foreach($result as $index => $row)
                    {
                        if (isset($exists[$row['id']]))
                        {
                            unset($result[$index]);
                            continue;
                        }
                        $exists[$row['id']] = 1;
                        if(isset($rootlist[$row['rootid']]))
                        {
                            //$result[$index]['root'] = $rootlist[$row['rootid']];
                            $tmp_root = (array)$rootlist[$row['rootid']];
                            if (isset($exists[$tmp_root['id']]))
                            {
                                unset($result[$tmp_root['id']]);
                                continue;
                            }
                            $exists[$tmp_root['id']] = 1;
                            //应客户端要求，在这里也添加block字段
                            $tmp_root['block'] = str_replace('blockpost', '', $this->_tableName);
                            $root_result[] = $tmp_root;
                        }
                        $result[$index] = $this->_tranNull2empty($result[$index]);
                    }
                    unset($exists);
                    $result = array_merge($result, $root_result);
                }
            }
        }
    }
    /**
     * 查询是否已经发送过[求邀请码]的信息
     * @param $id
     * @param $page
     * @param $pagesize
     */
    public function hasSendInvitePosts($userId, $supportCodeUserId=0, $unionId, $state='new')
    {
        //[寻邀请码]的舶来账号
        $user_sys_invite_id = $this->config->item('USER_SYS_INVITE', 'system_accounts');
        $this->db->select('id');
        $this->db->where('remark', $userId);
        $this->db->where('userid', $user_sys_invite_id);
        $this->db->where('receiveUserId', $supportCodeUserId);
        $this->db->where('unionId', $unionId);
        $this->db->where('state', $state);
        $this->db->limit(1);
        $query = $this->db->get($this->_tableName);
        return $query->row_array();
    }
    /**
     * 查询所有的[群福利]的信息
     * @param $id
     */
    public function getAllGroupAwardList($userId=0, $receiveUserId=1, $unionId=0, $state='new')
    {
        $this->load->model('Userinfo');
        $this->db->select('cbp.id,ui.nickname AS group_title');
        $this->db->join('userinfo ui', 'ui.id=cbp.userid', 'INNER');
        $userId > 0 && $this->db->where('cbp.userid', $userId);
        $receiveUserId > 0 && $this->db->where('cbp.receiveUserId', $receiveUserId);
        $unionId > 0 && $this->db->where('unionId', $unionId);
        $this->db->where('ui.blhRole', Userinfo::USER_ROLE_WELCOME);
        $this->db->where('cbp.state', $state);
        //只查询未过期的群福利帖子
        $this->db->ar_where[] = "AND UNIX_TIMESTAMP(cbp.`remark`) > ".SYS_TIME;
        $this->db->order_by('cbp.posttime' ,'DESC');
        $query = $this->db->get($this->_tableName . ' cbp');
        return $query->result_array();
    }
    protected function _obj2hash($data, $key)
    {
        $ret = array();
        if (!empty($data))
        {
            foreach($data as $row)
            {
                if (isset($row->$key)) $ret[$row->$key] = $row;
            }
        }
        return $ret;
    }
    protected function _tranNull2empty($row)
    {
        $ret = array();
        if (is_array($row) && !empty($row))
        {
            foreach($row as $k=>$v)
            {
                if(is_null($v))
                {
                    $ret[$k]="";
                }
                else if(is_string($v))
                {
                    $ret[$k] = trim($v);
                }
                else if(is_array($v))
                {
                    $ret[$k] = $this->_tranNull2empty($v);
                }
                else
                {
                    $ret[$k] = $v;
                }
            }
        }
        return $ret;
    }
    protected function _updatePostCount()
    {
       //update block set totalPost=(select count(1) from houseblockpost) where name="house";
       $blockname = str_ireplace('blockpost', '', $this->_tableName);
       $sql = "UPDATE `block` SET `updateDay`=CURRENT_DATE,`totalPost`=(SELECT COUNT(1) FROM `{$this->_tableName}`) WHERE `name`='{$blockname}'";
       $this->db->query($sql);
       $today = date('Y-m-d');
       $sql = "UPDATE `block` SET `updateDay`=CURRENT_DATE,`todayPost`=(SELECT COUNT(1) FROM `{$this->_tableName}` WHERE `posttime`>='{$today}') WHERE `name`='{$blockname}'";
       $this->db->query($sql);
    }
}
