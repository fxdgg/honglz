<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class BLH_Memcache {

    /**
     * memcached 连接句柄
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
        'port' => '11211',
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
        'persistent' => true,
        
        /**
         * 是否使用Memcached
         */
        'useMemcached' => false,
    );
    
    private $memcache = null;
    /**
     * memcache配置信息
     */
    private $config = null;
    /**
     * 配置列表
     */
    public $cache_config = array(//memcache config
        'memcache' => array(
    		'reader' => array('127.0.0.1:11211'),
    		'writer' => array('127.0.0.1:11211'),
            'type' => 'memcache',
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
        if (!extension_loaded('memcache'))
        {
            throw new Exception('The memcache extension must be loaded before use!');
        }
    }

	/**
	 * 重组为可用配置
	 * @param	array	$setting	自定义缓存配置
	 */
	public function get_servers()
	{
        if(is_array($this->_default_policy['servers']))
        {
            $hosts = isset($this->_default_policy['servers']['host']) ? $this->_default_policy['servers']['host'] : array();
            $ports = isset($this->_default_policy['servers']['port']) ? $this->_default_policy['servers']['port'] : array();
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
    public function load($memcache_host, $memcache_port = 11211, $cache_name = 'memcache', $read_tag = TRUE)
    {
        $object = null;
        if(isset($this->cache_config[$cache_name]['type'])) {
            switch($this->cache_config[$cache_name]['type']) {
                case 'memcache' :
                	$this->cache_config[$cache_name]['read_tag'] = (bool)$read_tag;
                    $this->cache_config[$cache_name]['reader'] = $this->cache_config[$cache_name]['writer'] = array($memcache_host.':'.$memcache_port);
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
            $this->_default_policy = array_merge($this->_default_policy, $current_config);
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
     * host:服务端监听地址、port:服务端监听端口、timeout:连接持续（超时）时间（单位秒），默认值1秒，修改此值之前请三思，过长的连接持续时间可能会导致失去所有的缓存优势
     * retry_interval:服务器连接失败时重试的间隔时间，默认值15秒。如果此参数设置为-1表示不重试、status:控制此服务器是否可以被标记为在线状态、
     * failure_callback:允许用户指定一个运行时发生错误后的回调函数。回调函数会在故障转移之前运行。回调函数会接受到两个参数，分别是失败主机的 主机名和端口号
     * 
     * Memcache::addServer方法的作用是添加一个可供使用的服务器地址，Memcache::addServer方法有8个参数，
     * 除了第一个参数之外，其他都是可选的，第一个参数表示服务器的地址，第二个参数表示端口，
     * 第三个参数表示是否是一个持久连接，第四个参数表示这台服务器在所有服务器中所占的权重，
     * 第五个参数表示连接的持续时间，第六个参数表示连接重试的间隔时间，默认为15,设置为-1表示不进行重试，
     * 第七个参数用来控制服务器的在线状态，第8个参数允许设置一个回掉函数来处理错误信息
     * 当使用这个方法的时候(与Memcache::connect()和Memcache::pconnect()相反) 网络连接并不会立刻建立，
     * 而是直到真正使用的时候才建立。 因此在加入大量服务器到连接池中时也是没有开销的
     * 故障转移可能在方法的任何一个层次发生，通常只要其他服务器可用用户就不会感受到。任何的socket或memcache服务器级别的错误 （比如内存溢出）都可能导致故障转移。
     * 而一般的客户端错误比如使用Memcache::add尝试增加一个已经存在的key则不会导致故障转移
     * 
     * Memcache::setServerParams方法的作用是在运行时修改服务器的参数，Memcache::setServerParams方法有六个参数，
     * 比Memcache::addServer方法少了第三和第四个参数。Memcache::getServerStatus方法的作用是获取运行服务器的参数，两个参数分别表示的地址和端口
     * $memcache->addServer('192.168.1.116', 11211);
	 * $memcache->setServerParams('192.168.1.116', 11211, 1, 15, true, '_callback_memcache_failure');
	 * $memcache->getServerStatus('192.168.1.116', 11211);
     * @return void
     */
    public function connect() 
    {
        if(is_resource($this->_conn)) return;
        $this->_conn = $this->_default_policy['useMemcached'] ? new Memcached : new Memcache;
        if(is_array($this->_default_policy['servers']) && isset($this->_default_policy['host']))
        {
            foreach ($this->_default_policy['servers'] as $server)
            {
                if($this->_default_policy['useMemcached'])
                {
                    $result = $this->_conn->addServer($server['host'], $server['port'], $this->_default_policy['weight']);
                }else{
                    $result = $this->_conn->addServer($server['host'], $server['port'], $this->_default_policy['persistent'], $this->_default_policy['weight'], $this->_default_policy['timeout']);
                }
                if (!$result)
                {
                    throw new Exception(sprintf('Connect memcached server [%s:%s] failed!', $server['host'], $server['port']));
                }
                /**
                 * setCompressThreshold方法的作用是对大于某一大小的数据进行压缩
                 * setCompressThreshold方法有两个参数，第一个参数表示处理数据大小的临界点
                 * 第二个参数表示压缩的比例，默认为0.2
                 */
                $this->_conn->setCompressThreshold(2000, 0.2);
            }
        }else{
            $method = $this->_default_policy['persistent'] ? 'pconnect' : 'connect';
            //Memcache::getServerStatus()返回一个服务器的在线/离线状态,0表示服务器离线，非0表示在线
            if ($this->_conn->getServerStatus($this->_default_policy['host'], $this->_default_policy['port']) === 0)
            {
                if (!$this->_conn->$method($this->_default_policy['host'], $this->_default_policy['port'], $this->_default_policy['timeout']))
                {
                    throw new Exception(sprintf('Unable to connect to the memcache server (%s:%s).', $this->_default_policy['host'], $this->_default_policy['port']));
                }
            }
            return true;
        }
//        $this->_conn = new Memcache();
//        foreach ($this->config['servers'] as $config)
//        {
//            if($this->_conn->addServer($config['host'], $config['port'], $config['timeout']))
//            {
//                $this->_conn->setCompressThreshold(2000, 0.2);
//            }
//            else
//            {
//                $this->_conn = NULL;
//                break;
//            }
//        }
//        return $this->_conn;
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
        return $this->_conn ? $this->_conn->get($key) : null;
    }
    /**
     * 获取指定键名序列的数据
     * 
     * @param array $keys
     * @return array
     */
    public function getMulti($keys)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? ($this->_default_policy['useMemcached'] ? $this->_conn->getMulti($keys) : $this->getMany($keys)) : null;
    }

    /**
     * 获取一组缓存数据
     * @see BLH_Memcache
     */
    public function getMany($keys)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        $values = array();
        foreach ($this->_conn->get(array_map(create_function('$k', 'return $k;'), $keys)) as $key => $value)
        {
            $values[$key] = $value;
        }
        
        return $values;
    }

    /**
    * @see BLH_Memcache
    */
    public function has($key)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return !(false === $this->_conn->get($key));
    }
    
    /**
     * 存储指定键名的数据（如存在则覆盖）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, array $policy = null) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        $compressed = isset($policy['compressed']) ? $policy['compressed'] : $this->_default_policy['compressed'];
        $life_time = isset($policy['life_time']) ? $policy['life_time'] : $this->_default_policy['life_time'];
        
        return $this->_conn ? ($this->_default_policy['useMemcached'] ? $this->_conn->set($key, $value, $life_time) : $this->_conn->set($key, $value, $compressed ? MEMCACHE_COMPRESSED : 0, $life_time)) : false;
    }

    /**
     * 存储指定数据序列（如存在则覆盖）
     * 
     * @param array $items
     * @param int $expiration
     * @return bool
     */
    public function setMulti($items, $expiration=0)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? ($this->_default_policy['useMemcached'] ? $this->_conn->setMulti($items, $expiration) : false) : false;
    }
    
    public function delete($key) 
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->delete($key) : false;
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
        return $this->_conn ? $this->_conn->increment($key, $offset) : false;
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
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->decrement($key, $offset) : false;
    }
    /**
     * 取得Memcached对象
     * 
     * @return \Memcached
     */
    public function getMemcached() 
    {
        return $this->_conn;
    }
    
    /**
     * 添加新数据（如存在则失败）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function add($key, $value, array $policy = null)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        $compressed = isset($policy['compressed']) ? $policy['compressed'] : $this->_default_policy['compressed'];
        $life_time = isset($policy['life_time']) ? $policy['life_time'] : $this->_default_policy['life_time'];
        
        return $this->_default_policy['useMemcached'] ? $this->_conn->add($key, $value, $life_time) : $this->_conn->add($key, $value, $compressed ? MEMCACHE_COMPRESSED : 0, $life_time);
    }
    
    /**
     * 替换指定键名的数据（如不存在则失败）
     * 
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function replace($key, $value, array $policy = null)
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        $compressed = isset($policy['compressed']) ? $policy['compressed'] : $this->_default_policy['compressed'];
        $life_time = isset($policy['life_time']) ? $policy['life_time'] : $this->_default_policy['life_time'];
        
        return $this->_conn ? $this->_conn->replace($key, $value, $compressed ? MEMCACHE_COMPRESSED : 0, $life_time) : false;
    }
    
    
    public function __call($method, $arguments)
    {
        return $this->_conn ? call_user_func_array(array($this->_conn, $method), $arguments) : false;
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
        return $this->_conn ? $this->_conn->flush() : false;
    }
    
    /**
     * 获取服务器统计信息
     * 
     * @return array
     */
    public function stat()
    {
        if(!is_resource($this->_conn)) 
        {
            $this->connect();
        }
        return $this->_conn ? $this->_conn->getStats() : null;
    }
}