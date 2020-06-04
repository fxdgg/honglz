<?php
/**
 * JD-公司类型列表
 */
class Jdjobcompanytype extends CI_Model{
    public $_table = 'tbl_jd_job_company_type';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobCompanyTypeList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='', $type=0)
    {
        $this->db->select('tjjct.id,tjjct.type,tjjct.companyTypeId,tjjct.companyTypeName,tjjct.state');
        !$isAdmin && $this->db->where('tjjct.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjct.state', $state);
        $isAdmin && strlen($type) > 0 && $this->db->where('tjjct.type', (int)$type);
        $this->db->order_by('tjjct.sortId DESC, tjjct.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjct');
        $res = $query->result_array('companyTypeId');
        return !empty($res) ? $res : array();
    }

    public function allJdJobCompanyTypeListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjct.id,tjjct.companyTypeId,tjjct.companyTypeName,tjjct.state');
        !$isAdmin && $this->db->where('tjjct.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjct.state', $state);
        $query = $this->db->get($this->_table . ' tjjct');
        $total = count($query->result_array());
        return $total;
    }

    /**
     * 获取公司类型信息
     * @param $id
     * @return array
     */
    public function fetchOne($id) {
        if (empty($id)) {
            return [];
        }
        $this->db->select('companyTypeId,companyTypeName');
        $this->db->where('type', 1);
        $this->db->where('state', 'new');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        $res = $query->row_array();
        return !empty($res) ? $res : array();
    }

    /**
     * 获取公司类型列表
     * @return array
     */
    public function fetchList() {
        $this->db->select('companyTypeId,companyTypeName');
        $this->db->where('type', 1);
        $this->db->where('state', 'new');
        $this->db->order_by('sortId DESC');
        $query = $this->db->get($this->_table);
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        $res = $query->result_array('companyTypeId');
        return !empty($res) ? $res : array();
    }
}