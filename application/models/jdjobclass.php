<?php
/**
 * JD-职位分类列表
 */
class Jdjobclass extends CI_Model{
    public $_table = 'tbl_jd_job_class';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobClassList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjc.id,tjjc.jobClassId,tjjc.jobClassName,tjjc.state');
        !$isAdmin && $this->db->where('tjjc.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjc.state', $state);
        $this->db->order_by('tjjc.sortId DESC, tjjc.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjc');
        $res = $query->result_array('jobClassId');
        return !empty($res) ? $res : array();
    }

    /**
     * 获取jobs_base里jdPushStatus=1的职位分类列表
     */
    public function fetchAllJdJobClassListNew($page = 1, $pagesize = 500)
    {
        $this->db->select('tjjc.id,tjjc.jobClassId,tjjc.jobClassName');
        $this->db->join('tbl_jd_job_base tjjb', 'tjjc.jobClassId = tjjb.jobClassId', 'LEFT');
        $this->db->where('tjjc.state', 'new');
        $this->db->where('tjjb.jdPushStatus', 1);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $this->db->group_by('tjjc.jobClassId');
        $this->db->order_by('tjjc.sortId DESC, tjjc.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjc');
        $res = $query->result_array();
        return !empty($res) ? $res : array();
    }

    public function allJdJobClassListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjc.id,tjjc.jobClassId,tjjc.jobClassName,tjjc.state');
        !$isAdmin && $this->db->where('tjjc.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjc.state', $state);
        $query = $this->db->get($this->_table . ' tjjc');
        $total = count($query->result_array());
        return $total;
    }

}