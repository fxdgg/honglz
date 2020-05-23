<?php
/**
 * 分析后的简历基本信息表
 */
class Spiderresume extends CI_Model{
    public $_table = 't_resume';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取原始简历列表-分页
     */
    public function fetchAllSpiderResumeList($page = 1, $pagesize = 1000, $start_time = 0)
    {
        $this->db->select();
        $this->db->where('tr.is_import', 0);
        $start_time > 0 && $this->db->where('tr.add_time >= ', $start_time * 1000);
        $this->db->order_by('tr.id ASC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tr');
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        $res = $query->result_array('id');
        return !empty($res) ? $res : array();
    }

    /**
     * 获取原始简历列表的总数
     * @param $type
     */
    public function fetchAllSpiderResumeTotal($start_time = 0)
    {
        $this->db->select('COUNT(1) AS num');
        $this->db->where('tr.is_import', 0);
        $start_time > 0 && $this->db->where('tr.add_time >= ', $start_time * 1000);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tr');
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        $row = $query->row_array();
        return $row['num'];
    }

    /**
     * 更新原始简历的记录
     * @param $id
     * @param $is_import
     */
    public function updateSpiderResumeData($id, $is_import = 1)
    {
        $data['is_import'] = $is_import;
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 更新原始简历的记录-批量
     * @param $id
     * @param $is_import
     */
    public function updateSpiderResumeBatch($ids, $is_import = 1)
    {
        $data['is_import'] = $is_import;
        $this->db->where_in('id', $ids);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

}