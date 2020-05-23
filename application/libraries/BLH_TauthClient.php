<?php

class BLH_TauthClient {//TAuthUtility

    private static $redis;
    private static $host_open_sina = 'http://i.open.t.sina.com.cn/openapi/';

    public static function getToken($obj = NUll, $class = 'SnsNetwork')
    {
        $obj->load->model('Userinfo');
        $tokenData = $obj->Userinfo->get_weibo_token();
        if (empty($tokenData) OR empty($tokenData['token']))
        {
            $time = SYS_TIME;
            $auth = md5($time . SINA_AUTH_KEY);
            $script_name = SINA_TOKEN_URL;
            $params = array(
                'auth' => $auth,
                'time' => $time,
            );
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . $class . '.php';
            BLH_Utilities::require_only($class_file);
            $ret = sns_network::makeRequest($script_name, $params, '', 'get');
            $tokenData = (isset($ret['result']) && TRUE == $ret['result']) ? json_decode($ret['msg'], TRUE) : array();
            if (!empty($tokenData) && isset($tokenData['token']) && $tokenData['token'] > 0)
            {
                $obj->Userinfo->replace_weibo_token($tokenData['token']);
            }
        }
        return isset($tokenData['token']) ? $tokenData['token'] : 0;
    }

    protected static function getRedis()
    {
        if (self::$redis == NULL)
        {
            /*self::$redis = new Redis();
            self::$redis->popen(REDIS_HOST_W, REDIS_PORT_W);*/
            self::$redis = BLH_Utilities::getCacheWriter('redis');
        }
        return self::$redis;
    }
    
    /**
     * 获取游戏应用的详细信息
     * @param string $app_key
     * http://i.open.t.sina.com.cn/openapi/getappinfo.php?appkey=3470157549
     */
    public static function get_app_info($app_key)
    {
        $script_name = self::$host_open_sina . 'getappinfo.php';
        $params = array(
            'appkey' => $app_key
        );
        $classes = array('BLH_SnsNetwork');
        foreach ($classes as $class)
        {
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . $class . '.php';
            BLH_Utilities::require_only($class_file);
        }
        $ret = sns_network::makeRequest($script_name, $params, '', 'get');
        return (isset($ret['result']) && TRUE == $ret['result']) ? json_decode($ret['msg'], TRUE) : array();
    }
}

class TAuthClient {

    private $initKey = "";
    private $preToken;
    private $todayToken;

    public function __construct() {
        $this->todayToken = getTodayToken();
        if ($this->todayToken == null) {
            $this->authenticate();
        }
    }

    private function authenticate() {
        $this->preToken = $this->getPreToken();
        if ($this->preToken == NULL) {
            $this->preToken = new TAuthToken();
            $this->preToken->token = $this->initKey;
            $this->preToken->randomKey = $this->getRandomKey();
        }
    }

    private function getLatestToken() {
        return null;
    }

    private function getPreToken() {
        return null;
    }

}

class TAuthToken {

    public $acquiredDate;
    public $token;
    public $randomKey;

    public function __construct() {
        $this->acquiredDate = time();
    }

    public function isSameDay($token) {
        $s = strftime("yyyyMMdd", $this->acquiredDate);
        $s1 = strftime("yyyyMMdd", $token->acquiredDate);
        return $s == $s1;
    }

    public function isToday() {
        $s = strftime("%Y%m%d", $this->acquiredDate);
        $s1 = strftime("%Y%m%d", time());
        return $s == $s1;
    }

    public function toString() {
        return implode("|", array($this->acquiredDate, $this->token, $this->randomKey));
    }

    public function fromString($s) {
        list($d, $t, $r) = explode("|", $s);
        $this->acquiredDate = $d;
        $this->token = $t;
        $this->randomKey = $r;
    }

}

/* End of file tauthclient.class.php */
/* Location: ./libs/classes/tauthclient.class.php */