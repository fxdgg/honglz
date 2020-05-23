<?php
/**
 * 用户-私聊表
 *
 */
class UserChat extends CI_Model
{
    public $_table = 'tbl_user_chat';

    function __construct()
    {
        parent::__construct();
    }
    /**
     * 新增私聊逻辑
     * @param int $userid
     * @param int $pay_fee
     */
    public function addChat($send_userid, $receive_userid, $sendUserUnionId, $pay_money, $expiresTime=-1, $returnLastId = FALSE)
    {
        $data = array(
            'send_userid' => $send_userid,
            'receive_userid' => $receive_userid,
            'unionId' => $sendUserUnionId,
            'pay_money' => $pay_money,
            'expiresTime' => ($expiresTime == -1 ? $expiresTime : (SYS_TIME + $expiresTime)), //是否开启有效期验证
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 获取该用户对某人发起的聊天记录
     * @param $send_userid
     * @param $receive_userid
     */
    public function getUserChatBySendUid($send_userid, $receive_userid=0, $send_unionId=0)
    {
        $this->db->select('send_userid,receive_userid,pay_money,expiresTime');
        $this->db->where('send_userid', $send_userid);
        $this->db->where('unionId', $send_unionId);
        if ($receive_userid > 0) $this->db->where('receive_userid', $receive_userid);
        $this->db->where('expiresTime > ', SYS_TIME);
        $this->db->or_where('expiresTime', -1);
        $this->db->order_by('createTime', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $result = $receive_userid > 0 ? $query->row_array() : $query->result_array();
        return $result;
    }
    /**
     * 获取该社团的所有流水记录
     * @param $unionId
     * @param $type
     */
    public function getUserChatByUnionId($unionId, $page = 1, $pagesize = 30, $source='union')
    {
        $this->db->select('tuc.send_userid,tuc.receive_userid,tuc.pay_money,tuc.createTime,tuc.expiresTime,tuu.unionid,tuu.unionRole,tuu.invitedBy,tu.unionName,tu.unionStatus');
        $this->db->join('tbl_user_union tuu', 'tuu.userid = tuc.send_userid AND tuu.unionId = tuc.unionId', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuc.unionId', 'LEFT');
        $source == 'union' && $this->db->where('tuc.unionId', $unionId);
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tuc');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 获取该社团的所有流水记录总条数
     * @param $unionId
     * @param $type
     */
    public function getUserChatByUnionIdTotal($unionId, $source='union')
    {
        $this->db->select('tuc.send_userid');
        $this->db->join('tbl_user_union tuu', 'tuu.userid = tuc.send_userid AND tuu.unionId = tuc.unionId', 'LEFT');
        $this->db->join('tbl_union tu', 'tu.unionId = tuc.unionId', 'LEFT');
        $source == 'union' && $this->db->where('tuc.unionId', $unionId);
        #$query = $this->db->get($this->_table . ' tuc');
        $cnt = $this->db->count_all_results($this->_table . ' tuc');
        return $cnt;
    }

}