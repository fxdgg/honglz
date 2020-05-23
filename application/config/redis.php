<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['redis']['enable'] = TRUE;
$config['redis']['servers'][0]['host'] = '127.0.0.1';//'115.28.47.162';
$config['redis']['servers'][0]['port'] = 6379;
$config['redis']['tag_prefix'] = (defined('ENVIRONMENT') && ENVIRONMENT == 'production') ? 'hlz|' : 'hlztest|';
$config['redis']['timeout'] = 10;
$config['redis']['pconnect'] = 0;
$config['redis']['autoconnect'] = 0;


/* End of file redis.php */
/* Location: ./application/config/redis.php */
