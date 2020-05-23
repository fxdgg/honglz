<?php
class UserUnion extends CI_Model{
    protected $_table = 'tbl_user_union';    
    /**
     * 被踢出或退出的成员
     * @var int
     */
    const USER_UNION_ROLE_DELETE = 0;
    /**
     * 普通成员
     * @var int
     */
    const USER_UNION_ROLE_MEMBER = 1;
    /**
     * 管理员
     * @var int
     */
    const USER_UNION_ROLE_MASTER_ADMIN = 2;
    /**
     * 二级管理员
     * @var int
     */
    const USER_UNION_ROLE_SECONDARY_ADMIN = 3;
    /**
     * 用户社团角色配置
     * @var array
     */
    public static $userUnionRoleConfig = array(
        self::USER_UNION_ROLE_DELETE => '禁用',//被踢出或退出
        self::USER_UNION_ROLE_MEMBER => '正常',//成员
        self::USER_UNION_ROLE_MASTER_ADMIN => '管理员',
        self::USER_UNION_ROLE_SECONDARY_ADMIN => '二级管理员',
    );
    
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 创建社团用户
     */
    public function createUnionUser($userId, $inviteInfo = array(), $returnLastId = FALSE, $unionRole = self::USER_UNION_ROLE_MASTER_ADMIN, $unionCode='')
    {
        list($invitedBy, $inviteIntro, $unionId) = $inviteInfo;
        $data = array(
            'userid' => $userId,
            'unionId' => $unionId,
            'unionRole' => $unionRole,
            'invitedBy' => $invitedBy,
        );
        //社团邀请码
        !empty($unionCode) && $data['unionCode'] = $unionCode;
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 更新社团用户状态等信息
     */
    public function updateUnionUserRole($userId=0, $unionId, $unionRole = self::USER_UNION_ROLE_MASTER_ADMIN)
    {
        $data = array(
            'unionRole' => $unionRole,
            'updateTime' => date('Y-m-d H:i:s', SYS_TIME),
        );
        $userId > 0 && $this->db->where('userId', $userId);
        $this->db->where('unionId', $unionId);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    /**
     * 根据社团ID搜索用户列表
     */
    public function searchUserListByUnionId($unionId, $unionRole = self::USER_UNION_ROLE_MEMBER)
    {
        $this->db->select('tuu.userid,tuu.unionId,tuu.unionRole,tuu.invitedBy,ui.email,ui.nickname');
        $this->db->join('userinfo ui', 'ui.id=tuu.userid', 'LEFT');
        $this->db->where('tuu.unionId', $unionId);
        $this->db->where('tuu.unionRole != ', self::USER_UNION_ROLE_DELETE);
        //$this->db->where_not_in('unionRole', self::USER_UNION_ROLE_DELETE);
        $query = $this->db->get($this->_table . ' tuu');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 根据社团ID搜索详细用户列表
     */
    public function searchJoinUserListByUnionId($unionId, $unionRole=-1)
    {
        $this->db->select('tuu.userid,tuu.unionId,tuu.unionRole,tuu.invitedBy,ui.email,ui.sex,ui.nickname,ui.iconUrl,ui.company,ui.position');
        $this->db->join('userinfo ui', 'ui.id = tuu.userid', 'INNER');
        $this->db->where('tuu.unionId', $unionId);
        if ($unionRole >= 0) $this->db->where('tuu.unionRole', $unionRole);
        $this->db->where('tuu.unionRole != ', self::USER_UNION_ROLE_DELETE);
        //$this->db->where_not_in('unionRole', self::USER_UNION_ROLE_DELETE);
        $query = $this->db->get($this->_table . ' tuu');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 根据用户ID搜索社团列表
     */
    public function searchUnionListByUserId($userId, $unionId=0, $unionRole=array())
    {
        $this->load->model('UnionManage');
        $this->db->select('tuu.userid,tuu.unionId,tu.unionName,tuu.unionRole,tu.unionStatus,tuu.createTime');
        $this->db->join('tbl_union tu', 'tu.unionId = tuu.unionId', 'INNER');
        $this->db->where('tuu.userid', $userId);
        if ($unionId > 0)
        {
            $this->db->where('tuu.unionId', $unionId);
        }
        if (is_array($unionRole) && !empty($unionRole))
        {
            //用户在社团里的角色(0:被踢出或退出的成员1:普通成员2:管理员3:二级管理员)
            $this->db->where_in('tuu.unionRole', $unionRole);
        }else{
            //用户在社团里的角色(0:被踢出或退出的成员1:普通成员2:管理员3:二级管理员)
            $this->db->where('tuu.unionRole != ', self::USER_UNION_ROLE_DELETE);
        }
        //用户在社团里的状态(0:被关闭1:非认证且临时2:非认证且生效3:认证且生效)
        $this->db->where('tu.unionStatus != ', UnionManage::UNION_STATUS_CLOSE);
        $query = $this->db->get($this->_table . ' tuu');
        $result = (is_array($unionRole) && !empty($unionRole)) ? $query->row_array() : $query->result_array();
        return $result;
    }
    /**
     * 根据用户是否已加入某社团
     */
    public function checkInUnionByUserId($userId, $unionId=0, $invitedBy=0)
    {
        $this->db->select('tuu.userid,tuu.unionId,tuu.unionRole,tuu.invitedBy,tuu.createTime');
        $this->db->where('tuu.userid', $userId);
        if ($unionId > 0)
        {
            $this->db->where('tuu.unionId', $unionId);
        }
        //邀请进入社团的用户ID
        $invitedBy > 0 && $this->db->where('tuu.invitedBy', $invitedBy);
        $query = $this->db->get($this->_table . ' tuu');
        $result = $query->row_array();
        return $result;
    }
    /**
     * 获取我加入的社团Id列表
     * @param $userId
     * @param $unionId
     */
    public function getMyJoinUnionIdList($userId, $isDetail = FALSE)
    {
        $data = array();
        $userUnionList = $this->searchUnionListByUserId($userId);
        if ($isDetail) return $userUnionList;
        if (!empty($userUnionList))
        {
            foreach ($userUnionList as $userUnionItem)
            {
                if (!isset($userUnionItem['unionId']) OR $userUnionItem['unionId'] <= 0) continue;
                $data[] = $userUnionItem['unionId'];
            }
        }
        return $data;
    }
    /**
     * 获取当天新建的社团ID列表
     * @param $userId
     */
    public function getTodayCreateUnionIds($userId)
    {
        $data = array();
        $userUnionList = $this->searchUnionListByUserId($userId);
        if (!empty($userUnionList))
        {
            $currentDate = date('Y-m-d');
            foreach ($userUnionList as $userUnionItem)
            {
                if (!isset($userUnionItem['unionId']) OR $userUnionItem['unionId'] <= 0) continue;
                if (date('Y-m-d', strtotime($userUnionItem['createTime'])) == $currentDate)
                {
                    $data[] = $userUnionItem['unionId'];
                }
            }
        }
        return $data;
    }

}