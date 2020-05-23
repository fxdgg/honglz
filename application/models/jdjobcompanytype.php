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
    public function fetchAllJdJobCompanyTypeList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjct.id,tjjct.companyTypeId,tjjct.companyTypeName,tjjct.state');
        !$isAdmin && $this->db->where('tjjct.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjct.state', $state);
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

}