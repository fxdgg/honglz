<?php
/**
 * 分析后的简历基本信息表
 */
class Jdjobanalyses extends CI_Model{
    public $_table = 'tbl_word';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取简历列表-分页
     */
    public function fetchAllAnalysesResumeList($page = 1, $pagesize = 1000)
    {
        $this->db->select('k.id,k.name,k.email,k.phone,k.professional,k.school,k.education,k.company,k.industry,k.other,k.status');
        $this->db->where('k.status', 0);
        $this->db->order_by('k.id ASC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' k');
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        $res = $query->result_array('id');
        return !empty($res) ? $res : FALSE;
    }

    /**
     * 获取简历列表的总数
     * @param $type
     */
    public function fetchAllAnalysesResumeTotal()
    {
        $this->db->select('COUNT(1) AS num');
        $this->db->where('k.status', 0);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' k');
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        $row = $query->row_array();
        return $row['num'];
    }

    /**
     * 更新简历的记录
     * @param $id
     * @param $status
     */
    public function updateAnalysesResumeData($id, $status = 1)
    {
        $data['status'] = $status;
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 更新简历的记录-批量
     * @param $id
     * @param $status
     */
    public function updateAnalysesResumeBatch($ids, $status = 1)
    {
        $data['status'] = $status;
        $this->db->where_in('id', $ids);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

}