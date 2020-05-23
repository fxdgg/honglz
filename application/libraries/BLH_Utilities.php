<?php
/**
 * 定义工具类
 *
 * @copyright Copyright (c) 2013-2015 .
 * @license New BSD License
 * @version $Id: BLH_Utilities.php 2013-11-12 11:30 $
 * @package classes
 */

class BLH_Utilities {
    /**
     * 工具类操作对象单例
     * @var array
     */
    private static $utilInstance;
    /**
     * 类搜索路径
     *
     * @var array
     */
    public static $_paths = array();
    /**
     * 类搜索路径的选项
     *
     * @var array
     */
    public static $_search_options_ = array();
    /**
     * 类加载列表
     *
     * @var array
     */
    public static $classes = array();
    private static $cache_factory = FALSE;//缓存访问方式
    private static $redisr = NULL;
    private static $redisw = NULL;
    private static $weibo = NULL;
    private static $browser_checker = NULL;
    public static $subclass_prefix = 'BLH_';
    /**
     * 配置文件的形式
     * server（服务器）,local（本地）,dedicated（专用）,release（发布）
     * @var string
     */
    const WHICHFILE = 'server';

    public static function getRedisReader()
    {
        if (TRUE == self::$cache_factory)
        {
            return BLH_Utilities::getCacheReader('redis');
        }
        else
        {
            if (!extension_loaded('redis'))
            {
                throw new RedisException("Cannot Initialize Redis Extension!\nFile：".__FILE__."\nLine：".__LINE__);
            }
            /* if (self::$redisr == NULL) {
              self::$redisr = new Redis();
              self::$redisr->popen(REDIS_HOST_R, REDIS_PORT_R);
              }
              return self::$redisr; */
            $redisr = new Redis();
            $redisr->open(REDIS_HOST_R, REDIS_PORT_R);
            return $redisr;
        }
    }

    public static function getRedisWriter()
    {
        if (TRUE == self::$cache_factory)
        {
            return BLH_Utilities::getCacheWriter('redis');
        }
        else
        {
            if (!extension_loaded('redis'))
            {
                throw new RedisException("Cannot Initialize Redis Extension!\nFile：".__FILE__."\nLine：".__LINE__);
            }
            /* if (self::$redisw == NULL) {
              self::$redisw = new Redis();
              self::$redisw->popen(REDIS_HOST_W, REDIS_PORT_W);
              }
              return self::$redisw; */
            $redisw = new Redis();
            $redisw->open(REDIS_HOST_W, REDIS_PORT_W);
            return $redisw;
        }
    }
    /**
     * 通过微游戏那边的高级权限的Token，访问微博weibo内部接口
     */
    public static function getWeibo($obj = NUll)
    {
        if (self::$weibo == NULL)
        {
            $classes = array('Weibotauth', 'TauthClient');
            foreach ($classes as $class)
            {
                $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . $class . '.php';
                BLH_Utilities::require_only($class_file);
            }
            self::$weibo = new WeiboTAuth(BLH_TauthClient::getToken($obj));
        }
        return self::$weibo;
    }
    /**
     * 获取客户端的类型
     */
    public static function getBrowserChecker()
    {
        if (self::$browser_checker == NULL)
        {
            $class = 'BrowserChecker';
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . $class . '.php';
            BLH_Utilities::require_only($class_file);
            $className = BLH_Utilities::$subclass_prefix.$class;
            self::$browser_checker = new $className();
        }
        return self::$browser_checker;
    }
    //获取缓存中的微游戏的token
    public static function get_wyx_token()
    {
        //tauth_tokens表
        $tauth_tokens_model = BLH_Utilities::load_model('tauth_tokens_model');
        $token = $tauth_tokens_model->get_tauth_tokens();
        return $token;
        
        //获取缓存中的微游戏的token
        /*$cache_token = BLH_Utilities::get_wyx_token_cache();
        if (!empty($cache_token))
        {
            return $cache_token;
        }
        //微游戏验证用token表
        $table_name = 'tauth_tokens';
        //微游戏用来存储高级权限token的缓存键值
        $key = WYX_TOKEN_KEY;
        //从数据库获取token
        $db_reader = BLH_Utilities::getDbReader($table_name);
        $where = array(
            'tokenkey' => $key
        );
        $order = '`id` DESC';
        $token_info = $db_reader->get_one($where, 'token', $order);
        if (isset($token_info['token']) && strlen($token_info['token']) > 0)
        {
            //设置缓存中的微游戏的token
            BLH_Utilities::set_wyx_token_cache($token_info['token']);
            return $token_info['token'];
        }
        return 0;*/
    }
    /**
     * 统一获取数据接口
     * @param $key
     * @param $func
     * @param $params
     * @param $expire
     * @param $fromDb
     */
    public static function accessCacheData($key, $func, $params, $expire=86400, $fromDb=FALSE)
    {
        try {
            if ($expire <= 0 OR TRUE == $fromDb)
            {
                $str = NULL;
            }
            else
            {
                $redisr = self::getRedisReader();
                $str = $redisr->get($key);
                $redisr->close();
            }
            if (!$str || strcasecmp($str, 'false') == 0)
            {
                $arr = call_user_func_array($func, $params);
                if (!empty($arr) && !isset($arr['error_code']))
                {
                    try {
                        $redisw = self::getRedisWriter();
                        if ($expire == 0 OR $expire == -1)
                        {
                            
                        }
                        elseif ($expire > 0)
                        {
                            $redisw->setex($key, $expire, json_encode($arr));
                        }
                        else
                        {
                            $redisw->setex($key, 3600, json_encode($arr));
                        }
                        $redisw->close();
                    } catch (Exception $e) {
                        
                    }
                }
                return $arr;
            } else {
                return json_decode($str, TRUE);
            }
        } catch (RedisException $ex) {
            return call_user_func_array($func, $params);
        }
    }
    public static function httpRequest($url, $postdata = NULL, $header = NULL, $cookie = NULL) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($postdata) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        if ($header) {
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
        }
        if ($cookie) {
            if (is_string($cookie)) {
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            } elseif (is_array($cookie)) {
                foreach ($cookie as $key => $value) {
                    curl_setopt($ch, CURLOPT_COOKIE, sprintf("%s=%s", $key, $value));
                }
            }
        }
        $ret = curl_exec($ch);
        $errno = curl_errno($ch);
        //$errmsg = curl_error($ch);
        curl_close($ch);
        return $errno == 0 ? $ret : FALSE;
    }
    /*
     * get oauth authentication headers
     */
    private static function getheaders() {
        $headers = self::headers();
        //$headers = getallheaders();
        $pheader = array ();
        if (array_key_exists ( "Authorization", $headers )) {
            $pheader [] = "Authorization: " . $headers ["Authorization"];
        }
        if (array_key_exists ( "X-Real-Content-Type", $headers )) {
            $pheader [] = "Content-Type: " . $headers ["X-Real-Content-Type"];
        }
        return $pheader;
    }
    /**
     * 取得请求的报头信息
     * apache_request_headers
     * @return array
     */
    public static function headers($is_apache = TRUE) {
        if ($is_apache && function_exists ( "getallheaders" )) {
            return getallheaders();
        }
        $headers = array ();
        foreach ( $_SERVER as $key => $value ) {
            if ($key == "PATH") {
                break;
            }
            $key = preg_replace ( "/^HTTP_/", "", $key );
            $key = preg_replace ( "/_(.)/e", "'-'.ucfirst('\\1')", strtolower ( $key ) );
            $key = ucfirst ( $key );
            $headers [$key] = $value;
        }
        return $headers;
    }
    public static function getReqParam($key, $default = NULL, $method = METHOD_ALL) {
        switch ($method) {
            case self::$METHOD_GET:
                $arr =& $_GET;
                break;
            case self::$METHOD_POST:
                $arr =& $_POST;
                break;
            default:
                $arr =& $_REQUEST;
                break;
        }
        return array_key_exists($key,$arr) ? $arr[$key] : $default;
    }

    public static function getReqPath() {
        return sprintf("%s/%s"
                , array_key_exists("cate", $_GET) ? $_GET["cate"] : ""
                , array_key_exists("cmd", $_GET) ? $_GET["cmd"] : "");
    }

    public static function isSelfService() {
        if (empty($_GET["ver"])) {
            exit;
        }
        $ver = strtolower(trim($_GET["ver"]));
        return $ver == "mgp";
    }
    
    public static function json2Line($json) {
        if (is_object($json) || is_array($json)) {
            $json = json_encode($json);
        }
        return str_replace(array("\r", "\n"), array("\\r", "\\n"), $json);
    }
    //返给前端错误消息
    public static function outputError($code, $msg = '', $data = array(), $status = FALSE) {
        echo (is_array($code) && !empty($code)) ? json_encode($code) : json_encode(self::genErrorMsg($code, $msg, $data, $status));
        exit;
    }
    //返给前端成功消息
    public static function outputSuccess($data = array(), $status = TRUE, $isReturn = FALSE)
    {
        $ret = array(
            'status' => $status,
            'request' => array_key_exists('REDIRECT_URL', $_SERVER) ?
                    $_SERVER['REDIRECT_URL'] :
                    $_SERVER['REQUEST_URI'],
        ) + (is_array($data) && !empty($data) ? $data : array());
        if ($isReturn) return $ret;
        else {echo json_encode($ret);exit;}
    }
    //生成返回的错误消息
    public static function genErrorMsg($code, $msg, $data = array(), $status = FALSE)
    {
        return array(
            'status' => $status,
            'request' => array_key_exists('REDIRECT_URL', $_SERVER) ?
                    $_SERVER['REDIRECT_URL'] :
                    $_SERVER['REQUEST_URI'],
            'errcode' => $code,
            'errmsg' => $msg
        ) + (is_array($data) && !empty($data) ? $data : array());
    }
    //返给前端正常消息
    public static function outputResult($data, $gzcompress = FALSE) {
        if (!$gzcompress) {
            if (is_array($data) || is_object($data)) {
                $data = json_encode($data);
            }
            echo $data;
        } else {
            $gzdata = gzencode($data, FORCE_DEFLATE);
            header("Content-Type: application/octet-stream");
            header("Accept-Ranges: none");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header("Content-Length: " . strlen($gzdata));
            ob_clean();
            flush();
            print $gzdata;
            flush();
        }
    }
    /**
     * 获取token
     */
    public static function get_remote_token()
    {
        $time = SYS_TIME;
        $auth = md5(SINA_AUTH_KEY, $time);
        $url = SINA_TOKEN_URL . '?auth='.$auth.'&time='.$time;
        
        
    }
    /**
     * 根据登录平台获取API
     * @param $platform string 登录平台标志
     * @return object 
     */
    public static function get_api_class($platform = '', $params = array())
    {
        $object = NULL;
        switch ($platform)
        {
            case 'renren':
                break;
            case 'qq':
                break;
            case 'sina':
            default:
                /**
                 * @param mixed $akey 微博开放平台应用APP KEY
                 * @param mixed $skey 微博开放平台应用APP SECRET
                 * @param mixed $access_token OAuth认证返回的token
                 * @param mixed $refresh_token OAuth认证返回的token secret
                 */
                if (!isset($params['refresh_token'])) $params['refresh_token'] = NULL;
                BLH_Utilities::require_only($params['app_class']);
                $object = BLH_Utilities::get_instance('SaeTClientV2', $params['app_key'], $params['app_secret'], $params['access_token'], $params['refresh_token']);
                break;
        }
        return $object;
    }
    /**
     * get microtime stamp
     * @return number
     */
    public static function microtimeFloat(){
        list($usec, $sec) = explode(" ", SYS_START_TIME);
        return ((float)$usec + (float)$sec);
    }
    public static function timerMaker()
    {
        $tomorrow_time = self::timerZero(1);
        return $tomorrow_time - SYS_TIME;
    }
    //获取当天凌晨零点的时间戳
    public static function timerZero($num = 0)
    {
        $t = getdate();
        return mktime(0, 0, 0, $t['mon'], $t['mday']+$num, $t['year']);
    }
    /**
     * 获取余数
     * @param $key
     * @param $total_num
     */
    public static function get_mod_name($key, $total_num = 8)
    {
        return fmod($key, $total_num);
    }
    /**
     * 对字符串或数组进行格式化，返回格式化后的数组
     * @param array|string $input 要格式化的字符串或数组
     * @param string $delimiter 按照什么字符进行分割
     * @return array 格式化结果
     */
    public static function filter($input, $delimiter = ',')
    {
        if (!is_array($input))
        {
            $input = explode($delimiter, trim($input));
        }
        return array_filter(array_map('trim', $input), 'strlen');
    }
    /**
     * 以一个数组的值作为键值,另一个数组的值作为其值
     * @param string $key_string
     * @param string $value_string
     * @param string $delimiter
     */
    public static function combine($key_string, $value_string, $delimiter = ',')
    {
        $key_array = explode($delimiter, trim($key_string));
        $value_array = explode($delimiter, trim($value_string));
        return array_combine($key_array, $value_array);
    }
    
    /**
     * 返回经addslashes处理过的字符串或数组
     * @param $string 需要处理的字符串或数组
     * @return mixed
     */
    public static function new_addslashes($string)
    {
        if(!is_array($string)) return addslashes($string);
        foreach($string as $key => $val) $string[$key] = self::new_addslashes($val);
        return $string;
    }
    /**
     * 返回经stripslashes处理过的字符串或数组
     * @param $string 需要处理的字符串或数组
     * @return mixed
     */
    public static function new_stripslashes($string)
    {
        if(!is_array($string)) return stripslashes($string);
        foreach($string as $key => $val) $string[$key] = self::new_stripslashes($val);
        return $string;
    }
    /**
     * 获取IP地址
     *
     * @return string IP地址
     */
    public static function getClientIp() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
        return $onlineip;
    }
    /**
     * ip limit
     * @param string $ip
     */
    public static function ipLimit($ip,$iprule)
    {
        $ipruleregexp = str_replace ( '.*', 'ph', $iprule );
        $ipruleregexp = preg_quote ( $ipruleregexp, '/' );
        $ipruleregexp = str_replace ( 'ph', '\.[0-9]{1,3}', $ipruleregexp );
        
        if (preg_match( '/^' . $ipruleregexp . '$/', $ip ))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    public static function get_instance_self()
    {
        if(!self::$utilInstance)
            self::$utilInstance = new self;
        return self::$utilInstance;
    }
    /**
    *
    * 在对象注册表中查找指定类名称的对象实例是否存在
    * 如果存在，则返回该对象实例
    * 如果不存在，则载入类定义文件，并构造一个对象实例
    * 将新构造的对象以类名称作为对象名登记到对象注册表
    * 返回新构造的对象实例
    * 多次使用同一个对象时不需要反复构造对象
    * 
    * @return object 返回对象实例
    */
    public static function get_instance()
    {
        $args = func_get_args();
        if(count($args) <= 0) return NULL;
        static $modelsObject = array();
        $className = count($args) > 0 ? array_shift($args) : 'User_Data';
        if(!empty($modelsObject) && array_key_exists($className, $modelsObject))
        {
            return $modelsObject[$className];
        }
        switch (count($args))
        {
            case 0 : $modelsObject[$className] = new $className();break;
            case 1 : $modelsObject[$className] = new $className($args[0]);break;
            case 2 : $modelsObject[$className] = new $className($args[0], $args[1]);break;
            case 3 : $modelsObject[$className] = new $className($args[0], $args[1], $args[2]);break;
            case 4 : $modelsObject[$className] = new $className($args[0], $args[1], $args[2], $args[3]);break;
            case 5 : $modelsObject[$className] = new $className($args[0], $args[1], $args[2], $args[3], $args[4]);break;
            default:
                $script = "\$modelsObject['{$className}'] = new {$className}({$args[0]}";
                for ($i = 1, $len = count($args); $i < $len; $i++)
                    $script .= ",{$args[$i]}";
                $script .= ");";
                eval($script);
                break;
        }
        return $modelsObject[$className];
    }
    public static function getCacheReader($cache_setting = 'redis')
    {
        $cache = self::load_sys_class('base_cache', '', 1, '', '', array('cache_setting'=>$cache_setting));
        return $cache->cache_reader;
    }
    public static function getCacheWriter($cache_setting = 'redis')
    {
        $cache = self::load_sys_class('base_cache', '', 1, '', '', array('cache_setting'=>$cache_setting));
        return $cache->cache_writer;
    }
    public static function getDbReader($table_name, $is_model = 'model', $db_setting = 'default')
    {
        $model = self::load_sys_class('base_model', '', 1, '', '', array('table_name'=>$table_name, 'db_setting'=>$db_setting));
        return $is_model == 'model' ? $model : $model->db_reader;
    }
    public static function getDbWriter($table_name, $is_model = 'model', $db_setting = 'default')
    {
        $model = self::load_sys_class('base_model', '', 1, '', '', array('table_name'=>$table_name, 'db_setting'=>$db_setting));
        return $is_model == 'model' ? $model : $model->db_writer;
    }
    /**
     * 根据$unique_id，计算表名后缀
     * @param float $unique_id 要计算的唯一标识
     * @param int $table_num 要分成几张表
     */
    public static function cal_mod($unique_id, $table_num = 10)
    {
        return fmod(floatval($unique_id), $table_num);
    }
    public static function cal_age( $birth_date )
    {
        if ( $birth_date == '0000-00-00' OR empty($birth_date) OR is_null($birth_date))
            return '_unknown';
        
        $bd = explode( '-', $birth_date );
        $age = date('Y') - $bd[0] - 1;
        
        $arr[1] = 'm';
        $arr[2] = 'd';
    
        for ( $i = 1; $arr[$i]; $i++ ) {
            $n = date( $arr[$i] );
            if ( $n < $bd[$i] )
                break;
            if ( $n > $bd[$i] ) {
                ++$age;
                break;
            }
        }
        
        return $age;
    }
    /**
     * 加载系统类方法
     * @param string $classname 类名
     * @param string $path 扩展地址
     * @param intger $initialize 是否初始化
     * @param string $classNameExt 特殊的类名
     * @param string $filePrefix 特殊的文件前缀名
     * @param array $params 实例化参数
     */
    public static function load_sys_class($classname, $path = '', $initialize = 1, $classNameExt = '', $filePrefix = '.class', $params = array()) {
        if (empty($filePrefix)) $filePrefix = '.class';
        return self::load_class($classname, $path, $initialize, $classNameExt, $filePrefix, $params);
    }
    /**
     * 加载应用类方法
     * @param string $classname 类名
     * @param intger $initialize 是否初始化
     * @param string $classNameExt 特殊的类名
     * @param string $filePrefix 特殊的文件前缀名
     * @param array $params 实例化参数
     */
    public static function load_app_class($classname, $initialize = 1, $classNameExt = '', $filePrefix = '', $params = array()) {
        return self::load_class($classname, 'classes', $initialize, $classNameExt, $filePrefix, $params);
    }
    /**
     * 加载模块类方法
     * @param string $classname 类名
     * @param string $m 模块
     * @param intger $initialize 是否初始化
     * @param string $join 是否把module与classname,首字母大写
     */
    public static function load_modules_class($classname, $m = '', $initialize = 1, $join = FALSE) {
        $m = empty($m) && defined('MODULE') ? MODULE : $m;
        if (empty($m)) return FALSE;
        //转换为正确的类名
        if($join == TRUE) $classname = self::ucfirst($m) . self::ucfirst($classname);
        return self::load_class($classname, 'services' . DS . $m . DS . 'classes', $initialize);
    }
    /**
     * 加载数据模型
     * @param string $classname 类名
     * @param array $params 实例化参数
     */
    public static function load_model($classname, $params = array()) {
        return self::load_class($classname, 'model', 1, '', '.class', $params);
    }
    /**
     * 加载类文件函数
     * @param string $classname 类名
     * @param string $path 扩展地址
     * @param intger $initialize 是否初始化
     * @param string $classNameExt 特殊的类名
     * @param string $filePrefix 特殊的文件前缀名
     * @param array $params 实例化参数
     */

    public static function load_class($classname, $path = '', $initialize = 1, $classNameExt = '', $filePrefix = '.class', $params = array()) {
        if (empty($path)) $path = 'libs/classes';
        $key = md5($path.$classname);
        if (isset(self::$classes[$key])) {
            if (!empty(self::$classes[$key])) {
                return self::$classes[$key];
            } else {
                return true;
            }
        }
        if (file_exists(CURRENT_PATH.DS.$path.DS.$classname.$filePrefix.'.php')) {
            include CURRENT_PATH.DS.$path.DS.$classname.$filePrefix.'.php';
            if ($initialize) {
                $name = strlen($classNameExt) > 0 ? $classNameExt : $classname;
                self::$classes[$key] = !empty($params) ? new $name($params) : new $name;
            } else {
                self::$classes[$key] = true;
            }
            return self::$classes[$key];
        } else {
            return false;
        }
    }
    /**
     * 加载函数库
     * @param string $func 函数库名
     * @param string $path 地址
     */
    public static function load_func($func, $path = '') {
        static $funcs = array();
        if (empty($path)) $path = 'libs/functions';
        $path .= DS.$func.'.function.php';
        $key = md5($path);
        if (isset($funcs[$key])) return true;
        if (file_exists(CURRENT_PATH.DS.$path)) {
            include CURRENT_PATH.DS.$path;
        } else {
            $funcs[$key] = false;
            return false;
        }
        $funcs[$key] = true;
        return true;
    }
    /**
     * 加载普通文件
     * @param string $file 函数库名
     * @param string $path 地址
     */
    public static function load_common($file, $path = '') {
        static $classes_common = array();
        if (empty($path)) $path = 'inc';
        $path .= DS.$file.'.php';
        $key   = md5($path);
        if (isset($classes_common[$key])) return true;
        if (file_exists(CURRENT_PATH.DS.$path)) {
            include CURRENT_PATH.DS.$path;
        } else {
            $classes_common[$key] = false;
            return false;
        }
        $classes_common[$key] = true;
        return true;
    }

    /**
     * 加载配置文件
     * @param string $file 配置文件
     * @param string $key  要获取的配置荐
     * @param string $default  默认配置。当获取配置项目失败时该值发生作用。
     * @param boolean $reload 强制重新加载。
     * @param string $filePrefix 特殊的文件前缀名
     * @param string $path 配置文件路径
     */
    public static function load_config($file, $key = '', $default = '', $reload = false, $filePrefix = '', $path='') {
        static $configs = array();
        if (!$reload && isset($configs[$file])) {
            if (empty($key)) {
                return $configs[$file];
            } elseif (isset($configs[$file][$key])) {
                return $configs[$file][$key];
            } else {
                return $default;
            }
        }
        if (empty($path)) $path = 'inc';
        $path = CURRENT_PATH . DS . $path . DS . $file . $filePrefix . '.php';
        if (file_exists($path)) {
            $GLOBALS['GAMECENTER_CONFIG'][$file] = $configs[$file] = include $path;
        }
        if (empty($key)) {
            return $configs[$file];
        } elseif (isset($configs[$file][$key])) {
            return $configs[$file][$key];
        } else {
            return $default;
        }
    }
    /*
     * 项目所有类的自动加载方法
     * 需要配合inc/sng_class_files.php使用
     */
    function sng_autoload($className)
    {
        if (class_exists($className, false) || interface_exists($className, false))
        {
            return;
        }
        //内部类的搜索路径
        static $_internal_classes = array();
        if (empty($_internal_classes))
        {
            if(file_exists(Q_CLASS_FILE) && is_file(Q_CLASS_FILE))
            {
                $_internal_classes = require Q_CLASS_FILE;
            }
        }
        $class_name_l = strtolower($className);
        if (isset($_internal_classes[$class_name_l]))
        {
            $dir_info = pathinfo($_internal_classes[$class_name_l]);
            $key = md5($dir_info['dirname'].$dir_info['basename']);
            if (isset(self::$classes[$key]))
            {
                return;
            }
            require CURRENT_PATH . DS . $_internal_classes[$class_name_l];
            self::$classes[$key] = true;
            return;
        }
        if (substr($className, -5) == 'model')
        {
            self::load_model('signin_model');
            return;
        }
        if (FALSE !== self::load_sys_class($className, '', 0))
        {
            return;
        }
        self::load_app_class($className, 0);
        return;
    }
    /**
     * 首字母大写类名
     */
    public static function ucfirst($name)
    {
        return ucfirst(strtolower($name));
    }
    public static function format_controller_name($unformatted)
    {
        return ucfirst(self::format_name($unformatted)) . 'Service';
    }
    public static function format_name($name)
    {
        if (strpos($name, '_') !== false) {
            $name = str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $name))));
        }
        return ucfirst($name);
    }
    /**
     * 把微秒转换为年月日格式
     */
    public static function convert_date_by_microtime($microtime)
    {
        return date('Y-m-d H:i:s', (floor($microtime / 1000)));
    }
    /**
     * 获取环境变量里配置的路径
     * 
     * SetEnv SINASRV_DATA_DIR "/data1/www/data/m.game.weibo.cn/"
     * SetEnv SINASRV_CACHE_DIR "/data1/www/cache/m.game.weibo.cn/"
     * SetEnv SINASRV_PRIVDATA_DIR "/data1/www/privdata/m.game.weibo.cn/"
     * SetEnv SINASRV_APPLOGS_DIR "/data1/www/applogs/m.game.weibo.cn/"
    
     * SetEnv SINASRV_DATA_URL "http://m.game.weibo.cn/data"
     * SetEnv SINASRV_CACHE_URL "http://m.game.weibo.cn/cache"
        
     * SetEnv SINASRV_MEMCACHED_KEY_PREFIX "m_game_weib-"
     */
    public static function load_env_config($log = 'app_log')
    {
        switch (self::check_ip())
        {
            case 'local.':
                switch ($log)
                {
                    case 'app_log':
                        return CURRENT_PATH . DS . 'logs' . DS . 'app_logs' . DS;
                        break;
                    case 'cache':
                        return CURRENT_PATH . DS . 'logs' . DS . 'caches' . DS;
                        break;
                    case 'data':
                        return CURRENT_PATH . DS . 'logs' . DS . 'data' . DS;
                        break;
                    case 'private_data':
                        return CURRENT_PATH . DS . 'logs' . DS . 'private_data' . DS;
                        break;
                    case 'data_url'://SINASRV_DATA_URL
                        return SNG_URL . DS . 'data';
                        break;
                    case 'cache_url'://SINASRV_CACHE_URL
                        return SNG_URL . DS . 'cache';
                        break;
                    case 'memcached_key_prefix'://SINASRV_MEMCACHED_KEY_PREFIX
                        $cache_setting_redis = 'memcache';
                        $cache_config = self::load_config('cache');
                        return isset($cache_config[$cache_setting_redis]['tag_prefix']) ? $cache_config[$cache_setting_redis]['tag_prefix'] : '';
                        break;
                    default:
                        return '';
                        break;
                }
                break;
            default:
                $tmp_domain = str_replace('http://', '', self::load_config('system', 'sng_url'));
                switch ($log)
                {
                    case 'app_log'://SINASRV_APPLOGS_DIR
                        return isset($_SERVER['SINASRV_APPLOGS_DIR']) ? rtrim($_SERVER['SINASRV_APPLOGS_DIR'],'/\\').DS : '/tmp' . DS . $tmp_domain . DS . 'app_logs' . DS;
                        break;
                    case 'cache'://SINASRV_CACHE_DIR
                        return isset($_SERVER['SINASRV_CACHE_DIR']) ? rtrim($_SERVER['SINASRV_CACHE_DIR'],'/\\').DS : '/tmp' . DS . $tmp_domain . DS . 'caches' . DS;
                        break;
                    case 'data'://SINASRV_DATA_DIR
                        return isset($_SERVER['SINASRV_DATA_DIR']) ? rtrim($_SERVER['SINASRV_DATA_DIR'],'/\\').DS : '/tmp' . DS . $tmp_domain . DS . 'data' . DS;
                        break;
                    case 'private_data'://SINASRV_PRIVDATA_DIR
                        return isset($_SERVER['SINASRV_PRIVDATA_DIR']) ? rtrim($_SERVER['SINASRV_PRIVDATA_DIR'],'/\\').DS : '/tmp' . DS . $tmp_domain . DS . 'private_data' . DS;
                        break;
                    case 'data_url'://SINASRV_DATA_URL
                        return isset($_SERVER['SINASRV_DATA_URL']) ? $_SERVER['SINASRV_DATA_URL'] : '';
                        break;
                    case 'cache_url'://SINASRV_CACHE_URL
                        return isset($_SERVER['SINASRV_CACHE_URL']) ? $_SERVER['SINASRV_CACHE_URL'] : '';
                        break;
                    case 'memcached_key_prefix'://SINASRV_MEMCACHED_KEY_PREFIX
                        return isset($_SERVER['SINASRV_MEMCACHED_KEY_PREFIX']) ? $_SERVER['SINASRV_MEMCACHED_KEY_PREFIX'] : 'm_game_weib-';
                        break;
                    default:
                        return '';
                        break;
                }
                break;
        }
    }
    //通过配置形式，获取配置文件中的ip分类部分
    public static function check_ip()
    {
        //RELEASE_ENV常常量为外部应用中定义的量
        $RELEASE_ENV = self::load_config('system', 'release_env');
        $sEnv = !empty($RELEASE_ENV) ? $RELEASE_ENV : self::WHICHFILE;
        switch (strtolower($sEnv)){
            case 'server'://服务器
                return '';
                break;
            case 'local'://本地
                return 'local.';
                break;
            case 'dedicated'://专用
                return 'ded.';
                break;
            case 'release'://发布
                return 'rls.';
                break;
            default:
                return '';
        }
    }
    /**
     * 输出自定义错误
     *
     * @param $errno 错误号
     * @param $errstr 错误描述
     * @param $errfile 报错文件地址
     * @param $errline 错误行号
     * @return string 错误提示
     */
    public static function my_error_handler($errno, $errstr, $errfile, $errline) {
        if(in_array($errno, array(8,2048))) return '';
        $current_path = str_replace(DS, '/', CURRENT_PATH);
        $errfile = str_replace(DS, '/', $errfile);
        $errfile = str_replace($current_path . '/', '', $errfile);
        $today = date('Ymd');
        self::writeLog('ERROR | '.$errno.' | '.str_pad($errstr,30).' | '.$current_path.' | '.$errfile.' | '.$errline."\r\n", 'ab+');
    }
    /**
     * 输出异常的详细信息和调用堆栈
     *
     */
    public static function framework_exception_handler($exception, $format = 'json')
    {
        switch ($format)
        {
            case 'json':
                $exceptionJson = array();
                $exceptionJson['exception'] = array(
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                );
                if (defined("DEBUG_MODE") && DEBUG_MODE)
                {
                    $exceptionJson['exception']['file'] = $exception->getFile();
                    $exceptionJson['exception']['line'] = $exception->getLine();
                    
                    $trace = $exception->getTrace();
                    foreach ($trace as $traceKey => $traceItem)
                    {
                        $exceptionJson['exception']['trace']['traceItem'][$traceKey] = array(
                            'file' => $traceItem['file'],
                            'line' => $traceItem['line'],
                            'function' => $traceItem['function'],
                            'class' => $traceItem['class'],
                            'type' => $traceItem['type'],
                        );
                        if (!empty($traceItem['args']))
                        {
                            foreach ($traceItem['args'] as $argsItem)
                            {
                                $exceptionJson['exception']['trace']['traceItem'][$traceKey]['args'] = $argsItem;
                            }
                        }
                    }
                }
                //记录日志
                if (defined('DEBUG_MODE') && DEBUG_MODE) {
                    if (defined('MONITOR_MODE') && MONITOR_MODE && self::load_config('system','errorlog'))
                    {
                        self::writeLog('EXCEPTION | '.json_encode($exceptionJson), 'ab+');
                    }
                }
                echo json_encode($exceptionJson);
                break;
            case 'xml':
                $exceptionXml = "<"."?xml version=\"1.0\" encoding=\"utf-8\" ?".">\n";
                $exceptionXml .= "<exception>\n";
                $exceptionXml .= "<code>".$exception->getCode()."</code>\n";
                $exceptionXml .= "<message>".$exception->getMessage()."</message>\n";
                if (defined("DEBUG_MODE") && DEBUG_MODE)
                {
                    $exceptionXml .= "<file>".$exception->getFile()."</file>\n";
                    $exceptionXml .= "<line>".$exception->getLine()."</line>\n";
                    $exceptionXml .= "<trace>\n";
            
                    $trace = $exception->getTrace();
                    foreach ($trace as $traceItem) {
                        $exceptionXml .= "<traceItem>";
                        $exceptionXml .= "<file>".$traceItem['file']."</file>\n";
                        $exceptionXml .= "<line>".$traceItem['line']."</line>\n";
                        $exceptionXml .= "<function>".$traceItem['function']."</function>\n";
                        $exceptionXml .= "<class>".$traceItem['class']."</class>\n";
                        $exceptionXml .= "<type>".$traceItem['type']."</type>\n";
                        $exceptionXml .= "<args>\n";
                        
                        if (!empty($traceItem['args'])) {
                            foreach ($traceItem['args'] as $argsItem) {
                                $exceptionXml .= "<argsItem>".$argsItem."</argsItem>\n";
                            }
                        }
                        $exceptionXml .= "</args>\n";
                        $exceptionXml .= "</traceItem>";
                    }
                    $exceptionXml .= "</trace>\n";
                }
                $exceptionXml .= "</exception>\n";
        
                //记录日志
                if (defined('DEBUG_MODE') && DEBUG_MODE) {
                    if (defined('MONITOR_MODE') && MONITOR_MODE && self::load_config('system','errorlog'))
                    {
                        self::writeLog('EXCEPTION | '.$exceptionXml, 'ab+');
                    }
                }
                echo $exceptionXml;
                break;
        }
    }
    /**
     * 写入文件
     * @date Fri Feb  3 11:41:27 CST 2012
     * @author feng.guo1
     * @param string $filename 要写入的文件全路径名
     * @param string $writetext 文件内容
     * @param string $openmod 文件打开的mode
     * @return boolean
     */
    public static function writeFile($filename, $writetext, $openmod='w')
    {
        self::mkdirs(dirname($filename), 0755);
        if(@$fp = fopen($filename, $openmod))
        {
            flock($fp, LOCK_EX);
            fwrite($fp, $writetext);
            flock($fp, LOCK_UN);
            fclose($fp);
            return TRUE;
        }else{
            exit("ERROR=>File: {$filename} write error.");
            return FALSE;
        }
    }
    public static function writeLog($msg){
        $log_name = ERR_LOG_PATH . sprintf('error_php_%s.log', strftime("%Y%m%d"));
        self::writeFile($log_name, date('Y-m-d H:i:s',SYS_TIME) . ' | ' . $msg . "\r\n", 'ab+');
    }
    /**
     * 重定向浏览器到指定的 URL
     *
     * @param string $url 要重定向的 url
     * @param int $delay 等待多少秒以后跳转
     * @param bool $js 指示是否返回用于跳转的 JavaScript 代码
     * @param bool $jsWrapped 指示返回 JavaScript 代码时是否使用 <script> 标签进行包装
     * @param bool $return 指示是否返回生成的 JavaScript 代码
     * @param string $clientNoticeUrl 指示传说给客户端的数据
     */
    public static function redirect($url, $delay = 0, $js = false, $jsWrapped = true, $return = false, $jsFrame = false)
    {
        $delay = (int)$delay;
        if (!$js) {
            if (headers_sent() || $delay > 0) {
                echo <<<EOT
                    <html>
                    <head>
                    <meta http-equiv="refresh" content="{$delay};URL={$url}" />
                    </head>
                    </html>
EOT;
                exit;
            } else {
                header("Location: {$url}");
                exit;
            }
        }
    
        $out = '';
        if ($jsWrapped) {
            $out .= '<script language="JavaScript" type="text/javascript">';
        }
        if ($delay > 0) {
            $out .= "window.setTimeout(function () { document.location='{$url}'; }, {$delay});";
        } else if($jsFrame) {
            $out .= "parent.location='{$url}';";
        } else {
            $out .= "document.location='{$url}';";
        }
        if ($jsWrapped) {
            $out .= '</script>';
        }
    
        if ($return) {
            return $out;
        }
    
        echo $out;
        exit;
    }
    /**
     * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
     * showmessage('登录成功', '默认跳转地址'));
     * @param string $msg 提示信息
     * @param mixed(string/array) $url_forward 跳转地址
     * @param int $ms 跳转等待时间
     */
    public static function showmessage($msg, $url_forward = 'goback', $direct = 0, $ms = 1250, $clientNoticeUrl = '')
    {
        //if($url_forward && $url_forward != 'goback' && $url_forward != 'close') $url_forward = $url_forward;
        if($direct && $url_forward && $url_forward!='goback')
        {
            ob_clean();
            header("location:$url_forward");
            exit("<script>self.location='$url_forward';</script>");
        }
        include self::template('message','admin');
        exit;
    }
    public static function template($file, $m='admin')
    {
        $file = APPPATH . 'views/'.$m.'/' . $file . '.php';
        clearstatcache();
        if(!file_exists($file))
        {
            self::showmessage('Error 404 - Page Not Found！');
        }
        return $file;
    }
    /**
     * 创建一个目录树，失败抛出异常
     *
     * 用法：
     * @code php
     * self::mkdirs('/top/second/3rd');
     * @endcode
     *
     * @param string $dir 要创建的目录
     * @param int $mode 新建目录的权限
     *
     */
    public static function mkdirs($dir, $mode = 0777)
    {
        if (!is_dir($dir))
        {
            $ret = @mkdir($dir, $mode, true);
            if (!$ret)
            {
                throw new Exception(sprintf('Create dir "%s" failed.', $dir));
            }
        }
        return true;
    }
    /**
     * 字符截取 支持UTF8/GBK
     * @param $string
     * @param $length
     * @param $dot
     */
    public static function str_cut($string, $length, $dot = '...')
    {
        $strlen = strlen($string);
        if($strlen <= $length) return $string;
        $string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
        $strcut = '';
        if(strtolower(CHARSET) == 'utf-8') {
            $length = intval($length-strlen($dot)-$length/3);
            $n = $tn = $noc = 0;
            while($n < strlen($string)) {
                $t = ord($string[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1; $n++; $noc++;
                } elseif(194 <= $t && $t <= 223) {
                    $tn = 2; $n += 2; $noc += 2;
                } elseif(224 <= $t && $t <= 239) {
                    $tn = 3; $n += 3; $noc += 2;
                } elseif(240 <= $t && $t <= 247) {
                    $tn = 4; $n += 4; $noc += 2;
                } elseif(248 <= $t && $t <= 251) {
                    $tn = 5; $n += 5; $noc += 2;
                } elseif($t == 252 || $t == 253) {
                    $tn = 6; $n += 6; $noc += 2;
                } else {
                    $n++;
                }
                if($noc >= $length) {
                    break;
                }
            }
            if($noc > $length) {
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
            $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
        } else {
            $dotlen = strlen($dot);
            $maxi = $length - $dotlen - 1;
            $current_str = '';
            $search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
            $replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
            $search_flip = array_flip($search_arr);
            for ($i = 0; $i < $maxi; $i++) {
                $current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
                if (in_array($current_str, $search_arr)) {
                    $key = $search_flip[$current_str];
                    $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
                }
                $strcut .= $current_str;
            }
        }
        return $strcut.$dot;
    }
    /**
     * UCENTER跟DISCUZ里用到的加密方式
     * @param string $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     */
    public static function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;// 随机密钥长度 取值 0-32;
                         // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
                         // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
                         // 当此值为 0 时，则不产生随机密钥
        $key = md5($key ? $key : SINA_AUTH_KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
    
        $result = '';
        $box = range(0, 255);
    
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
    /**
     * 产生随机字符
     * @param $length
     * @param $numeric
     */
    public static function random($length, $numeric = 0)
    {
        PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
        $seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for($i = 0; $i < $length; $i++)
        {
            $hash .= $seed[mt_rand(0, $max)];
        }
        return $hash;
    }
    /**
     * Compute a SN. Use the server ip, and server software string as seed, and an rand number, two micro time
     * md5(uniqid(mt_rand(0, mt_getrandmax()), TRUE))
     * @access public
     * @return string
     */
    public static function computerSN()
    {
        $random = self::random(32);
        $info = md5($_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'] . SYS_TIME . SYS_START_TIME);
        $return = '';
        for($i=0; $i<64; $i++) {
            $p = intval($i/2);
            $return[$i] = $i % 2 ? $random[$p] : $info[$p];
        }
        return implode('', $return);
    }
    /**
     * 生成 UUID
     *
     * @param int $being_timestamp 计算种子数的开始时间 
     * @param int $suffix_len 计算 ID 时要添加多少位随机数
     *
     * @return string
     */
    public static function number_uuid($being_timestamp=1206576000, $suffix_len=3)
    {
        $time = explode(' ', SYS_START_TIME);
        $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
        if ($suffix_len > 0)
        {
            $id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
        }
        return $id;
    }
    public static function gen_uuid()
    {
        $node = $_SERVER['SERVER_ADDR'];
        
        if (strpos($node, ':') !== false) {
            if (substr_count($node, '::')) {
                $node = str_replace(
                    '::', str_repeat(':0000', 8 - substr_count($node, ':')) . ':', $node
                );
            }
            $node = explode(':', $node) ;
            $ipv6 = '' ;

            foreach ($node as $id) {
                $ipv6 .= str_pad(base_convert($id, 16, 2), 16, 0, STR_PAD_LEFT);
            }
            $node =  base_convert($ipv6, 2, 10);

            if (strlen($node) < 38) {
                $node = null;
            } else {
                $node = crc32($node);
            }
        } elseif (empty($node)) {
            $host = $_SERVER['HOSTNAME'];

            if (empty($host)) {
                $host = $_SERVER['HOST'];
            }

            if (!empty($host)) {
                $ip = gethostbyname($host);

                if ($ip === $host) {
                    $node = crc32($host);
                } else {
                    $node = ip2long($ip);
                }
            }
        } elseif ($node !== '127.0.0.1') {
            $node = ip2long($node);
        } else {
            $node = null;
        }

        if (empty($node)) {
            $node = crc32(AUTH_KEY);
        }

        if (function_exists('zend_thread_id')) {
            $pid = zend_thread_id();
        } else {
            $pid = getmypid();
        }

        if (!$pid || $pid > 65535) {
            $pid = mt_rand(0, 0xfff) | 0x4000;
        }

        list($timeMid, $timeLow) = explode(' ', microtime());
        $uuid = sprintf(
            "%08x-%04x-%04x-%02x%02x-%04x%08x", (int)$timeLow, (int)substr($timeMid, 2) & 0xffff,
            mt_rand(0, 0xfff) | 0x4000, mt_rand(0, 0x3f) | 0x80, mt_rand(0, 0xff), $pid, $node
        );

        return $uuid;
    }
    /**
     * 设置 cookie
     * @param string $var     变量名
     * @param string $value   变量值
     * @param int $time    过期时间
     */
    public static function set_cookie($var, $value = '', $time = 0, $cookie_path='/', $cookie_pre='', $cookie_domain='', $httponly = false)
    {
        if(defined('IN_MOBILE'))
        {
            $httponly = false;
        }
        $time = $time > 0 ? time() + $time : ($value == '' ? time() - 3600 : 0);
        $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
        $path = $httponly && PHP_VERSION < '5.2.0' ? $cookie_path.'; HttpOnly' : $cookie_path;
        $var = $cookie_pre.$var;
        $_COOKIE[$var] = $value;
        if (is_array($value)) {
            foreach ($value as $k=>$v) {
                if (PHP_VERSION < '5.2.0') {
                    setcookie($var.'['.$k.']', self::uc_authcode($v, 'ENCODE'), $time, $path, (!empty($cookie_domain) ? $cookie_domain : $_SERVER['HTTP_HOST']), $secure);
                } else {
                    setcookie($var.'['.$k.']', self::uc_authcode($v, 'ENCODE'), $time, $path, (!empty($cookie_domain) ? $cookie_domain : $_SERVER['HTTP_HOST']), $secure, $httponly);
                }
            }
        } else {
            if (PHP_VERSION < '5.2.0') {
                setcookie($var, self::uc_authcode($value, 'ENCODE'), $time, $path, (!empty($cookie_domain) ? $cookie_domain : $_SERVER['HTTP_HOST']), $secure);
            } else {
                setcookie($var, self::uc_authcode($value, 'ENCODE'), $time, $path, (!empty($cookie_domain) ? $cookie_domain : $_SERVER['HTTP_HOST']), $secure, $httponly);
            }
        }
    }
    /**
     * 获取通过 set_cookie 设置的 cookie 变量 
     * @param string $var 变量名
     * @param string $default 默认值 
     * @return mixed 成功则返回cookie 值，否则返回 false
     */
    public static function get_cookie($var, $default = '', $isPrefix = FALSE, $cookie_pre='')
    {
		$var = $isPrefix ? $cookie_pre . $var : $var;
        return isset($_COOKIE[$var]) ? self::uc_authcode($_COOKIE[$var], 'DECODE') : $default;
    }
    public static function decodeDownloadUrl($url) {
        $str = base64_decode($url);
        return explode("|", $str);
    }
    public static function encodeDownloadUrl($id, $url) {
        return urlencode(base64_encode($id . "|" . $url));
    }
    public static function getDownloadPath($apk) {
        $path = $apk["downpath"] . $apk["filename"];
        $i = stripos($path, "androidgame");
        if ($i === FALSE) {
            return "";
        } else {
            $path = sprintf("http://dl.kjava.sina.cn/androidgames/%s", substr($path, $i + strlen("androidgame/")));
            return $path;
        }
    }
    //获取手机操作系统
    public static function get_mobile_browser($default_os = '')
    {
        $bc = new browser_checker();
        if ($bc->isIOS())
        {
            return download_interface::$mobie_os_config[1];
        }
        else if ($bc->isAndroid())
        {
            return download_interface::$mobie_os_config[0];
        }
        else
        {
            return !empty($default_os) ? $default_os : download_interface::$mobie_os_config[0];
        }
    }
    public static function shortUrl( $long_url )
    {
        $key = AUTH_KEY;
        $base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        
        // 利用md5算法方式生成hash值  
        $hex = hash('md5', $long_url.$key);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;
        
        $output = array();
        for( $i = 0; $i < $subHexLen; $i++ )
        {
            // 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作  
            $subHex = substr($hex, $i*8, 8);
            $idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));
            
            // 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符  
            $out = '';
            for( $j = 0; $j < 6; $j++ )  
            {
                $val = 0x0000003D & $idx;  
                $out .= $base32[$val];  
                $idx = $idx >> 5;  
            }
            $output[$i] = $out;  
        }
        return $output;  
    }
    
    public static function getRequestFullUrl() {
        $_SERVER["HTTP_HOST"] = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '';
        $_SERVER["SERVER_PORT"] = isset($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : '';
        $_SERVER["PHP_SELF"] = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : '';
        $url = sprintf("%s://%s%s%s", (!isset($_SERVER["HTTPS"]) || ($_SERVER["HTTPS"] == "")) ? "http" : "https", ($_SERVER["HTTP_HOST"]), ($_SERVER["SERVER_PORT"] == "80" ? "" : ":" . $_SERVER["HTTP_HOST"]), ($_SERVER["PHP_SELF"]));
        if (isset($_SERVER["QUERY_STRING"])) {
            $q = trim($_SERVER["QUERY_STRING"]);
            if ($q != "") {
                $url = sprintf("%s?%s", $url, $q);
            }
        }
        return $url;
    }
    public static function getBase() {
        $_SERVER["HTTP_HOST"] = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '';
        $_SERVER["SERVER_PORT"] = isset($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : '';
        $url = sprintf("%s://%s:%d", (!isset($_SERVER["HTTPS"]) || ($_SERVER["HTTPS"] == "")) ? "http" : "https", ($_SERVER["HTTP_HOST"]), ($_SERVER["SERVER_PORT"]));
        return $url;
    }
    public static function getHomeUrl() {
        return self::getBase() . '/';//home.php
    }
    public static function getSizeStr($size) {
        if ($size <= 0) {
            return "";
        } elseif ($size > 1024 * 1024) {
            return sprintf("%0.1fM", $size / (1024 * 1024));
        } else {
            return sprintf("%0.1fK", $size / 1024);
        }
    }
    public static function getCallBackUrl($url = NULL) {
        if ($url == NULL) {
            $url = self::getRequestFullUrl();
        }
        $callbackurl = self::getBase();
        $callbackurl = sprintf("%s/gamecenter/callback?rt=%s", $callbackurl, urlencode($url));
        return $callbackurl;
    }
    public static function getLoginUrl($jumptourl = NULL) {
        $callbackurl = urlencode(self::getCallBackUrl($jumptourl));
        $wm = isset($_GET["wm"]) ? $_GET["wm"] : "3346_0001";
        $bc = new browser_checker();
        if ($bc->isIOS() || $bc->isAndroid()) {
            return sprintf("http://weibo.cn/dpool/ttt/h5/login.php?wm=%s&backURL=%s", $wm, $callbackurl);
        } elseif ($bc->supportWap2()) {
            return sprintf("http://3g.sina.com.cn/prog/wapsite/sso/login.php?wm=%s&ns=1&revalid=2&backURL=%s&vt=4", $wm, $callbackurl);
        } else {
            return sprintf("http://weibo.cn/dpool/ttt/h5/login.php?wm=%s&backURL=%s", $wm, $callbackurl);
        }
    }
    public static function getLoginUrlH5($back_url = '') {
        if(empty($back_url)) $back_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //设置跳转成功后,回跳地址,不设置为当前页面
        SSOWirelessClient::set_back_url($back_url);
        //获取跳转地址,需要登陆的时候,会跳转到微博h5登陆页面
        $loginurl = SSOWirelessClient::get_login_url('h5');
        if (FALSE === $loginurl) {
            $loginurl = str_replace(array('hassetsso=1','&hassetsso=1','hassetsso='), '', $back_url);
        }
        return $loginurl;
    }
    public static function getFriendIdString($friends)
    {
        if (!is_array($friends)) {
            return "";
        }
        $result = array();
        if (array_key_exists("users", $friends)) {
            foreach ($friends["users"] as $friend) {
                $result[] = $friend["id"];
            }
        } elseif (array_key_exists("ids", $friends)) {
            foreach ($friends["ids"] as $friendid) {
                $result[] = $friendid;
            }
        }
        return implode(",", $result);
    }
    /**
     * 获取游戏应用的详细信息
     * @param string $app_key
     * http://i.open.t.sina.com.cn/openapi/getappinfo.php?appkey=3470157549
     */
    public static function get_app_info($app_key, $field='')
    {
        $app_info = TAuthUtility::get_app_info($app_key);
        if (is_array($app_info) && !empty($app_info) && !empty($app_info['result']))
        {
            return (!empty($field) && isset($app_info['result'][$field])) ? $app_info['result'][$field] : $app_info['result'];
        }
        return '';
    }
    // 优化的require_once
    public static function require_only($filename)
    {
        static $_importFiles = array();
        if (!isset($_importFiles[$filename]))
        {
            if (is_file($filename))
            {
                require $filename;
                $_importFiles[$filename] = true;
            }
            else
            {
                $_importFiles[$filename] = false;
            }
        }
        return $_importFiles[$filename];
    }
    public static function server($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }
    public static function get_header($header, $default = '')
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        return server($name, $default);
    }
    /**
     * 取得正在执行的脚本
     *
     * @return string
     */
    public static function get_script()
    {
        if (isset ( $_SERVER ["SCRIPT_NAME"] )) {
            return $_SERVER ["SCRIPT_NAME"];
        }
        if (isset ( $_SERVER ["PHP_SELF"] )) {
            return $_SERVER ["PHP_SELF"];
        }
        return null;
    }

    /**
     * 获取用户信息
     */
    public static function getUserInfo($uid, $weibo, $expire = 3600, $weiboTauth = FALSE) {
        $key = sprintf("%s|getUserInfo|%s", 'USER', $uid);
        $weibo_method = $weiboTauth ? 'getUserInfo1' : 'show_user_by_id';
        $value = self::accessCacheData($key, array($weibo, $weibo_method), array($uid), $expire);
        return $value;
    }
    /**
     * 将一个二维数组转换为 HashMap，并返回结果
     *
     * 用法1：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = self::hashMap($rows, 'id', 'value');
     *
     * dump($hashmap);
     *   // 输出结果为
     *   // array(
     *   //   1 => '1-1',
     *   //   2 => '2-1',
     *   // )
     * @endcode
     *
     * 如果省略 $value_field 参数，则转换结果每一项为包含该项所有数据的数组。
     *
     * 用法2：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1'),
     *     array('id' => 2, 'value' => '2-1'),
     * );
     * $hashmap = self::hashMap($rows, 'id');
     *
     * dump($hashmap);
     *   // 输出结果为
     *   // array(
     *   //   1 => array('id' => 1, 'value' => '1-1'),
     *   //   2 => array('id' => 2, 'value' => '2-1'),
     *   // )
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_field 按照什么键的值进行转换
     * @param string $value_field 对应的键值
     *
     * @return array 转换后的 HashMap 样式数组
     */
    static final function hashMap($arr, $key_field, $value_field = null)
    {
        $ret = array();
        if ($value_field) 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row[$value_field];
            }
        } 
        else 
        {
            foreach ($arr as $row) 
            {
                $ret[$row[$key_field]] = $row;
            }
        }
        return $ret;
    }
    /**
     * 将一个二维数组按照指定字段的值分组
     *
     * 用法：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     * $values = self::groupBy($rows, 'parent');
     *
     * dump($values);
     *   // 按照 parent 分组的输出结果为
     *   // array(
     *   //   1 => array(
     *   //        array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *   //        array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *   //        array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *   //   ),
     *   //   2 => array(
     *   //        array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *   //        array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *   //   ),
     *   //   3 => array(
     *   //        array('id' => 6, 'value' => '6-1', 'parent' => 3),
     *   //   ),
     *   // )
     * @endcode
     *
     * @param array $arr 数据源
     * @param string $key_field 作为分组依据的键名
     *
     * @return array 分组后的结果
     */
    static final function groupBy($arr, $key_field)
    {
        $ret = array();
        foreach ($arr as $row) 
        {
            if(!isset($row[$key_field])) continue;
            $key = $row[$key_field];
            $ret[$key][] = $row;
        }
        return $ret;
    }
    /**
     * 根据指定的键对数组排序
     *
     * 用法：
     * @code php
     * $rows = array(
     *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
     *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
     *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
     *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
     *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
     *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     *
     * $rows = self::sortByCol($rows, 'id', SORT_DESC);
     * dump($rows);
     * // 输出结果为：
     * // array(
     * //   array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * //   array('id' => 5, 'value' => '5-1', 'parent' => 2),
     * //   array('id' => 4, 'value' => '4-1', 'parent' => 2),
     * //   array('id' => 3, 'value' => '3-1', 'parent' => 1),
     * //   array('id' => 2, 'value' => '2-1', 'parent' => 1),
     * //   array('id' => 1, 'value' => '1-1', 'parent' => 1),
     * // )
     * @endcode
     *
     * @param array $array 要排序的数组
     * @param string $keyname 排序的键
     * @param int $dir 排序方向
     *
     * @return array 排序后的数组
     */
    static final function sortByCol($array, $keyname, $dir = SORT_ASC)
    {
        return self::sortByMultiCols($array, array($keyname => $dir));
    }
    /**
     * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
     *
     * 用法：
     * @code php
     * $rows = self::sortByMultiCols($rows, array(
     *     'parent' => SORT_ASC, 
     *     'name' => SORT_DESC,
     * ));
     * @endcode
     *
     * @param array $rowset 要排序的数组
     * @param array $args 排序的键
     *
     * @return array 排序后的数组
     */
    static final function sortByMultiCols($rowset, $args)
    {
        $sortArray = array();
        $sortRule = '';
        foreach ($args as $sortField => $sortDir) 
        {
            foreach ($rowset as $offset => $row) 
            {
                $sortArray[$sortField][$offset] = $row[$sortField];
            }
            $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
        }
        if (empty($sortArray) || empty($sortRule)) { return $rowset; }
        eval('array_multisort(' . $sortRule . '$rowset);');
        return $rowset;
    }
}

/**
 * 格式化显示出变量
 *
 * @param  any
 * @return void
 */
function printr_dump($arr)
{
    echo '<pre>';
    $args = func_get_args();
    $function = create_function('&$item, $key', 'print_r($item);');
    array_walk($args, $function);
    echo '</pre>';
}
/**
 * 格式化并显示出变量类型
 *
 * @param  any
 * @return void
 */
function vdump($arr)
{
    echo '<pre>';
    $args = func_get_args();
    $function = create_function('&$item, $key', 'var_dump($item);');
    array_walk($args, $function);
    echo '</pre>';
}
/**
 * 用于输出一个变量的内容
 *
 * @return string
 */
function dump()
{
    $args = func_get_args();
    call_user_func_array('printr_dump', $args);
}