<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class BLH_Redis {

    /**
     * redis 连接句柄
     *
     * @var resource
     */
    protected $_conn;

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
        'timeout'=>5,

        /**
         * 是否使用持久连接
         */
        'pconnect' => true,
    	/**
    	 * key前缀
    	 */
        'tag_prefix' => 'blh|',
    );
    
    private $memcache = null;
    /**
     * redis配置信息
     */
    private $config = null;
    /**
     * 配置列表
     */
    public $cache_config = array(//redis config
        'redis' => array(
    		'reader' => array('127.0.0.1:6379'),
    		'writer' => array('127.0.0.1:6379'),
            'type' => 'redis',
    		'tag_prefix' => 'blh|',
            'timeout' => 5,
            'pconnect' => 0,
            'autoconnect' => 0
        )
	);

    /**
     * 构造函数
     *
     * @param 缓存策略 $policy
     */
    public function __construct(array $policy = null) 
    {
    	try {
	        if (!extension_loaded('redis'))
	        {
	            throw new Exception('The redis extension must be loaded before use!');
	        }
    	} catch (RedisException $ex) {}
    }

	/**
	 * 重组为可用配置
	 * @param	array	$setting	自定义缓存配置
	 */
	public function get_servers()
	{
        if(is_array($this->_default_policy['servers']))
        {
            if(is_array($hosts) && is_array($ports) && $hosts && $ports)
            {
                foreach($hosts as $host)
                {
                    foreach($ports as $port)
                    {
                        $this->_default_policy['servers'][] = array('host'=>$host,'port'=>$port);
                    }
                }
            }
        }
	}
    /**
     *  加载缓存驱动
     * @param $cache_name   缓存配置名称
     * @param string $read_tag 只读标签
     * @return object
     */
    public function load($redis_host, $redis_port = 6379, $cache_name = 'redis', $read_tag = TRUE)
    {
        $object = null;
        if(isset($this->cache_config[$cache_name]['type'])) {
            switch($this->cache_config[$cache_name]['type']) {
                case 'redis' :
                	$this->cache_config[$cache_name]['read_tag'] = (bool)$read_tag;
                    $this->cache_config[$cache_name]['reader'] = $this->cache_config[$cache_name]['writer'] = array($redis_host.':'.$redis_port);
                    $object = $this->open($this->cache_config[$cache_name]);
                    break;
            }
        }
        return $object;
    }
    /**
     * 打开数据库连接,有可能不真实连接数据库
     * @param $config   数据库连接参数
     *          
     * @return void
     */
    public function open(array $policy = null) 
    {
        if(is_array($policy))
        {
	        if($policy['read_tag']){
	            $current_config = isset($policy['reader']) ? $policy['reader'] : $policy;
	            if (!empty($this->_default_policy['servers'])) $this->_default_policy['servers'] = array();
            	$this->_default_policy['servers'] = array_merge($this->_default_policy['servers'], $current_config);
            	unset($policy['reader'], $policy['writer']);
            }else{
	            $current_config = isset($policy['writer']) ? $policy['writer'] : $policy;
	            if (!empty($this->_default_policy['servers'])) $this->_default_policy['servers'] = array();
            	$this->_default_policy['servers'] = array_merge($this->_default_policy['servers'], $current_config);
            	unset($policy['reader'], $policy['writer']);
            }
            $this->_default_policy = array_merge($this->_default_policy, $policy);
        }
        if (empty($this->_default_policy['servers']))
        {
            $this->_default_policy['servers'][] = $this->_default_server;
        }
        //重组为可用配置
        //$this->get_servers();
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
        if(is_resource($this->_conn)) return;
        $server_connections = array();
        if(is_array($this->_default_policy['servers']) && !empty($this->_default_policy['servers']))
        {
            foreach ($this->_default_policy['servers'] as $server)
            {
            	if (strpos($server, ':') !== FALSE)
	            {
                    list($server_hosts, $server_ports) = explode(':', $server);
                    $server_connections[] = array('host'=>$server_hosts, 'port'=>$server_ports);
	            }
            }
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
    private function isConnected() {
        return $this->handler;
    }
    /**
     * 获取指定键名的数据
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->get($key) : NULL;
    }

    /**
     * 获取一组缓存数据
     * @see BLH_Redis
     */
    public function getMany($keys)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		//array_map()函数返回用户自定义函数作用后的数组
		$realKey = array_map(array($this,'getRealKey'),$keys);
        $values = array();
        foreach ($this->_conn->get(array_map(create_function('$k', 'return $k;'), $realKey)) as $key => $value)
        {
            $values[$key] = $value;
        }
        
        return $values;
    }

    /**
    * @see BLH_Redis
    */
    public function has($key)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
        return !(FALSE === $this->_conn->exists($key));
    }
    
    /**
     * 存储指定键名的数据（如存在则覆盖）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $policy = null) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        $life_time = (is_array($policy['life_time']) && isset($policy['life_time'])) ? $policy['life_time'] : ( int )$policy;//$this->_default_policy['life_time'];
    	
		$key = $this->getRealKey($key);
        if(is_int($life_time) && $life_time > 0) {
            $result = $this->_conn->setex($key, $life_time, $value);
        }else{
            $result = $this->_conn->set($key, $value);
        }
        return $result;
    }

    /**
     * 设置过期时间
     * @param string $key
     * @param int $policy
     * @param mixed $value
     * @param boolean $isAddPrefix
     */
    public function expireAt($key, $expire_time=5)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        $life_time = SYS_TIME + (int)$expire_time;
    	$key = $this->getRealKey($key);
        $ret = $this->_conn->expireAt($key, $life_time);
        return $ret;
    }

    /**
     * Hash操作-hGetAll
     * @param string $key 缓存key
     */
    public function hGetAll($key) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
		$result = $this->_conn ? $this->_conn->hGetAll($key) : NULL;
        return $result;
    }

    /**
     * Hash操作-hMset
     * @param string $key 缓存key
     * $redis->hMset('user:1', array('name' => 'Joe', 'salary' => 2000));
     */
    public function hMset($key, $field_list = array()) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
		$result = $this->_conn ? $this->_conn->hMset($key, $field_list) : FALSE;
        return $result;
    }

    /**
     * Hash操作-hIncrBy
     * @param string $key 缓存key
     * @param string $field 字段名
     * @param string $value 字段值
     */
    public function hIncrBy($key, $field, $increment=1) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
		$result = $this->_conn->hIncrBy($key, $field, $increment);
        return $result;
    }

    public function delete($key) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
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
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        if (!$this->_conn || empty($keys))
        {
            return false;
        }
        
        foreach ($keys as $key)
        {
            $this->_conn->delete($key);
        }
        
        return true;
    }
    /**
     * 增加整数数据的值
     * 
     * @param string $key
     * @param int $offset
     * @return bool
     */
    public function increment($key, $offset=1)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->incrBy($key, $offset) : FALSE;
    }
    
    /**
     * 减少整数数据的值
     * 
     * @param string $key
     * @param int $offset
     * @return bool
     */
    public function decrement($key, $offset=1)
    {
        if( ! is_resource($this->_conn)) 
        {
            $this->connect();
        }
		$key = $this->getRealKey($key);
        return $this->_conn ? $this->_conn->decrBy($key, $offset) : FALSE;
    }
    
    public function __call($method, $arguments)
    {
        if( ! is_resource($this->_conn)) 
        {
            $this->connect();
        }
        if ( ! method_exists($this->_conn, $method))
		{
			return null;
		}
        return $this->_conn ? call_user_func_array(array($this->_conn, $method), $arguments) : FALSE;
    }
    
    /**
     * 无效化所有缓存数据（清空缓存，慎用）
     * 
     * @return bool
     */
    public function flush() 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->flushall() : FALSE;
    }
	/**
	 * 关闭redis的连接
	 */
	public function close(){
		return $this->_conn ? $this->_conn->close() : FALSE;
	}
	/**
	 * 获取redis中实际存储的key
	 * @param string $key
	 */
	public function getRealKey($key){
		//返回前缀
		return $this->_default_policy['tag_prefix'] . $key;
		//return md5($this->_prefix.'_'.$this->_tag).md5($key);
	}
}