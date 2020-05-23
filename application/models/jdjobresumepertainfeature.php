<?php
/**
 * 简历-附属特征列表
 */
class Jdjobresumepertainfeature extends CI_Model{
    public $_table = 'tbl_jd_resume_pertain_feature';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobResumePertainFeatureList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjrpf.id,tjrpf.pertainFeatureId,tjrpf.pertainFeatureName,tjrpf.state');
        !$isAdmin && $this->db->where('tjrpf.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjrpf.state', $state);
        $this->db->order_by('tjrpf.sortId DESC, tjrpf.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjrpf');
        $res = $query->result_array('pertainFeatureId');
        return !empty($res) ? $res : array();
    }

    public function allJdJobResumePertainFeatureListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjrpf.id,tjrpf.pertainFeatureId,tjrpf.pertainFeatureName,tjrpf.state');
        !$isAdmin && $this->db->where('tjrpf.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjrpf.state', $state);
        $query = $this->db->get($this->_table . ' tjrpf');
        $total = count($query->result_array());
        return $total;
    }

}