<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* memcached操作类
*
*/
class Memcached_model extends CI_Model{

	var $memcached;
	var $hostname;
	var $port;
	var $timeout;
	var $tag_prefix;
	var $_mem;
    /**
     * 是否允许使用该扩展
     * @var boolean
     */
	protected $_enable = FALSE;
	
	function __construct()
	{
		parent::__construct();
		$this->config->load('memcached', TRUE);
		$this->memcached = $this->config->item('memcached', 'memcached');
		$this->hostname = $this->memcached['hostname'];
		$this->port = $this->memcached['port'];
		$this->timeout = $this->memcached['timeout'];
		$this->tag_prefix = $this->memcached['tag_prefix'];
		$this->autoconnect = $this->memcached['autoconnect'];
		if (isset($this->memcached['enable']) && TRUE == $this->memcached['enable'])
		{
			$this->enable();
	        if (!extension_loaded('memcache'))
	        {
	            throw new Exception('The memcache extension must be loaded before use!');
	        }
		}
	}
    /**
     * 启用缓存
	 * @param boolean $is_read
	 * @return object $this
     */
    public function enable()
    {
    	$this->_enable = TRUE;
    }
    /**
     * 禁用缓存
     * 
     */
    public function disable()
    {
        if (!empty($this->_mem))
        {
            $this->_mem = null;
            $this->_enable = FALSE;
        }
    }
    /**
     * 打开memcached连接,有可能不真实连接数据库
     * @param $config   memcached连接参数
     *          
     * @return void
     */
    public function open() 
    {
    	if (!$this->_enable) return FALSE;
        if ($this->autoconnect == 1) 
        {
            $this->connect();
        }
    }
    /**
     * 真正开启连接
     * host:服务端监听地址、port:服务端监听端口、timeout:连接持续（超时）时间（单位秒）
     * @return void
     */
    public function connect() 
    {
    	if (!$this->_enable) return FALSE;
        if (is_resource($this->_mem)) return;
        
		$this->_mem = new Memcache;
		$this->_mem->connect($this->hostname, $this->port, $this->timeout);
    }
    /**
     * 获取指定键名的数据
     * 
     * @param string $key
     * @param boolean $isAddPrefix
     * @return mixed
     */
	function get($key, $isAddPrefix = TRUE)
	{
    	if (!$this->_enable) return NULL;
		$data = $this->_mem->get($key);
		return $data;
	}
    /**
     * 存储指定键名的数据（如存在则覆盖）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @param boolean $isAddPrefix
     * @return bool
     */
	function set($key, $value, $expire = 0, $isAddPrefix = TRUE)
	{
    	if (!$this->_enable) return FALSE;
		$isAddPrefix && $key = $this->getRealKey($key);
		$flag = $this->_mem->set($key, $value, false, $expire);
		return $flag;
	}

    /**
     * 更新指定键名的数据（如存在则覆盖）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $expire
     * @param boolean $isAddPrefix
     * @return bool
     */
	function replace($key, $value, $expire = 0, $isAddPrefix = TRUE)
	{
    	if (!$this->_enable) return FALSE;
		$isAddPrefix && $key = $this->getRealKey($key);
		$flag = $this->_mem->replace($key, $value, false, $expire);
		return $flag;
	}

    /**
     * 删除指定键名的数据
     * 
     * @param string $key
     * @param int $expire
     * @param boolean $isAddPrefix
     * @return mixed
     */
	function delete($key, $expire = 0, $isAddPrefix = TRUE)
	{
    	if (!$this->_enable) return FALSE;
		$isAddPrefix && $key = $this->getRealKey($key);
		$flag = $this->_mem->delete($key, $expire);
		return $flag;
	}

	function close()
	{//断开连接
    	if (!$this->_enable) return FALSE;
		$this->_mem->close();
	}
	
    public function __call($method, $arguments)
    {
    	if (!$this->_enable) return FALSE;
        if (!method_exists($this->_mem, $method))
		{
			return NULL;
		}
        return $this->_mem ? call_user_func_array(array($this->_mem, $method), $arguments) : FALSE;
    }
	function &getInstance()
	{
         static $instance = null; 
         if (is_null($instance))
         {
             $instance = new self(); 
         }
         return $instance; 
    }
	/**
	 * 获取memcached中实际存储的key
	 * @param string $key
	 */
	public function getRealKey($key)
	{
		//返回前缀
		return $this->tag_prefix . $key;
	}
}