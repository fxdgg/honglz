<?php

class BLH_Controller extends CI_Controller{
    protected $_userid = 0;
    protected $third_userid = 0;
    protected $login_error_data = array('status'=>FALSE, 'errcode'=>-100, 'errmsg'=>'please login first!');
    protected static $skip_auth_api = array('pics'=>array('p'));
    protected $weibo;
    public $appkey;
    protected $default_email_suffix = '@exunion.com';
    protected $jd_email_suffix = '@runningjd.com';
    protected $default_passwd = '123456';
    protected $default_passwd_from_union = 'ldgr';
    public static $message_tips_config = array(
        'message' => 'MESSAGE', //是否有新消息
        'invite_code' => 'INVITE_CODE', //未验证的用户，寻求邀请码确认/拒绝的新消息
        'system_userinfo' => 'SYSTEM_USERINFO', //是否有用户未完善资料，系统提示的新消息
    );
    /**
     * 普通用户
     * @var int
     */
    const UNION_ROLE_COMMON = 0;
    /**
     * 社团管理员
     * @var int
     */
    const UNION_ROLE_UNION = 1;
    /**
     * 舶来管理员
     * @var int
     */
    const UNION_ROLE_WELCOME = 2;
    /**
     * 系统管理员
     * @var int
     */
    const UNION_ROLE_SYSTEM = 3;

    public function __construct($need_auth=true)
    {
        parent::__construct();
        //引入工具类
        $this->load->library('BLH_Utilities');
        //引入加密类
        $this->load->library('BLH_Base62');
        if($need_auth)
        {
            $this->auth();
        }
    }

    public function auth($ret=FALSE, $isRegister=FALSE)
    {
       $isLogin = FALSE;
       $this->load->library('session');
       $errorArr = $this->login_error_data;
       /*if (strlen($this->router->fetch_class()) > 0 && strlen($this->router->fetch_method()) > 0
           && isset(self::$skip_auth_api[$this->router->fetch_class()]) && in_array($this->router->fetch_method(), self::$skip_auth_api[$this->router->fetch_class()]))
       {
           $this->session->sess_match_useragent = FALSE;
       }*/
       if (false != $this->session->userdata('userid') && 1 == $this->session->userdata('logged_in') )
       {
            $this->load->model('Userinfo');
            list($status, $user_data) = $this->Userinfo->checkStatus($this->session->userdata('userid'));
            if ($status == $this->Userinfo->USER_STATUS_OK)
            {
                //用户ID
                $this->_userid = $this->session->userdata('userid');
                //昵称
                $this->_nickname = $this->session->userdata('nickname');
                //用户数据
                $this->_user_data = $user_data;
                $isLogin = TRUE;
                return TRUE;
            }elseif ($status == $this->Userinfo->USER_STATUS_REGISTER)
            {
                //用户ID
                $this->_userid = $user_data->id;
                //昵称
                $this->_nickname = $user_data->nickname;
                //用户数据
                $this->_user_data = $user_data;
                $isLogin = TRUE;
                if ($isRegister)
                {
                    return TRUE;
                }
            }else
            {
                $errArr['regState'] = $status;
            }
       }
       //!$isLogin && $this->session->sess_destroy();
       if (!$ret)
       {
           echo json_encode($errorArr);
           exit;
       }
       return FALSE;
    }
    /**
     * 直接调用微博weibo、内部接口方法
     *
     * @access public
     * @param int $method 微博接口方法名
     * @param int $params 参数列表
     * @return array
     */
    public function send_weibo_api($method, $params = array(), $weiboTauth = FALSE, $isReturnObject = FALSE)
    {
        if(empty($method))
        {
            return array();
        }
        if ($weiboTauth)
        {
            return $this->send_weibo_auth_api($method, $params, $isReturnObject);
        }else{
            return $this->send_weibo_common_api($method, $params, $isReturnObject);
        }
    }
    /**
     * 直接调用微博weibo接口方法
     *
     * @access public
     * @param int $method 微博接口方法名
     * @param int $params 参数列表
     * @return array
     */
    public function send_weibo_common_api($method, $params = array(), $isReturnObject = FALSE, $platform = 'sina')
    {
        if(empty($method))
        {
            return array();
        }
        $access_token = !empty($params[ACCESS_TOKEN]) ? $params[ACCESS_TOKEN] : (isset($_REQUEST[ACCESS_TOKEN]) ? $_REQUEST[ACCESS_TOKEN] : NULL);
        $class = 'Saetv2';
        $weibo_class_file = APPPATH . 'libraries/' . $this->config->item('subclass_prefix') . $class . '.php';
        $this->weibo = BLH_Utilities::get_api_class($platform, array('app_key'=>WEIBO_APP_KEY, 'app_secret'=>'', 'access_token'=>$access_token, 'app_class'=>$weibo_class_file));
        if ($isReturnObject) return $this->weibo;
        if (!is_array($params)) { $params = array($params); }
        $ret = call_user_func_array(array($this->weibo, $method), $params);
        return $ret;
    }
    /**
     * 直接调用微博weibo-内部接口方法
     *
     * @access public
     * @param int $method 微博接口方法名
     * @param int $params 参数列表
     * @return array
     */
    public function send_weibo_auth_api($method, $params = array(), $isReturnObject = FALSE)
    {
        if(empty($method))
        {
            return array();
        }
        $this->weibo = BLH_Utilities::getWeibo($this);
        if ($isReturnObject) return $this->weibo;
        $ret = call_user_func_array(array($this->weibo, $method), $params);
        return $ret;
    }
    public function validateToken($params = array(), $checkAppKey = TRUE, $platform = 'sina', $expire = DAY_TIMESTAMP)
    {
        $this->appkey = (defined('ENVIRONMENT') && ENVIRONMENT == 'production') ? WEIBO_APP_KEY : WEIBO_APP_KEY_DEBUG;
        $access_token = !empty($params[ACCESS_TOKEN]) ? $params[ACCESS_TOKEN] : (isset($_REQUEST[ACCESS_TOKEN]) ? $_REQUEST[ACCESS_TOKEN] : NULL);
        $class = 'Saetv2';
        $weibo_class_file = APPPATH . 'libraries/' . $this->config->item('subclass_prefix') . $class . '.php';
        !$this->weibo && $this->weibo = BLH_Utilities::get_api_class($platform, array('app_key'=>$this->appkey, 'app_secret'=>'', 'access_token'=>$access_token, 'app_class'=>$weibo_class_file));

        $method = 'get_token_info';
        $this->load->model('Userinfo');
        $r = $this->weibo_token_info = $this->Userinfo->fetch_user_weibo_token($access_token, $this->weibo, $method, $expire);
        //$r = $this->weibo_token_info = $this->weibo->get_token_info($access_token);
        if (!is_array($r))
        {
            //gen error msg
            $errorData = BLH_Utilities::genErrorMsg(27001, 'Token verify error');
            //output
            BLH_Utilities::outputError($errorData);
        }
        if (array_key_exists('error_code', $r))
        {
            //gen error msg
            $errorData = BLH_Utilities::genErrorMsg($r['error_code'], $r['error']);
            //output
            BLH_Utilities::outputError($errorData);
        }
        if (isset($_REQUEST['ADJUST']) && $_REQUEST['ADJUST'] == ADJUST) $checkAppKey = FALSE;
        if ($checkAppKey && $r['appkey'] != $this->appkey)
        {
            //gen error msg
            $errorData = BLH_Utilities::genErrorMsg(27019, 'the token does not match appkey@r=>'.var_export($r, true));
            //output
            BLH_Utilities::outputError($errorData);
        }
        if ($r['expire_in'] < 0 )
        {
            //gen error msg
            $errorData = BLH_Utilities::genErrorMsg(21327, 'Token expired less than zero@r=>'.var_export($r, true));
            //output
            //BLH_Utilities::outputError($errorData);
        }
        $this->third_userid = (isset($this->req['ADJUST']) && $this->req['ADJUST'] == ADJUST) ? $params['uid'] : $r['uid'];
        $this->weibo_appkey = $r['appkey'];
        return $r;
    }
    public function sendEmail($email, $title, $body, $fromName)
    {
        $time = SYS_TIME;
        $auth = md5($time . SINA_AUTH_KEY);
        $script_name = SINA_EMAIL_URL;
        //加密邮箱密码
        $email_config = $this->config->item('email_config');
        $userName = $email_config['email_account_config']['UserName'];
        $email_password = $email_config['email_account_config']['Password'];
    	$email_password_sign = BLH_Utilities::uc_authcode($email_password, 'ENCODE');
        $from = $email_config['email_account_config']['From'];
        $params = array(
            'auth' => $auth,
            'time' => $time,
            'email' => $email,
            'userName' => $userName,
            'psign' => $email_password_sign,
            'title' => $title,
            'body' => $body,
            'from' => $from,
            'fromName' => $fromName,
            'md' => 0, // 是否开启调试模式,默认为0关闭
        );
        $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'SnsNetwork.php';
        BLH_Utilities::require_only($class_file);
        $retryNum = 10;
        do{
            $mAuthMap = array(1, 0, 1);
            foreach($mAuthMap as $ma) {
                // 是否开启SMTPAuth,默认为1开启
                $params['ma'] = (int)$ma;
                $ret = sns_network::makeRequest($script_name, $params, '', 'post');
                echo 'ret=><pre>';var_dump($ret);
                $retData = (isset($ret['result']) && TRUE == $ret['result']) ? json_decode($ret['msg'], TRUE) : array();
                echo 'retData=><pre>';var_dump($retData);
                if (!empty($retData['ret']) && $retData['ret'] == 1) {
                    return TRUE;
                }
            }
            --$retryNum;
            usleep(1000);
        }while($retryNum > 0);
        return FALSE;
    }
    public function setEmail($email, $title, $body, $fromName = '') {
        include_once APPPATH . 'libraries/BLH_SinaMail.php';
        $email_config = $this->config->item('email_config');
        $UserName = $email_config['email_account_config']['UserName'];
        $Password = $email_config['email_account_config']['Password'];
        $From = $email_config['email_account_config']['From'];
        $FromName = $email_config['email_account_config']['FromName'];
        $mailObj = new BLH_SinaMail($UserName, $Password, $From, $FromName);
        $mailObj->CharSet = "utf-8";
        $mailDebug = TRUE;
        $mailRet = $mailObj->send($title, explode(',', $email), $body, NULL, $mailDebug);
        return $mailRet ? TRUE : FALSE;
    }

    public function render($view_name, $view_vars = array())
    {
        $file_exists = FALSE;
        foreach ($this->load->_ci_view_paths as $view_file => $cascade)
        {
            if (file_exists($view_file.$view_name))
            {
                $_ci_path = $view_file.$view_name;
                $file_exists = TRUE;
                break;
            }
        }
        if ( $file_exists && file_exists($_ci_path))
        {
            //return array('ret'=>TRUE, 'file'=>$_ci_path);
            !empty($view_vars) && extract($view_vars);
            include($_ci_path);
        }
        //return array('ret'=>FALSE, 'file'=>'');
    }

	/**
	 * 输出错误
	 *
	 * @param number $code
	 * @param string $data
	 * @param number $return
	 * @return string
	 */
	public function print_err($code = 0, $data = '', $return = 1, $json = false)
	{
		$response = array (
			'code' => $code,
			'data' => $data
		);
		if ($json == false)
		{
			echo "<script>alert('.$data.');window.history.go(-1);</script>";
			exit;
		}

		if ($return)
		{
			echo json_encode ( $response );
			exit;
		} else {
			return json_encode ( $response );
		}
	}

}
