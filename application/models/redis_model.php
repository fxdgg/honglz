<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* redis操作类
*
*/
class Redis_model extends CI_Model{

    /**
     * redis配置信息
     */
	protected $redis_config = null;

    /**
     * redis 连接句柄
     *
     * @var resource
     */
    protected $_conn;
    /**
     * 是否允许使用该扩展
     * @var boolean
     */
	protected $_enable = FALSE;

    /**
     * 默认的缓存服务器
     *
     * @var array
     */
    protected $_default_server = array(
        /**
         * 缓存服务器地址或主机名
         */
        'host' => '127.0.0.1',

        /**
         * 缓存服务器端口
         */
        'port' => '6379',
    );

    /**
     * 默认的缓存策略
     *
     * @var array
     */
    protected $_default_policy = array(
        /**
         * 缓存服务器配置，参看$_default_server
         * 允许多个缓存服务器
         */
        'servers' => array(),

        /**
         * 是否压缩缓存数据
         */
        'compressed' => false,

        /**
         * 缓存有效时间
         *
         * 如果设置为 0 表示缓存永不过期
         */
        'life_time' => 0,
    
        /**
         * 为此服务器创建的桶的数量，用来控制此服务器被选中的权重
         * 单个服务器被选中的概率是相对于所有服务器weight总和而言的
         */
        'weight'=>50,
        
        /**
         * 连接持续（超时）时间（单位秒），默认值1秒
		 * 修改此值之前请三思，过长的连接持续时间可能会导致失去所有的缓存优势
         */
        'timeout'=>10,

        /**
         * 是否使用持久连接
         */
        'pconnect' => true,
    	/**
    	 * key前缀
    	 */
        'tag_prefix' => 'hlz|',
	    /**
	     * 连接密码
	     */
    	'password' => '',
    );

    /**
     * 构造函数
     *
     * @param 缓存策略 $policy
     */
	public function __construct()
	{
		parent::__construct();
		$this->config->load('redis', TRUE);
		$this->redis_config = $this->config->item('redis', 'redis');
		if (isset($this->redis_config['enable']) && TRUE == $this->redis_config['enable'])
		{
			$this->enable();
	        if (!extension_loaded('redis'))
	        {
	            throw new Exception('The redis extension must be loaded before use!');
	        }
	        $this->open();
		}
	}
	public function &getInstance()
	{
         static $instance = null; 
         if (is_null($instance))
         {
             $instance = new self(); 
         }
         return $instance; 
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
        if (!empty($this->_conn))
        {
            $this->_conn = null;
            $this->_enable = FALSE;
        }
    }
    /**
     * 打开redis连接,有可能不真实连接数据库
     * @param $config   redis连接参数
     *          
     * @return void
     */
    public function open($policy = array()) 
    {
    	if (!$this->_enable) return FALSE;
        if (is_object($this->_conn)) return;
        if (empty($policy))
        {
            $policy =& $this->redis_config;
            $this->_default_policy['servers'] = array_merge($this->_default_policy['servers'], $policy['servers']);
            unset($policy['servers']);
        }
        $this->_default_policy = array_merge($this->_default_policy, $policy);
        if (empty($this->_default_policy['servers']))
        {
            $this->_default_policy['servers'][] = $this->_default_server;
        }
        if($this->_default_policy['autoconnect'] == 1) 
        {
            $this->connect();
        }
    }
    
    /**
     * 真正开启连接
     * host:服务端监听地址、port:服务端监听端口、timeout:连接持续（超时）时间（单位秒）
     * $redis->connect('127.0.0.1', 6379);
	 * $redis->pconnect('127.0.0.1', 6379, 5);
     * @return void
     */
    public function connect() 
    {
    	if (!$this->_enable) return FALSE;
        if (is_object($this->_conn)) return;
        $server_connections = array();
        if(is_array($this->_default_policy['servers']) && !empty($this->_default_policy['servers']))
        {
        	$server_connections =& $this->_default_policy['servers'];
        }
        if(!is_array($server_connections) || empty($server_connections)) $server_connections[] = array('host'=>'127.0.0.1','port'=>6379);
        $server_config = $server_connections[array_rand($server_connections)];
        $this->_conn = new Redis();
        $func = $this->_default_policy['pconnect'] ? 'pconnect' : 'connect';
        $this->handler = $this->_default_policy['timeout'] === false ?
        	$this->_conn->$func($server_config['host'], $server_config['port']) :
            $this->_conn->$func($server_config['host'], $server_config['port'], $this->_default_policy['timeout']);
        if ( ! $this->handler)
        {
        	throw new Exception(sprintf('Connect redis server [%s:%s] failed!', $server_config['host'], $server_config['port']));
        }
        if (!empty($this->_default_policy['password']))
        {
        	$this->_conn->auth($this->_default_policy['password']);
        }
    }
    /**
     +----------------------------------------------------------
     * 是否连接
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    private function isConnected()
    {
        return $this->handler;
    }
    /**
     * 选择一个数据库
     * 
     * @param int $dbID    数据库ID
     * @return mixed
     */
    public function select($dbID = 0)
    {
    	if (!$this->_enable) return NULL;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->select($dbID) : NULL;
    }
    /**
     * 获取指定键名的数据
     * 
     * @param string $key
     * @param boolean $isAddPrefix
     * @return mixed
     */
    public function get($key, $isAddPrefix = TRUE) 
    {
    	if (!$this->_enable) return NULL;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->get($key) : NULL;
    }

    /**
     * 获取一组缓存数据
     * @param string $key
     * @param boolean $isAddPrefix
     * @see cache_redis
     */
    public function getMany($keys, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return array();
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		//array_map()函数返回用户自定义函数作用后的数组
		$realKey = $isAddPrefix ? array_map(array($this,'getRealKey'), $keys) : $keys;
        $values = array();
        foreach ($this->_conn->get(array_map(create_function('$k', 'return $k;'), $realKey)) as $key => $value)
        {
            $values[$key] = $value;
        }
        
        return $values;
    }

    /**
     * @param string $key
     * @param boolean $isAddPrefix
    * @see cache_redis
    */
    public function has($key, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return !(FALSE === $this->_conn->exists($key));
    }
    
    /**
     * 存储指定键名的数据（如存在则覆盖）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $life_time
     * @param boolean $isAddPrefix
     * @return bool
     */
    public function set($key, $value, $life_time = 0, $isAddPrefix = TRUE) 
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        if(is_int($life_time) && $life_time > 0) {
            $result = $this->_conn->setex($key, $life_time, $value);
        }else{
            $result = $this->_conn->set($key, $value);
        }
        return $result;
    }
    /**
     * 获取符合条件的key的列表
     * @param string $key
     * @param boolean $isAddPrefix
     */
    public function keys($key, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return array();
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn->keys($key);
    }
    /**
     * 增加指定键名的数据（如存在则返回FALSE）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $life_time
     * @param boolean $isAddPrefix
     * @return bool
     */
    public function add($key, $value, $life_time = 0, $isAddPrefix = TRUE) 
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        if(is_int($life_time) && $life_time > 0) {
			$result = $this->_conn->setnx($key, $value) && $this->_conn->setTimeout($key, $life_time);
        }else{
        	$result = $this->_conn->setnx($key, $value);
        }
        return $result;
    }
    
    public function delete($key) 
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
        if ($key)
        {
        	if (substr($key, 0, 4) != $this->_default_policy['tag_prefix'])
        	{
        		$key = $this->getRealKey($key);
        	}
        }
        return $this->_conn ? $this->_conn->delete($key) : FALSE;
    }

    /**
     * 删除指定键名序列的数据
     * 
     * @param array $keys
     * @return bool
     */
    public function deleteMulti($keys)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
        if (!$this->_conn || empty($keys))
        {
            return false;
        }
        
        foreach ($keys as $key)
        {
        	$this->delete($key);
        }
        
        return true;
    }
    /**
     * 增加整数数据的值
     * 
     * @param string $key
     * @param int $offset
     * @param boolean $isAddPrefix
     * @return bool
     */
    public function increment($key, $offset=1, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->incrBy($key, $offset) : FALSE;
    }
    
    /**
     * 减少整数数据的值
     * 
     * @param string $key
     * @param int $offset
     * @param boolean $isAddPrefix
     * @return bool
     */
    public function decrement($key, $offset=1, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->decrBy($key, $offset) : FALSE;
    }
    public function lRange($key, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return NULL;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
		$data = array();
		$dataSize = $this->_conn ? $this->_conn->lSize($key) : 0;
    	if ($dataSize > 0)
		{
			$data = $this->_conn ? $this->_conn->lRange($key, 0, -1) : array();
		}
		return $data;
    }
    public function lPush($key, $data, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->lPush($key, $data) : FALSE;
    }
    public function rPush($key, $data, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->rPush($key, $data) : FALSE;
    }
    /**
     * LRANGE greet 0 4         # 查看所有元素
     * LREM greet 2 morning     # 移除从表头到表尾，最先发现的两个 morning
     * LREM greet -1 morning    # 移除从表尾到表头，第一个 morning
     * LREM greet 0 hello      # 移除表中所有 hello
     * @param string $key
     * @param string $data
     * @param bool $isAddPrefix
     */
    public function lRem($key, $data, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->lRem($key, $data) : FALSE;
    }
    /**
     * @param string $key
     * @param string $data
     * @param bool $isAddPrefix
     */
    public function sAdd($key, $data, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->sAdd($key, $data) : FALSE;
    }
    /**
     * @param string $key
     * @param string $data
     * @param bool $isAddPrefix
     */
    public function sMembers($key, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->sMembers($key) : FALSE;
    }
    /**
     * @param string $key
     * @param string $data
     * @param bool $isAddPrefix
     */
    public function sIsMember($key, $member, $isAddPrefix = TRUE)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
		$isAddPrefix && $key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->sIsMember($key, $member) : FALSE;
    }
    
    public function __call($method, $arguments)
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
        if ( ! method_exists($this->_conn, $method))
		{
			return NULL;
		}
		/*if (isset($arguments[0]))
		{
			$arguments[0] = $this->getRealKey($arguments[0]);
		}*/
        return $this->_conn ? call_user_func_array(array($this->_conn, $method), $arguments) : FALSE;
    }
    
    /**
     * 无效化所有缓存数据（清空缓存，慎用）
     * 
     * @return bool
     */
    public function flush() 
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->flushall() : FALSE;
    }
    /**
     * 清空缓存DB里的数据
     * 
     * @return bool
     */
    public function flushdb($dbID = 0) 
    {
    	if (!$this->_enable) return FALSE;
        if (!is_object($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->flushdb($dbID) : FALSE;
    }
	/**
	 * 关闭redis的连接
	 */
	public function close()
	{
    	if (!$this->_enable) return FALSE;
		return $this->_conn ? $this->_conn->close() : FALSE;
	}
	public function getPrefixKey()
	{
		return $this->_default_policy['tag_prefix'];
	}
	/**
	 * 获取redis中实际存储的key
	 * @param string $key
	 */
	public function getRealKey($key)
	{
		//返回前缀
		return $this->_default_policy['tag_prefix'] . $key;
	}
}