<?php
class Invitation extends CI_Model{
    public $_table = 'invitation';
    /**
     * 邀请状态-新邀请
     * @var string
     */
    const INVITE_STATE_NEW = 'new';
    /**
     * 邀请状态-同意
     * @var string
     */
    const INVITE_STATE_AGREE = 'agree';
    /**
     * 邀请状态-拒绝
     * @var string
     */
    const INVITE_STATE_REFUSED = 'refused';
    /**
     * 邀请状态-已使用
     * @var string
     */
    const INVITE_STATE_USED = 'used';
    /**
     * 清理半个月之前的，已使用的邀请码信息
     * @var iint
     */
    protected $gc_expiration = 1296000;
    /**
     * gc清理概率
     * @var int
     */
    protected $gc_probability = 5;

    function __construct(){
        parent::__construct();
    }
    /**
     * 获取邀请码
     * 如果有多个人赠送邀请码，则使用第1个赠送的邀请码
     * @param int $beInviteUserId
     */
    public function fetchInviterCode($beInviteUserId, $state='new')
    {
       $this->db->select('iv.invitationcode,iv.userid,ui.nickname,iv.unionId,iv.inviteIntro');
       $this->db->join('userinfo ui', 'ui.id = iv.userid', 'INNER');
       $this->db->where('iv.beInviteUserId', $beInviteUserId);
       $this->db->where('iv.state', $state);
       $state == 'new' && $this->db->where('iv.expires > ', SYS_TIME);
       $this->db->order_by('iv.createtime ASC');
       $this->db->limit(1);
       $query = $this->db->get($this->_table . ' iv');
       $res = $query->row_array();
       return !empty($res) ? $res : FALSE;
    }
    /**
     * 获取未读的消息
     * @param int $beInviteUserId
     */
    public function hasNewInviterCode($beInviteUserId, $supportCodeUserId=0, $unionId=0)
    {
       $this->db->select('invitationcode,userid,unionId,inviteIntro,isRead');
       $this->db->where('beInviteUserId', $beInviteUserId);
       $supportCodeUserId > 0 && $this->db->where('userid', $supportCodeUserId);
       $unionId > 0 && $this->db->where('unionId', $unionId);
       //$this->db->where('isRead', 0);
       $this->db->order_by('createtime DESC');
       $this->db->limit(1);
       $query = $this->db->get($this->_table);
       $res = $query->row_array();
       return !empty($res) ? $res : FALSE;
    }
    /**
     * 获取该用户发出的未读的请求消息
     * @param int $beInviteUserId 该用户发出的寻邀请码的请求消息
     */
    public function hasNewPostsInviterCode($beInviteUserId, $fromTime=0)
    {
        //[寻邀请码]的舶来账号
        $user_sys_invite_id = $this->config->item('USER_SYS_INVITE', 'system_accounts');
        $this->db->select('cbp.id,cbp.title,cbp.isRead,iv.invitationcode,iv.state,iv.userid,ui.nickname,iv.beInviteUserId,iv.unionId,iv.inviteIntro,iv.createtime');
        $this->db->join('communicationblockpost cbp', 'cbp.remark=iv.beInviteUserId and cbp.unionId=iv.unionId and cbp.receiveUserId=iv.userid', 'INNER');
        $this->db->join('userinfo ui', 'ui.id = iv.userid', 'INNER');
        $this->db->where('iv.beInviteUserId', $beInviteUserId);
        //由[舶来帐号]发送的寻邀请码的消息
        $this->db->where('cbp.userid', $user_sys_invite_id);
        //$this->db->where('cbp.isRead', 0);
        $this->db->where('iv.expires > ', SYS_TIME);
        //查询该时间之后的消息
        $this->db->where('iv.createtime >=', $fromTime);
        $this->db->group_by('iv.invitationcode');
        $this->db->order_by('iv.createtime DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' iv');
        $res = $query->row_array();
        return !empty($res) ? $res : FALSE;
    }
    /**
     * 验证邀请码
     * @param int $code
     */
    public function getInviter($code){
       $this->db->select('userid,unionId,inviteIntro');
       $this->db->where('invitationcode', $code);
       $this->db->where('state', self::INVITE_STATE_NEW);
       $this->db->where('expires > ', SYS_TIME);
       $this->db->limit(1);
       $query = $this->db->get($this->_table);
       //$count = $this->db->count_all_results($this->_table);
       $res = $query->result();
       if(count($res) == 1)
       {
            $row = $res[0];
            return array($row->userid, $row->inviteIntro, $row->unionId);
       }
       return false;
    }
    /**
     * 删除邀请码的记录
     * @param $code
     */
    public function rmCode($code, $inviteInfo = array())
    {
        if (!empty($inviteInfo))
        {
            list($inviterId, $inviteIntro, $unionId) = $inviteInfo;
            $this->db->where('userid', $inviterId);
            $this->db->where('unionId', $unionId);
        }else{
            $this->db->where('userid !=', 0);
        }
        $this->db->where('invitationcode', $code);
        $this->db->or_where('expires <', SYS_TIME);
        $this->db->delete($this->_table);
        //echo $this->db->last_query();
    }
    /**
     * 更新邀请码的状态
     * @param int $code
     */
    public function updateCode($code, $inviteInfo = array())
    {
        if (!empty($inviteInfo))
        {
            list($inviterId, $inviteIntro, $unionId) = $inviteInfo;
            $this->db->where('userid', $inviterId);
            $this->db->where('unionId', $unionId);
        }else{
            $this->db->where('userid !=', 0);
        }
        $this->db->where('invitationcode', $code);
        $this->db->where('state', self::INVITE_STATE_NEW);
        $this->db->update($this->_table, array('state'=>self::INVITE_STATE_USED));
    }
    /**
     * 生成邀请码
     * @param int $userid
     * @param string $intro
     */
    public function genCode($userid, &$inviteData = array())
    {
        try{
            $intro = !empty($inviteData['intro']) ? $inviteData['intro'] : '';
            $invite_code_timeout = $this->config->item('invite_code_timeout');
            $invite_code_length = $this->config->item('invite_code_length');
            $data = array('userid'=>$userid, 'expires'=>SYS_TIME+$invite_code_timeout, 'state'=>self::INVITE_STATE_NEW, 'inviteIntro'=>$intro, 'createtime'=>date('Y-m-d H:i:s', SYS_TIME));
            //生成邀请码的社团ID
            if (isset($inviteData['unionId']) && !empty($inviteData['unionId']))
            {
                $data['unionId'] = $inviteData['unionId'];
            }
            //被邀请的用户ID
            if (isset($inviteData['beInviteUserId']) && !empty($inviteData['beInviteUserId']))
            {
                $data['beInviteUserId'] = $inviteData['beInviteUserId'];
            }
            //清理过期邀请码
            $this->_invite_gc();
            //$data['invitationcode'] = sprintf("%7d%02d",  mt_rand(1000000,9999999), $userid%1000);
            $this->load->library('BLH_Utilities');
            $data['invitationcode'] = BLH_Utilities::random($invite_code_length, 1);
            $ret = $this->db->insert($this->_table, $data);
            return $ret ? $data['invitationcode'] : FALSE;
        }catch (Exception $e){
            return FALSE;
        }
    }
    /**
     * 同意别人申请邀请码的请求
     * @param $userid 当前登录用户
     * @param $inviteData
     */
    public function beAgree($userid, &$inviteData = array())
    {
        try{
            $intro = !empty($inviteData['intro']) ? $inviteData['intro'] : '';
            $invite_code_timeout = $this->config->item('invite_code_timeout');
            $data = array('invitationcode'=>SYS_TIME,'state'=>self::INVITE_STATE_AGREE,'userid'=>$userid,'beInviteUserId'=>$inviteData['beInviteUserId'],'unionId'=>$inviteData['unionId'],'expires'=>SYS_TIME+$invite_code_timeout,'inviteIntro'=>$intro,'createtime'=>date('Y-m-d H:i:s', SYS_TIME));
            return $this->db->insert($this->_table, $data);
        }catch (Exception $e){
            return FALSE;
        }
    }
    /**
     * 拒绝别人申请邀请码的请求
     * @param $userid 当前登录用户
     * @param $inviteData
     */
    public function beRefused($userid, &$inviteData = array())
    {
        try{
            $intro = !empty($inviteData['intro']) ? $inviteData['intro'] : '';
            $invite_code_timeout = $this->config->item('invite_code_timeout');
            $data = array('invitationcode'=>SYS_TIME,'state'=>self::INVITE_STATE_REFUSED,'userid'=>$userid,'beInviteUserId'=>$inviteData['beInviteUserId'],'unionId'=>$inviteData['unionId'],'expires'=>SYS_TIME+$invite_code_timeout,'inviteIntro'=>$intro,'createtime'=>date('Y-m-d H:i:s', SYS_TIME));
            return $this->db->insert($this->_table, $data);
        }catch (Exception $e){
            return FALSE;
        }
    }

    /**
     * 获取邀请我加入该社团的用户信息
     * @param int $code
     */
    public function getInviteMyUserInfo($beInviteUserId, $unionId, $unionName='', $unionRoleName='', $userObj=NULL)
    {
        $ret = array();
        /*$this->db->select('ic.userid,ic.unionId');
        $this->db->join('userinfo ui', 'ui.id = ic.beInviteUserId', 'LEFT');
        $this->db->where('ic.beInviteUserId', $beInviteUserId);
        $this->db->where('ic.state', self::INVITE_STATE_USED);
        $this->db->where('ic.unionId', $unionId);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' ic');*/
        $this->db->select('tuu.invitedBy AS userid,tuu.unionId');
        $this->db->join('userinfo ui', 'ui.id = tuu.userid', 'LEFT');
        $this->db->where('tuu.userid', $beInviteUserId);
        //$this->db->where('tuu.invitedBy !=', $beInviteUserId);
        $this->db->where('tuu.unionId', $unionId);
        $this->db->limit(1);
        $query = $this->db->get('tbl_user_union tuu');
        $res = $query->row_array();
        $ret = array(
            'myUnionId' => $unionId,
            'myUnionName' => $unionName,
            'myUnionRoleName' => $unionRoleName,
            'inviteNickName' => '',
        );
        if(!empty($res))
        {
            if (is_null($userObj))
            {
                $this->load->model('Userinfo');
                $userObj =& $this->Userinfo;
            }
            if($res['userid'] == $beInviteUserId)
            {
                $ret['inviteNickName'] = '';
            }else{
                $user_data = $userObj->fetch_user_by_id($res['userid']);
                if (!empty($user_data))
                {
                    $ret['inviteNickName'] = $user_data['nickname'];
                }
            }
        }
        return $ret;
    }
    /**
     * Garbage collection invite code
     *
     * This deletes expired invite rows from database
     * if the probability percentage is met
     *
     * @access  private
     * @return  void
     */
    private function _invite_gc()
    {
        $now = SYS_TIME;
        srand($now);
        if ((rand() % 100) < $this->gc_probability)
        {
            $expire = $now - $this->gc_expiration;
            
            $this->db->where('state', self::INVITE_STATE_USED);
            $this->db->where("expires < {$expire}");
            $this->db->delete($this->_table);
        }
    }
}
