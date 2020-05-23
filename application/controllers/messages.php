<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages extends BLH_Controller{
    public function __construct(){
        parent::__construct(false);
    }

    public function total($id=0)
    {
        $count = 0;
        if($this->auth(true))
        {
            $id = intval($id);
            if ($id > 0)
            {
                $this->load->model('Userposts');
                $count = $this->Userposts->total($id);
            }
        }
        echo $count;
    }
    /**
     * 用户发帖列表
     * @param int $userid
     * @param int $page
     * @param int $pagesize
     */
    public function itemlist($userid=0, $page=1, $pagesize=20)
    {
        $data = array();
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
        $userid = intval($userid);
        if($page<0) $page = 1;
        if($pagesize<0) $pagesize = 20;
        $ret = array('status'=>false);
        if($this->auth(true))
        {
            if($page>=1 && $pagesize>=1)
            {
                if($userid)
                {
                    $this->load->model('Userposts');
                    //$ret['status'] = true;
                    $ret['posts'] = $this->Userposts->itemlist($userid, $page, $pagesize, $this->_userid);
                    $ret['loginUserId'] = $this->_userid;
                    BLH_Utilities::outputSuccess($ret);
                }
            }
            BLH_Utilities::outputError(-1, 'params [page/pagesize] is error');
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->login_error_data['errmsg']);
    }

    /**
     * 我的消息列表
     * @param int $userid
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function mine($page=1, $pagesize=20, $timestamp = 0)
    {
        //$pagesize = 20;
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
        if($page<0) $page = 1;
        if($pagesize<0) $pagesize = 20;
        $ret = array('status'=>FALSE);
        if($this->auth(true))
        {
            if($page>=1 && $pagesize>=1)
            {
                $this->load->model('Userposts');
                $ret = $this->Userposts->mine($this->_userid, $page, $pagesize, $timestamp);
                //$ret['status'] = TRUE;
                $ret['timestamp'] = SYS_TIME;
                $ret['loginUserId'] = $this->_userid;
                BLH_Utilities::outputSuccess($ret);
            }
            BLH_Utilities::outputError(-1, 'params [page/pagesize] is error');
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->login_error_data['errmsg']);
    }

    /**
     * 我的消息列表
     * @param int $userid
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function mineTest($page=1, $pagesize=20, $timestamp = 0)
    {
        $page = max(1, intval($page));
        $pagesize = intval($pagesize);
        if($page<0) $page = 1;
        if($pagesize<0) $pagesize = 20;
        $ret = array('status'=>FALSE);
        if($this->auth(true))
        {
            if($page>=1 && $pagesize>=1)
            {
                $this->load->model('Userposts');
                $ret = $this->Userposts->mineTest($this->_userid, $page, $pagesize, $timestamp);
                $ret['status'] = TRUE;
            }
        }
        echo json_encode($ret);
    }
    /**
     * 是否有新消息
     * @param int $userid
     * @param datetime/timestamp $fromTime
     */
    public function hasNew($fromTime=null)
    {
        $ret = array();
        if($this->auth(true, true))
        {
            //传入时间戳
            if (is_numeric($fromTime))
            {
                $fromTime = $fromTime > 0 ? date('Y-m-d H:i:s', $fromTime) : date('Y-m-d H:i:s', SYS_TIME);
            }
            else
            {
                $fromTime = !$fromTime ? date('Y-m-d H:i:s', SYS_TIME) : date('Y-m-d H:i:s', strtotime($fromTime));
            }
            foreach(BLH_Controller::$message_tips_config as $message_key => $message_val)
            {
                $message = array();
                switch($message_key)
                {
                    case 'message':
                        //消息页签是否有最新的、[未读]的消息
                        $this->load->model('Userposts');
                        $result = $this->Userposts->hasNew($this->_userid, $fromTime);
                        $message['type'] = BLH_Controller::$message_tips_config[$message_key]; //json_encode($result ? 1 : 0);
                        $message['status'] = $result ? 1 : 0;
                        break;
                    case 'invite_code':
                        //查询是否有邀请码的确认/拒绝的最新的、[未读]的消息
                        $this->load->model('Invitation');
                        $message['type'] = BLH_Controller::$message_tips_config[$message_key];
                        $newInviteMessage = $this->Invitation->hasNewPostsInviterCode($this->_userid, $fromTime);
                        //$message['data'] = $newInviteMessage;
                        $message['status'] = (is_array($newInviteMessage) && !empty($newInviteMessage) && $newInviteMessage['state'] != Invitation::INVITE_STATE_USED) ? 1 : 0;
                        break;
                    case 'system_userinfo':
                        //是否有用户未完善资料，系统提示的新消息
                        $message['type'] = BLH_Controller::$message_tips_config[$message_key];
                        $message['status'] = 0;
                        break;
                    default:break;
                }
                $ret[] = $message;
            }
        }
        echo json_encode($ret);
    }
    /**
     * 同步更新客户端接收消息的状态
     * @param string $blockName
     * @param int $id
     */
    public function rsyncMessageStatus()
    {
        $ret = array();
        if($this->auth(true, true))
        {
            $data = $this->input->post(NULL, true);
            if (isset($data['type']) && !empty($data['type']))
            {
                switch($data['type'])
                {
                    case 'message':
                        $this->load->model('Userposts');
                        //消息页签是否有最新的、[未读]的消息
                        $this->_setTableName($blockName);
                        $this->load->model('Block');
                        $this->Block->init($this->_tableName);
                        $ret['timestamp'] = SYS_TIME;
                        $ret['status'] = $this->Block->rsyncMessageStatus($data, $this->_userid);
                        break;
                    case 'invite_code':
                        //查询是否有邀请码的确认/拒绝的最新的、[未读]的消息
                        $this->load->model('Invitation');
                        break;
                    case 'system_userinfo':
                        
                        break;
                    default:break;
                }
            }
        }
        BLH_Utilities::outputSuccess($ret);
    }
}
