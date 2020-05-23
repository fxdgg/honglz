<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		log_message('debug', "Model Class Initialized");
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
	/**
	 * 获取缓存操作句柄
	 * @param string $cache
	 */
	function getCacheAdapter($cache = 'Redis')
	{
        static $instance = null; 
        if (!isset($instance[$cache]) OR is_null($instance[$cache]))
        {
		    $cache_obj = "{$cache}_model";
	    	$this->load->model($cache_obj);
	    	$instance[$cache] = $this->$cache_obj;
        }
    	return $instance[$cache];
	}
	function getCacheData($cache_key, $return = 'array', $cache = 'Redis')
	{
		$data_json = $this->getCacheAdapter($cache)->get($cache_key);
		if (!empty($data_json))
		{
			if ($return == 'array')
			{
				$data = json_decode($data_json, TRUE);
			}else{
				$data = json_decode($data_json);
			}
			if (!empty($data))
			{
				return $data;
			}
		}
    	return FALSE;
	}
	function setCacheData($cache_key, $cache_data, $expire = DAY_TIMESTAMP, $cache = 'Redis')
	{
		return $this->getCacheAdapter($cache)->set($cache_key, json_encode($cache_data), $expire);
	}
	function deleteCacheData($cache_key, $cache = 'Redis')
	{
		return $this->getCacheAdapter($cache)->delete($cache_key);
	}
	function getCacheHashData($cache_key, $cache = 'Redis')
	{
		$cache_data = $this->getCacheAdapter($cache)->hGetAll($cache_key);
		if (is_array($cache_data) && !empty($cache_data))
		{
			return $$cache_data;
		}
    	return array();
	}
	function setCacheHashData($cache_key, $cache_data, $expire = DAY_TIMESTAMP, $cache = 'Redis')
	{
		$ret = $this->getCacheAdapter($cache)->hMset($cache_key, $cache_data);
		return $ret && $this->getCacheAdapter($cache)->expireAt($cache_key, $expire);
	}
	function deleteMultiCacheData($cache_key, $cache = 'Redis')
	{
		return $this->getCacheAdapter($cache)->deleteMulti($cache_key);
	}
    function set_database($database)
    {
        $this->db = $this->load->database($database, TRUE);
        return $this;
    }
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */