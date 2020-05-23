<?php
/**
 * JD生成-职位列表模块
 */
class Jdposition extends CI_Model{
    public $_table = 'tbl_jd_position';

    function __construct()
    {
        parent::__construct();
    }
    /**
     * 获取职位列表
     * @param int $id 职位ID
     */
    public function fetchJdPosition($id = 0)
    {
        $this->db->select('tjp.id,tjp.name');
        $id > 0 && $this->db->where('tjp.id', $id);
        $this->db->where('tjp.state', 'new');
        $id == 0 && $this->db->where('tjp.id > ', $id);
        $id == 0 && $this->db->order_by('tjp.sortId ASC');
        $id > 0 && $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tjp');
        $res = $id > 0 ? $query->row_array() : $query->result_array();
        return !empty($res) ? $res : FALSE;
    }
    /**
     * 根据关键字获取职位列表
     * @param int $keyword 关键字
     */
    public function fetchLikeKeyword($keyword = '')
    {
        $this->db->select('tjpd.id,tjpd.pid,tjpd.positionName');
        $this->db->join('tbl_jd_position_detail tjpd', 'tjpd.pid = tjp.id', 'LEFT');
        $this->db->like('tjp.name', $keyword, 'both');
        $this->db->where('tjp.state', 'new');
        $this->db->order_by('tjpd.sortId DESC');
        $query = $this->db->get($this->_table);
        $res = $query->result_array();
        return !empty($res) ? $res : FALSE;
    }
    
    /**
     * 创建职位
     * @param $userid
     */
    public function createPosition($name, $returnLastId = FALSE)
    {
        $data = array(
            'name' => $name,
            'sortId' => 0,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 更新职位
     * @param $unionId
     * @param $unionName
     * @param $unionStatus
     */
    public function updatePosition($id, $name='')
    {
        if (!empty($name)) $data['name'] = $name;
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    /**
     * 删除职位
     * @param $userid
     */
    public function dropPosition($id)
    {
    	$data = array('state'=>'delete');
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
}