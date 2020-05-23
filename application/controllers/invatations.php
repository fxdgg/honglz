<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invatations extends BLH_Controller{
    public function __construct()
    {
        parent::__construct(FALSE);
    }
    
    public function getCode()
    {
        $data = $_POST;
        $code = $data['invitationcode'];
        $this->load->model("Invitation");
    }
    /**
     * 向社团内成员求邀请码（ppt-9）
     * 1.更新该登录用户的头像、昵称、性别、公司、职位等信息
     * 2.向supportCodeUserId的社团成员UserId发送消息
     */
    public function exploreInviteCode()
    {
        if($this->auth(TRUE, TRUE))
        {
            $data = $this->input->post(NULL, TRUE);
            if(count($data) > 0)
            {
                //if (!empty($data['supportCodeUserId']) && !empty($data['unionId']))
                //{
                    $this->load->model('UserUnion');
                    //社团ID
                    $unionId = $data['unionId'];
                    //向supportCodeUserId的社团成员UserId发送消息
                    //$supportCodeUserId = $data['supportCodeUserId'];
                    //检查该用户是否已加入社团
                    $currentUserUnionList = $this->UserUnion->searchUnionListByUserId($this->_userid, $unionId);
                    if (!empty($currentUserUnionList))
                    {
                        BLH_Utilities::outputError(-4, 'you has in union');
                    }
                    //检查被邀请人是否已加入该社团
                    /*$inviteUserUnionList = $this->UserUnion->searchUnionListByUserId($supportCodeUserId, $unionId);
                    if (empty($inviteUserUnionList))
                    {
                        BLH_Utilities::outputError(-5, 'he is not in the union');
                    }*/
                    //获取当前用户的信息
                    $this->load->model('Userinfo');
                    if (isset($this->_user_data) && !empty($this->_user_data))
                    {
                        $user_data = (array)$this->_user_data;
                    }else{
                        $user_data = $this->Userinfo->info($this->_userid, $this->_userid);
                    }
                    //是否社团邀请码申请进入
                    //if (isset($data['code']) && !empty($data['code']))
                    //{
                        $unionCode = isset($data['code']) ? $data['code'] : '';
                        //插入社团用户表
                        $inviteInfo = array(0, '', $unionId);
                        $id = $this->UserUnion->createUnionUser($this->_userid, $inviteInfo, FALSE, UserUnion::USER_UNION_ROLE_MEMBER, $unionCode);
                        if (!$id)
                        {
                            BLH_Utilities::outputError(-9, 'join union user failed');
                        }
                    /*}else{
                        $this->load->model('Block');
                        $this->Block->set_table_name('communication');
                        //检查是否多次重复发送
                        $invitePostsData = $this->Block->hasSendInvitePosts($this->_userid, $supportCodeUserId, $unionId);
                        if (is_array($invitePostsData) && !empty($invitePostsData))
                        {
                            $this->load->model('Invitation');
                            //检查邀请码表中是否已经有同意/拒绝的记录
                            $inviteCodeRet = $this->Invitation->hasNewInviterCode($this->_userid, $supportCodeUserId, $unionId);
                            if (!$inviteCodeRet)
                            {
                                BLH_Utilities::outputError(-6, 'you has send to the user');
                            }
                        }
                        //获取申请加入的社团ID的详细信息
                        $this->load->model('UnionManage');
                        $currentUnionInfo = $this->UnionManage->getUnionById($unionId);
                        $currentUnionName = !empty($currentUnionInfo['unionName']) ? $currentUnionInfo['unionName'] : '社团';
                        //[寻邀请码]的舶来账号
                        $user_sys_invite_id = $this->config->item('USER_SYS_INVITE', 'system_accounts');
                        //欢迎新人的模版列
                        $welcome_templates = $this->config->item('welcome_templates');
                        $posts_data = array(
                            'unionId' => $unionId,
                            'userid' => $user_sys_invite_id,
                            'receiveUserId' => $supportCodeUserId,
                            'title' => 'communication',
                            'content' => sprintf($welcome_templates[$user_sys_invite_id], $data['nickname'], $data['company'], $data['position'], $currentUnionName),
                            'remark' => $this->_userid,
                            'type' => Block::SYSTEM_TYPE_STATUS,
                        );
                        $id = $this->Block->create($posts_data);
                    }*/
                    if(!$id)
                    {
                        BLH_Utilities::outputError(-3, '操作失败，请重试');
                    }else{
                        $user_post = $ret = array();
                        $category_type_popu = '1'; //知名度
                        $category_type_area = '01'; //地区
                        $category_type_posi = '1'; //职级
                        $category_type_voca = '001'; //行业类别
                        //更新登录者的信息
                        if (empty($user_data['nickname']) && !empty($data['nickname']))
                        {
                            $user_post['nickname'] = $data['nickname'];
                        }
                        if (empty($user_data['sex']) && !empty($data['sex']))
                        {
                            $user_post['sex'] = $data['sex'];
                        }
                        if (empty($user_data['iconUrl']) && !empty($data['iconUrl']))
                        {
                            $user_post['iconUrl'] = $data['iconUrl'];
                        }
                        //所在公司名称
                        if (empty($user_data['company']) && !empty($data['company']))
                        {
                            $user_post['company'] = $data['company'];
                        }
                        //所在区域、地区
                        if (empty($user_data['area']) && !empty($data['area']))
                        {
                            $user_post['area'] = $data['area'];
                            //获取分类的HashMap信息
                            $this->load->model('Category');
                            $category_list_config = $this->Category->fetch_category_group_data();
                            if (!empty($category_list_config['area']))
                            {
                                foreach ($category_list_config['area'] as $areaItem)
                                {
                                    if (!empty($areaItem['cname']) && strpos($user_post['area'], $areaItem['cname']) !== FALSE)
                                    {
                                        $category_type_area = $areaItem['cid'];
                                        break;
                                    }
                                }
                            }
                        }
                        //职位
                        if (empty($user_data['position']) && !empty($data['position']))
                        {
                            $user_post['position'] = $data['position'];
                        }
                        //qq
                        if (empty($user_data['qq']) && !empty($data['qq']))
                        {
                            $user_post['qq'] = $data['qq'];
                        }
                        //行业
                        if (empty($user_data['vocation']) && !empty($data['vocation']))
                        {
                            //获取分类的HashMap信息
                            $this->load->model('Category');
                            $category_list_config = $this->Category->fetch_category_hashmap_data();
                            list($cate_id, $cate_cid) = explode('_', $data['vocation']); //格式:5_021
                            //根据传上来的cate_id来查找分类名称
                            if (!empty($category_list_config[$cate_id]['cname']))
                            {
                                $user_post['vocation'] = $category_list_config[$cate_id]['cname'];
                            }
                            //检查传上来的cate_cid是否与数据表的cid一致
                            if (!empty($category_list_config[$cate_id]['cid']) && $category_list_config[$cate_id]['cid']== $cate_cid)
                            {
                                $category_type_voca = $category_list_config[$cate_id]['cid'];
                            }
                        }
                        //工作时间
                        if (empty($user_data['workDate']) && !empty($data['workDate']))
                        {
                            $user_post['workDate'] = $data['workDate'];
                        }
                        //毕业学校
                        if (empty($user_data['gradeSchool']) && !empty($data['gradeSchool']))
                        {
                            $user_post['gradeSchool'] = $data['gradeSchool'];
                        }
                        //用户的职业分类
                        $user_post['category'] = $category_type_popu . '|' . $category_type_area . '|' . $category_type_posi . '|' . $category_type_voca;;
                        
                        $status = !empty($user_post) && $this->Userinfo->edit($this->_userid, $user_post);
                        if($status)
                        {
                            $user_data_session = $this->session->userdata('user_data');
                            if (!empty($user_data))
                            {
                                !empty($user_post['nickname']) && $user_data_session->nickname = $user_post['nickname'];
                                !empty($user_post['sex']) && $user_data_session->sex = $user_post['sex'];
                                !empty($user_post['iconUrl']) && $user_data_session->iconUrl = $user_post['iconUrl'];
                                !empty($user_post['company']) && $user_data_session->company = $user_post['company'];
                                !empty($user_post['position']) && $user_data_session->position = $user_post['position'];
                                !empty($user_post['qq']) && $user_data_session->qq = $user_post['qq'];
                                !empty($user_post['vocation']) && $user_data_session->vocation = $user_post['vocation'];
                                !empty($user_post['workDate']) && $user_data_session->workDate = $user_post['workDate'];
                                !empty($user_post['gradeSchool']) && $user_data_session->gradeSchool = $user_post['gradeSchool'];
                                !empty($user_post) && $this->session->set_userdata('user_data', $user_data_session);
                            }
                            !empty($user_post['nickname']) && $this->session->set_userdata('nickname', $user_post['nickname']);
                            
                            //把用户状态设置为激活状态(用户基本信息模块)
                            $this->Userinfo->updateUserStatus($this->_userid, $this->Userinfo->USER_STATUS_OK);
                        }
                        //返给前端
                        $ret['id'] = $id;
                        BLH_Utilities::outputSuccess($ret);
                    }
                //}
                //BLH_Utilities::outputError(-2, 'supportCodeUserId or unionId is empty');
            }
            BLH_Utilities::outputError(-1, 'params is empty');
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->login_error_data['errmsg']);
    }
    
    /**
     * 在[舶来]页面，领取[群福利]（ppt-38）
     */
    public function receiveGroupAward()
    {
        if($this->auth(TRUE, TRUE))
        {
            $data = $this->input->post(NULL, TRUE);
            if(count($data) > 0)
            {
                if (!empty($data['id'])) 
                {
                    //领取[群福利]的逻辑
                    $this->load->model('UserAward');
                    //[舶来]的群福利
                    $data['block'] = UserAward::GROUP_AWARD_DEFAULT_BLOCK;
                    //判断该block是否存在
                    /*$this->load->model('Userposts');
                    if (!in_array(($blockName), $this->Userposts->blockLists))
                    {
                        BLH_Utilities::outputError(-1, 'Invalid BlockName ' . $blockName);
                    }*/
                    //检查该ID是否合法
                    $this->load->model('Block');
                    $this->Block->set_table_name($data['block']);
                    $welcomePosts = $this->Block->fetchSimpleById($data['id']);
                    //没有该记录或该记录不是群福利的记录或该记录已过期
                    if (empty($welcomePosts) OR $welcomePosts['receiveUserId'] != Block::GROUP_AWARD_WELCOME_STATUS 
                        OR empty($welcomePosts['remark']) OR strtotime($welcomePosts['remark']) < SYS_TIME)
                    {
                        BLH_Utilities::outputError(-4, 'id is invalid');
                    }
                    //检查该用户是否已经领取过了
                    $userGroupRet = $this->UserAward->getUserJoinGroupAward($this->_userid, $data['id']);
                    if (!empty($userGroupRet))
                    {
                        BLH_Utilities::outputError(-5, 'you has receive the award');
                    }
                    $receiveGroupRet = $this->UserAward->receiveGroupAwardProcess($this->_userid, $data, TRUE);
                    if(!$receiveGroupRet)
                    {
                        BLH_Utilities::outputError(-3, '操作失败，请重试');
                    }else{
                        $ret = array();
                        $ret['id'] = $receiveGroupRet;
                        BLH_Utilities::outputSuccess($ret);
                    }
                }
                BLH_Utilities::outputError(-2, 'id is empty');
            }
            BLH_Utilities::outputError(-1, 'params is empty');
         }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->login_error_data['errmsg']);
     }
}
