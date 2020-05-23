<?php
class UserJobs extends CI_Model{
    protected $_table = 'tbl_user_jobs';
    
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 创建用户的过往履历
     */
    public function createUserJobs($data, $returnLastId = FALSE)
    {
        $data = array(
            'userid' => $data['userid'],
            'companyName' => $data['companyName'],
            'positionName' => $data['positionName'],
            'joinTime' => $data['joinTime'],
            'leaveTime' => $data['leaveTime'],
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 更新用户的过往履历
     */
    public function updateUserJobs($data)
    {
        if (!isset($data['id']) OR empty($data['id']))
        {
            return FALSE;
        }
        $update_data = array(
            'companyName' => $data['companyName'],
            'positionName' => $data['positionName'],
            'joinTime' => $data['joinTime'],
            'leaveTime' => $data['leaveTime'],
            'updateTime' => date('Y-m-d H:i:s', SYS_TIME),
        );
        $this->db->where('id', $data['id']);
        $this->db->where('userid', $data['userid']);
        return $this->db->update($this->_table, $update_data);
    }
    /**
     * 删除用户的过往履历
     */
    public function deleteUserJobs($userid)
    {
        $this->db->where('userid', $userid);
        return $this->db->delete($this->_table);
    }
    /**
     * 根据用户ID搜索过往履历
     */
    public function searchUserJobsListByUserId($searchId, $searchField = 'userid')
    {
        $this->db->select('id,userid,companyName,positionName,joinTime,leaveTime');
        $this->db->where($searchField, $searchId);
        $query = $this->db->get($this->_table);
        $result = $searchField == 'id' ? $query->row_array() : $query->result_array();
        return $result;
    }

}