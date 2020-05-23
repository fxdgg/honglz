<?php
/**
 * JD-职位程度列表
 */
class Jdjoblevel extends CI_Model{
    public $_table = 'tbl_jd_job_level';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobLevelList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjl.id,tjjl.jobLevelId,tjjl.jobLevelName,tjjl.state');
        !$isAdmin && $this->db->where('tjjl.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjl.state', $state);
        $this->db->order_by('tjjl.sortId DESC, tjjl.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjl');
        $res = $query->result_array('jobLevelId');
        return !empty($res) ? $res : array();
    }

    public function allJdJobLevelListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjl.id,tjjl.jobLevelId,tjjl.jobLevelName,tjjl.state');
        !$isAdmin && $this->db->where('tjjl.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjl.state', $state);
        $query = $this->db->get($this->_table . ' tjjl');
        $total = count($query->result_array());
        return $total;
    }

}