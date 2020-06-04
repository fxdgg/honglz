<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends BLH_Controller{
    /**
     * 用户的注册状态，设置默认值
     * _regState：-1（未注册），1(注册成功，正常用户) 0（无邀请码） 2（被禁用）
     */
    private $_regState = -1;
    
    public function __construct()
    {
        parent::__construct(false);
    }

    public function register()
    {
        $data = $_POST;
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $ret = array('status'=>false);
        if ($this->form_validation->run('account/reg') == FALSE)
        {
            $this->form_validation->set_error_delimiters('', '');
            $ret['status'] = false;
            $ret['errcode'] = !empty($this->errcode) ? $this->errcode : 1;
            $ret['msg'] = validation_errors();
        }else{
            //校验密码
            if(strcmp($data['passwd'], $data['passwdconf']) != 0)
            {
                $ret['status'] = FALSE;
                $ret['errcode'] = -1;
                $ret['msg'] = 'passwd is not equal passwd of confirm.';
            }
            else
            {
                $isRegister = TRUE;
                if (isset($data['isUnionInvite']) && $data['isUnionInvite'] == 1)
                {
                    if (!isset($data['code']) OR empty($data['code']))
                    {
                        $isRegister = FALSE;
                    }else{
                        //检查社团邀请码是否存在
                        $this->load->model('UnionManage');
                        $isExistUnion = $this->UnionManage->isExistUnionByCode($data['code']);
                        !$isExistUnion && $isRegister = FALSE;
                    }
                }
                if ($isRegister)
                {
                    unset($data['invitationcode'], $data['isUnionInvite'], $data['code']);
                    $this->load->model('Userinfo');
                    $id = $this->Userinfo->addNew($data);
                    $ret['status'] = $id > 0;
                    if(!$ret['status'])
                    {
                        $ret['errcode'] = $id;
                        $ret['msg'] = 'register failed';
                    }else
                    {
                        $ret['userid'] = $id;
                    }
                }else{
                    $ret['status'] = FALSE;
                    $ret['errcode'] = -2;
                    $ret['msg'] = 'params is error or union is not exists';
                }
            }
        }
        echo json_encode($ret);
    }
    /**
     * 用户登录逻辑
     */
    public function login()
    {
        $this->source = !empty($_REQUEST[SOURCE]) ? $_REQUEST[SOURCE] : '';
        switch ($this->source)
        {
            case 'weibo': //新浪微博weibo登录方式
                $params = array(ACCESS_TOKEN=>$_REQUEST[ACCESS_TOKEN]);
                $weibo_token = $this->validateToken($params);
                $weibo_token[ACCESS_TOKEN] = $params[ACCESS_TOKEN];
                if (!$this->third_userid)
                {
                    BLH_Utilities::outputError(27002, 'Invalid weibo user');
                }
                $ret = array();
                $this->load->model('Userinfo');
                //验证该用户是否已创建帐号
                $userData = $this->Userinfo->fetch_user_by_third($this->third_userid, 'object', 'sinaUserId');
                if (empty($userData))
                {
                    $weiboEmail = '';
                    $email = $this->third_userid . $this->default_email_suffix;
                    //$weiboUserInfo = $this->send_weibo_api('account_profile_basic', array($this->third_userid), TRUE);
                    //if (!$weiboUserInfo OR isset($weiboUserInfo['error_code']) OR !empty($weiboUserInfo['error']))
                    //{
                        $weiboUserInfo = $this->send_weibo_api('account_profile_basic', array($this->third_userid, $params[ACCESS_TOKEN]));
                    //}
                    if (isset($weiboUserInfo['email']) && !empty($weiboUserInfo['email']))
                    {
                        $email_user_data = $this->Userinfo->fetch_user_by_email($weiboUserInfo['email']);
                        if (!is_array($email_user_data) OR empty($email_user_data))
                        {
                            $weiboEmail = $email = $weiboUserInfo['email'];
                        }
                    }
                    $register_data = array(
                        'email' => $email,
                        'weiboEmail' => $weiboEmail,
                        'nickname' => !empty($weiboUserInfo['screen_name']) ? $weiboUserInfo['screen_name'] : '',
                        'realname' => !empty($weiboUserInfo['real_name']) ? $weiboUserInfo['real_name'] : '',
                        'passwd' => $this->default_passwd,
                        'passwdconf' => $this->default_passwd,
                        'sex' => (!empty($weiboUserInfo['gender']) && $weiboUserInfo['gender'] == 'm') ? 1 : 2,
                        'qq' => !empty($weiboUserInfo['qq']) ? $weiboUserInfo['qq'] : '',
                        'weibo' => !empty($weiboUserInfo['domain']) ? $weiboUserInfo['domain'] : '',
                        'area' => !empty($weiboUserInfo['location']) ? $weiboUserInfo['location'] : '',
                        'birthday' => !empty($weiboUserInfo['birthday']) ? $weiboUserInfo['birthday'] : '',
                        'blogUrl' => !empty($weiboUserInfo['url']) ? $weiboUserInfo['url'] : '',
                        'logoUrl' => !empty($weiboUserInfo['avatar_hd']) ? $weiboUserInfo['avatar_hd'] : (!empty($weiboUserInfo['avatar_large']) ? $weiboUserInfo['avatar_large'] : ''),
                        'iconUrl' => !empty($weiboUserInfo['profile_image_url']) ? $weiboUserInfo['profile_image_url'] : '',
                        'sinaUserId' => $this->third_userid,
                    );
                    if (isset($weiboUserInfo['msn']) && !empty($weiboUserInfo['msn']))
                    {
                        $register_data['other'] = $weiboUserInfo['msn'];
                    }
                    $id = $this->Userinfo->addNew($register_data);
                    if(!($id > 0))
                    {
                        $ret['regState'] = -1;
                        BLH_Utilities::outputError(-100, 'register failed', $ret);
                    }
                    $userData = $this->Userinfo->fetch_user_by_id($id, 'object');
                }
                //职业经历
                /*$this->load->model('UserJobs');
                $userJobsList = $this->UserJobs->searchUserJobsListByUserId($userData->id);
                if (!$userJobsList OR empty($userJobsList))
                {
                    $weiboUserJobs = $this->send_weibo_api('account_career', array($this->third_userid), TRUE);
                    if (!empty($weiboUserJobs) && !isset($weiboUserJobs['error_code']) && empty($weiboUserJobs['error']))
                    {
                        $lastJobsInfo = array();
                        //清理掉旧的职业信息
                        $this->UserJobs->deleteUserJobs($userData->id);
                        foreach ($weiboUserJobs as $userJobsItem)
                        {
                            if (empty($userJobsItem)) continue;
                            $jobsParams = array(
                                'userid' => $userData->id,
                                'companyName' => !empty($userJobsItem['company']) ? $userJobsItem['company'] : '',
                                'positionName' => !empty($userJobsItem['department']) ? $userJobsItem['department'] : '',
                                'joinTime' => !empty($userJobsItem['start']) ? $userJobsItem['start'].'-00-00' : '',
                                'leaveTime' => !empty($userJobsItem['end']) ? $userJobsItem['end'].'-00-00' : '',
                            );
                            //记录最新的职业信息
                            $this->UserJobs->createUserJobs($jobsParams);
                            if (isset($userJobsItem['end']) && $userJobsItem['end'] == 9999)
                            {
                                $lastJobsInfo = $jobsParams;
                            }
                        }
                        if (!empty($lastJobsInfo))
                        {
                            $jobsData = array(
                                'company' => $lastJobsInfo['companyName'],
                                'position' => $lastJobsInfo['positionName'],
                            );
                            $this->Userinfo->edit($userData->id, $jobsData);
                            if (empty($userData->company))
                            {
                                $userData->company = $lastJobsInfo['companyName'];
                            }
                            if (empty($userData->position))
                            {
                                $userData->position = $lastJobsInfo['positionName'];
                            }
                        }
                    }
                }*/
                if (isset($userData->status) && $userData->status == $this->Userinfo->USER_STATUS_OK)
                {
                    $ret['userid'] = $userData->id;
                    $ret['nickname'] = $userData->nickname;
                    $ret['email'] = $userData->email;
                    $ret['sex'] = $userData->sex;
                    $ret['iconUrl'] = $userData->iconUrl;
                    $ret['company'] = $userData->company;
                    $ret['position'] = $userData->position;
                    $ret['regState'] = $userData->status;
                    $ret['qq'] = $userData->qq;
                    $ret['workDate'] = $userData->workDate;
                    $ret['gradeSchool'] = $userData->gradeSchool;
                    //设置登录状态
                    $this->Userinfo->afterLogin($ret['userid'], $ret['nickname'], $userData, $weibo_token);
                    //用户登录日志
                    $this->load->model('UserLoginLog');
                    $this->UserLoginLog->insertUserLoginLog($ret['userid'], $ret['nickname'], $userData->status, UserLoginLog::USER_LOGIN_SOURCE_WEIBO);
                    BLH_Utilities::outputSuccess($ret);
                }
                else
                {
                    //status==0、2时，表示无邀请码、被禁用之后，也返回userid
                    $ret['userid'] = $userData->id;
                    $ret['nickname'] = $userData->nickname;
                    $ret['email'] = $userData->email;
                    $ret['sex'] = $userData->sex;
                    $ret['iconUrl'] = $userData->iconUrl;
                    $ret['company'] = $userData->company;
                    $ret['position'] = $userData->position;
                    $ret['regState'] = $userData->status;
                    $ret['qq'] = $userData->qq;
                    $ret['workDate'] = $userData->workDate;
                    $ret['gradeSchool'] = $userData->gradeSchool;
                    $msg = 'login failed';
                    //在用户刚注册时，也设置session
                    if (isset($userData->status) && $userData->status == $this->Userinfo->USER_STATUS_REGISTER)
                    {
                        //设置登录状态
                        $this->Userinfo->afterLogin($ret['userid'], $ret['nickname'], $userData, $weibo_token);
                        //用户登录日志
                        $this->load->model('UserLoginLog');
                        $this->UserLoginLog->insertUserLoginLog($ret['userid'], $ret['nickname'], $userData->status, UserLoginLog::USER_LOGIN_SOURCE_WEIBO);
                    }
                    BLH_Utilities::outputError(-100, $msg, $ret);
                }
                break;
            default:
                $ret = array();
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('', '');
                $this->_email = $this->input->post('email');
                $this->load->model('Userinfo');
                if( $this->form_validation->run('account/login') == false)
                {
                    //$ret['status'] = false;
                    $ret['regState'] = $this->_regState;
                    $msg = validation_errors();
                    //_regState==0时，表示无邀请码，也返回userid
                    if ($this->_regState == $this->Userinfo->USER_STATUS_REGISTER OR $this->_regState == $this->Userinfo->USER_STATUS_STOPPED)
                    {
                        $ret['userid'] = $this->_userid;
                        $ret['nickname'] = $this->_nickname;
                        $ret['email'] = $this->_email;
                        $ret['sex'] = $this->_sex;
                        $ret['iconUrl'] = $this->_iconUrl;
                        $ret['company'] = $this->_company;
                        $ret['position'] = $this->_position;
                        $ret['qq'] = $this->_user_data->qq;
                        $ret['workDate'] = $this->_user_data->workDate;
                        $ret['gradeSchool'] = $this->_user_data->gradeSchool;
                    }
                    //在用户刚注册时，也设置session
                    if ($this->_regState == $this->Userinfo->USER_STATUS_REGISTER)
                    {
                        //设置登录状态
                        $this->Userinfo->afterLogin($this->_userid, $this->_nickname, $this->_user_data);
                        //用户登录日志
                        $this->load->model('UserLoginLog');
                        $this->UserLoginLog->insertUserLoginLog($this->_userid, $this->_nickname, $this->_regState, UserLoginLog::USER_LOGIN_SOURCE_APPSITE);
                    }
                    BLH_Utilities::outputError(-100, $msg, $ret);
                }else{
                    //设置登录状态
                    $this->Userinfo->afterLogin($this->_userid, $this->_nickname, $this->_user_data);
                    //用户登录日志
                    $this->load->model('UserLoginLog');
                    $this->UserLoginLog->insertUserLoginLog($this->_userid, $this->_nickname, $this->_regState, UserLoginLog::USER_LOGIN_SOURCE_APPSITE);
                    $ret['userid'] = $this->_userid;
                    $ret['nickname'] = $this->_nickname;
                    $ret['email'] = $this->_email;
                    $ret['sex'] = $this->_sex;
                    $ret['iconUrl'] = $this->_iconUrl;
                    $ret['company'] = $this->_company;
                    $ret['position'] = $this->_position;
                    $ret['regState'] = $this->_regState;
                    $ret['qq'] = $this->_user_data->qq;
                    $ret['workDate'] = $this->_user_data->workDate;
                    $ret['gradeSchool'] = $this->_user_data->gradeSchool;
                    BLH_Utilities::outputSuccess($ret);
                }
                break;
        }
    }

    public function logout()
    {
        if($this->auth(TRUE, TRUE))
        {
            $this->session->sess_destroy();
        }
        echo json_encode(array('status'=>TRUE));
    }
    /**
     * 获取我获得的邀请码列表
     */
    public function get_my_invite()
    {
        if($this->auth(TRUE, TRUE))
        {
            $ret = array();
            $this->load->model('Invitation');
            //查询该用户有效的邀请码
            /*$inviteData = $this->Invitation->fetchInviterCode($this->_userid, 'new');
            if (!empty($inviteData) && !empty($inviteData['invitationcode']))
            {
                $ret = array('code'=>$inviteData['invitationcode'], 'sendInviteUserNickName'=>$inviteData['nickname']);
            }
            //查询该用户被拒绝的记录
            $inviteRefusedData = $this->Invitation->fetchInviterCode($this->_userid, 'refused');
            if (!empty($inviteRefusedData) && !empty($inviteRefusedData['invitationcode']))
            {
                $ret = array('intro'=>$inviteData['inviteIntro'], 'sendInviteUserNickName'=>$inviteData['nickname']);
            }*/
            //获取该用户发出的未读的请求消息
            $inviteMessage = $this->Invitation->hasNewPostsInviterCode($this->_userid);
            if (!empty($inviteMessage) && !empty($inviteMessage['invitationcode']))
            {
                //$ret['userid'] = $inviteMessage['userid'];
                //$ret['nickname'] = $inviteMessage['nickname'];
                switch($inviteMessage['state'])
                {
                    case 'new': //被邀请
                        $ret['code'] = $inviteMessage['invitationcode'];
                        break;
                    case 'refused': //被拒绝
                        $ret['intro'] = $inviteMessage['inviteIntro'];
                        break;
                }
            }
            BLH_Utilities::outputSuccess($ret);
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));
    }
    /**
     * 生成邀请码（点击【确认/回绝】同意赠送邀请码）
     */
    public function invitationcode()
    {
        $ret = array();
        if($this->auth(TRUE))
        {
            $data = $this->input->post(NULL, TRUE);
            //参数检查
            if (!isset($data['beInviteUserId']) OR empty($data['beInviteUserId']) OR !isset($data['unionId']) OR empty($data['unionId']))
            {
                BLH_Utilities::outputError(-5, $this->lang->line('union_params_empty'));
            }
            //不能自己同意自己
            if ($this->_userid == $data['beInviteUserId'])
            {
                BLH_Utilities::outputError(-6, 'do not self agree self');
            }
            $this->load->model('UserUnion');
            //检查当前用户是否已加入该社团
            $inviteUserUnionList = $this->UserUnion->searchUnionListByUserId($this->_userid, $data['unionId']);
            if (empty($inviteUserUnionList))
            {
                BLH_Utilities::outputError(-7, 'you are not in the union');
            }
            //检查被邀请人是否已加入该社团
            /*$beInviteUserUnionList = $this->UserUnion->searchUnionListByUserId($data['beInviteUserId'], $data['unionId'], array(UserUnion::USER_UNION_ROLE_MEMBER,UserUnion::USER_UNION_ROLE_MASTER_ADMIN,UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
            if (!empty($beInviteUserUnionList))
            {
                BLH_Utilities::outputError(-7, 'he has in the union');
            }*/
            if (isset($data['inviteOther']) && $data['inviteOther'] == 1)
            {
                $this->load->model('Invitation');
                $code = $this->Invitation->genCode($this->_userid, $data);
            }else{
                //根据用户是否已加入某社团
                $this->load->model('UserUnion');
                $userInUnion = $this->UserUnion->checkInUnionByUserId($data['beInviteUserId'], $data['unionId']);
                if (is_array($userInUnion) && !empty($userInUnion))
                {
                    BLH_Utilities::outputError(-8, 'he has join the union');
                }else{
                    //批量把用户状态设置为未验证状态(用户基本信息模块)
                    $this->load->model('Userinfo');
                    $ret_status = $this->Userinfo->updateUserStatus($data['beInviteUserId'], $this->Userinfo->USER_STATUS_OK);
                    //插入社团用户表
                    //社团邀请码
                    $unionCodeTag = (isset($data['code']) && !empty($data['code'])) ? $data['code'] : '';
                    $inviteInfo = array($this->_userid, '', $data['unionId']);
                    $ret_union = $this->UserUnion->createUnionUser($data['beInviteUserId'], $inviteInfo, FALSE, UserUnion::USER_UNION_ROLE_MEMBER, $unionCodeTag);
                    if (!$ret_union)
                    {
                        BLH_Utilities::outputError(-9, 'create union user failed');
                    }
                    $this->load->model('Invitation');
                    //插入同意别人进入社团的邀请记录
                    $this->Invitation->beAgree($this->_userid, $data);
                    //生成邀请码
                    $invite_code_length = $this->config->item('invite_code_length');
                    $code = BLH_Utilities::random($invite_code_length, 1);
                }
            }
            if (!$code) $code = 0;
            $ret['code'] = $code;
            if($code)
            {
                BLH_Utilities::outputSuccess($ret);
            }else{
                BLH_Utilities::outputError(-1, '生成验证码失败，请重试', $ret);
            }
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));
    }
    /**
     * 拒绝别人申请邀请码的请求
     */
    public function refuseInvitationCode()
    {
        $ret = array();
        if($this->auth(TRUE))
        {
            $data = $this->input->post(NULL, TRUE);
            //参数检查
            if (!isset($data['beInviteUserId']) OR empty($data['beInviteUserId']) OR !isset($data['unionId']) OR empty($data['unionId']))
            {
                BLH_Utilities::outputError(-5, 'params is empty');
            }
            //不能自己拒绝自己
            if ($this->_userid == $data['beInviteUserId'])
            {
                BLH_Utilities::outputError(-6, 'do not self refuse self');
            }
            $this->load->model('UserUnion');
            //检查被邀请人是否已加入该社团
            $inviteUserUnionList = $this->UserUnion->searchUnionListByUserId($this->_userid, $data['unionId']);
            if (empty($inviteUserUnionList))
            {
                BLH_Utilities::outputError(-7, 'you are not in the union');
            }
            $userInUnion = $this->UserUnion->checkInUnionByUserId($data['beInviteUserId'], $data['unionId']);
            if (is_array($userInUnion) && !empty($userInUnion))
            {
                BLH_Utilities::outputError(-8, 'he has join the union');
            }
            $result = FALSE;
            $this->load->model('Invitation');
            if (!isset($data['intro']) OR empty($data['intro']))
            {
                $union_refuse_invite_tips = $this->config->item('union_refuse_invite_tips', 'system_union_config');
                $data['intro'] = sprintf($union_refuse_invite_tips, $this->_nickname);
            }
            $result = $this->Invitation->beRefused($this->_userid, $data);
            //$ret['ret'] = $result;
            if($result)
            {
                BLH_Utilities::outputSuccess($ret);
            }else{
                BLH_Utilities::outputError(-1, '拒绝邀请码失败', $ret);
            }
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));
    }
    
    /**
     * 使用邀请码
     * @param int $userid
     * @param int $code
     */
    public function updateInvitation($userid, $code)
    {
        $ret = array('code'=>-1, 'message'=>'邀请码不符合条件');
        
        $code = trim($code);
        $loginRet = true;
        if($code && is_numeric($userid) && $userid)
        {
            $this->load->model('Invitation');
            $inviteInfo = $this->Invitation->getInviter($code);
            if($inviteInfo !== false)
            {
                $this->load->model('Userinfo');
                if(TRUE == $this->Userinfo->updateInvitation($userid, $inviteInfo))
                {
                    $registUser = $this->Userinfo->info($userid, $userid);
                    $invitor = $this->Userinfo->info($inviteInfo[0], 0);
                    $this->load->model('Block');
                    $this->Block->init('welcomeblockpost');
                    //发送邀请码的系统ID
                    $user_sys_welcome_id = $this->config->item('USER_SYS_WELCOME', 'system_accounts');
                    //欢迎新人的模版列
                    $welcome_templates = $this->config->item('welcome_templates');
                    //欢迎新人的模版标题
                    $welcome_titles = $this->config->item('welcome_titles');
                    //获取准备加入的社团名称
                    $this->load->model('UnionManage');
                    $unionDetailInfo = $this->UnionManage->getUnionById($inviteInfo[2]);
                    $unionName = (isset($unionDetailInfo['unionName']) && !empty($unionDetailInfo['unionName'])) ? $unionDetailInfo['unionName'] : '';
                    $welcomePost = array(
                        'title' => $welcome_titles[$user_sys_welcome_id],
                        'content' => sprintf($welcome_templates[$user_sys_welcome_id], ($registUser['nickname']?$registUser['nickname']:$registUser['email']), $invitor['nickname'], $unionName, $invitor['nickname'], $inviteInfo[1]),
                        'userid' => $user_sys_welcome_id,
                        'unionId' => $inviteInfo[2],//每个社团只能看到该社团的欢迎新人
                        'remark' => $inviteInfo[0],
                        'type' => Block::SYSTEM_TYPE_STATUS,
                    );
                    $this->Block->create($welcomePost);
                    //插入社团用户表
                    $this->load->model('UserUnion');
                    $this->UserUnion->createUnionUser($userid, $inviteInfo, FALSE, UserUnion::USER_UNION_ROLE_MEMBER);
                    ////////////
                    //删除该邀请码
                    //$this->Invitation->rmCode($code, $inviteInfo);
                    //更新邀请码的状态，不再删除
                    $this->Invitation->updateCode($code, $inviteInfo);
                    //$ret['status'] = true;
                    BLH_Utilities::outputSuccess(array('code'=>0));
                }
                else
                {
                    //$ret['status'] = false;
                    $ret['code'] = 2;
                    $ret['message'] = '邀请码更新失败，请重试';//'update failed';
                }
            }
            else
            {
                $ret['code'] = 1;
                $ret['message'] = '邀请码错误或已使用，暂不能加入';//'invalid code';
            }
        }
        BLH_Utilities::outputError($ret['code'], $ret['message'], array('code'=>$ret['code']));
    }
    /**
     * 编辑用户基本信息
     * @param $id
     */
    public function edit($id=null)
    {
        $ret = array('status'=>FALSE);
        $data = $this->input->post(NULL, TRUE);
        if (isset($data['email'])) unset($data['email']);
        if (isset($data['invitedby'])) unset($data['invitedby']);
        $loginRet=true;
        if(count($data)>0 &&($loginRet=$this->auth(TRUE)) == TRUE)
        {
            $id = $this->_userid;
            $this->load->model('Userinfo');
            $ret['status'] = $this->Userinfo->edit($id, $data);
            if($ret['status'] && $this->input->post('nickname'))
            {
                $this->session->set_userdata('nickname', $this->input->post('nickname', true));
            }
        }
        if(!$loginRet)
        {
            echo json_encode($this->login_error_data);
        }else{
            echo json_encode($ret);
        }
    }
    /**
     * 编辑过往履历
     */
    public function editJobs()
    {
        $result = array('status'=>FALSE);
        $data = $this->input->post(NULL, TRUE);
        $loginRet = TRUE;
        if(count($data)>0 &&($loginRet = $this->auth(TRUE)) == TRUE)
        {
            $this->load->model('Userinfo');
            $result['ret'] = $this->Userinfo->editJobs($this->_userid, $data);
        }
        if(!$loginRet)
        {
            $this->login_error_data['errmsg'] = $this->lang->line('user_login_failed');
            echo BLH_Utilities::outputError($this->login_error_data);
        }else{
            echo BLH_Utilities::outputSuccess($result);
        }
    }
    /**
     * 获取用户的详情(名片详情)
     * @param int $id
     */
    function info_bak($id=null)
    {
        $ret = array('status'=>FALSE);
        $loginRet = TRUE;
        if ($id && is_numeric($id) && ($loginRet = $this->auth(TRUE)) == TRUE)
        {
            $user_data = array();
            $selfId = $this->_userid;
            if (isset($this->_user_data) && !empty($this->_user_data))
            {
                $user_data = $this->_user_data;
            }
            $this->load->model('Userinfo'); 
            $data = $this->Userinfo->info($id, $selfId, $user_data);
            if (FALSE != $data && !empty($data))
            {
                $ret['status'] = TRUE;
                $ret['info'] = $data;
            }
        }
        if (!$loginRet)
        {
            echo json_encode($this->login_error_data);
        }else{
            echo json_encode($ret);
        }
    }
    /**
     * 获取用户的详情(名片详情)
     * @param int $id
     */
    function info($id=null)
    {
        $ret = array('status'=>FALSE);
        $loginRet = TRUE;
        if ($id && is_numeric($id) && ($loginRet = $this->auth(TRUE)) == TRUE)
        {
            $this->load->model('Userinfo'); 
            $data = $this->Userinfo->userone($id, $this->_userid);
            if (FALSE != $data && !empty($data))
            {
                $ret['status'] = TRUE;
                $ret['info'] = $data;
            }
        }
        if (!$loginRet)
        {
            $this->login_error_data['errmsg'] = $this->lang->line('user_login_failed');
            echo json_encode($this->login_error_data);
        }else{
            echo json_encode($ret);
        }
    }
    /**
     * 密码检查
     * _regState：-1（未注册），0（无邀请码） 2（被禁用）
     * @param $passwd
     */
    function password_check($passwd=null)
    {
        $email = $this->_email ? $this->_email : $this->input->post('email');
        if(null == $passwd || !isset($email)) return FALSE;
        $this->load->model('Userinfo');
        $ret = $this->Userinfo->checkUserPass($email, $passwd);
        $loginResult = FALSE;
        $this->_regState = -1;
        if ($ret != false && is_array($ret))
        {
            if ($ret['regState'] == $this->Userinfo->USER_STATUS_OK)
            {
                $this->_userid = $ret['id'];
                $this->_nickname = $ret['nickname'];
                $this->_sex = $ret['sex'];
                $this->_iconUrl = $ret['iconUrl'];
                $this->_company = $ret['company'];
                $this->_position = $ret['position'];
                $this->_user_data = $ret['user_data'];
                $loginResult = TRUE;
            }
            else
            {
                $this->_userid = $ret['id'];
                $this->_nickname = $ret['nickname'];
                $this->_sex = $ret['sex'];
                $this->_iconUrl = $ret['iconUrl'];
                $this->_company = $ret['company'];
                $this->_position = $ret['position'];
                $this->_user_data = $ret['user_data'];
                $this->form_validation->set_message('password_check', 'account is disabled');
            }
            $this->_regState = $ret['regState'];
        }else{
            $this->form_validation->set_message('password_check', 'login failed or password is error');
        }
        return $loginResult;
    }
    /**
     * 邮箱检查，是否已存在
     * @param $email
     */
    function email_check($email = '', $is_return = 0)
    {
        $ret = TRUE;
        $retNum = 0;
        $data = array();
        $email = $this->input->post('email');
        $is_return = $this->input->post('is_return');
        if (!empty($email))
        {
            $this->load->model('Userinfo');
            $data = $this->Userinfo->fetch_user_by_email($email);
        }
        if (is_array($data) && !empty($data))
        {
            $this->form_validation->set_message('email_check', 'email is exist.');
            $this->errcode = 1001;
            $ret = FALSE;
            $retNum = 1;
        }
        return !$is_return ? $ret : $retNum;
    }
    /**
     * 邮箱检查，是否已存在-New
     * @param $email
     */
    function email_exist()
    {
        $data = array();
        $ret = array('ret'=>0, 'data'=>$data);
        $email = $this->input->post('email');
        if (!empty($email))
        {
            $this->load->model('Userinfo');
            $data = $this->Userinfo->fetch_user_by_email($email);
        }
        if (is_array($data) && !empty($data))
        {
            $ret = array('ret'=>1, 'data'=>$data);
        }
        unset($ret['data']);
        echo json_encode($ret);
        exit;
    }

    function enable($id, $code)
    {
        $ret = array('status'=>false);
        $this->load->model('Invitation');
        $inviteInfo = $this->Invitation->getInviter($code);
        if($inviteInfo!==false)
        {
            list($invitedby, $inviteIntro, $unionId) = $inviteInfo;
            $this->load->model('Userinfo');
            $ret['status'] = $this->Userinfo->enable($id, $invitedby, $inviteIntro);
            //删除该邀请码
            //$this->Invitation->rmCode($code, $inviteInfo);
            //更新邀请码的状态，不再删除
            $this->Invitation->updateCode($code, $inviteInfo);
        }
        echo json_encode($ret);
    }

    function disable($id, $reason='')
    {
        $ret = array('status'=>FALSE);
        $loginRet=true;
        if($id && is_numeric($id) && ($loginRet=$this->auth(TRUE)) == TRUE)
        {
            $this->load->model('Userinfo');
            $idlist = $this->Userinfo->getInviteUsers($this->_userid, $id);
            if($idlist)
            {
                $ret['status'] = $this->Userinfo->disable($idlist, $reason);
                if($ret['status'])
                {
                    $ret['disabled'] = $idlist;
                }
            }
        }
        if($loginRet == FALSE)
        {
            $this->login_error_data['errmsg'] = $this->lang->line('user_login_failed');
            echo json_encode($this->login_error_data);
        }else{
            echo json_encode($ret);
        }
    }


    function authfailed()
    {
        if($this->session)
        {
            $this->session->sess_destroy();
        }
        $this->login_error_data['errmsg'] = $this->lang->line('user_login_failed');
        echo json_encode($this->login_error_data);
        //echo json_encode(array("status"=>false, "errcode"=>-100,"errmsg"=>"please login first!"));
    }

    function count()
    {
        $status = FALSE;
        $total = $today = $card = 0;
        if($this->auth(TRUE))
        {
            $this->load->model('Userinfo');
            $total = $this->Userinfo->total();
            $today = $this->Userinfo->today();
            $card = $this->Userinfo->usercard($this->_userid);
            $status = TRUE;
        }
        $data = array('status'=>$status, 'total'=>$total, 'today'=>$today, 'card'=>$card);
        
        echo json_encode($data);
    }
    /**
     * 获取用户列表(名片页)
     * @param $page
     * @param $pagesize
     * @param $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    function all($page = 1, $pagesize = 10, $timestamp = 0)
    {
        $page = $page > 0 ? $page : $this->input->post('page');
        $pagesize = $pagesize > 0 ? $pagesize : $this->input->post('pagesize');
        $timestamp = $timestamp > 0 ? $timestamp : $this->input->post('timestamp');
        $page = intval($page);
        $pagesize = intval($pagesize);
        if($page < 0) $page = 1;
        if($pagesize < 0) $pagesize = 10;
        $data = array('status' => FALSE);
        if($this->auth(TRUE))
        {
            $this->load->model('Userinfo');
            $data['status'] = TRUE;
            $result = $this->Userinfo->userlist($page, $pagesize, $timestamp, $this->_userid);
            $data['count'] = count($result);
            $data['list'] = $result;
        }
        echo json_encode($data);exit;
    }
    /**
     * 微博登录的回调处理逻辑
     * {"access_token":"2.00MbO7vBsQMq5B75c6d5a748Bjsp4D","remind_in":"645492","expires_in":645492,"uid":"1764636634"}
     */
    public function weiboCallback()
    {
        if (isset($_REQUEST[ACCESS_TOKEN]))
        {
            try
            {
//              $keys = array();
//              $keys['code'] = $_REQUEST['code'];
//              $keys['redirect_uri'] = WEIBO_CALLBACK_URL;
//              $weiboObject = $this->send_weibo_common_api('getAccessToken', array(), TRUE);
//              $token = $weiboObject->getAccessToken('code', $keys);
                
                if (!empty($_REQUEST[ACCESS_TOKEN]))
                {
                    $this->load->model('Userinfo');
                    $userData = $this->Userinfo->fetch_user_by_third($token['uid'], 'object', 'sinaUserId');
                    //$_SESSION['token'] = $token;
                    setcookie('weibojs_'.$weiboObject->client_id, http_build_query($token));
                    $this->Userinfo->afterLogin($userData->id, $userData->nickname, $userData, $token);
                    $ret['userid'] = $userData->id;
                    $ret['nickname'] = $userData->nickname;
                    $ret['email'] = $userData->email;
                    BLH_Utilities::outputSuccess($ret);
                }
            } catch (OAuthException $e) {
            }
        }
        BLH_Utilities::outputError(-1, 'code is empty');
    }
    /**
     * 发布消息时，获取我加入的社团列表
     * 邀请更多人时，显示我加入的社团列表
     */
    public function getMyUnionList()
    {
        if($this->auth(TRUE, TRUE))
        {
            //检查该用户目前已创建的社团信息
            $this->load->model('UserUnion');
            $userUnionList = $this->UserUnion->searchUnionListByUserId($this->_userid);
            BLH_Utilities::outputSuccess(array('data'=>$userUnionList));
        }
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));
    }

    /**
     * 登录页面
     */
    public function login_page()
    {
        include(APPPATH . 'libraries/BLH_BrowserChecker.php');
        $bc = new BLH_BrowserChecker();
        if ($bc->isIOS() OR $bc->isAndroid())
        {
            $isView = 'mobile';
        }else{
            $isView = 'pc';
        }
        $params = array(
            'isView' => $isView,
        );
        $this->load->view('default/login_page', $params);
    }

    public function logout_page()
    {
        if($this->auth(TRUE, TRUE))
        {
            $this->session->sess_destroy();
        }
        BLH_Utilities::showmessage('退出成功，请重新登录', APP_SITE_URL . '/users/login_page');
    }
}
