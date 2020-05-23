<?php
class RecruitLog extends CI_Model{
    public $_table = 'tbl_recruit_log';
    /**
     * 类型区分
     * @var string
     */
    public static $category_type = array(
        0 => 'apply', //应聘信息
        1 => 'like', //感兴趣
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
     * 获取【感兴趣】的列表
     */
    public function fetch_like_list($userId=0, $page=1, $pagesize=10)
    {
        $this->db->select(array_merge(array('rbp.*'), array('IF(trl.type, 1, 0) AS userLike')));
        $this->db->join('recruitblockpost rbp', "trl.jobId=rbp.id AND rbp.state='new'", 'INNER');
        $this->db->where('trl.type', self::$category_type[1]);
        $this->db->where('trl.userId', $userId);
        $this->db->ar_where[] = sprintf("AND trl.createTime >= DATE_ADD(now(), INTERVAL -%d DAY)", WEEK_DAY_NUM * 2);
        $this->db->order_by('trl.createTime' ,'DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' trl');
        return $query->result_array();
    }
    /**
     * 记录用户应聘或感兴趣的操作
     */
    public function record_operation_log($params = array())
    {
        $data = array(
            'type' => $params['type'],
            'userId' => $params['userId'],
            'jobId' => $params['jobId'],
            'createTime' => date('Y-m-d H:i:s'),
        );
        !empty($params['categoryLast']) && $data['categoryLast'] = $params['categoryLast'];
        !empty($params['category']) && $data['category'] = $params['category'];
        return $this->db->insert($this->_table, $data);
    }
}
