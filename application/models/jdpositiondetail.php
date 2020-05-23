<?php
/**
 * JD生成-职位详细列表模块
 */
class Jdpositiondetail extends CI_Model{
    public $_table = 'tbl_jd_position_detail';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取职位详细列表
     * @param int $pdid 职位ID
     */
    public function fetchJdPositionDetail($pdid = 0, $isAdmin = FALSE)
    {
        $this->db->select('tjpd.pdid,tjpd.pid,tjpd.positionName,tjpd.sortId,tjpd.state');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $pdid == 0 && $this->db->where('tjpd.pdid > ', $pdid);
        $pdid == 0 && $this->db->order_by('tjpd.sortId DESC');
        $pdid > 0 && $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $pdid > 0 ? $query->row_array() : $query->result_array();
        return !empty($res) ? $res : FALSE;
    }

    /**
     * 获取职位列表-分页
     * @param int $pdid 职位ID
     */
    public function fetchAllJdPosition($page = 1, $pagesize = 50, $pdid = 0, $isAdmin = FALSE)
    {
        $this->db->select('tjpd.pdid,tjpd.pid,tjpd.positionName,tjpd.sortId,tjpd.state');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $this->db->order_by('tjpd.sortId DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $query->result_array();
        return !empty($res) ? $res : FALSE;
    }

    public function allJdPositionTotal($pdid = 0, $isAdmin = FALSE)
    {
        $this->db->select('tjpd.pdid,tjpd.pid,tjpd.positionName,tjpd.sortId,tjpd.state');
        $pdid > 0 && $this->db->where('tjpd.pdid', $pdid);
        !$isAdmin && $this->db->where('tjpd.state', 'new');
        $query = $this->db->get($this->_table . ' tjpd');
        $total = count($query->result_array());
        return $total;
    }

    /**
     * 获取匹配到的某职位的列表
     * @param string $pname 职位名称
     */
    public function fetchJdPositionDetailByName($pname = '', $isLike = TRUE)
    {
        $this->db->select('tjpd.pdid,tjpd.positionName');//tjpd.pdid,tjpd.pid,tjpd.positionName,tjpd.sortId,tjpd.state
        $isLike && $this->db->like('tjpd.positionName', $pname, 'after');
        !$isLike && $this->db->where('tjpd.positionName', $pname);
        $this->db->where('tjpd.state', 'new');
        $isLike && $this->db->order_by('tjpd.sortId DESC');
        $isLike && $this->db->limit(20);
        !$isLike && $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjpd');
        $res = $isLike ? $query->result_array() : $query->row_array();
        return !empty($res) ? $res : array();
    }

    /**
     * 创建职位
     * @param $userid
     */
    public function createPositionDetail($pid = 0, $positionName, $sortId = 0, $state = 'new', $returnLastId = FALSE)
    {
        $data = array(
            'pid' => $pid,
            'positionName' => $positionName,
            'sortId' => $sortId,
            'state' => $state,
        );
        $ret = $this->db->insert($this->_table, $data, "positionName='{$positionName}',updatetime=NOW();");
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    
    /**
     * 更新职位
     * @param $unionId
     * @param $unionName
     * @param $unionStatus
     */
    public function updatePositionDetail($pdid, $positionName, $sortId = 0, $state = '')
    {
        if (!empty($positionName)) $data['positionName'] = $positionName;
        if (!empty($sortId)) $data['sortId'] = $sortId;
        if (!empty($state)) $data['state'] = $state;
        $this->db->where('pdid', $pdid);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    
    /**
     * 删除职位
     * @param $userid
     */
    public function dropPositionDetail($pdid)
    {
        $data = array('state'=>'delete');
        $this->db->where('pdid', $pdid);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
}