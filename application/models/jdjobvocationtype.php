<?php
/**
 * JD-行业类别列表
 */
class Jdjobvocationtype extends CI_Model{
    public $_table = 'tbl_jd_job_vocation_type';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobVocationTypeList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjvt.id,tjjvt.vocationTypeId,tjjvt.vocationTypeName,tjjvt.state');
        !$isAdmin && $this->db->where('tjjvt.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjvt.state', $state);
        $this->db->order_by('tjjvt.sortId DESC, tjjvt.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjvt');
        $res = $query->result_array('vocationTypeId');
        return !empty($res) ? $res : array();
    }

    public function allJdJobVocationTypeListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjvt.id,tjjvt.vocationTypeId,tjjvt.vocationTypeName,tjjvt.state');
        !$isAdmin && $this->db->where('tjjvt.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjvt.state', $state);
        $query = $this->db->get($this->_table . ' tjjvt');
        $total = count($query->result_array());
        return $total;
    }

}