<?php
/**
 * JD生成-关键词对应的JD描述表-用户编辑描述模块
 */
class Jddescribelog extends CI_Model{
    public $_table = 'tbl_jd_describe_log';

    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 记录用户编辑的描述内容
     * @param $kid
     * @param $content
     */
    public function createJdDescribeLog($uid = 0, $pdid = 0, $level = 0, $kid = 0, $keyword = '', $deid = 0, $content, $returnLastId = FALSE)
    {
        $data = array(
            'uid' => $uid,
            'pdid' => $pdid,
            'level' => $level,
            'kid' => $kid,
            'keyword' => $keyword,
            'deid' => $deid,
            'content' => $content,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }

    /**
     * 根据关键词、描述，获取数据条数
     * @param string $searchText 搜索内容
     */
    public function fetchKeywordDescribeCnt($searchText = '', $field = 'keyword')
    {
        $this->db->select('COUNT(1) AS num');
        $this->db->where($field, $searchText);
        //$this->db->like($field, $searchText, 'after');
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $row = $query ? $query->row_array() : array();
        return $row ? $row['num'] : 0;
    }
}