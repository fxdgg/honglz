<?php
/**
 * 提供资源的对接表
 */
class Jdsupply extends CI_Model{
    public $_table = 'tbl_jb_supply';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取某条对接信息
     * @param int $id
     */
    public function fetchInfoById($id, $isAdmin = false)
    {
        $this->db->select();
        $this->db->where('id', $id);
        !$isAdmin && $this->db->where('state', 'new');
        $query = $this->db->get($this->_table);
        $res = $query->row_array();
        return !empty($res) ? $res : array();
    }

    /**
     * 录入基本信息
     */
    public function createBaseInfo(&$params, $returnLastId = FALSE)
    {
        $data = array(
            'need_id'    => isset($params['need_id']) ? intval($params['need_id']) : 0,
            'asker_uid'  => isset($params['asker_uid']) ? intval($params['asker_uid']) : 0,
            'answer_uid' => isset($params['answer_uid']) ? intval($params['answer_uid']) : 0,
            'resource'   => isset($params['resource']) ? htmlspecialchars($params['resource']) : '',
            'contact'    => isset($params['contact']) ? htmlspecialchars($params['contact']) : '',
            'ctime'      => date('Y-m-d H:i:s')
        );
        $ret = $this->db->insert($this->_table, $data);
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return $returnLastId ? $this->db->insert_id() : $ret;
    }

    /**
     * 更新基本信息
     */
    public function updateBaseInfo(&$params)
    {
        if (isset($params['resource'])) $data['resource'] = htmlspecialchars($params['resource']);
        if (isset($params['contact'])) $data['contact'] = htmlspecialchars($params['contact']);
        if (isset($params['is_alert'])) $data['is_alert'] = (int)$params['is_alert'];
        if (isset($params['money'])) $data['money'] = (int)$params['money'];
        if (isset($params['state'])) $data['state'] = $params['state'];
        $data['utime'] = !empty($params['utime']) ? $params['utime'] : date('Y-m-d H:i:s');
        $this->db->where('id', $params['id']);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 删除某条记录
     * @param $id
     */
    public function deleteBaseInfo($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->_table);
    }

    /**
     * 根据不同的uid，获取总金额
     * @param   int  $uid
     * @param string $field
     * @return array
     */
    public function fetchMySumMoney($uid, $field = 'asker_uid')
    {
        $this->db->select_sum('money');
        $this->db->where($field, (int)$uid);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $res = $query->row_array();
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 获取JD-对接列表-分页
     */
    public function fetchAllJdSupplyList($page = 1, $pagesize = 50, $isAdmin = FALSE, $state = '', $isForceNew = FALSE, $supplyId = 0, $needId = 0)
    {
        $this->db->select('tjjs.*');
        (!$isAdmin OR $isForceNew) && $this->db->where('tjjs.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjs.state', $state);
        $supplyId > 0 && $this->db->where('tjjs.id', $supplyId);
        $needId > 0 && $this->db->where('tjjs.need_id', $needId);
        $this->db->order_by('tjjs.utime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjs');
        $res = $query->result_array();
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    public function allJdSupplyListTotal($isAdmin = FALSE, $state = '', $isForceNew = FALSE, $supplyId = 0, $needId = 0)
    {
        $this->db->select('tjjs.*');
        (!$isAdmin OR $isForceNew) && $this->db->where('tjjs.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjs.state', $state);
        $supplyId > 0 && $this->db->where('tjjs.id', $supplyId);
        $needId > 0 && $this->db->where('tjjs.need_id', $needId);
        $query = $this->db->get($this->_table . ' tjjs');
        $total = count($query->result_array());
        //echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        return $total;
    }

}