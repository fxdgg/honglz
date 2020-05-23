<?php
class Category extends CI_Model{
    public $_table = 'tbl_category';
    /**
     * 分类类型-知名度，1位数字
     * @var string
     */
    public static $category_type_popu = 'popularity';
    /**
     * 分类类型-地区，2位数字
     * @var string
     */
    public static $category_type_area = 'area';
    /**
     * 分类类型-职级，1位数字
     * @var string
     */
    public static $category_type_posi = 'position';
    /**
     * 分类类型-行业类别，3位数字
     * @var string
     */
    public static $category_type_voca = 'vocation';
    /**
     * 分类类型-详细配置
     * @var string
     */
    public static $category_name_config = array(
        'popularity' => '知名度',
        'area' => '地区',
        'position' => '职级',
        'vocation' => '行业类别',
    );
    /**
     * 分类类型-详细排序配置
     * @var string
     */
    public static $category_name_sort_config = array(
        'popularity' => array(
            'strategyId' => '1',
            'length' => 1,
        ),
        'area' => array(
            'strategyId' => '2',
            'length' => 2,
        ),
        'position' => array(
            'strategyId' => '3',
            'length' => 1,
        ),
        'vocation' => array(
            'strategyId' => '4',
            'length' => 3,
        ),
    );
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = FALSE;

    function __construct()
    {
        parent::__construct();
    }
    /**
     * 添加招聘分类
     */
    public function add_new_category($data)
    {
        if (!isset($data['type']) OR empty(self::$category_name_config[$data['type']]))
        {
            return FALSE;
        }
        if (isset($data['editPosts'])) unset($data['editPosts']);
        if (isset($data['typeName'])) unset($data['typeName']);
        if (isset($data['cid'])) unset($data['cid']);
        //获取分类名称
        $data['sortId'] = self::$category_name_sort_config[$data['type']]['strategyId'];
        //获取分类名称
        $data['typeName'] = self::$category_name_config[$data['type']];
        //获取最大的cid
        $max_cid = $this->fetch_max_cid($data['type']);
        $cal_cid = (int)$max_cid['cid'] + 1;
        $max_cid_cal = str_pad($cal_cid, self::$category_name_sort_config[$data['type']]['length'], '0', STR_PAD_LEFT);
        //计算最新的cid
        $data['cid'] = $max_cid_cal;
        $data['cname'] = htmlspecialchars($data['cname']);
        $ret = $this->db->insert($this->_table, $data); 
        if($ret){
            //清理分类的缓存
            $this->clear_category_all_data();
            return TRUE;
        }
        return FALSE;
    }
    /**
     * 获取分类的HashMap信息
     */
    public function fetch_category_hashmap_data($group_field = 'id')
    {
        $category_list = $this->fetch_category_all_data();
        return !empty($category_list) ? BLH_Utilities::hashMap($category_list, $group_field) : array();
    }
    /**
     * 获取分类的分组信息
     */
    public function fetch_category_group_data($group_field = 'type')
    {
        $category_list = $this->fetch_category_all_data();
        return !empty($category_list) ? BLH_Utilities::groupBy($category_list, $group_field) : array();
    }
    /**
     * 获取分类列表-Cache
     */
    public function fetch_category_all_data()
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s", __CLASS__, __FUNCTION__);
            $category_list_data = $this->getCacheData($cache_key);
            if (!empty($category_list_data))
            {
                return $category_list_data;
            }
        }
        $category_list_data_db = $this->fetch_category_all_data_db();
        if (self::$enableCache && !empty($category_list_data_db))
        {
            $this->setCacheData($cache_key, $category_list_data_db);
        }
        return $category_list_data_db;
    }
    /**
     * 获取分类列表-Db
     */
    public function fetch_category_all_data_db()
    {
        $this->db->select();
        $this->db->order_by('sortId', 'ASC');
        $this->db->order_by('sortCid', 'ASC');
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }
    /**
     * 获取某分类下，最大的职位ID
     */
    public function fetch_max_cid($type)
    {
        $this->db->select('cid');
        $this->db->where('type', $type);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get($this->_table);
        return $query->row_array();
    }
    /**
     * 清理分类列表-Cache
     */
    public function clear_category_all_data()
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s", __CLASS__, 'fetch_category_all_data');
            return $this->deleteCacheData($cache_key);
        }
        return FALSE;
    }
}
