<?php
class Userinfo extends CI_Model{
    public $weibo;
    public $USER_STATUS_REGISTER=0;
    public $USER_STATUS_OK=1;
    public $USER_STATUS_STOPPED=2;

    public $USER_TYPE_NORMAL=0;
    public $USER_TYPE_PUBLIC=1;
    /**
     * 普通用户
     * @var int
     */
    const USER_ROLE_COMMON = 0;
    /**
     * 社团管理员
     * @var int
     */
    const USER_ROLE_UNION = 1;
    /**
     * 舶来管理员
     * @var int
     */
    const USER_ROLE_WELCOME = 2;
    /**
     * 系统管理员
     * @var int
     */
    const USER_ROLE_SYSTEM = 3;

    protected $_table = 'userinfo';
    protected $_table_tauth_tokens = 'tbl_tauth_tokens';
    protected $_basicFields = array('id','nickname',/*"icon",*/'iconUrl','status');
    protected $_infoFields = array('email','sex','company','position','cellphone','qq','weibo','weixin');
    protected $_autoFields = array('inviteCount', 'inviteIntro', 'invitedby');
    protected $_allFields = array('ui.id','ui.email',/*'ui.passwd',*/'ui.sex','ui.nickname','ui.realname','ui.blhRole','ui.company','ui.position','ui.cellphone',
        'ui.qq','ui.weibo','ui.weixin','ui.other',/*'ui.icon',*/'ui.blogUrl','ui.iconUrl','ui.invitedby','ui.inviteIntro','ui.inviteCount','ui.publicemail','ui.publicsex',
        'ui.publiccompany','ui.publicposition','ui.publiccellphone','ui.publicqq','ui.publicweibo','ui.publicweixin','ui.publicother','ui.status',
        'ui.usertype','ui.createTime','ui.updateTime','ui.disableReason','ui.career','ui.area','ui.birthday','ui.isMarried','ui.signature','ui.sinaUserId',
        'ui.qqUserId','ui.lastActivity','ui.vocation','ui.workDate','ui.gradeSchool','ui.category','ui.isHlz','ui.subjection_uid','ui.my_money','ui.partner_money','ui.actual_money'
    );
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = FALSE;

    function __construct()
    {
        parent::__construct();
    }

    public function addNew($data)
    {
        if(!isset($data['passwd']) OR empty($data['passwd']) OR ($data['passwd'] != $data['passwdconf']))
        {
            return -1;
        }
        if (isset($data['invitedby'])) unset($data['invitedby']);
        if (isset($data['invitationcode'])) unset($data['invitationcode']);
        if (isset($data['passwdconf'])) unset($data['passwdconf']);
        if (isset($data['inviteCount'])) unset($data['inviteCount']);
        
        $data['passwd'] = md5($this->config->item('salt').$data['passwd']);
        //$data['passwd'] = md5($data['passwd']);
        if (isset($data['birthday']))
        {
            if (empty($data['birthday']))
            {
                $data['birthday'] = NULL;
            }
            else
            {
                list($year, $month, $day) = explode('-', $data['birthday']);
                $is_date = checkdate($month, $day, $year);
                if (!$is_date)
                {
                    $data['birthday'] = NULL;
                }
            }
        }
        if (!isset($data['updateTime']))
        {
            $data['updateTime'] = date('Y-m-d H:i:s', SYS_TIME);
        }
        //禁用用户的原因
        if (!empty($data['disableReason']))
        {
            $data['disableReason'] = urldecode($data['disableReason']);
        }
        
        $ret = $this->db->insert($this->_table, $data);
        if($ret){
            $newUserId = $this->db->insert_id();
            //系统默认赠送100荔枝币
            //系统默认赠送的荔枝币
            $user_default_lizhi = $this->config->item('default_lizhi', 'system_money_config');
            $this->load->model('UserMoney');
            $this->UserMoney->addMoney($newUserId, $user_default_lizhi);
            return $newUserId;
        }
        return -2;
    }
    /**
     * 使用邀请码
     * @param int $userid    (被邀请人的UID)
     * @param array $inviteInfo    (发起邀请人的UID|邀请理由)
     */
    public function updateInvitation($userid, $inviteInfo)
    {
        $data = array('status' => 1);
        list($data['invitedby'], $data['inviteIntro'], $data['unionId']) = $inviteInfo;
        $inviter = $data['invitedby'];
        unset($data['unionId']);
        $this->db->where('id', $userid);
        //之前是只允许注册的用户使用邀请码
        //$this->db->where('status', $this->USER_STATUS_REGISTER);
        $res = $this->db->update($this->_table, $data);
        
        //add count
        if($inviter && $res){
            //更新发起邀请人的邀请数
            //$this->db->where('id', $inviter);
            //$this->db->update($this->_table, array('inviteCount'=>'inviteCount+1'));
            $this->db->query("update `{$this->_table}` set `inviteCount`=`inviteCount`+1 where `id` = {$inviter}");
            return TRUE;
        }
        return FALSE;
    }

    public function edit($id, $data)
    {
        $this->db->where('id', $id);
        if(isset($data['passwd']))
        {
            $data['passwd'] = md5($this->config->item('salt').$data['passwd']);
        }
        //邮箱不可编辑
        if (isset($data['email']))
        {
            $email = $data['email'];
            unset($data['email']);
        }
        //不为空时再更新
        if (isset($data['nickname']) && empty($data['nickname'])) unset($data['nickname']);
        if (isset($data['sex']) && empty($data['sex'])) unset($data['sex']);
        if (isset($data['iconUrl']) && empty($data['iconUrl'])) unset($data['iconUrl']);
        if (isset($data['company']) && empty($data['company'])) unset($data['company']);
        if (isset($data['position']) && empty($data['position'])) unset($data['position']);
        //过滤不合格的iconUrl
        if (isset($data['iconUrl']) && empty($data['iconUrl']))
        {
            $data['iconUrl'] = str_replace(APP_SITE_URL, '', $data['iconUrl']);
        }
        foreach($this->_autoFields as $f)
        {
            if(isset($data[$f]))
            {
                unset($data[$f]);
            }
        }
        if (isset($data['birthday']))
        {
            if (empty($data['birthday']))
            {
                $data['birthday'] = NULL;
            }
            else
            {
                list($year, $month, $day) = explode('-', $data['birthday']);
                $is_date = checkdate($month, $day, $year);
                if (!$is_date)
                {
                    $data['birthday'] = NULL;
                }
            }
        }
        if (!isset($data['updateTime']))
        {
            $data['updateTime'] = date('Y-m-d H:i:s', SYS_TIME);
        }
        //禁用用户的原因
        if (!empty($data['disableReason']))
        {
            $data['disableReason'] = urldecode($data['disableReason']);
        }
        //用户所在区域
        if (isset($data['area']) && !empty($data['area']))
        {
            $data['area'] = urldecode($data['area']);
        }
        //QQ号
        if (isset($data['qq']) && !empty($data['qq']))
        {
            $data['qq'] = htmlspecialchars($data['qq']);
        }
        //行业
        if (isset($data['vocation']) && !empty($data['vocation']))
        {
            $data['vocation'] = htmlspecialchars($data['vocation']);
        }
        //工作时间
        if (isset($data['workDate']) && !empty($data['workDate']))
        {
            $data['workDate'] = htmlspecialchars($data['workDate']);
        }
        //毕业学校
        if (isset($data['gradeSchool']) && !empty($data['gradeSchool']))
        {
            $data['gradeSchool'] = htmlspecialchars($data['gradeSchool']);
        }
        //用户的职业分类
        if (isset($data['category']) && !empty($data['category']))
        {
            $data['category'] = htmlspecialchars($data['category']);
        }
        // 隶属于哪个用户ID
        if (isset($data['subjection_uid']) && !empty($data['subjection_uid']))
        {
            $data['subjection_uid'] = intval($data['subjection_uid']);
        }
        // 自有金额，单位分
        if (isset($data['my_money']) && !empty($data['my_money']))
        {
            $data['my_money'] = intval($data['my_money']);
        }
        // 合作者金额，单位分
        if (isset($data['partner_money']) && !empty($data['partner_money']))
        {
            $data['partner_money'] = intval($data['partner_money']);
        }
        // 实际金额，单位分
        if (isset($data['actual_money']) && !empty($data['actual_money']))
        {
            $data['actual_money'] = intval($data['actual_money']);
        }
        $res = $this->db->update($this->_table, $data);
        //开启缓存的处理
        if (self::$enableCache && $res)
        {
            $cache_key = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'id', $id);
            $cache_key_userone = sprintf("%s|%s|%d|%d", 'Userinfo', 'userone', $id, $id);
            if (empty($email))
            {
                $user_data = $this->getCacheData($cache_key, 'object');
                if (!empty($user_data))
                {
                    $user_data = (array)$user_data;
                    $email = (isset($user_data['email']) && !empty($user_data['email'])) ? $user_data['email'] : '';
                    //删除微博登录的缓存
                    if (isset($user_data['sinaUserId']) && !empty($user_data['sinaUserId']))
                    {
                        $cache_key_weibo = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'sinaUserId', $user_data['sinaUserId']);
                        $this->getCacheAdapter()->delete($cache_key_weibo);
                    }
                }
            }
            $this->getCacheAdapter()->delete($cache_key);
            $this->getCacheAdapter()->delete($cache_key_userone);
            if (!empty($email))
            {
                $cache_key_email = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'email', $email);
                $this->getCacheAdapter()->delete($cache_key_email);
            }
        }
        return $res;
    }
    /**
     * 编辑过往履历
     * @param $userid
     * @param $data
     */
    public function editJobs($userid, $data)
    {
        if (!isset($data['jobsList']) OR empty($data['jobsList']))
        {
            return FALSE;
        }
        $jobsList = json_decode($data['jobsList'], TRUE);
        if (!is_array($jobsList) OR empty($jobsList))
        {
            return FALSE;
        }
        $this->load->model('UserJobs');
        $ret = FALSE;
        foreach ($jobsList as $jobsItem)
        {
            //当前登录用户
            $jobsItem['userid'] = $userid;
            if (isset($jobsItem['id']) && !empty($jobsItem['id']))
            {
                $ret = $this->UserJobs->updateUserJobs($jobsItem);
            }
            else
            {
                $ret = $this->UserJobs->createUserJobs($jobsItem);
            }
        }
        return $ret;
    }
    //验证是否存在该ID的记录
    public function isValidId($id)
    {
        $this->db->where('id', $id);
        return 1 == $this->db->count_all_results($this->_table);
    }
    /**
     * 用户登录后的处理逻辑
     * @param $userid 用户UID
     * @param $username 用户昵称
     * @param $user_data 用户基本信息数据
     * @param $token 用户在微博登录后的token信息
     */
    public function afterLogin($userid, $username, $user_data = array(), $token = array())
    {
        $this->load->library('session');
        $data = array('userid'=>$userid, 'nickname'=>$username, 'status'=>$user_data->status, 'logged_in'=>1, 'login_time'=>SYS_TIME, 'user_data'=>$user_data, 'token'=>$token);
        $this->session->set_userdata($data);
        unset($user_data);
        //更新用户最近登录时间
        $this->db->where('id', $userid);
        $user_update_data = array('lastActivity' => SYS_TIME);
        $this->db->update($this->_table, $user_update_data);
    }
    //获取用户状态
    public function checkStatus($userid)
    {
        $user_data = $this->fetch_user_data($userid, 'id', 'object');
        if ($user_data == FALSE)
        {
            return array(-1, $user_data);
        }
        return array($user_data->status, $user_data);
        
        /*$this->db->select('id,status');
        $this->db->where('id', $userid);
        //$this->db->where("status", $this->USER_STATUS_OK);
        $query = $this->db->get($this->_table);
        if($query->num_rows() == 0)
        {
            return -1;
        }
        return $query->row()->status;*/
    }

    public function checkUserPass($email, $passwd)
    {
        $user_data = $this->fetch_user_data($email, 'email', 'object', array('ui.passwd'));
        if ($user_data != FALSE && !empty($user_data))
        {
            if (!empty($user_data->passwd) && $user_data->passwd == md5($this->config->item('salt').$passwd))
            {
                 return array('id'=>$user_data->id, 'nickname'=>$user_data->nickname, 'realname'=>$user_data->realname, 'email'=>$user_data->email,'sex'=>$user_data->sex,'iconUrl'=>$user_data->iconUrl,'company'=>$user_data->company,'position'=>$user_data->position,'blhRole'=>$user_data->blhRole, 'regState'=>$user_data->status, 'user_data'=>$user_data);
            }
        }
        return FALSE;
        
        /*$this->db->select('id, nickname, passwd, status');
        $this->db->where('email', $email);
        //$this->db->where("status", $this->USER_STATUS_OK);
        $query = $this->db->get($this->_table);
        if($query->num_rows() == 1){
            $row = $query->row();
            if($row->passwd == md5($this->config->item('salt').$passwd)){
                 return array('id' => $row->id, 'nickname' => $row->nickname, 'regState' => $row->status);
            }
        }
        return false;*/
    }
    public function fetch_user_data($search_tag = 0, $type = 'id', $return = 'array', $special_fields = array())
    {
        if (!empty($search_tag))
        {
            //开启缓存的处理
            if (self::$enableCache)
            {
                $cache_key = sprintf("%s|%s|%s|%s", __CLASS__, __FUNCTION__, $type, $search_tag);
                $user_data = $this->getCacheData($cache_key, $return);
                if (!empty($user_data))
                {
                    return $user_data;
                }
            }
            $user_data_db = $this->fetch_user_data_db($search_tag, $type, $return, $special_fields);
            if (self::$enableCache && !empty($user_data_db))
            {
                $this->setCacheData($cache_key, $user_data_db);
                if ($type == 'email' OR $type == 'weiboEmail' OR $type == 'sinaUserId')
                {
                    $uid = $return == 'array' ? $user_data_db['id'] : $user_data_db->id;
                    $cache_key = sprintf("%s|%s|%s|%s", __CLASS__, __FUNCTION__, 'id', $uid);
                    $this->setCacheData($cache_key, $user_data_db);
                }
            }
            return $user_data_db;
        }
        return FALSE;
    }
    public function fetch_user_data_db($search_tag = 0, $type = 'id', $return = 'array', $special_fields = array())
    {
        if (!empty($search_tag))
        {
            //查询这些字段的数据
            $this->db->select(array_merge($this->_allFields, $special_fields));
            $this->db->where('ui.'.$type, $search_tag);
            $res = $this->db->get($this->_table . ' ui');
            if($res->num_rows() == 1)
            {
               return $return == 'array' ? (array)$res->row() : $res->row();
            }
        }
        return FALSE;
    }
    /**
     * 通过id来获取某用户的信息
     * @param $email
     */
    public function fetch_user_by_id($id, $return = 'array'){
        return $this->fetch_user_data($id, 'id', $return);
    }
    /**
     * 通过email来获取某用户的信息
     * @param $email
     */
    public function fetch_user_by_email($email, $return = 'array')
    {
        $email_data = $this->fetch_user_data($email, 'email', $return);
        if (!is_array($email_data) OR empty($email_data))
        {
            $email_data = $this->fetch_user_data($email, 'weiboEmail', $return);
        }
        return $email_data;
    }
    /**
     * 通过微博/QQ的UID来获取某用户的信息
     * @param $email
     */
    public function fetch_user_by_third($id, $return = 'array', $third_field = 'sinaUserId')
    {
        return $this->fetch_user_data($id, $third_field, $return);
    }
    /**
     * 获取最大的[舶来]帐号的UID
     */
    public function fetch_max_welcome_userid($min_welcome_uid=0, $max_welcome_uid =0)
    {
        if ($min_welcome_uid <= 0)
        {
            //最小的[舶来]账户的UID
            $min_welcome_uid = $this->config->item('min_welcome_uid', 'system_admin_config');
        }
        if ($max_welcome_uid <= 0)
        {
            //最大的[舶来]账户的UID
            $max_welcome_uid = $this->config->item('max_welcome_uid', 'system_admin_config');
        }
        //$this->db->select('MAX(id) AS maxId');
        $this->db->select_max('id');
        $this->db->where('id >=', $min_welcome_uid);
        $this->db->where('id <=', $max_welcome_uid);
        $this->db->limit(1);
        $res = $this->db->get($this->_table);
        $result = $res->row_array();
        return (isset($result['id']) && !empty($result['id'])) ? $result['id'] : FALSE;
    }
    public function batchUser($idArr)
    {
        $idArr = array_keys($idArr); //array_unique($idArr);
        $fieldStr = join(',',$this->_basicFields);
        $this->db->select($fieldStr);
        $this->db->where_in('id', $idArr);
        $query= $this->db->get($this->_table);
        $result = $query->result_array();
        $ret = array();
        foreach($result as $row){
            $id = $row['id'];
            $row['userid'] = $row['id'];
            if (isset($row['icon']) && empty($row['icon'])) $row['icon'] = '';
            $ret[$id] = $this->_tranNull2empty($row);
        }
        return $ret;
    }
    /**
     * 获取所有用户信息
     * @param $page
     * @param $pagesize
     * @param $timestamp
     */
    public function allUserList($page = 1, $pagesize = 50, $unionId = 0, $userId = 0)
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s|%s|%s|%s|%s", __CLASS__, __FUNCTION__, $page, $pagesize, $unionId, $userId);
            $userlist_data = $this->getCacheData($cache_key);
            if (is_array($userlist_data) && !empty($userlist_data))
            {
                return $userlist_data;
            }
        }
        $condition = '';
        //查询这些字段的数据
        $this->db->select(array_merge($this->_allFields, array('tum.lizhi','GROUP_CONCAT(tuu.unionId ORDER BY tuu.createTime) AS unionIdMuti','GROUP_CONCAT(`tuu`.unionRole) AS unionRoleMuti','GROUP_CONCAT(tu.unionName) AS unionNameMuti')));
        $this->db->join('tbl_user_union tuu', 'tuu.userid = ui.id', 'LEFT');
        #$this->db->join('userinfo ui', 'ui.id = tuu.userid', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->join('tbl_user_money tum', 'tum.userid = ui.id', 'LEFT');
        /*$this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        $this->db->where('ui.position != ', '');
        $this->db->where('ui.company != ', '');*/
        $unionId > 0 && $this->db->ar_where[] = "`tuu`.`unionId` IN (SELECT tuu.unionId FROM `tbl_user_union` tuu WHERE tuu.unionId='{$unionId}')";
        $unionId > 0 && $condition = 'AND';
        $userId > 0 && $this->db->ar_where[] = "{$condition} `ui`.`id`='{$userId}'";
        $this->db->group_by('ui.id');
        $this->db->order_by('ui.lastActivity DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' ui');
        $list = $userId > 0 ? $query->row_array() : $query->result_array();
        if (self::$enableCache && !empty($list))
        {
            $this->setCacheData($cache_key, $list, HOUR_TIMESTAMP);
        }
        return $list;
    }
    public function allUserListTotal($unionId = 0)
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s|%s", __CLASS__, __FUNCTION__, $unionId);
            $userlisttotal_data = $this->getCacheData($cache_key);
            if (is_numeric($userlisttotal_data) && $userlisttotal_data > 0)
            {
                return $userlisttotal_data;
            }
        }
        //查询这些字段的数据
        $this->db->select(array_merge($this->_allFields, array('tum.lizhi','GROUP_CONCAT(tuu.unionId ORDER BY tuu.createTime) AS unionIdMuti','GROUP_CONCAT(`tuu`.unionRole) AS unionRoleMuti','GROUP_CONCAT(tu.unionName) AS unionNameMuti')));
        $this->db->join('tbl_user_union tuu', 'tuu.userid = ui.id', 'LEFT');
        #$this->db->join('userinfo ui', 'ui.id = tuu.userid', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->join('tbl_user_money tum', 'tum.userid = ui.id', 'LEFT');
        /*$this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        $this->db->where('ui.position != ', '');
        $this->db->where('ui.company != ', '');*/
        $unionId > 0 && $this->db->ar_where[] = "`tuu`.`unionId` IN (SELECT tuu.unionId FROM `tbl_user_union` tuu WHERE tuu.unionId='{$unionId}')";
        $this->db->group_by('ui.id');
        $query = $this->db->get($this->_table . ' ui');
        $user_total = count($query->result_array());
        if (self::$enableCache && $user_total > 0)
        {
            $this->setCacheData($cache_key, $user_total, HOUR_TIMESTAMP);
        }
        return $user_total;
    }
    public function total()
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s", __CLASS__, __FUNCTION__);
            $total_data = $this->getCacheData($cache_key);
            if (is_numeric($total_data) && $total_data > 0)
            {
                return $total_data;
            }
        }
        $total_data = $this->db->count_all_results($this->_table);
        if (self::$enableCache && $total_data > 0)
        {
            $this->setCacheData($cache_key, $total_data, DAY_TIMESTAMP);
        }
        return $total_data;
    }

    public function today()
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s", __CLASS__, __FUNCTION__);
            $today_data = $this->getCacheData($cache_key);
            if (is_numeric($today_data) && $today_data > 0)
            {
                return $today_data;
            }
        }
        $this->db->where('createTime >=', date('Y-m-d'));
        $iCount = $this->db->count_all_results($this->_table);
        if (self::$enableCache && $iCount > 0)
        {
            $this->setCacheData($cache_key, $iCount, DAY_TIMESTAMP);
        }
        return $iCount;
    }
    /**
     * 通过id来获取某用户的信息
     * @param $id
     * @param $selfId
     */
    public function info($id, $selfId, $user_data = array())
    {
        if (empty($user_data))
        {
            $user_data = $this->fetch_user_data($id, 'id', 'object');
        }
        if (!empty($user_data))
        {
            return $this->_tranNull2empty($this->_fetchFields($user_data, $id == $selfId));
        }
        return FALSE;
        /*$this->db->where('id', $id);
        $res = $this->db->get($this->_table);
        if($res->num_rows() == 1){
           $row = $res->row();
           return $this->_tranNull2empty($this->_fetchFields($row, $id==$selfId));
        }
        return FALSE;*/
    }
    /**
     * 获取某用户的信息
     * @param $id 要查看的用户ID
     * @param $selfId 当前登录用户的ID
     */
    public function userone($id, $selfId, $openCache = TRUE)
    {
        //开启缓存的处理
        if (self::$enableCache && $openCache)
        {
            $cache_key = sprintf("%s|%s|%s|%s", __CLASS__, __FUNCTION__, $id, $selfId);
            $userone_data = $this->getCacheData($cache_key);
            if (is_array($userone_data) && !empty($userone_data))
            {
                if (isset($userone_data['baseInfo']) && !empty($userone_data['baseInfo']))
                {
                    $userDataCache = $this->info($id, $id);
                    $userone_data['baseInfo'] = array_merge($userone_data['baseInfo'], $userDataCache);
                }
                return $userone_data;
            }
        }
        $ret = array('baseInfo'=>array(), 'recommendInfo'=>array(), 'jobsInfo'=>array());
        //查询这些字段的数据
        $this->db->select(array_merge($this->_allFields, array('tum.lizhi','GROUP_CONCAT(tuu.unionId ORDER BY tuu.createTime) AS unionIdMuti','GROUP_CONCAT(`tuu`.unionRole) AS unionRoleMuti','GROUP_CONCAT(tu.unionName) AS unionNameMuti')));
        $this->db->join('userinfo ui', 'ui.id = tuu.userid', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->join('tbl_user_money tum', 'tum.userid = ui.id', 'LEFT');
        $id != $selfId && $this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        //$this->db->where('ui.position != ', '');
        $this->db->where('ui.company != ', '');
        $this->db->ar_where[] = "AND `tuu`.`unionId` IN (SELECT tuu.unionId FROM `tbl_user_union` tuu WHERE tuu.userid={$selfId})";
        $this->db->where('tu.unionStatus !=0');//未关闭的社团
        $this->db->where('ui.status !=2');//未禁用的用户
        $this->db->where('ui.id', $id);
        $this->db->group_by('tuu.userid');
        $this->db->limit(1);
        $query = $this->db->get('tbl_user_union tuu');
        $ret['baseInfo'] = $query->row_array();
        if (!empty($ret['baseInfo']))
        {
            //计算该登录用户的年龄
            $birthday_init = isset($ret['baseInfo']['birthday']) ? $ret['baseInfo']['birthday'] : '';
            $birth_age = BLH_Utilities::cal_age($birthday_init);
            $ret['baseInfo']['bitrhday_age'] = $birth_age == '_unknown' ? '未知' : $birth_age;
        }
        //社团ID字符串 (3,6,7)
        if (!empty($ret['baseInfo']['unionIdMuti']))
        {
            //获取社团的邀请信息
            $this->load->model('Invitation');
            $unionIdList = BLH_Utilities::combine($ret['baseInfo']['unionIdMuti'], $ret['baseInfo']['unionNameMuti']);
            //$unionIdList = explode(',', $ret['baseInfo']['unionIdMuti']);
            if (!empty($unionIdList))
            {
                $unionRoleList = explode(',', $ret['baseInfo']['unionRoleMuti']);
                $this->load->model('UserUnion');
                $unionKey = 0;
                foreach ($unionIdList as $unionId => $unionName)
                {
                    if (isset($unionRoleList[$unionKey]))
                    {
                        $unionRoleName = UserUnion::$userUnionRoleConfig[$unionRoleList[$unionKey]];
                    }else{
                        $unionRoleName = UserUnion::$userUnionRoleConfig[UserUnion::USER_UNION_ROLE_MEMBER];
                    }
                    $ret['recommendInfo'][] = $this->Invitation->getInviteMyUserInfo($id, $unionId, $unionName, $unionRoleName, $this);
                    ++$unionKey;
                }
            }
        }
        //过往履历信息
        $this->load->model('UserJobs');
        $ret['jobsInfo'] = $this->UserJobs->searchUserJobsListByUserId($id);
        if (self::$enableCache && $openCache && !empty($ret))
        {
            $this->setCacheData($cache_key, $ret, FIVE_MINU_TIMESTAMP);
        }
        return $ret;
    }
    /**
     * 获取用户列表(全家福)
     * 按用户的加入时间倒序，只显示“有头像+有用户名+有性别+有职务+有公司名”的用户
     * @param $page
     * @param $pagesize
     * @param $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function userlist_bak($page = 1, $pagesize = 10, $timestamp = 0)
    {
        //查询这些字段的数据
        $this->db->select(array_merge($this->_allFields, array('tum.lizhi','GROUP_CONCAT(tuu.unionId ORDER BY tuu.createTime) AS unionIdMuti','GROUP_CONCAT(`tuu`.unionRole) AS unionRoleMuti','GROUP_CONCAT(tu.unionName) AS unionNameMuti')));
        $this->db->join('tbl_user_union tuu', 'tuu.userid = ui.id', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->join('tbl_user_money tum', 'tum.userid = ui.id', 'LEFT');
        $this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        $this->db->where('ui.position != ', '');
        $this->db->where('ui.company != ', '');
        //获取系统虚拟用户帐号
        //$this->db->or_where('ui.usertype', 1);
        //只获取[最新更新过]的用户列表
        if (is_numeric($timestamp) && $timestamp > 0)
        {
            $this->db->where('ui.updateTime >= ', date('Y-m-d H:i:s', $timestamp));
        }
        $this->db->group_by('ui.id');
        $this->db->order_by('ui.updateTime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' ui');
        return $query->result_array();
    }
    /**
     * 获取用户列表(全家福)，只查询当前用户所在社团的用户列表+舶来用户列表
     * 按用户的加入时间倒序，只显示“有头像+有用户名+有性别+有职务+有公司名”的用户
     * @param $page
     * @param $pagesize
     * @param $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function userlist($page = 1, $pagesize = 10, $timestamp = 0, $userid=0, $openCache = FALSE)
    {
        //开启缓存的处理
        if (self::$enableCache && $openCache)
        {
            $cache_key = sprintf("%s|%s|%s|%s|%s|%s", __CLASS__, __FUNCTION__, $userid, $page, $pagesize, $timestamp);
            $userlist_data = $this->getCacheData($cache_key);
            if (is_array($userlist_data) && !empty($userlist_data))
            {
                return $userlist_data;
            }
        }
        //查询这些字段的数据IFNULL
        $this->db->select(array_merge($this->_allFields, array('COALESCE(tum.lizhi, 0) AS lizhi','GROUP_CONCAT(DISTINCT tuu.unionId ORDER BY tuu.createTime) AS unionIdMuti','GROUP_CONCAT(DISTINCT `tuu`.unionRole) AS unionRoleMuti','GROUP_CONCAT(DISTINCT tu.unionName) AS unionNameMuti')));
        #$this->db->join('tbl_user_union tuu', 'tuu.userid = ui.id', 'LEFT');
        $this->db->join('userinfo ui', 'ui.id = tuu.userid OR ui.blhRole='.Userinfo::USER_ROLE_WELCOME, 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->join('tbl_user_money tum', 'tum.userid = ui.id', 'LEFT');
        $this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        //$this->db->where('ui.position != ', '');
        $this->db->where('ui.company != ', '');
        //获取系统虚拟用户帐号
        //$this->db->or_where('ui.usertype', 1);
        //只获取[最新更新过]的用户列表
        if (is_numeric($timestamp) && $timestamp > 0)
        {
            $this->db->where('ui.updateTime >= ', date('Y-m-d H:i:s', $timestamp));
        }
        $this->db->ar_where[] = "AND `tuu`.`unionId` IN (SELECT tuu.unionId FROM `tbl_user_union` tuu WHERE tuu.userid={$userid})";
        $this->db->where('tu.unionStatus !=0');//未关闭的社团
        $this->db->where('ui.status !=2');//未禁用的用户
        $this->db->group_by('ui.id');
        $this->db->order_by('ui.createTime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get('tbl_user_union tuu');
        $userlist_data = $query->result_array();
        if (self::$enableCache && $openCache && !empty($userlist_data))
        {
            $this->setCacheData($cache_key, $userlist_data, DAY_TIMESTAMP);
        }
        return $userlist_data;
    }
    /**
     * 名片显示的数量
     */
    public function usercard($userId = 0)
    {
        /*$this->db->select('ui.id');
        $this->db->join('userinfo ui', 'ui.id = tuu.userid', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->join('tbl_user_money tum', 'tum.userid = ui.id', 'LEFT');
        $this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        $this->db->where('ui.company != ', '');
        $this->db->where('ui.position != ', '');
        $this->db->where('ui.status != ', 2);
        $iCount = $this->db->count_all_results('tbl_user_union tuu');*/
        //查询这些字段的数据
        $this->db->select('ui.id');
        $this->db->join('userinfo ui', 'ui.id = tuu.userid', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'LEFT');
        $this->db->where('ui.iconUrl != ', '');
        $this->db->where('ui.nickname != ', '');
        //$this->db->where('ui.position != ', '');
        $this->db->where('ui.company != ', '');
        $this->db->ar_where[] = "AND `tuu`.`unionId` IN (SELECT tuu.unionId FROM `tbl_user_union` tuu WHERE tuu.userid={$userId})";
        $this->db->where('tu.unionStatus !=0');//未关闭的社团
        $this->db->where('ui.status !=2');//未禁用的用户
        $this->db->group_by('tuu.userid');
        $iCount = $this->db->count_all_results('tbl_user_union tuu');
        return $iCount;
    }
    public function getInviteUsers($invitor, $invitee)
    {
        $this->db->select('ui.id');
        $this->db->where('ui.invitedby', $invitor);
        $this->db->where('ui.id', $invitee);
        $this->db->where('ui.status', $this->USER_STATUS_OK);

        $res = $this->db->get($this->_table . ' ui');
        $ret = array();
        if($res->num_rows() == 1)
        {
            $ret[] = $invitee;
            $ret = array_merge($ret, $this->_getInviteUsers(array($invitee)) );
        }
        return $ret;
    }

    protected function _getInviteUsers($invitor)
    {
        $this->db->select('ui.id');
        $this->db->where_in('ui.invitedby', $invitor);
        $this->db->where('ui.status', $this->USER_STATUS_OK);
        $query= $this->db->get($this->_table . ' ui');
        $result = $query->result_array();
        $ret = array();
        foreach($result as $row)
        {
            $ret[] = $row['id'];
        }
        if(count($ret) > 0)
        {
            $ret = array_merge( $ret, $this->_getInviteUsers($ret) );
        }
        return $ret;
    }

    public function disable($idlist, $disableReason="")
    {
        $this->db->where_in('id', $idlist);
        $this->db->where('status', $this->USER_STATUS_OK);
        $this->db->update($this->_table, array('status'=>$this->USER_STATUS_STOPPED,'disableReason'=>$disableReason) );
        return $this->db->affected_rows() > 0;
    }

    public function editEmail($uid, $email)
    {
        $this->db->where('id', $uid);
        $this->db->update($this->_table, array('email'=>$email));
        return $this->db->affected_rows() > 0;
    }

    public function enable($id, $invitedby, $inviteIntro)
    {
        $this->db->where('id', $id);
        $this->db->where('status', $this->USER_STATUS_STOPPED);
        $this->db->update($this->_table, array('status'=>$this->USER_STATUS_OK,'invitedby'=>$invitedby,'inviteIntro'=>$inviteIntro) );

        $ret = $this->db->affected_rows() > 0;

        
        if($invitedby > 0)
        {
            //更新发起邀请人的邀请数
            //$this->db->where('id', $invitedby);
            //$this->db->update($this->_table, array('inviteCount'=>'inviteCount+1'));
            $this->db->query("update `{$this->_table}` set `inviteCount`=`inviteCount`+1 where `id` = {$invitedby}");
        }
        return $ret;
    }
    /**
     * 更新用户状态-批量
     * @param $userId
     */
    public function updateUserStatusBatch($userId, $status)
    {
        $this->db->where_in('id', $userId);
        $this->db->update($this->_table, array('status'=>$status, 'updateTime'=>date('Y-m-d H:i:s', SYS_TIME)));
        $ret = $this->db->affected_rows() > 0;
        $userIdArr = !is_array($userId) ? explode(',', $userId) : $userId;
        //开启缓存的处理
        if (self::$enableCache)
        {
            if (is_array($userIdArr) && !empty($userIdArr))
            {
                foreach ($userIdArr as $userIdItem)
                {
                    $cache_key_id = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'id', $userIdItem);
                    $user_data = $this->getCacheData($cache_key_id, 'object');
                    if (!empty($user_data))
                    {
                        $user_data = (array)$user_data;
                        //删除邮箱缓存
                        $email = (isset($user_data['email']) && !empty($user_data['email'])) ? $user_data['email'] : '';
                        if (!empty($email))
                        {
                            $cache_key_email = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'email', $email);
                            $this->getCacheAdapter()->delete($cache_key_email);
                        }
                        //删除微博缓存
                        $sinaUserId = (isset($user_data['sinaUserId']) && !empty($user_data['sinaUserId'])) ? $user_data['sinaUserId'] : '';
                        if (!empty($sinaUserId))
                        {
                            $cache_key_sinaUserId = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'sinaUserId', $sinaUserId);
                            $this->getCacheAdapter()->delete($cache_key_sinaUserId);
                        }
                    }
                    $this->getCacheAdapter()->delete($cache_key_id);
                }
            }
        }
        return $ret;
    }
    /**
     * 更新用户状态
     * @param $userId
     */
    public function updateUserStatus($userId, $status)
    {
        $this->db->where('id', $userId);
        $this->db->update($this->_table, array('status'=>$status, 'updateTime'=>date('Y-m-d H:i:s', SYS_TIME)));
        $ret = $this->db->affected_rows() > 0;
        //开启缓存的处理
        if (self::$enableCache && $ret)
        {
            $cache_key_id = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'id', $userId);
            $user_data = $this->getCacheData($cache_key_id, 'object');
            if (!empty($user_data))
            {
                $user_data = (array)$user_data;
                $email = (isset($user_data['email']) && !empty($user_data['email'])) ? $user_data['email'] : '';
                if (!empty($email))
                {
                    $cache_key_email = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'email', $email);
                    $this->getCacheAdapter()->delete($cache_key_email);
                }
                //登录微博的邮箱
                $weibo_email = (isset($user_data['weiboEmail']) && !empty($user_data['weiboEmail'])) ? $user_data['weiboEmail'] : '';
                if (!empty($weibo_email))
                {
                    $cache_key_weibo_email = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'weiboEmail', $weibo_email);
                    $this->getCacheAdapter()->delete($cache_key_weibo_email);
                }
                //删除微博缓存
                $sinaUserId = (isset($user_data['sinaUserId']) && !empty($user_data['sinaUserId'])) ? $user_data['sinaUserId'] : '';
                if (!empty($sinaUserId))
                {
                    $cache_key_sinaUserId = sprintf("%s|%s|%s|%s", 'Userinfo', 'fetch_user_data', 'sinaUserId', $sinaUserId);
                    $this->getCacheAdapter()->delete($cache_key_sinaUserId);
                }
            }
            $this->getCacheAdapter()->delete($cache_key_id);
        }
        return $ret;
    }
    /**
     * 读取weibo的token
     * @param int $token
     */
    public function get_weibo_token()
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s", __CLASS__, __FUNCTION__);
            $weibo_token_data = $this->getCacheData($cache_key);
            if (!empty($weibo_token_data))
            {
                return $weibo_token_data;
            }
        }
        $this->db->select('token');
        $this->db->where('tokenkey', WEIBO_TOKEN_KEY);
        $this->db->ar_where[] = 'AND `intday`=CURRENT_DATE';
        $this->db->order_by('id DESC');
        $this->db->limit(1);
        $query= $this->db->get($this->_table_tauth_tokens);
        $result = $query->row_array();
        if (self::$enableCache && !empty($result))
        {
            $this->setCacheData($cache_key, $result, BLH_Utilities::timerMaker());
        }
        return $result;
    }
    /**
     * 写入weibo的token
     * @param int $token
     */
    public function replace_weibo_token($token)
    {
        //微博用来存储高级权限token的缓存键值
        $key = WEIBO_TOKEN_KEY;
        $data = array(
            'tokenkey' => mysql_escape_string($key),
            'token' => mysql_escape_string($token),
            'intday' => self::getintday(),
        );
        if (self::$enableCache && !empty($token))
        {
            $cache_key = sprintf("%s|%s", __CLASS__, 'get_weibo_token');
            $this->setCacheData($cache_key, array('token'=>$token), BLH_Utilities::timerMaker());
        }
        //记录到系统设置表
        return $this->db->replace($this->_table_tauth_tokens, $data);
    }
    /**
     * 缓存用户的access_token
     * @param string $access_token
     */
    public function fetch_user_weibo_token($access_token = '', &$classObj = NULL, &$method = '', $expire = DAY_TIMESTAMP)
    {
        //开启缓存的处理
        if (self::$enableCache && $expire > 0)
        {
            $cache_key = sprintf("%s|%s|%s", __CLASS__, 'access_token', $access_token);
            $token_info = $this->getCacheData($cache_key);
            if (!empty($token_info) && ($token_info['create_at'] + $token_info['expire_in']) > SYS_TIME)
            {
                return $token_info;
            }
        }
        $token_info = $classObj->{$method}($access_token);
        if (self::$enableCache && $expire > 0 && !empty($token_info))
        {
            $this->setCacheData($cache_key, $token_info, $expire);
        }
        return $token_info;
    }
    private static function getintday()
    {
        $timestr = strftime("%Y%m%d");
        return intval($timestr);
    }
    protected function _fetchFields($row, $isSelf=false)
    {
        $data = array();
        foreach ($this->_basicFields as $f)
        {
            $data[$f] = $row->$f;
        }
        foreach ($this->_autoFields as $f)
        {
            $data[$f] = $row->$f;
        }

        foreach ($this->_infoFields as $f)
        {
            $privateControl = "public{$f}";
            if ($isSelf || $row->$privateControl)
            {
                $data[$f] = $row->$f;
            }
            if ($isSelf)
            {
                $data[$privateControl] = $row->$privateControl; 
            }
        }
        $this->_allFields = str_replace('ui.', '', $this->_allFields);
        foreach ($this->_allFields as $f)
        {
            if (!isset($data[$f]))
            {
                $data[$f] = isset($row->$f) ? $row->$f : '';
            }
        }
        return $data;
    }

    protected function _tranNull2empty($row)
    {
        $ret = array();
        foreach($row as $k=>$v)
        {
            if(is_null($v))
            {
                $ret[$k] = "";
            }
            else if(is_string($v))
            {
                $ret[$k] = trim($v);
            }
            else
            {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    /**
     * 按页获取所有用户列表
     * @param int $page
     * @param int $pagesize
     * @return array
     */
    public function fetchAllUserByPage($page = 1, $pagesize = 100)
    {
        //查询这些字段的数据
        $this->db->select(array_merge($this->_allFields));
        $this->db->where('status != ' . $this->USER_STATUS_STOPPED);
        $this->db->order_by('ui.id ASC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' ui');
        $list = $query->result_array();
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($list) ? $list : array();
    }

    /**
     * 获取所有用户数量
     * @param bool $fromCache
     * @return bool|mixed
     */
    public function fetchAllUserCount($fromCache = false)
    {
        //开启缓存的处理
        if ($fromCache)
        {
            $cache_key = sprintf("%s|%s", __CLASS__, __FUNCTION__);
            $total_data = $this->getCacheData($cache_key);
            if (is_numeric($total_data) && $total_data > 0)
            {
                return $total_data;
            }
        }

        $this->db->where('status != ' . $this->USER_STATUS_STOPPED);
        $total_data = $this->db->count_all_results($this->_table);
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'|total_data:'.$total_data.'<br />';
        if ($fromCache && $total_data > 0)
        {
            $this->setCacheData($cache_key, $total_data, 3600 * 10);
        }
        return $total_data;
    }

    /**
     * 根据不同的uid，获取总金额
     * @param   int  $uid
     * @param string $field
     * @return array
     */
    public function fetchMySumMoney($uid, $field = 'subjection_uid')
    {
        $this->db->select_sum('my_money');
        $this->db->where($field, (int)$uid);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $res = $query->row_array();
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 根据不同的uid，获取合作者列表
     * @param   int  $uid
     * @param string $field
     * @return array
     */
    public function fetchPartnerList($uid, $field = 'subjection_uid')
    {
        $this->db->select('*');
        $this->db->where($field, (int)$uid);
        $query = $this->db->get($this->_table);
        $list = $query->result_array();
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($list) ? $list : array();
    }
}
