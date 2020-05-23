<?php
/**
 * JD生成-职位-岗位描述关键词对应表模块
 */
class Jdpositionduty extends CI_Model{
    public $_table = 'tbl_jd_position_duty';

    function __construct()
    {
        parent::__construct();
    }
    /**
     * 获取职位-岗位描述关键词对应表
     * @param int $id 职位ID
     */
    public function fetchJdPositionDutyList($pdid = 0, $level = -1, $type = '', $limit = 0, $isAdmin = FALSE, $keywords = array())
    {
        $this->db->select('tjpd.kid,tjpd.pdid,tjpd.level,tjpd.type,tjpd.keyword,tjpd.state,tjpd.sortId');
        $this->db->join('tbl_jd_describe tjd', 'tjd.kid = tjpd.kid', 'RIGHT');
        $this->db->where('tjpd.pdid', $pdid);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $level > 0 && $this->db->where('tjpd.level', $level);
        !empty($type) && $this->db->where('tjpd.type', $type);
        !empty($keywords) && $this->db->where_in('tjpd.keyword', $keywords);
        $this->db->order_by('tjpd.sortId DESC, tjpd.kid ASC');
        $limit > 0 && $this->db->limit($limit);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $query->result_array('kid');
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 获取关键词详细记录
     * @param int $kid 关键词ID
     */
    public function fetchJdDutyDetail($kid = 0, $type = '', $isAdmin = FALSE)
    {
        $this->db->select('tjpd.kid,tjpd.pdid,tjpd.level,tjpd.type,tjpd.keyword,tjpd.sortId,tjpd.state,tjpde.positionName');
        $this->db->join('tbl_jd_position_detail tjpde', 'tjpde.pdid = tjpd.pdid', 'LEFT');
        $this->db->where('tjpd.kid', $kid);
        !empty($type) && $this->db->where('tjpd.type', $type);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $query->row_array();
        return !empty($res) ? $res : FALSE;
    }

    /**
     * 获取关键词详细记录
     * @param int $kid 关键词ID
     */
    public function fetchJdDutyDetailByKid($kid = 0, $type = '')
    {
        $this->db->select('tjpd.kid,tjpd.pdid,tjpd.level,tjpd.type,tjpd.keyword,tjpd.sortId,tjpd.state');
        $this->db->where('tjpd.kid', $kid);
        !empty($type) && $this->db->where('tjpd.type', $type);
        $this->db->where('tjpd.state', 'new');
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $query->row_array();
        return !empty($res) ? $res : FALSE;
    }

    /**
     * 获取关键词详细记录-不区分状态
     * @param int $pdid 职位ID
     */
    public function fetchJdPositionDetailByPltk($pdid = 0, $level = 0, $type = '', $keyword = '')
    {
        $this->db->select('tjpd.kid,tjpd.pdid,tjpd.level,tjpd.type,tjpd.keyword,tjpd.sortId,tjpd.state');
        $this->db->where('tjpd.pdid', $pdid);
        $this->db->where('tjpd.level', $level);
        $this->db->where('tjpd.type', $type);
        $this->db->where('tjpd.keyword', $keyword);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjpd');
        $row = $query ? $query->row_array() : array();
        return $row;
    }

    /**
     * 获取职位关键词列表-分页
     * @param int $pdid 职位ID
     */
    public function fetchAllJdDuty($page = 1, $pagesize = 50, $pdid = 0, $level = 0, $type = '', $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjpd.kid,tjpd.pdid,tjpd.level,tjpd.type,tjpd.keyword,tjpd.sortId,tjpd.state,tjpde.positionName');
        $this->db->join('tbl_jd_position_detail tjpde', 'tjpde.pdid = tjpd.pdid', 'LEFT');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjpd.state', $state);
        $level > 0 && $this->db->where('tjpd.level', $level);
        !empty($type) && $this->db->where('tjpd.type', $type);
        $this->db->order_by('tjpd.kid DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $query->result_array();
        return !empty($res) ? $res : FALSE;
    }

    public function allJdDutyTotal($pdid = 0, $level = 0, $type='', $isAdmin = FALSE, $state='')
    {
        $this->db->select('tjpd.kid,tjpd.pdid,tjpd.level,tjpd.type,tjpd.keyword,tjpd.sortId,tjpd.state');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjpd.state', $state);
        $level > 0 && $this->db->where('tjpd.level', $level);
        !empty($type) && $this->db->where('tjpd.type', $type);
        $query = $this->db->get($this->_table . ' tjpd');
        $total = count($query->result_array());
        return $total;
    }

    /**
     * 根据关键词，获取数据条数
     * @param string $searchText 搜索内容
     */
    public function fetchKeywordCnt($searchText = '', $pdid = 0)
    {
        $this->db->select('COUNT(1) AS num');
        $pdid > 0 && $this->db->where('pdid', $pdid);
        $this->db->where('keyword', $searchText);
        //$this->db->like('keyword', $searchText, 'after');
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $row = $query ? $query->row_array() : array();
        return $row ? $row['num'] : 0;
    }

    /**
     * 创建关键字
     * @param $userid
     */
    public function createPositionDuty($pdid = 0, $level = 1, $type = '', $keyword, $sortId = 0, $state = '', $returnLastId = FALSE)
    {
        $data = array(
            'pdid' => $pdid,
            'level' => $level,
            'type' => $type,
            'keyword' => $keyword,
            'sortId' => $sortId,
            'state' => $state,
        );
        $ret = $this->db->insert($this->_table, $data, "`keyword`='{$keyword}'");
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    
    /**
     * 更新关键字
     * @param $unionId
     * @param $unionName
     * @param $unionStatus
     */
    public function updatePositionDuty($kid, $keyword='', $level = 1, $sortId = 0, $state = '')
    {
        if (!empty($keyword)) $data['keyword'] = $keyword;
        if (!empty($level)) $data['level'] = $level;
        if (!empty($sortId)) $data['sortId'] = $sortId;
        if (!empty($state)) $data['state'] = $state;
        $this->db->where('kid', $kid);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    
    /**
     * 删除关键字
     * @param $kid
     */
    public function dropPositionDuty($kid)
    {
        $data = array('state'=>'delete');
        $this->db->where('kid', $kid);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 删除关键字-物理删除
     * @param $pdid
     */
    public function deletePositionDuty($pdid = 0, $level = 1, $type = '', $keyword)
    {
        $this->db->where('pdid', $pdid);
        $this->db->where('level', $level);
        $this->db->where('type', $type);
        $this->db->where('keyword', $keyword);
        return $this->db->delete($this->_table);
    }
}