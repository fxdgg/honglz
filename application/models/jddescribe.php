<?php
/**
 * JD生成-关键词对应的JD描述表模块
 */
class Jddescribe extends CI_Model{
    public $_table = 'tbl_jd_describe';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据关键词ID，获取描述列表
     * @param int $kid 关键词ID
     */
    public function fetchJdDescribeByKid($kid = 0, $limit = 3)
    {
       $this->db->select('tjd.id,tjd.kid,tjd.sortId,tjd.content');
       $this->db->where('tjd.kid', $kid);
       $this->db->where('tjd.state', 'new');
       $this->db->order_by('tjd.sortId DESC');
       $limit > 0 && $this->db->limit($limit);
       $query = $this->db->get($this->_table . ' tjd');
       $res = $query->result_array();
       return !empty($res) ? $res : FALSE;
    }

    /**
     * 根据描述ID，获取描述信息
     * @param int $id 描述ID
     */
    public function fetchJdDescribeById($id = 0, $kid = 0, $pdid = 0, $isAdmin = FALSE)
    {
        $this->db->select('tjd.id,tjd.kid,tjd.sortId,tjd.content,tjd.state,tjpd.level,tjpd.pdid,tjpd.type,tjpd.keyword,tjpde.positionName');
        $this->db->join('tbl_jd_position_duty tjpd', 'tjpd.kid = tjd.kid', 'LEFT');
        $this->db->join('tbl_jd_position_detail tjpde', 'tjpde.pdid = tjpd.pdid', 'LEFT');
        $this->db->where('tjd.id', $id);
        $kid > 0 && $this->db->where('tjd.kid', $kid);
        !$isAdmin && $this->db->where('tjd.state', 'new');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjd');
        $res = $query->row_array();
        return !empty($res) ? $res : FALSE;
    }

    /**
     * 根据关键词ID，获取描述列表2
     * @param int $kid 关键词ID
     */
    public function fetchJdDescribeByKidKeys($kids = array(), $limit = 0, $isAdmin = FALSE)
    {
       $this->db->select('tjd.id,tjd.kid,tjd.sortId,tjd.content,tjd.state,tjpd.keyword');
       $this->db->join('tbl_jd_position_duty tjpd', 'tjpd.kid = tjd.kid', 'LEFT');
       $this->db->where_in('tjd.kid', $kids);
       !$isAdmin && $this->db->where('tjd.state', 'new');
       $this->db->order_by('tjpd.sortId DESC,tjd.sortId DESC,tjd.id DESC');
       $limit > 0 && $this->db->limit($limit);
       $query = $this->db->get($this->_table . ' tjd');
       $res = $query->result_array('kid', '', 'LIST');
       //echo 'last_query=>'.$this->db->last_query().'<br />';
       return !empty($res) ? $res : FALSE;
    }

    /**
     * 获取职位描述列表-分页
     * @param int $pdid 职位ID
     */
    public function fetchAllJdDescribe($page = 1, $pagesize = 50, $kid = 0, $level = 0, $pdid = 0, $isAdmin = FALSE)
    {
        $this->db->select('tjd.id,tjd.kid,tjd.sortId,tjd.content,tjd.state,tjpd.level,tjpd.pdid,tjpd.type,tjpd.keyword,tjpde.positionName');
        $this->db->join('tbl_jd_position_duty tjpd', 'tjpd.kid = tjd.kid', 'LEFT');
        $this->db->join('tbl_jd_position_detail tjpde', 'tjpde.pdid = tjpd.pdid', 'LEFT');
        $kid > 0 && $this->db->where('tjd.kid', $kid);
        !$isAdmin && $this->db->where('tjd.state', 'new');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        $level > 0 && $this->db->where('tjpd.level', $level);
        $this->db->order_by('tjd.id DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjd');
        $res = $query->result_array();
        return !empty($res) ? $res : FALSE;
    }

    public function allJdDescribeTotal($kid = 0, $level = 0, $pdid = 0, $isAdmin = FALSE)
    {
        $this->db->select('tjd.id,tjd.kid,tjd.sortId,tjd.content,tjd.state,tjpd.level,tjpd.pdid,tjpd.type,tjpd.keyword,tjpde.positionName');
        $this->db->join('tbl_jd_position_duty tjpd', 'tjpd.kid = tjd.kid', 'LEFT');
        $this->db->join('tbl_jd_position_detail tjpde', 'tjpde.pdid = tjpd.pdid', 'LEFT');
        $kid > 0 && $this->db->where('tjd.kid', $kid);
        !$isAdmin && $this->db->where('tjd.state', 'new');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        $level > 0 && $this->db->where('tjpd.level', $level);
        $query = $this->db->get($this->_table . ' tjd');
        $total = count($query->result_array());
        return $total;
    }

    /**
     * 创建关键字描述
     * @param $kid
     * @param $content
     */
    public function createJdDescribe($kid = 0, $content, $sortId = 0, $state = 'new', $returnLastId = FALSE)
    {
        $data = array(
            'kid' => $kid,
            'content' => $content,
            'sortId' => $sortId,
            'state' => $state,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    
    /**
     * 更新关键字描述
     * @param $id
     * @param $content
     */
    public function updateJdDescribe($id, $kid = 0, $content='', $sortId = 0, $state = '')
    {
        if (!empty($content)) $data['content'] = $content;
        if (!empty($kid)) $data['kid'] = $kid;
        if (!empty($sortId)) $data['sortId'] = $sortId;
        if (!empty($state)) $data['state'] = $state;
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    
    /**
     * 删除关键字描述
     * @param $id
     */
    public function dropPositionDuty($id)
    {
        $data = array('state'=>'delete');
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
}