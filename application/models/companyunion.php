<?php
/**
 * 企业-社团关系表
 *
 */
class CompanyUnion extends CI_Model{
    protected $_table = 'tbl_company_union';
    /**
     * 企业[已退出]-社团
     * @var int
     */
    const UNION_ROLE_QUIT = 0;
    /**
     * 企业[已认证]社团
     * @var int
     */
    const UNION_ROLE_AUTH = 1;
    /**
     * 企业[未认证]社团
     * @var int
     */
    const UNION_ROLE_UNAUTH = 2;
    
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 根据企业名称搜索社团逻辑
     * @param string $companyName
     */
    public function searchUnionByCompany($companyName)
    {
        if (!empty($companyName))
        {
            return $this->searchUnionList($companyName);
        }
    }
    /**
     * 搜索社团列表
     */
    public function searchUnionList($companyName, $status = self::UNION_ROLE_AUTH, $source='union', $page=1, $pagesize=30)
    {
        $this->db->select('tcu.companyId,tcu.companyName,tcu.companyNick,tcu.companySimple,tcu.unionRole,tcu.createTime,tcu.updateTime,tu.unionId,tu.unionName,tu.unionNick,tu.unionStatus');
        $this->db->join('tbl_union tu', 'tcu.unionId = tu.unionId', 'INNER');
        $source == 'union' && $this->db->where('tcu.unionRole', $status);
        $source == 'union' && $this->db->like('tcu.companyName', $companyName, 'both');//both after before
        //用户在社团里的状态(0:被关闭1:非认证且临时2:非认证且生效3:认证且生效)
        $source == 'union' && $this->db->where('tu.unionStatus', UnionManage::UNION_STATUS_AUTH_VALID);
        $source != 'union' && $this->db->order_by('tcu.companySimple ASC,tcu.unionRole ASC,tcu.companyId ASC');
        $source != 'union' && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tcu');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 搜索社团列表的总数
     */
    public function searchUnionListTotal($companyName, $status = self::UNION_ROLE_AUTH, $source='union')
    {
        $this->db->select('tcu.companyId');
        $this->db->join('tbl_union tu', 'tcu.unionId = tu.unionId', 'INNER');
        $source == 'union' && $this->db->where('tcu.unionRole', $status);
        $source == 'union' && $this->db->like('companyName', $companyName, 'after');
        //用户在社团里的状态(0:被关闭1:非认证且临时2:非认证且生效3:认证且生效)
        $source == 'union' && $this->db->where('tu.unionStatus', UnionManage::UNION_STATUS_AUTH_VALID);
        #$query = $this->db->get($this->_table . ' tcu');
        #$result = $query->result_array();
        $cnt = $this->db->count_all_results($this->_table . ' tcu');
        return $cnt;
    }
    /**
     * 查询某一公司的详细信息
     */
    public function getOneCompanyUnionInfo($companyId)
    {
        $this->db->select('tcu.companyId,tcu.companyName,tcu.companyNick,tcu.companySimple,tcu.unionRole,tcu.createTime,tcu.updateTime,tu.unionId,tu.unionName,tu.unionStatus');
        $this->db->join('tbl_union tu', 'tcu.unionId = tu.unionId', 'INNER');
        $this->db->where('tcu.companyId', $companyId);
        $query = $this->db->get($this->_table . ' tcu');
        $result = $query->row_array();
        return $result;
    }
    /**
     * 获取某企业的信息
     * @param int $unionName
     */
    public function getCompanyByName($companyName)
    {
        $this->db->select('companyId,companyName,unionId');
        $this->db->where('unionRole != ', CompanyUnion::UNION_ROLE_QUIT);
        $this->db->where('companyName', $companyName);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $result = $query->row_array();
        return $result;
    }
    /**
     * 创建企业-社团关系的逻辑
     * @param $userid
     */
    public function createCompanyUnion($companyName, $unionId, $status = self::UNION_ROLE_UNAUTH, $returnLastId = FALSE)
    {
        $companySimple = 'NULL';
        if (!empty($companyName))
        {
            $this->load->library('BLH_Pinyin');
            $companySimple = substr(BLH_Pinyin::pinYin($companyName, 'UTF-8'), 0, 1);
        }
        $companyNick = '';
        $data = array(
            'companyName' => $companyName, //公司全称
            'companyNick' => $companyNick, //公司简称
            'companySimple' => $companySimple, //公司名首字拼音
            'unionId' => $unionId,
            'unionRole' => $status,
        );
        $ret = $this->db->insert($this->_table, $data); 
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 更新企业-社团关系的逻辑-管理后台
     * @param $unionId
     * @param $unionName
     * @param $unionStatus
     */
    public function updateCompanyUnion($companyId, $companyName='', $companySimple='', $companyNick='', $unionRole = -1)
    {
        if (!empty($companyName)) $data['companyName'] = $companyName;
        if (!empty($companyNick)) $data['companyNick'] = $companyNick;
        if (!empty($companySimple)) $data['companySimple'] = $companySimple;
        if ($unionRole != -1) $data['unionRole'] = $unionRole;
        $data['updateTime'] = date('Y-m-d H:i:s', SYS_TIME);
        $this->db->where('companyId', $companyId);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    /**
     * 更新企业-社团关系表的状态字段
     * @param int $unionId
     * @param int $unionRole
     */
    public function updateCompanyUnionStatus($unionId, $unionRole)
    {
        $data = array();
        $data['unionRole'] = $unionRole;
        $data['updateTime'] = date('Y-m-d H:i:s', SYS_TIME);
        $this->db->where('unionId', $unionId);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

}