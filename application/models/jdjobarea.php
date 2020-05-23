<?php
/**
 * JD-地区列表
 */
class Jdjobarea extends CI_Model{
    public $_table = 'tbl_jd_job_area';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobAreaList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='', $field_key = 'areaId')
    {
        $this->db->select('tjja.id,tjja.areaId,tjja.areaName,tjja.state');
        !$isAdmin && $this->db->where('tjja.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjja.state', $state);
        $this->db->order_by('tjja.sortId DESC, tjja.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjja');
        $res = $query->result_array($field_key);
        return !empty($res) ? $res : array();
    }

    public function allJdJobAreaListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjja.id,tjja.areaId,tjja.areaName,tjja.state');
        !$isAdmin && $this->db->where('tjja.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjja.state', $state);
        $query = $this->db->get($this->_table . ' tjja');
        $total = count($query->result_array());
        return $total;
    }

}