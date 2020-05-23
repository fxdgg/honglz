<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',                            'rb');
define('FOPEN_READ_WRITE',                      'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',        'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',   'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',                    'ab');
define('FOPEN_READ_WRITE_CREATE',               'a+b');
define('FOPEN_WRITE_CREATE_STRICT',             'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',        'x+b');

//应用名称
define('APP_SITE_NAME', '');//红荔枝、老同事、劳动光荣
//应用名称-后台显示用
define('APP_SITE_NAME_BACKEND', '劳动光荣');//红荔枝、老同事、劳动光荣
//应用名称-JD生成PC端显示用
define('APP_SITE_NAME_JD', 'RunningJd');//红荔枝、老同事、劳动光荣、RunningJd
//应用名称-JD生成PC端显示用
define('APP_SITE_NAME_SITE', '劳动光荣（北京）网络科技有限公司');
//系统开始时间
define('SYS_START_TIME', microtime());
//定义为当前时间，减少框架调用 time() 的次数
define('SYS_TIME', time());
//DIRECTORY_SEPARATOR 的简写
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', '/');
define('IMAGE_PATH', PUBLIC_PATH.'bootstrap/img/');
define('CSS_PATH', PUBLIC_PATH.'bootstrap/css/');
define('JS_PATH', PUBLIC_PATH.'bootstrap/js/');
//一分钟-时间戳
define('MINU_TIMESTAMP', 60);
//五分钟-时间戳
define('FIVE_MINU_TIMESTAMP', MINU_TIMESTAMP * 5);
//一小时-时间戳
define('HOUR_TIMESTAMP', MINU_TIMESTAMP * 60);
//一天-时间戳
define('DAY_TIMESTAMP', 86400);
//一周-时间戳
define('WEEK_TIMESTAMP', DAY_TIMESTAMP * 7);
//一月-时间戳
define('MONTH_TIMESTAMP', DAY_TIMESTAMP * 30);
//一年-时间戳
define('YEAR_TIMESTAMP', DAY_TIMESTAMP * 365);
//一周-天数
define('WEEK_DAY_NUM', 7);
//微博相关配置(WB_AKEY、WB_SKEY、WB_CALLBACK_URL)
define('WEIBO_APP_KEY', 2594901488);
define('WEIBO_APP_SECRET', '2cf25a8caa8f3f44a84f8bffac5a2ba2');
define('WEIBO_APP_KEY_TEST', 81582687);
define('WEIBO_APP_SECRET_TEST', 'dade5c38cb90560ecd6cbefc2138f804');
define('WEIBO_APP_KEY_DEBUG', 1921863444);
define('WEIBO_APP_SECRET_DEBUG', 'd3e88309068dd6d6b1464f3d4c519947');
define('APP_KEY', 1283205126);
define('WEIBO_CALLBACK_URL', 'http://115.28.47.162/testindex.php/users/weiboCallback');
define('WEIBO_TOKEN_KEY', 'weibo_token');
define('SINA_TOKEN_URL', 'http://m2.game.weibo.cn/api/process/get-token');
define('SINA_EMAIL_URL', 'http://m2.game.weibo.cn/api/process/ldgr');
define('SINA_AUTH_KEY', 'aWx59aYTmwyxl8y60ada$688d4d5c@)#0a3y=');
define('SOURCE', 'source');
define('ACCESS_TOKEN', 'access_token');
//校验微博接口返回的appkey
define('ADJUST', 'hLxr5UwiK5YTmwycenterD1ioa8DN9g9ntZ');
define('FORM_CSRF_KEY', '657A6943-93AE-5446-C913-D412D842C184');
//redis配置
define('REDIS_HOST_R', '127.0.0.1');
define('REDIS_PORT_R', '6379');
define('REDIS_HOST_W', '127.0.0.1');
define('REDIS_PORT_W', '6379');

/* End of file constants.php */
/* Location: ./application/config/constants.php */