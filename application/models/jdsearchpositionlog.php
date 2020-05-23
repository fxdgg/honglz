<?php
/**
 * JD生成-记录用户搜索的暂不存在的职位关键词模块
 */
class jdsearchpositionlog extends CI_Model{
    public $_table = 'tbl_jd_search_position_log';

    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 记录用户搜索的暂不存在的职位关键词
     * @param $uid
     * @param $content
     */
    public function createJdSearchPositionLog($uid = 0, $positionName, $returnLastId = FALSE)
    {
        $data = array(
            'uid' => $uid,
            'positionName' => $positionName,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
}