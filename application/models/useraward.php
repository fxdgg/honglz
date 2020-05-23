<?php
/**
 * 用户-参与领奖活动的表
 *
 */
class UserAward extends CI_Model
{
    public $_table = 'tbl_user_award';
    /**
     * [群福利]的默认板块-舶来
     * @var int
     */
    const GROUP_AWARD_DEFAULT_BLOCK = 'welcome';
    /**
     * [群福利]-状态-未参与
     * @var int
     */
    const AWARD_STATUS_NO_JOIN = 0;
    /**
     * [群福利]-状态-已参与
     * @var int
     */
    const AWARD_STATUS_HAS_JOIN = 1;
    /**
     * [群福利]-状态-已收到奖品/系统已发放
     * @var int
     */
    const UNION_ROLE_HAS_SEND = 2;
    /**
     * [群福利]-状态-已领奖
     * @var int
     */
    const UNION_ROLE_HAS_RECEIVE = 3;
    
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 领取[群福利]的逻辑
     * @param array $data
     */
    public function receiveGroupAwardProcess($userId, $data, $returnLastId = FALSE)
    {
        $data = array(
            'userid' => $userId,
            'blockName' => $data['block'],
            'postId' => $data['id'],
            'status' => self::AWARD_STATUS_HAS_JOIN,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 获取用户是否已经加入了群福利的领取信息
     * @param $userId
     * @param $postId
     */
    public function getUserJoinGroupAward($userId, $postId=0, $blockName=self::GROUP_AWARD_DEFAULT_BLOCK)
    {
        $this->db->select('id,userid,blockName,postId,status');
        $this->db->where('userid', $userId);
        $this->db->where('postId', $postId);
        !empty($blockName) && $this->db->where('blockName', $blockName);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $result = $query->row_array();
        return $result;
    }
    /**
     * 系统发奖后，更新获奖用户的领取状态
     * @param int $userId
     * @param array $postId
     */
    public function updateUserGroupAward($userId, $postId)
    {
    	$data = array('status'=>self::UNION_ROLE_HAS_SEND, 'updateTime'=>date('Y-m-d H:i:s'));
        $this->db->where('userid', $userId);
        $this->db->where('postId', $postId);
        $this->db->where('status', self::AWARD_STATUS_HAS_JOIN);
        return $this->db->update($this->_table, $data);
        //return $this->db->affected_rows() > 0;
    }

}