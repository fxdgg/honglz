<?php
/**
 * 用户-登录日志表
 *
 */
class UserLoginLog extends CI_Model
{
	/**
	 * 表名
	 * @var string
	 */
    public $_tableName = 'tbl_user_login_log';
    /**
     * 数据库字段
     * @var array
     */
    protected $_allFields = array('tull.userid','tull.nickname','tull.ip_address','tull.user_agent','tull.login_time',);
    /**
     * 登录来源-网站登录
     * @var string
     */
    const USER_LOGIN_SOURCE_WEBSITE = 'website';
    /**
     * 登录来源-应用登录
     * @var string
     */
    const USER_LOGIN_SOURCE_APPSITE = 'appsite';
    /**
     * 登录来源-微博登录
     * @var string
     */
    const USER_LOGIN_SOURCE_WEIBO = 'weibo';
    /**
     * 登录来源-微信登录
     * @var string
     */
    const USER_LOGIN_SOURCE_WEIXIN = 'weixin';
    /**
     * 登录来源-QQ登录
     * @var string
     */
    const USER_LOGIN_SOURCE_QQ = 'qq';
    /**
     * 登录来源-人人网登录
     * @var string
     */
    const USER_LOGIN_SOURCE_RENREN = 'renren';
    /**
     * 用户登录来源(website/appsite/weibo/weixin/qq/renren)
     * @var array
     */
    public static $_loginSource = array(
    	self::USER_LOGIN_SOURCE_WEBSITE, self::USER_LOGIN_SOURCE_APPSITE, self::USER_LOGIN_SOURCE_WEIBO,
    	self::USER_LOGIN_SOURCE_WEIXIN, self::USER_LOGIN_SOURCE_QQ, self::USER_LOGIN_SOURCE_RENREN
    );
    /**
     * 登录平台-安卓登录
     * @var string
     */
    const USER_LOGIN_PLATFORM_ANDROID = 'android';
    /**
     * 登录平台-IOS登录
     * @var string
     */
    const USER_LOGIN_PLATFORM_IOS = 'ios';
    /**
     * 登录平台-PC登录
     * @var string
     */
    const USER_LOGIN_PLATFORM_PC = 'pc';
    
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 获取用户某天或某段时间的登录日志
     * @param $userId
     * @param $loginStartTime 登录开始时间
     * @param $loginEndTime 登录结束时间
     */
    public function getUserLoginLog($userId, $loginStartTime='', $loginEndTime='', $page=1, $pagesize = 100)
    {
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
    	empty($loginStartTime) && $loginStartTime = date('Y-m-d');
    	empty($loginEndTime) && $loginEndTime = date('Y-m-d');
        $this->db->select(array_merge($this->_allFields));
        $this->db->ar_where[] = "tull.login_time BETWEEN DATE_FORMAT('{$loginStartTime}', '%Y-%m-%d 00:00:00') AND DATE_FORMAT('{$loginEndTime}', '%Y-%m-%d 00:00:00')";
        $this->db->ar_where[] = "AND tull.userid = '{$userId}'";
        $this->db->order_by('tull.login_time' ,'ASC');
        $this->db->limit($pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName . ' tull');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 获取某天或某段时间的登录日志
     * @param $loginStartTime 登录开始时间
     * @param $loginEndTime 登录结束时间
     */
    public function getAllLoginLogByDate($userId, $loginStartTime='', $loginEndTime='')
    {
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
    	empty($loginStartTime) && $loginStartTime = date('Y-m-d');
    	empty($loginEndTime) && $loginEndTime = date('Y-m-d');
        $this->db->select(array_merge($this->_allFields, array('GROUP_CONCAT(login_time ORDER BY login_time DESC) AS last_login_time','DATE_FORMAT(login_time, "%Y-%m-%d") AS login_date')));
        $this->db->ar_where[] = "tull.login_time BETWEEN DATE_FORMAT('{$loginStartTime}', '%Y-%m-%d 00:00:00') AND DATE_FORMAT('{$loginEndTime}', '%Y-%m-%d 00:00:00')";
        $this->db->group_by('tull.userid');
        $this->db->order_by('tull.login_time' ,'ASC');
        $query = $this->db->get($this->_tableName . ' tull');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 系统发奖后，更新获奖用户的领取状态
     * @param int $userId
     * @param array $postId
     */
    public function insertUserLoginLog($userId, $nickName, $status = 0, $source = '', $platform = '')
    {
    	if (empty($platform))
    	{
	    	$bc = BLH_Utilities::getBrowserChecker();
	    	if ($bc->isIOS())
	 		{
	 			$platform = self::USER_LOGIN_PLATFORM_IOS;
	 		}else if ($bc->isAndroid())
	 		{
	 			$platform = self::USER_LOGIN_PLATFORM_ANDROID;
	 		}else{
	 			$platform = self::USER_LOGIN_PLATFORM_PC;
	 		}
    	}
    	if (empty($source) OR !in_array($source, self::$_loginSource)) $source = '_unknown';
    	$data = array(
    		'userid' => $userId,
    		'nickname' => $nickName,
    		'status' => $status, //用户状态
    		'source' => $source, //用户登录来源
    		'platform' => $platform, //用户登录平台
    		'ip_address' => $this->input->ip_address(),
    		'user_agent' => substr($this->input->user_agent(), 0, 120),
    	);
        return $this->db->insert($this->_tableName, $data);
    }

}