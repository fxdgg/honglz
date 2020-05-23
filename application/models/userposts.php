<?php
/**
 * 用户发布消息的数据模型
 *
 */
class Userposts extends CI_Model
{
    protected $_table = 'userposts';
    public $_originTables = array('activityblockpost','communicationblockpost','houseblockpost','loveblockpost','recruitblockpost','secondhandblockpost','welcomeblockpost');
    public $blockLists = array('activity','chatroom','communication','house','love','recommend','recruit','secondhand','sports','welcome');
    
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = TRUE;
    
    public function total($id)
    {
        $this->db->where('userid', $id);
        $count = $this->db->count_all_results($this->_table);
        //echo $this->db->last_query();
        return $count;
    }
    /**
     * 用户发帖列表
     * @param int $id
     * @param int $page
     * @param int $pagesize
     */
    public function itemlist($id, $page, $pagesize, $userId = 0)
    {
        //获取当前用户所加入的社团unionId列表
        $this->load->model('UserUnion');
        $userUnionIds = $this->UserUnion->getMyJoinUnionIdList($userId);
        if (empty($userUnionIds))
        {
            return array();
        }
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s|%s|%s|%s|%s", __CLASS__, __FUNCTION__, $id, $userId, $page, $pagesize);
            $itemlist_data = $this->getCacheData($cache_key);
            if (is_array($itemlist_data) && !empty($itemlist_data))
            {
                return $itemlist_data;
            }
        }
        $this->db->select('viewId,block,id,pid,rootid,title,content,imgUrl,userid,receiveUserId,state,posttime,replycount,lastReplyTime,remark,tu.unionId,tu.unionName');
        $this->db->join('tbl_union tu', 'tu.unionId = up.unionId', 'LEFT');
        $this->db->where('up.userid', $id);
        //当前用户只能查看所加入社团的消息
        $this->db->where_in('up.unionId', $userUnionIds);
        $this->db->order_by('up.posttime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query= $this->db->get($this->_table . ' up');
        //echo $this->db->last_query();
        $result = $query->result_array();
        //根据userposts里的数据组成root的内容
        $this->process_block_root($result);
        if (self::$enableCache && !empty($result))
        {
            $this->setCacheData($cache_key, $result, HOUR_TIMESTAMP);
        }
        return $result;
    }

    /**
     * 是否有新消息
     * @param int $userid
     * @param datetime/timestamp $fromTime
     */
    public function hasNew($userid, $fromTime)
    {
        $count = 0;
        $result = array();
        list($viewIdList, $rootReplySum) = $this->_postRootCareNew($userid);
        if(!empty($viewIdList['root']) OR !empty($viewIdList['posts']))
        {
            //查询最新帖子的内容
            if (isset($viewIdList['posts']) && !empty($viewIdList['posts']))
            {
                $posts_viewIds = array_values($viewIdList['posts']);
                $count = $this->fetch_sum_by_viewid($posts_viewIds, $userid, $fromTime);
            }
        }
        return $count > 0;//$result;
    }
    
    /**
     * 我的消息列表
     * @param int $userid
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function mine($userid, $page = 1, $pagesize = 20, $timestamp = 0)
    {
        list($viewIdList, $rootReplySum) = $this->_postRootCareNew($userid, $timestamp);
        $ret = array('posts'=>array(), 'root'=>array(), 'users'=>array());
        if(!empty($viewIdList['root']) OR !empty($viewIdList['posts']))
        {
            $userIds = array();
            //查询最新帖子的内容
            if (isset($viewIdList['posts']) && !empty($viewIdList['posts']))
            {
                $posts_viewIds = array_values($viewIdList['posts']);
                $ret['posts'] = $this->fetch_data_by_viewid('posts', $posts_viewIds, $userid, $page, $pagesize, $rootReplySum, $userIds);
            }
            //查询root帖子的内容
            if (isset($viewIdList['root']) && !empty($viewIdList['root']))
            {
                $root_viewIds = array_values($viewIdList['root']);
                $ret['root'] = $this->fetch_data_by_viewid('root', $root_viewIds, $userid, $page, $pagesize, $rootReplySum, $userIds);
            }
            //批量获取用户信息
            $this->get_user_by_batch($userIds, $ret);
        }
        return $ret;
    }

    /**
     * 我的消息列表
     * @param int $userid
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function mineTest($userid, $page = 1, $pagesize = 20, $timestamp = 0)
    {
        list($viewIdList, $rootReplySum) = $this->_postRootCareNew($userid);
        $ret = array('posts'=>array(), 'root'=>array());
        if(!empty($viewIdList['root']) OR !empty($viewIdList['posts']))
        {
            //查询最新帖子的内容
            if (isset($viewIdList['posts']) && !empty($viewIdList['posts']))
            {
                $posts_viewIds = array_values($viewIdList['posts']);
                $ret['posts'] = $this->fetch_data_by_viewid('posts', $posts_viewIds, $userid, $page, $pagesize, $rootReplySum);
            }
            //查询root帖子的内容
            if (isset($viewIdList['root']) && !empty($viewIdList['root']))
            {
                $root_viewIds = array_values($viewIdList['root']);
                $ret['root'] = $this->fetch_data_by_viewid('root', $root_viewIds, $userid, $page, $pagesize, $rootReplySum);
            }
        }
        return $ret;
    }
    /**
     * 帖子是(a+b+c+d+e)
     * a b c d中，有登录者发布的就算
     * a是root，e是最新回复
     * 确定了这样的帖子后，如果e是登录者发布的，就把e去掉返回，如果e不是登录者发布的，那就包括e全都返回
     * @param $userid
     */
    protected function _postRootCareNew($userid, $timestamp = 0)
    {
        $ret = array('root'=>array(),'posts'=>array());
        //获取当前用户所加入的社团unionId列表
        $this->load->model('UserUnion');
        $userUnionIds = $this->UserUnion->getMyJoinUnionIdList($userid);
        if (empty($userUnionIds))
        {
            return array($ret, array());
        }
        $this->load->model('Block');
        //系统帐号ID列表
        $user_system_accounts = $this->config->item('system_accounts');
        $this->db->select('viewId,block,id,pid,rootid,posttime,userid,receiveUserId,unionId,state,replycount,lastReplyTime,remark,type');
        //当前用户只能查看所加入社团的消息
        //$this->db->where_in('unionId', $userUnionIds);
        $this->db->ar_where[] = "(unionId IN ('".join("','", $userUnionIds)."')";
        //不显示舶来等用户信息
        $this->db->ar_where[] = "AND `userid` NOT IN ('".join("','", $user_system_accounts)."') AND `block` != 'welcome'";
        //只显示自己的信息
        $this->db->ar_where[] = "AND (`userid` = '{$userid}' OR `receiveUserId` = '{$userid}')";
        //显示除welcome之外的所有属于自己的信息
        //$this->db->ar_where[] = "OR (`userid`='{$userid}' AND `block`!='welcome')"; // AND `rootid`=0 AND `replycount` > 0
        //信息流里不应有加入新人的信息。加入新人都在舶来里
        //$this->db->ar_where[] = "OR (`block`='welcome' AND `remark`='{$userid}' AND `userid` !='{$user_system_accounts['USER_SYS_WELCOME']}')";
        //私聊、邀请码的消息
        $this->db->ar_where[] = "OR (`receiveUserId`='{$userid}' AND `type` IN ('".join("','", Block::$message_type_config)."')))";// AND `unionId`=0
        //查询该时间之后的消息
        $timestamp > 0 && $this->db->ar_where[] = "AND posttime >= '".date('Y-m-d H:i:s', $timestamp)."'";
        
        $this->db->order_by('`posttime` DESC');
        $query = $this->db->get($this->_table);
        $data = $query->result('object', 'id');
        $rootWhere = $replyResult = $replyWhere = $root_exists = $root_reply_sum = array();
        if (!empty($data))
        {
            foreach($data as $row)
            {
                //查找最新回复的帖子信息
                if (isset($row->rootid) && ($row->rootid == 0) && (int)$row->replycount > 0)
                {
                    //收集该用户发布的root信息
                    $ret['root'][$row->id] = $row->viewId;
                    $rootWhere[] = " (`rootid`={$row->id}) ";//  AND `block`='{$row->block}'
                }
                else if (isset($row->rootid) && $row->rootid > 0)
                {
                    if (isset($root_exists[$row->rootid]))
                    {
                        continue;
                    }
                    !isset($root_exists[$row->rootid]) && $root_exists[$row->rootid] = 0;
                    $root_exists[$row->rootid] += 1;
                    //收集该用户的回复信息
                    $rootWhere[] = " (`rootid`={$row->rootid} OR `id`={$row->rootid}) ";
                    /*$sql = "SELECT `viewId`,`id`,`rootid`,`userid` FROM `{$this->_table}` WHERE `rootid`={$row->rootid} ORDER BY `posttime` DESC"; 
                    $rQuery = $this->db->query($sql);
                    $tmpResult = $rQuery->result();
                    if (!empty($tmpResult))
                    {
                        $replyResult = array_merge($replyResult, $tmpResult);
                    }*/
                }
                else if (isset($row->block) && $row->block == 'welcome')
                {
                    $ret['posts'][$row->id] = $row->viewId;
                }
                else if (isset($row->receiveUserId) && $row->receiveUserId > 0)// && $row->unionId == 0
                {
                    //私聊的消息
                    $ret['posts'][$row->id] = $row->viewId;
                }
            }
            /*if (count($replyWhere) > 0)
            {
                $wheresql = join('OR', $replyWhere);
                $sql = "SELECT `viewId`,`id`,`rootid`,`userid` FROM `{$this->_table}` WHERE {$wheresql} ORDER BY `posttime` DESC"; 
                $rQuery = $this->db->query($sql);
                $replyResult = $rQuery->result();
            }*/
            //log_message('debug', 'Method=>'.__METHOD__.'@@Line=>'.__LINE__.'@@$ret=>'.var_export($ret,true).'@@$rootWhere=>'.var_export($rootWhere,true).'@@$root_exists=>'.var_export($root_exists,true));
            unset($root_exists, $data);
            if (count($rootWhere) > 0)
            {
                $wheresql = join('OR', $rootWhere);
                $sql = "SELECT `viewId`,`id`,`pid`,`rootid`,`userid`,`receiveUserId`,`unionId`,`remark` FROM `{$this->_table}` WHERE (`block`!='welcome') AND ({$wheresql}) ORDER BY `posttime` DESC"; 
                $rQuery = $this->db->query($sql);
                $rootResult = $rQuery->result();
                $this->_processPostsStep($userid, $ret, $rootResult, $root_reply_sum);
            }
            /*if (count($replyResult) > 0)
            {
                $this->_processPostsStep($userid, $ret, $replyResult);
            }*/
            //log_message('debug', 'Method=>'.__METHOD__.'@@Line=>'.__LINE__.'@@$ret=>'.var_export($ret,true));
        }
        return array($ret, $root_reply_sum);
    }
    protected function _processPostsStep($userid, &$ret, &$result, &$root_reply_sum)
    {
        if (!empty($result))
        {
            // 每条root信息对应有几条回复信息
            $total_rootids = $real_rootids = $uid_rootids = array();
            foreach($result as $rKey => $row)
            {
                if (empty($row) OR !isset($row->userid) OR empty($row->userid))
                {
                    unset($result[$rKey]);
                    continue;
                }
                !isset($total_rootids[$row->rootid]) && $total_rootids[$row->rootid] = 0;
                $total_rootids[$row->rootid] += 1;
                !isset($uid_rootids[$row->rootid][$row->userid]) && $uid_rootids[$row->rootid][$row->userid] = 0;
                $uid_rootids[$row->rootid][$row->userid] += 1;
                //表示最新回复的帖子，是用户自己回复的，所以不能显示
                if (isset($row->userid) && $row->userid == $userid)
                {
                    //私聊的消息
                    if ($row->receiveUserId > 0 && $row->pid > 0 && $row->rootid > 0)// && $row->unionId == 0
                    {
                        $ret['posts'][$row->id] = $row->viewId;
                        continue;
                    }
                    //检测之前是否有非登录者(userid)的信息
                    $is_exists = FALSE;
                    if (isset($uid_rootids[$row->rootid]) && !empty($uid_rootids[$row->rootid]))
                    {
                        foreach ($uid_rootids[$row->rootid] as $rootidUid => $rootidVal)
                        {
                            if (strcasecmp($rootidUid, $userid) != 0)
                            {
                                $is_exists = TRUE;
                                break;
                            }
                        }
                    }
                    if (FALSE == $is_exists)
                    {
                        !isset($real_rootids[$row->rootid]) && $real_rootids[$row->rootid] = 0;
                        $real_rootids[$row->rootid] += 1;
                        unset($result[$rKey]);
                        continue;
                    }
                    else if (isset($row->rootid) && $row->rootid > 0)
                    {
                        $ret['posts'][$row->id] = $row->viewId;
                        !isset($root_reply_sum[$row->rootid]) && $root_reply_sum[$row->rootid] = 0;
                        $root_reply_sum[$row->rootid] += 1;
                    }
                }
                else if (isset($row->userid) && $row->userid != $userid)
                {
                    //root信息不能放到posts中
                    $row->rootid > 0 && $ret['posts'][$row->id] = $row->viewId;
                    $row->rootid == 0 && !isset($ret['root'][$row->id]) && $ret['root'][$row->id] = $row->viewId;
                    !isset($root_reply_sum[$row->rootid]) && $root_reply_sum[$row->rootid] = 0;
                    $root_reply_sum[$row->rootid] += 1;
                }
            }
            //log_message('debug', 'Method=>'.__METHOD__.'@@Line=>'.__LINE__.'@@$ret=>'.var_export($ret,true).'@@$total_rootids=>'.var_export($total_rootids,true).'@@$real_rootids=>'.var_export($real_rootids,true).'@@$uid_rootids=>'.var_export($uid_rootids,true));
            // 去除不符合条件的root信息
            if (isset($ret['root']) && !empty($ret['root']))
            {
                foreach ($ret['root'] as $root_id => $root_viewid)
                {
                    if (isset($total_rootids[$root_id]) && isset($real_rootids[$root_id])
                        && ($total_rootids[$root_id] == $real_rootids[$root_id]))
                    {
                        unset($ret['root'][$root_id]);
                    }
                }
            }
            //log_message('debug', 'Method=>'.__METHOD__.'@@Line=>'.__LINE__.'@@$ret=>'.var_export($ret,true));
        }
    }
    /**
     * 先用userid=登录者，查到root帖，然后再查root帖的最新帖，如果这个最新帖是登录者回复的，就去掉
     * 如果不是登录者回复的，则把最新帖子放到列表里
     * @param $userid
     */
    protected function _postRootCare($userid)
    {
        $this->db->select('viewId,block,id,pid,rootid,posttime,state,lastReplyTime,remark');
        $this->db->ar_where[] = "(`userid`='{$userid}' AND `block`!='welcome' AND `rootid`=0 AND `replycount` > 0)";
        $this->db->ar_where[] = " OR (`block`='welcome' AND `remark`='{$userid}')";
        $this->db->order_by('`posttime` DESC');
        $query = $this->db->get($this->_table);
        $data = $query->result();
        $ret = $rootWhere = array();
        if (!empty($data))
        {
            foreach($data as $row)
            {
                //查找最新回复的帖子信息
                if (isset($row->remark) && ($row->block != 'welcome') && (int)$row->remark > 0)
                {
                    $ret['root'][$row->id] = $row->viewId;
                    $rootWhere[] = " (`id`='{$row->remark}' AND `block`='{$row->block}' AND `rootid`={$row->id}) ";
                }else if (isset($row->block) && $row->block == 'welcome')
                {
                    $ret['posts'][$row->id] = $row->viewId;
                }
            }
            if(count($rootWhere) > 0)
            {
                $wheresql = join('OR', $rootWhere);
                $sql = "SELECT `viewId`,`id`,`rootid`,`userid` FROM `{$this->_table}` WHERE {$wheresql} ORDER BY `posttime` DESC"; 
                $rQuery = $this->db->query($sql);
                $rootResult = $rQuery->result();
                if (!empty($rootResult))
                {
                    foreach($rootResult as $row)
                    {
                        if (empty($row) OR !isset($row->userid))
                        {
                            continue;
                        }
                        //表示最新回复的帖子，是用户自己回复的，所以不能显示
                        if (isset($row->userid) && $row->userid == $userid)
                        {
                            if (isset($ret['root'][$row->rootid])) unset($ret['root'][$row->rootid]);
                            continue;
                        }
                        else
                        {
                            $ret['posts'][$row->id] = $row->viewId;
                        }
                    }
                }
            }
        }
        return $ret;
    }
    /**
    * 获取我发的贴子或者我回过的帖子的viewId
    * 我发布的消息，但是必须是【被其他人回复过的】，需要查userid=我、rootid=0、lastReplyTime大于posttime的
    * 我回复过的消息，需要查userid=我、rootid != 0的
    */
    protected function _postRootCareBak($userid)
    {
        $this->db->select('viewId,block,id,rootid,posttime,state,replycount,lastReplyTime');
        //$query = $this->db->get_where($this->_table, array('userid'=>$userid));
        $this->db->ar_where[] = "(`userid`='{$userid}' AND `block`!='welcome')";
        $this->db->ar_where[] = " OR (`remark`='{$userid}' AND `block`='welcome')";
        $this->db->order_by('`posttime` DESC');
        $query = $this->db->get($this->_table);
        /*$sql = "SELECT `viewId`,`block`,`id`,`rootid`,`posttime`,`state`,`lastReplyTime`,`remark` 
            FROM `{$this->_table}` WHERE (`userid`='{$userid}' AND `block`!='welcome') 
            OR (`remark`='{$userid}' AND `block`='welcome')
            ORDER BY `posttime` DESC"; 
        $query = $this->db->query($sql);*/
        $data = $query->result();
        //echo $this->db->last_query();
        $ret = array();

        $rootWhere = array();
        if (!empty($data))
        {
            foreach($data as $row)
            {
                //rootid == 0表示是我本人发布的帖子、replycount大于0的(lastReplyTime大于posttime的)表示被其他人回复过的
                if ($row->rootid == 0 && $row->replycount > 0)
                {
                    $ret[] = $row->viewId;
                }
                /*else if ($row->rootid > 0)
                {
                    //rootid != 0表示我回复的帖子
                    $rootWhere[] = " (`block`='{$row->block}' AND `rootid`={$row->rootid}) ";
                }*/
            }
        }

        if(count($rootWhere) > 0)
        {
            $wheresql = join('OR', $rootWhere);
            $sql = "SELECT `viewId` FROM `{$this->_table}` WHERE `userid`='{$userid}' AND ({$wheresql}) ORDER BY `posttime` DESC"; 
            $rQuery = $this->db->query($sql);
            //echo $this->db->last_query();
            $rootResult = $rQuery->result();
            if (!empty($rootResult))
            {
                foreach($rootResult as $row)
                {
                    $ret[] = $row->viewId;
                }
            }
        }
        return $ret;

    }
    
    /**
     * 根据viewid获取相应的block内容
     * @param array $result
     */
    protected function fetch_data_by_viewid($tag = 'posts', &$viewIds, $userid, $page, $pagesize, &$rootReplySum, &$userIds)
    {
        //[寻邀请码]的舶来账号
        $user_sys_invite_id = $this->config->item('USER_SYS_INVITE', 'system_accounts');
        $this->db->select(array_merge(explode(',','up.block,up.id,up.pid,up.rootid,up.title,up.content,up.imgUrl,up.userid,up.receiveUserId,up.state,up.posttime,up.replycount,up.lastReplyTime,up.remark,up.isRead,up.type,tu.unionId,tu.unionName'), array('IFNULL(tuu.unionId > 0, 0) AS inviteStatus')));
        $this->db->join('tbl_union tu', 'tu.unionId = up.unionId', 'LEFT');
        $this->db->join('tbl_user_union tuu', 'tuu.invitedBy=up.receiveUserId AND up.remark=tuu.userid AND up.unionId=tuu.unionId AND up.userid=' . $user_sys_invite_id, 'LEFT');
        $this->db->where_in('up.viewId', $viewIds);
        //$this->db->where('userid', $userid);
        //查询最新回复的帖子使用的条件
        if ($tag == 'posts')
        {
            //$this->db->ar_where[] = " OR (`block`='welcome' AND `remark`='{$userid}')";
            $this->db->limit($pagesize, ($page-1)*$pagesize);
        }
        $this->db->order_by('up.posttime DESC,up.id DESC');
        $query = $this->db->get($this->_table . ' up');
        $ret = $query->result_array();
        //返回root数据的实际的回复数
        if (is_array($ret) && !empty($ret))
        {
            foreach ($ret as &$root_item)
            {
                if ($tag == 'root')
                {
                    if ($root_item['block'] != 'welcome' && $root_item['rootid'] == 0)
                    {
                        if (isset($rootReplySum[$root_item['id']]))
                        {
                            $root_item['replycount'] = $rootReplySum[$root_item['id']];
                        }
                    }
                }
                //汇总userid
                if (isset($root_item['userid']) && $root_item['userid'] > 0)
                {
                    $userIds[$root_item['userid']] = 1;
                }
            }
        }
        return $ret;
    }
    /**
     * 根据viewid获取总的block内容
     * @param array $result
     */
    protected function fetch_sum_by_viewid(&$viewIds, $userid, $fromTime)
    {
        $this->db->select('id,block');
        $this->db->where_in('viewId', $viewIds);
        //$this->db->where('userid', $userid);
        //$this->db->ar_where[] = " OR (`block`='welcome' AND `remark`='{$userid}')";
        //查询该时间之后的消息
        $this->db->where('posttime >=', $fromTime);
        //查询未读的消息
        #$this->db->where('isRead', 0);
        $count = $this->db->count_all_results($this->_table);
        return $count;
        //$this->db->limit(10);
        //$this->db->order_by('posttime DESC,id DESC');
        #$query = $this->db->get($this->_table);
        #$result = $query->result_array();
        #return $result;
    }
    /**
     * 根据userposts里的数据组成root的内容
     * @param array $result
     */
    protected function process_block_root(&$result)
    {
        if (is_array($result) && !empty($result))
        {
            $exists = $root_result = array();
            foreach ($result as $post_key => $post_value)
            {
                if (isset($post_value['rootid']) && $post_value['rootid'] > 0)
                {
                    if (isset($exists[$post_value['id']]))
                    {
                        continue;
                    }
                    $exists[$post_value['id']] = 1;
                    $root_result[] = array(
                        'id' => $post_value['id'],
                        'userid' => $post_value['userid'],
                        'pid' => $post_value['pid'],
                        'rootid' => $post_value['rootid'],
                        'title' => $post_value['title'],
                        'content' => $post_value['content'],
                        'posttime' => $post_value['posttime'],
                        'block' => $post_value['block'],
                    );
                }
            }
            $result = array_merge($result, $root_result);
        }
    }
    //批量获取用户信息
    protected function get_user_by_batch($uids, &$ret)
    {
        if(count($uids) > 0)
        {
            $this->load->model('Userinfo');
            $ret['users'] = array_values($this->Userinfo->batchUser($uids));
        }
    }
}
