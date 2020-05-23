<?php
/**
 * jd数据库中的关键词、描述配置表
 */
class Keyword extends CI_Model{
    public $_table = 't_keyword';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取某类型关键词、描述的列表-分页
     * @param int $type 类型（0:任职要求1:岗位职责）
     */
    public function fetchAllKeywordList($type = 0, $page = 1, $pagesize = 1000)
    {
        $this->db->select('k.id,k.type,k.industry_id,k.position_id,k.keyword,k.content,k.status');
        $this->db->where('k.type', $type);
        $this->db->where('k.status', 0);
        $this->db->order_by('k.id ASC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' k');
        $res = $query->result_array('keyword');
        return !empty($res) ? $res : FALSE;
    }

    /**
     * 获取某类型关键词、描述的总数
     * @param $type
     */
    public function fetchAllKeywordTotal($type = 0)
    {
        $this->db->select('COUNT(1) AS num');
        $this->db->where('k.type', $type);
        $this->db->where('k.status', 0);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' k');
        $row = $query->row_array();
        return $row['num'];
    }

    /**
     * 更新某条关键词、描述记录
     * @param $id
     * @param $status
     */
    public function updateKeywordData($id, $status = 1)
    {
        $data['status'] = $status;
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 更新某条关键词、描述记录-批量
     * @param $id
     * @param $status
     */
    public function updateKeywordDataBatch($ids, $status = 1)
    {
        $data['status'] = $status;
        $this->db->where_in('id', $ids);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

}