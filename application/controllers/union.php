<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 社团相关模块
 *
 */
class Union extends BLH_Controller{
    
    public function __construct()
    {
        parent::__construct(false);
    }
    /**
     * 获取所有的社团列表
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp 时间戳，默认不传，传了的话，则查该时间之后的数据
     */
    public function getUnionList($page=1, $pagesize=20, $timestamp=0)
    {
        if($this->auth(TRUE, TRUE))
        {
            $page = max(1, intval($page));
            $pagesize = intval($pagesize);
            if($pagesize < 0) $pagesize = 20;
            
            $ret = array();
            if($page > 0 && $pagesize > 0)
            {
                $data = $this->input->post(NULL, true);
                $unionNameTag = (isset($data['unionName']) && !empty($data['unionName'])) ? $data['unionName'] : '';
                //社团邀请码
                $unionCodeTag = (isset($data['code']) && !empty($data['code'])) ? $data['code'] : '';
                $this->load->model('UnionManage');
                $unionList = $this->UnionManage->getUnionList($page, $pagesize, $timestamp, $unionNameTag, $unionCodeTag);
                if (!empty($unionList))
                {
                    $this->load->model('UserUnion');
                    //获取分类的HashMap信息
                    $this->load->model('Category');
                    $category_list_config = $this->Category->fetch_category_hashmap_data();
                    foreach ($unionList as $unionValue)
                    {
                        $unionUserList = $this->UserUnion->searchUserListByUnionId($unionValue['unionId']);
                        $unionUserTotal = count($unionUserList);
                        $isJoin = 0;
                        $unionMasterName = '';
                        if (!empty($unionUserList))
                        {
                            foreach ($unionUserList as &$unionUserItem)
                            {
                                if (isset($unionUserItem['userid']) && $unionUserItem['userid'] == $this->_userid)
                                {
                                    $isJoin = 1;
                                }
                                //获取该社团的管理员
                                if (isset($unionUserItem['unionRole']) && $unionUserItem['unionRole'] == UserUnion::USER_UNION_ROLE_MASTER_ADMIN)
                                {
                                    $unionMasterName = !empty($unionUserItem['nickname']) ? $unionUserItem['nickname'] : $unionUserItem['email'];
                                }
                            }
                        }
                        unset($unionUserList);
                        $unionIntro = array();
                        if (!empty($unionValue['unionIntro']) && !empty($category_list_config))
                        {
                            $unionIntroList = explode('|', $unionValue['unionIntro']);
                            if (!empty($unionIntroList))
                            {
                                foreach ($unionIntroList as $unionCateId)
                                {
                                    if (isset($category_list_config[$unionCateId]) && !empty($category_list_config[$unionCateId]['cid']))
                                    {
                                        $unionIntro[] = $unionCateId . '_' . $category_list_config[$unionCateId]['cid'] . '|' . $category_list_config[$unionCateId]['cname'];
                                    }
                                }
                            }
                        }
                        $ret[] = array(
                            'unionId' => $unionValue['unionId'],
                            'unionName' => $unionValue['unionName'],//社团名称
                            'unionNick' => $unionValue['unionNick'],//社团简称
                            'companyName' => $unionValue['companyName'],
                            'companyNick' => ($unionValue['unionRole'] == 1) ? (!empty($unionValue['companyNick']) ? $unionValue['companyNick'] : $unionValue['unionName']) : $unionValue['companyNick'],
                            'unionRole' => $unionValue['unionRole'],
                            'unionDesc' => $unionValue['unionDesc'],//简介描述
                            'unionIntro' => $unionIntro,//行业说明
                            'unionSmallLogo' => $unionValue['unionSmallLogo'],//小图
                            'unionBigLogo' => $unionValue['unionBigLogo'],//大图
                            'unionMasterName' => $unionMasterName,//社团管理者昵称
                            'unionCreateTime' => $unionValue['createTime'],//创建该社团的时间
                            'unionUserTotal' => $unionUserTotal,
                            'isJoin' => $isJoin, //是否已加入该社团
                        );
                    }
                }
                BLH_Utilities::outputSuccess(array('data'=>$ret));
            }
            BLH_Utilities::outputError(27004, $this->lang->line('union_params_empty'));
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));//$this->login_error_data['errmsg']
    }
    /**
     * 获取某一个社团里的用户列表
     * @param int $unionId 社团ID
     */
    public function getOneUnionList($unionId)
    {
        if($this->auth(TRUE, TRUE))
        {
            $ret = array();
            if($unionId > 0)
            {
                $this->load->model('UserUnion');
                $unionUserList = $this->UserUnion->searchJoinUserListByUnionId($unionId);
                if (!empty($unionUserList))
                {
                    //检查该用户目前已创建的社团信息
                    $this->load->model('UserUnion');
                    $userUnionList = $this->UserUnion->searchUnionListByUserId($this->_userid, $unionId);
                    if (empty($userUnionList))
                    {
                        $this->load->model('Block');
                        $this->Block->set_table_name('communication');
                    }
                    foreach ($unionUserList as $userValue)
                    {
                        $userUserInfo = array(
                            'userId' => $userValue['userid'],
                            'nickName' => $userValue['nickname'],
                            'iconUrl' => $userValue['iconUrl'],
                            'sex' => $userValue['sex'],
                            'company' => $userValue['company'],
                            'position' => $userValue['position'],
                        );
                        if (empty($userUnionList))
                        {
                            //检查是否已向该成员申请过邀请码
                            $invitePostsData = $this->Block->hasSendInvitePosts($this->_userid, $userValue['userid'], $unionId);
                            $userUserInfo['isApply'] = (is_array($invitePostsData) && !empty($invitePostsData) ? 1 : 0);//是否已向该成员申请过邀请码
                        }else{
                            $userUserInfo['isApply'] = 2;//已加入该社团
                        }
                        $ret[] = $userUserInfo;
                    }
                }
                BLH_Utilities::outputSuccess(array('data'=>$ret));
            }
            BLH_Utilities::outputError(27004, $this->lang->line('union_params_empty'));
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));//$this->login_error_data['errmsg']
    }
    /**
     * 曾就职的企业、社团邀请码搜索
     */
    public function searchUnion()
    {
        if($this->auth(TRUE, TRUE))
        {
            $data = $this->input->post(NULL, true);
            $searchTag = (isset($data['unionName']) && !empty($data['unionName'])) ? $data['unionName'] : '';
            //根据企业名称搜索社团
            if (!empty($searchTag) && !is_numeric($searchTag))
            {
                //$this->load->model('CompanyUnion');
                //$result = $this->CompanyUnion->searchUnionByCompany($searchTag);
                $this->load->model('UnionManage');
                $result = $this->UnionManage->searchUnionListByUnionName($searchTag);
                BLH_Utilities::outputSuccess(array('data'=>$result));
            }
            BLH_Utilities::outputError(27004, 'params union is error');
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));//$this->login_error_data['errmsg']
    }
    /**
     * 创建社团
     * 建社团时，填公司名，然后形成xx老同事作为社团名，
     * 社团会有两个名字。第一个是“公司名+老同事”，这个只要建立了社团就有；
     * 百老汇这样的名字属于认证的时候填。用户自己新建社团时就不用填了，减低用户负担
     */
    public function createUnion()
    {
        if($this->auth(TRUE, TRUE))
        {
            if (empty($this->_user_data->blhRole) OR $this->_user_data->blhRole != BLH_Controller::UNION_ROLE_SYSTEM)
            {
                BLH_Utilities::outputError(27009, 'No permission resources.');
            }
            $data = $this->input->post(NULL, TRUE);
            $companyName = $data['companyName'];
            $unionName = $companyName . APP_SITE_NAME;//$data['unionName'];
            if (!empty($unionName) && !empty($companyName))
            {
                //社团名称
                if (!is_numeric($unionName) && !is_numeric($companyName))
                {
                    try {
                        $unionName = trim($unionName);
                        $companyName = trim($companyName);
                        if (isset($this->_user_data) && !empty($this->_user_data))
                        {
                            $user_data = $this->_user_data;
                        }
                        $this->load->model('UnionManage');
                        $ret = $this->UnionManage->createUnionProcess($this->_userid, $unionName, $companyName, $data, $user_data);
                        BLH_Utilities::outputResult($ret);
                        exit;
                    }catch(Exception $ex){
                        BLH_Utilities::outputError(27007, $this->lang->line('union_create_failed'));
                    }
                }
            }
            BLH_Utilities::outputError(27008, $this->lang->line('union_company_name_error'));
        }
        //尚未登录或登录失效
        BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));//$this->login_error_data['errmsg']
    }

}
