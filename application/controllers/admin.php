<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends BLH_Controller {
    public $common_data = array('title'=>APP_SITE_NAME_BACKEND);
    /**
     * 普通用户
     * @var int
     */
    const UNION_ROLE_COMMON = 0;
    /**
     * 社团管理员
     * @var int
     */
    const UNION_ROLE_UNION = 1;
    /**
     * 舶来管理员
     * @var int
     */
    const UNION_ROLE_WELCOME = 2;
    /**
     * 系统管理员
     * @var int
     */
    const UNION_ROLE_SYSTEM = 3;
    /**
     * 用户在社团里的状态配置
     * @var array
     */
    public static $unionRoleConfig = array(
        self::UNION_ROLE_UNION,
        self::UNION_ROLE_WELCOME,
        self::UNION_ROLE_SYSTEM,
    );
    /**
     * cookie名称-关键词-岗位职责列表、任职资格列表
     * @var string
     */
    public static $cookie_key_kw_pdid = 'jd_kw_pdid';
    /**
     * cookie名称-关键词-岗位职责列表、任职资格列表-添加关键字
     * @var string
     */
    public static $cookie_key_kw_pdid_detail = 'jd_kw_pdid_detail';

    /**
     * 性别对应关系
     * @var array
     */
    private static $gender_config_map = array(
        '男' => 1,
        '女' => 2,
    );

    /**
     * 当前状态对应关系
     * @var array
     */
    private static $state_config_map = array(
        '离职' => 0,
        '在职' => 1,
    );

    /**
     * 是否考虑过创业公司对应关系
     * @var array
     */
    private static $innovate_config_map = array(
        '是' => 1,
        '否' => 2,
    );

    /**
     * 简历状态对应关系
     * @var array
     */
    private static $isFindJobConfig = array(
        1 => '待企业反馈意见',
        2 => '待约面试时间',
        3 => '面试进行中',
        4 => '意向撮合',
        5 => '完成',
        6 => '企业无反馈',
    );

    public function __construct()
    {
        parent::__construct(false);
    }
    /**
     * 后台管理系统登录
     */
    public function login()
    {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $data = array(
            'title' => $this->common_data['title'],
            'r' => $ref,
        );
        $this->load->view('admin/admin_login', $data);
    }
    /**
     * Ajax登录管理后台
     */
    public function login_check_login()
    {
        $data = $this->input->post(NULL, TRUE);
        $account = $data['account'];
        $password = $data['password'];
        $check_code = $data['check_code'];
        $this->load->library('session');
        $this->load->model('Userinfo');
        $ret = $this->Userinfo->checkUserPass($account, $password);
        $admin_auth_code = $this->session->userdata('admin_auth_code');
        if (strcasecmp($admin_auth_code, $check_code) != 0)
        {
            exit('check_code_error');
        }
        $this->session->unset_userdata('admin_auth_code');
        if ($ret != FALSE && is_array($ret))
        {
            //获取允许登录的管理员账号
            /*$login_account_list = $this->config->item('login_account_list', 'system_admin_config');
            if (!empty($login_account_list) && !in_array($ret['email'], $login_account_list))
            {
                exit('platform_server_refuse');
            }*/
            //必须是管理员、状态为正常
            if ($ret['regState'] == 1 && in_array($ret['blhRole'], self::$unionRoleConfig))
            {
                //社团管理员的话，需要验证是否是某社团的管理员
                if ($ret['blhRole'] == 1)
                {
                    $this->load->model('UserUnion');
                    $userUnionInfo = $this->UserUnion->searchUnionListByUserId($ret['id'], 0, array(UserUnion::USER_UNION_ROLE_MASTER_ADMIN, UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
                    if (empty($userUnionInfo))
                    {
                        exit('platform_server_refuse');
                    }
                }
                $this->Userinfo->afterLogin($ret['id'], $ret['nickname'], $ret['user_data']);
                exit('success');
            }
            exit('account_refuse');
        }
        exit('login_fail');
    }
    //top页面使用
    public function login_check_jump_login()
    {
        $data = $this->input->post(NULL, TRUE);

    }
    //Js跳转到某链接
    public function jsJumpUrl($url)
    {
        BLH_Utilities::redirect($url, 0, true, true, false, true);
    }
    public function index_top()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //判断权限
        //校验用户身份
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_WELCOME)
        {
            //舶来账号
            $special_tips = '【舶来管理员】';
        }elseif (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_UNION)
        {
            //普通用户-社团管理员
            //获取当前登录用户的社团ID，并验证是否管理员
            $this->load->model('UserUnion');
            $userUnionInfo = $this->UserUnion->searchUnionListByUserId($this->_userid, 0, array(UserUnion::USER_UNION_ROLE_MASTER_ADMIN, UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
            if (empty($userUnionInfo))
            {
                $special_tips = '';
            }else{
                $special_tips = '【'.$userUnionInfo['unionName'].'】-社团管理员';
            }
        }elseif (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM){
            //系统管理员
            $special_tips = '【系统管理员】';
        }else{
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $data = array(
            'title' => $this->common_data['title'],
            'special_tips' => $special_tips,
            'account' => $user_data->nickname,
            'time' => SYS_TIME,
            'key' => '',
            'sign' => '',
            'lang' => 'zh_CN',
        );
        $this->load->view('admin/admin_index_top', $data);
    }
    public function index_left()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $menus = include APPPATH.'helpers/admin_group_helper.php';
        //$menus = $this->load->helper('admin_group');
        if ($menus == FALSE)
        {
            $menus = array();
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //非管理员
        $viewManagerMenu = 0;
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_WELCOME)
        {
            $menuType = 3;//舶来账号
        }elseif (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_UNION)
        {
            $menuType = 2;//普通用户-社团管理员
        }elseif (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM){
            $menuType = 10;//系统管理员
        }else{
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->view('admin/admin_index_left', array('menus'=>$menus, 'menuType'=>$menuType, 'viewManagerMenu'=>$viewManagerMenu));
    }
    public function index_right()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->view('admin/admin_index_right', $this->common_data);
    }
    /**
     * 后台登录验证码
     */
    public function check_code()
    {
        $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Checkcode' . '.php';
        BLH_Utilities::require_only($class_file);
        $checkcode = new BLH_Checkcode();
        $checkcode->width = 90;
        $checkcode->height = 50;
        $checkcode->doimage();
        $this->load->library('session');
        $_code = $checkcode->get_code();
        $this->session->set_userdata(array('admin_auth_code'=>$_code));
    }
    /**
     * 后台管理首页
     */
    public function index()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        /*if (isset($_GET['r']) && !empty($_GET['r']))
        {
            BLH_Utilities::redirect($_GET['r']);
        }*/
        $this->load->view('admin/admin_index', $this->common_data);
    }
    public function logout()
    {
        $this->load->library('session');
        $this->session->sess_destroy();
        $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
    }
    /**
     * 用户列表
     */
    public function user_show_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $page = max(1, $this->input->get('page'));
        $pagesize = max(30, $this->input->get('pagenum'));
        $page = intval($page);
        $pagesize = intval($pagesize);
        //获取当前登录用户的社团ID，并验证是否管理员
        $this->load->model('UserUnion');
        $userUnionInfo = $this->UserUnion->searchUnionListByUserId($this->_userid, 0, array(UserUnion::USER_UNION_ROLE_MASTER_ADMIN, UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
        if (empty($userUnionInfo))
        {
            $userList = array();$userTotal = 0;
        }else{
            $this->load->model('Userinfo');
            $userList = $this->Userinfo->allUserList($page, $pagesize, $userUnionInfo['unionId']);
            $userTotal = $this->Userinfo->allUserListTotal($userUnionInfo['unionId']);
        }
        //分页显示
        $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
        BLH_Utilities::require_only($class_file);
        $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$userTotal));
        $params = array(
            'title' => $this->common_data['title'],
            'user_list' => $userList,
            'page' => $Pager->show(),
        );
        $this->load->view('admin/admin_user_list', $params);
    }
    /**
     * 总用户列表
     */
    public function user_show_list_all()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $page = max(1, $this->input->get('page'));
        $pagesize = max(30, $this->input->get('pagenum'));
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Userinfo');
            $userList = $this->Userinfo->allUserList($page, $pagesize);
            $userTotal = $this->Userinfo->allUserListTotal();
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$userTotal));
            $params = array(
                'title' => $this->common_data['title'],
                'user_list' => $userList,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_user_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 获取某用户的信息
     */
    public function user_show_info($userId = 0)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $unionId = -1;
        $isAdmin = 0;
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $isAdmin = 1;
        }

        if ($userId <= 0 OR !is_numeric($userId))
        {
            $userinfo = array();
        }else{
            $this->load->model('Userinfo');
            if ($unionId == -1)
            {
                //获取当前登录用户的社团ID，并验证是否管理员
                $this->load->model('UserUnion');
                $userUnionInfo = $this->UserUnion->searchUnionListByUserId($this->_userid, 0, array(UserUnion::USER_UNION_ROLE_MASTER_ADMIN, UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
                if (empty($userUnionInfo))
                {
                    $unionId = 0;
                }else{
                    $unionId = $userUnionInfo['unionId'];
                }
            }
            if ($isAdmin) $unionId = 0;
            $userinfo = $this->Userinfo->allUserList(1, 1, $unionId, $userId);
        }
        if (empty($userinfo))
        {
            BLH_Utilities::showmessage('该用户尚未加入该社团，信息为空');
        }

        if ($userId > 0 && $userId != $this->_userid && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $post_data = $this->input->post(NULL, TRUE);
            $this->load->model('Userinfo');
            if (isset($post_data['status']) && in_array($post_data['status'], array($this->Userinfo->USER_STATUS_REGISTER,$this->Userinfo->USER_STATUS_OK,$this->Userinfo->USER_STATUS_STOPPED)))
            {
                $this->Userinfo->edit($userId, array('status'=>$post_data['status']));
            }
            if (isset($post_data['is_kick']) && !empty($post_data['is_kick']) && !$isAdmin)
            {
                //获取当前登录用户的社团ID，并验证是否管理员
                $this->load->model('UserUnion');
                $userUnionInfo = $this->UserUnion->searchUnionListByUserId($this->_userid, 0, array(UserUnion::USER_UNION_ROLE_MASTER_ADMIN, UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
                if (!empty($userUnionInfo))
                {
                    $unionId = $userUnionInfo['unionId'];
                    $this->load->model('UserUnion');
                    //获取发送邮件的配置
                    $email_config = $this->config->item('email_config');
                    if ($post_data['is_kick'] == 1)
                    {
                        //社团状态-正常+发送邮件
                        $userUnionRole = UserUnion::USER_UNION_ROLE_MEMBER;
                        $email_tag = 'email_content_succ';
                    }else if ($post_data['is_kick'] == 2)
                    {
                        //社团状态-禁用+发送邮件
                        $userUnionRole = UserUnion::USER_UNION_ROLE_DELETE;
                        $email_tag = 'email_content_fail';
                    }
                    $this->UserUnion->updateUnionUserRole($userId, $unionId, $userUnionRole);
                    if (!empty($userinfo['email']))
                    {
                        //发送邮件
                        //require(APPPATH . 'libraries/BLH_SinaMail.php');
                        //require(APPPATH . 'libraries/BLH_PhpMailer.php');
                        $title = $email_config['union_config'][$unionId]['email_title'];
                        $body = $email_config['union_config'][$unionId][$email_tag];
                        $ret = FALSE;
                        if (!empty($title) && !empty($body))
                        {
                            $ret = $this->sendEmail($userinfo['email'], $title, $body, $email_config['email_account_config']['FromName']);
                            //$BLH_SinaMail = new BLH_SinaMail($email_config['email_account_config']['UserName'],$email_config['email_account_config']['Password'],$email_config['email_account_config']['From'],$email_config['email_account_config']['FromName'],$email_config['email_account_config']['SMTPServer']);
                            //$BLH_SinaMail->CharSet = "utf-8";
                            //$BLH_SinaMail->send($title, array($userinfo['email']), $body, NULL, TRUE);
                        }
                        $msg = $ret ? '审核并发送邮件成功' : '审核成功，邮件发送失败，请重试';
                        BLH_Utilities::showmessage($msg, APP_SITE_URL . '/admin/user_show_info/'.$userId);
                    }else{
                        BLH_Utilities::showmessage('该用户的邮箱为空，不能发送邮件，请重试', APP_SITE_URL . '/admin/user_show_info');
                    }
                }
            }
        }
        $params = array(
            'title' => $this->common_data['title'],
            'userinfo' => $userinfo,
            'isAdmin' => $isAdmin,
        );
        $this->load->view('admin/admin_user_show_info', $params);
    }
    /**
     * 社团列表
     */
    public function union_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $page = max(1, $this->input->get('page'));
        $pagesize = max(30, $this->input->get('pagenum'));
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('UnionManage');
            $union_list = $this->UnionManage->getOnlyUnionListAdmin($page, $pagesize);
            $union_total = $this->UnionManage->getOnlyUnionListAdminTotal();
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$union_total));
            $params = array(
                'title' => $this->common_data['title'],
                'union_list' => $union_list,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_union_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 社团详细列表
     */
    public function union_detial_info($unionId)
    {

        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if ($unionId > 0 && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                $this->load->model('UnionManage');
                if (!empty($post_data['unionName']) OR ($post_data['unionStatus'] != -1))
                {
                    $this->UnionManage->updateUnion($unionId, $post_data['unionName'], $post_data['unionStatus'], $post_data['unionNick']);
                }
            }
            if ($unionId <= 0 OR !is_numeric($unionId))
            {
                $union_info = array();
            }else{
                $this->load->model('UnionManage');
                $union_info = $this->UnionManage->getUnionById($unionId, 'admin');
            }
            $params = array(
                'title' => $this->common_data['title'],
                'union_info' => $union_info,
            );
            $this->load->view('admin/admin_union_detail_info', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 公司-社团关系表
     */
    public function company_union_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $page = max(1, $this->input->get('page'));
        $pagesize = max(30, $this->input->get('pagenum'));
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('CompanyUnion');
            $this->load->model('UnionManage');
            $companyUnionList = $this->CompanyUnion->searchUnionList('', 0, 'admin', $page, $pagesize);
            $companyUnionTotal = $this->CompanyUnion->searchUnionListTotal('', 0, 'admin');
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$companyUnionTotal));
            $params = array(
                'title' => $this->common_data['title'],
                'company_union_list' => $companyUnionList,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_company_union_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 公司-社团详细信息
     */
    public function company_detail_info($companyId)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if ($companyId > 0 && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (!empty($post_data['companyName']) OR !empty($post_data['companyNick'])
                    OR !empty($post_data['companySimple']) OR $post_data['unionRole'] != -1)
                {
                    $this->load->model('CompanyUnion');
                    $this->CompanyUnion->updateCompanyUnion($companyId, $post_data['companyName'], $post_data['companySimple'], $post_data['companyNick'], $post_data['unionRole']);
                }
            }
            if ($companyId <= 0 OR !is_numeric($companyId))
            {
                $company_info = array();
            }else{
                $this->load->model('CompanyUnion');
                $this->load->model('UnionManage');
                $company_info = $this->CompanyUnion->getOneCompanyUnionInfo($companyId);
            }
            $params = array(
                'title' => $this->common_data['title'],
                'company_info' => $company_info
            );
            $this->load->view('admin/admin_company_detail_info', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 荔枝币流水
     */
    public function money_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $page = max(1, $this->input->get('page'));
        $pagesize = max(30, $this->input->get('pagenum'));
        //获取当前登录用户的社团ID，并验证是否管理员
        $this->load->model('UserUnion');
        $userUnionInfo = $this->UserUnion->searchUnionListByUserId($this->_userid, 0, array(UserUnion::USER_UNION_ROLE_MASTER_ADMIN, UserUnion::USER_UNION_ROLE_SECONDARY_ADMIN));
        if (empty($userUnionInfo))
        {
            $money_list = array();$money_total = 0;
        }else{
            $this->load->model('UserChat');
            $money_list = $this->UserChat->getUserChatByUnionId($userUnionInfo['unionId'], $page, $pagesize);
            $money_total = $this->UserChat->getUserChatByUnionIdTotal($userUnionInfo['unionId']);
        }
        $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
        BLH_Utilities::require_only($class_file);
        $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$money_total));
        $params = array(
            'title' => $this->common_data['title'],
            'money_list' => $money_list,
            'page' => $Pager->show(),
        );
        $this->load->view('admin/admin_money_list', $params);
    }
    /**
     * 总荔枝币流水
     */
    public function money_list_all()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $money_list = array();
        $money_total = 0;
        $page = max(1, $this->input->get('page'));
        $pagesize = max(30, $this->input->get('pagenum'));
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('UserChat');
            $money_list = $this->UserChat->getUserChatByUnionId(0, $page, $pagesize, 'admin');
            $money_total = $this->UserChat->getUserChatByUnionIdTotal(0, 'admin');

            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$money_total));
            $params = array(
                'title' => $this->common_data['title'],
                'money_list' => $money_list,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_money_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 舶来录入
     */
    public function welcome_insert()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_WELCOME)
        {
            $unionManageList = $message = array();
            //获取社团列表
            $this->load->model('UnionManage');
            $allUnionList = $this->UnionManage->getAllUnionListAdmin(array(UnionManage::UNION_STATUS_UNAUTH_TMP, UnionManage::UNION_STATUS_UNAUTH_VALID, UnionManage::UNION_STATUS_AUTH_VALID));
            if (is_array($allUnionList) && !empty($allUnionList))
            {
                foreach ($allUnionList as $unionItem)
                {
                    $unionManageList[$unionItem['unionId']] = $unionItem['unionName'];
                }
            }
            //默认的群福利截止时间
            $default_activity_endtime = date('Y-m-d 00:00:00', strtotime('+8 days'));
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                //处理舶来类型
                $group_id = isset($post_data['group_id']) && !empty($post_data['group_id']) ? (int)$post_data['group_id'] : 0;
                switch ($group_id)
                {
                    case 2: //招聘
                        $message[] =  '招聘信息-操作非法';
                        break;
                    case 0: //文字类
                    case 1: //群福利
                    default:
                        if (!isset($post_data['check_union_ids']) OR !is_array($post_data['check_union_ids']) OR empty($post_data['check_union_ids']))
                        {
                            BLH_Utilities::showmessage('请选择要发布的社团');
                        }
                        if (!isset($post_data['content']) OR empty($post_data['content']))
                        {
                            BLH_Utilities::showmessage('请输入舶来内容');
                        }
                        //奖品通知的舶来UID
                        $user_system_send_award = $this->config->item('USER_SYS_SEND_AWARD', 'system_accounts');
                        //奖品通知的模版标题
                        $welcome_titles = $this->config->item('welcome_titles');
                        if (!empty($post_data['content'])) {
                            if (get_magic_quotes_gpc()) {
                                $htmlContent = stripslashes($post_data['content']);
                            } else {
                                $htmlContent = $post_data['content'];
                            }
                        }
                        //$htmlContent = trim(strip_tags($post_data['content']));
                        $this->load->model('Block');
                        $this->Block->init('welcomeblockpost');
                        $welcomePost = array(
                            'title' => (!empty($user_data->nickname) ? $user_data->nickname : $welcome_titles[$user_system_send_award]),
                            'content' => $htmlContent,
                            'userid' => $this->_userid,
                        );
                        if (isset($post_data['group_id']) && !empty($post_data['group_id']))
                        {
                            $welcomePost['receiveUserId'] = $post_data['group_id'];
                            $welcomePost['remark'] = !empty($post_data['activity_end_time']) ? $post_data['activity_end_time'] : $default_activity_endtime;
                        }
                        foreach ($post_data['check_union_ids'] as $unionId)
                        {
                            $welcomePost['unionId'] = (int)$unionId;
                            if (!empty($unionManageList[$unionId]))
                            {
                                $welcomePost['unionName'] = $unionManageList[$unionId];
                            }
                            $ret = $this->Block->create($welcomePost);
                            if ((is_array($ret) && !empty($ret)))
                            {
                                $message[] = (is_array($unionManageList) && !empty($unionManageList)) ? "[{$unionManageList[$unionId]}]操作成功" : '操作成功';
                            }else{
                                $message[] = (is_array($unionManageList) && !empty($unionManageList)) ? "[{$unionManageList[$unionId]}]操作失败" : '操作失败';
                            }
                        }
                        break;
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/welcome_insert');
            }else{
                //获取分类的分组列表
                $this->load->model('Category');
                $category_list_config = $this->Category->fetch_category_group_data();
                $params = array(
                    'title' => $this->common_data['title'],
                    'data' => array(),
                    'unionManageList' => $unionManageList,
                    'category_list_config' => $category_list_config,
                    'activity_end_time' => $default_activity_endtime,
                );
                $this->load->view('admin/admin_welcome_insert', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 招聘录入
     */
    public function recruit_insert()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_WELCOME)
        {
            $unionManageList = $message = array();
            //获取社团列表
            $this->load->model('UnionManage');
            $allUnionList = $this->UnionManage->getAllUnionListAdmin(array(UnionManage::UNION_STATUS_UNAUTH_TMP, UnionManage::UNION_STATUS_UNAUTH_VALID, UnionManage::UNION_STATUS_AUTH_VALID));
            if (is_array($allUnionList) && !empty($allUnionList))
            {
                foreach ($allUnionList as $unionItem)
                {
                    $unionManageList[$unionItem['unionId']] = $unionItem['unionName'];
                }
            }
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                //处理舶来类型
                $group_id = isset($post_data['group_id']) && !empty($post_data['group_id']) ? (int)$post_data['group_id'] : 0;
                switch ($group_id)
                {
                    case 2: //招聘
                        if (!isset($post_data['content']) OR empty($post_data['content']))
                        {
                            BLH_Utilities::showmessage('请输入招聘内容');
                        }
                        $this->load->model('Category');
                        //舶来内容
                        if (!empty($post_data['content'])) {
                            //echo '<pre>content_init=>'.$post_data['content'];echo '<br />';
                            //$post_data['content'] = str_replace(array("&nbsp;"), " ", $post_data['content']);
                            //$post_data['content'] = str_replace(array("<p>", "</p>"), "", $post_data['content']);
                            //$post_data['content'] = str_replace(array("<br>", "<br />", "<br/>"), "\n", $post_data['content']);
                            //echo 'content_after=>'.$post_data['content'];echo '<br />';
                            if (get_magic_quotes_gpc()) {
                                $htmlContent = stripslashes($post_data['content']);
                            } else {
                                $htmlContent = $post_data['content'];
                            }
                        }
                        /*$htmlContent2 = trim(strip_tags($post_data['content']));
                        preg_match_all("|<[^>]+>(.*)</[^>]+>|U", $post_data['content'], $out, PREG_PATTERN_ORDER);
                        preg_match_all("|<img src=\"(.*)\"|U", $post_data['content'], $out2, PREG_PATTERN_ORDER);
                        echo 'content=>'.$post_data['content'];echo '<br />';
                        echo 'htmlContent=>'.$htmlContent;echo '<br />';
                        echo 'htmlContent2=>'.$htmlContent2;echo '<br />';
                        echo '<pre>out=>';var_dump($out);echo '<br />';
                        echo '<pre>out2=>';var_dump($out2);echo '<br />';
                        exit;*/
                        //$htmlContent = trim(strip_tags($post_data['content']));
                        //知名度
                        $category_type_popu = (!empty($post_data[Category::$category_type_popu]) && (int)$post_data[Category::$category_type_popu] > 0) ? (int)$post_data[Category::$category_type_popu] : 1;
                        //地区
                        $category_type_area = (!empty($post_data[Category::$category_type_area]) && $post_data[Category::$category_type_area] != -1) ? $post_data[Category::$category_type_area] : '01';
                        //职级
                        $category_type_posi = (!empty($post_data[Category::$category_type_posi]) && (int)$post_data[Category::$category_type_posi] > 0) ? (int)$post_data[Category::$category_type_posi] : 1;
                        //行业类别
                        $category_type_voca = (!empty($post_data[Category::$category_type_voca]) && $post_data[Category::$category_type_voca] != -1) ? $post_data[Category::$category_type_voca] : '001';
                        //最终的分类字段
                        $category_final = $category_type_popu . '|' . $category_type_area . '|' . $category_type_posi . '|' . $category_type_voca;

                        $this->load->model('Block');
                        $this->Block->init('recruitblockpost');
                        $recruitPost = array(
                            'title' => 'recruit',
                            'content' => $htmlContent,
                            'userid' => $this->_userid,
                            'category' => $category_final, //分类信息
                        );
                        $ret = $this->Block->create($recruitPost);
                        if ((is_array($ret) && !empty($ret)))
                        {
                            $message[] = '招聘信息-录入成功';
                        }else{
                            $message[] =  '招聘信息-录入失败';
                        }
                        break;
                    default:
                        $message[] =  '招聘信息-招聘分类错误';
                        break;
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/recruit_insert');
            }else{
                //获取分类的分组列表
                $this->load->model('Category');
                $category_list_config = $this->Category->fetch_category_group_data();
                $params = array(
                    'title' => $this->common_data['title'],
                    'data' => array(),
                    'unionManageList' => $unionManageList,
                    'category_list_config' => $category_list_config,
                );
                $this->load->view('admin/admin_recruit_insert', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 创建招聘分类
     */
    public function create_recruit_tag()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_WELCOME)
        {
             $this->load->model('Category');
             if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
             {
                  $post_data = $this->input->post(NULL, TRUE);
                  if (!isset($post_data['type']) OR empty($post_data['type']))
                  {
                      BLH_Utilities::showmessage('请选择分类');
                   }
                   if (!isset($post_data['cname']) OR empty($post_data['cname']))
                   {
                       BLH_Utilities::showmessage('请填写行业类别');
                   }
                   $ret = $this->Category->add_new_category($post_data);
                   $msg = $ret ? '招聘分类创建成功' : '招聘分类创建失败';
                   BLH_Utilities::showmessage($msg, APP_SITE_URL.'/admin/create_recruit_tag');
              }
              $params = array(
                  'title' => $this->common_data['title'],
                  'category_type' => Category::$category_name_config,
              );
              $this->load->view('admin/admin_create_recruit_tag', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 创建舶来帐号
     */
    public function create_user_welcome($userId=0)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //判断权限
        //校验用户身份
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            //最小的[舶来]账户的UID
            $min_welcome_uid = $this->config->item('min_welcome_uid', 'system_admin_config');
            //最大的[舶来]账户的UID
            $max_welcome_uid = $this->config->item('max_welcome_uid', 'system_admin_config');
            $max_userid = $this->Userinfo->fetch_max_welcome_userid($min_welcome_uid, $max_welcome_uid);
            if ($max_userid <= 0 OR !$max_userid)
            {
                BLH_Utilities::showmessage('获取最大的舶来帐号失败，请重试');
            }
            if ($userId > 0 && $userId != $this->_userid && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (($max_userid == $max_welcome_uid) OR (($max_userid + 1) != $userId))
                {
                    BLH_Utilities::showmessage('数据错误，请重试');
                }
                if (empty($post_data['email']) OR empty($post_data['nickname']) OR empty($post_data['password']))
                {
                    BLH_Utilities::showmessage('必填项不能为空，请重试');
                }
                $this->load->model('Userinfo');
                $register_data = array(
                    'id' => ($max_userid+1),
                    'email' => $post_data['email'],
                    'nickname' => !empty($post_data['nickname']) ? $post_data['nickname'] : '',
                    'passwd' => $post_data['password'],
                    'passwdconf' => $post_data['password'],
                    'blhRole' => self::UNION_ROLE_WELCOME,
                    'sex' => $post_data['sex'],
                    'company' => !empty($post_data['company']) ? $post_data['company'] : '',
                    'position' => !empty($post_data['position']) ? $post_data['position'] : '',
                    'area' => !empty($post_data['area']) ? $post_data['area'] : '',
                    'status' => $this->Userinfo->USER_STATUS_OK,
                    'usertype' => $this->Userinfo->USER_TYPE_PUBLIC,
                );
                $id = $this->Userinfo->addNew($register_data);
                if(!($id > 0))
                {
                    BLH_Utilities::showmessage('注册失败');
                }
                BLH_Utilities::showmessage('创建成功', APP_SITE_URL.'/admin/create_user_welcome');
            }else{
                $params = array(
                    'title' => $this->common_data['title'],
                    'userid' => ($max_userid+1),
                );
                $this->load->view('admin/admin_user_create', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 群福利发奖
     */
    public function send_group_award()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //判断权限
        //校验用户身份
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (empty($post_data['content']))
                {
                    BLH_Utilities::showmessage('必填项不能为空，请重试');
                }
                $this->load->model('Block');
                $this->load->model('Userinfo');
                $this->load->model('UserAward');
                $this->Block->set_table_name('communication');
                $content_arr = explode("\n", $post_data['content']);
                $content_arr_new = BLH_Utilities::filter($content_arr);
                $ret = array();
                //[奖品通知]的舶来账号
                $user_sys_send_award_id = $this->config->item('USER_SYS_SEND_AWARD', 'system_accounts');
                foreach ($content_arr_new as $user_item)
                {
                    list($user_key, $user_value) = explode('|', $user_item);
                    $user_id = (int)trim($user_key);
                    $user_data = $this->Userinfo->fetch_user_by_id($user_id);
                    if (empty($user_data))
                    {
                        $ret[] = '[' . $user_id . ']用户不存在，群福利发奖失败';
                        continue;
                    }
                    $user_content = trim($user_value);
                    $posts_data = array(
                        //'unionId' => 0,
                        'userid' => $user_sys_send_award_id,
                        'title' => 'communication',
                    );
                    $posts_data['receiveUserId'] = $user_id;
                    $posts_data['content'] = $user_content;
                    $posts_data['type'] = Block::SYSTEM_TYPE_STATUS;

                    $id = $this->Block->create($posts_data);
                    $ret[] = '[' . $user_id . '][' . $user_data['nickname'] . ']' . (!$id ? '群福利发奖失败' : '群福利发奖成功');
                    unset($user_data, $posts_data);
                    //系统发奖后，更新获奖用户的领取状态
                    $this->UserAward->updateUserGroupAward($user_id, $post_data['group_id']);
                }
                BLH_Utilities::showmessage(join('<br />', $ret));//, APP_SITE_URL.'/admin/send_group_award'
            }else{
                $this->load->model('Block');
                $this->Block->set_table_name('welcome');
                $groupList = $this->Block->getAllGroupAwardList();
                $params = array(
                    'title' => $this->common_data['title'],
                    'groupList' => $groupList,
                );
                $this->load->view('admin/admin_send_group_award', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }
    /**
     * 获取Block模块句柄
     * @param string $blockName
     */
    private function get_block_model($blockName = 'communication')
    {
        $_tableName = "{$blockName}blockpost";
        $this->load->model('Block');
        $this->Block->init($_tableName);
    }
    /**
     * 获取某帖子列表-针对游客（无需登录）
     */
    public function visitor_posts($uid=0, $rootId=0)
    {
        //未登录/登录失效后跳转到登录页面
        /*if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }*/
        if ($uid <=0 OR !is_numeric($uid) OR !is_numeric($rootId) OR $rootId <= 0)
        {
            BLH_Utilities::showmessage('参数错误，请重试');
        }
        $blockName = 'communication';
        $this->get_block_model($blockName);
        $rootInfo = $this->Block->fetchSimpleById($rootId);
        if (!is_array($rootInfo) OR empty($rootInfo) OR $rootInfo['unionId'] <= 0)
        {
            BLH_Utilities::showmessage('帖子不存在', APP_SITE_URL . "/admin/visitor_posts/{$uid}/{$rootId}");
        }
        $ret = array('root'=>array(), 'posts'=>array());
        $ret['status'] = TRUE;
        $ret['uid'] = $uid;
        $ret['rootid'] = $rootId;
        $ret['timestamp'] = SYS_TIME;
        list($ret_posts, $userIds) = $this->Block->postsRootList($uid, $blockName, $rootId, TRUE);
        if (!empty($ret_posts))
        {
            foreach ($ret_posts as $post_item)
            {
                //把根帖子单独出来
                if ($post_item['id'] == $rootId)
                {
                    $ret['root'] = $post_item;
                }else{
                    $ret['posts'][$post_item['id']] = $post_item;
                }
            }
        }
        unset($ret_posts);
        if(count($userIds) > 0)
        {
            $this->load->model('Userinfo');
            $ret['users'] = $this->Userinfo->batchUser($userIds);
        }
        $params = array(
            'title' => $this->common_data['title'],
            'posts_list' => $ret,
        );
        $this->load->view('admin/admin_visitor_posts', $params);
    }
    /**
     * 回复某帖子-针对游客（无需登录）
     */
    public function visitor_posts_reply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $data = $this->input->post(NULL, true);
            $uid = is_numeric($data['u']) && $data['u'] > 0 ? (int)$data['u'] : 0;
            $rootId = is_numeric($data['rid']) && $data['rid'] > 0 ? (int)$data['rid'] : 0;
            if ($uid <=0 OR !is_numeric($uid) OR !is_numeric($rootId) OR $rootId <= 0)
            {
                BLH_Utilities::showmessage('参数错误，请重试');
            }
            $this->get_block_model();
            $rootInfo = $this->Block->fetchSimpleById($rootId);
            if (!is_array($rootInfo) OR empty($rootInfo) OR $rootInfo['unionId'] <= 0)
            {
                BLH_Utilities::showmessage('帖子不存在', APP_SITE_URL . "/admin/visitor_posts/{$uid}/{$rootId}");
            }
            //消息标题
            $data['title'] = 'communication';
            //消息内容
            $data['content'] = urlencode($data['content']);
            //帖子所属社团ID
            $data['unionId'] = (int)$rootInfo['unionId'];
            $data['pid'] = $data['rootid'] = $rootId;
            $data['unionName'] = '社团';
            $data['userid'] = $uid;
            //是否是回复消息/帖子
            $data['isReply'] = TRUE;
            if (isset($data['u'])) unset($data['u']);
            if (isset($data['rid'])) unset($data['rid']);
            if (isset($data['ru'])) unset($data['ru']);
            if (isset($data['editPosts'])) unset($data['editPosts']);
            $blockRet = $this->Block->create($data);
            if(is_array($blockRet) && !empty($blockRet['id']))
            {
                BLH_Utilities::showmessage('发布成功', APP_SITE_URL . "/admin/visitor_posts/{$uid}/{$rootId}");
            }else{
                BLH_Utilities::showmessage('操作失败，请重试', APP_SITE_URL . "/admin/visitor_posts/{$uid}/{$rootId}");
            }
        }else{
            BLH_Utilities::showmessage('禁止操作');
        }
    }

    /**
     * 获取职位详细列表
     */
    public function jd_position_list_all()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $get = $this->input->get();
        $pdid = isset($get['pdid']) ? (int)$get['pdid'] : 0;
        $page = max(1, (isset($get['page'])?$get['page']:1));
        $pagenum = max(50, (isset($get['pagenum'])?$get['pagenum']:1));
        $pagesize = $pagenum > 0 ? max(50, $pagenum) : 50;
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdpositiondetail');
            $jdPositionDetailList = $this->Jdpositiondetail->fetchAllJdPosition($page, $pagesize, $pdid, TRUE);
            $jdPositionDetailTotal = $this->Jdpositiondetail->allJdPositionTotal($pdid, TRUE);
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$jdPositionDetailTotal));
            $params = array(
                'title' => $this->common_data['title'],
                'jd_position_list' => $jdPositionDetailList,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_jd_position_list_all', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 获取职位详细的某条记录
     */
    public function jd_position_detail_info($pdid = 0)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdpositiondetail');
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                $pdid = isset($post_data['pdid']) ? (int)$post_data['pdid'] : 0;
                $positionName = isset($post_data['positionName']) ? $post_data['positionName'] : '';
                $state = isset($post_data['state']) && !empty($post_data['state']) ? $post_data['state'] : 'new';
                if (empty($positionName))
                {
                    BLH_Utilities::showmessage('请选择职位名称', APP_SITE_URL . "/admin/jd_position_detail_info/");
                }
                if ($pdid <= 0)
                {
                    //创建职位
                    $ret = $this->Jdpositiondetail->createPositionDetail(0, $positionName, (int)$post_data['sortId'], $state);
                }else{
                    //更新职位
                    $ret = $this->Jdpositiondetail->updatePositionDetail($pdid, $positionName, (int)$post_data['sortId'], $state);
                }
                $msg = $ret ? '提交成功' : '提交失败';
                BLH_Utilities::showmessage($msg, APP_SITE_URL . '/admin/jd_position_list_all');
            }
            if ($pdid > 0)
            {
                $jdPositionDetailInfo = $this->Jdpositiondetail->fetchJdPositionDetail($pdid, TRUE);
                $actionName = '编辑';
            }else{
                $jdPositionDetailInfo = array();
                $actionName = '添加';
            }
            $params = array(
                'title' => $this->common_data['title'],
                'actionName' => $actionName,
                'jdinfo' => $jdPositionDetailInfo,
            );
            $this->load->view('admin/admin_jd_position_info', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 删除职位详细的某条记录
     */
    public function jd_position_detail_del($pdid = 0)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            //$this->jsJumpUrl(APP_SITE_URL . '/admin/login');
            BLH_Utilities::outputSuccess(array('data'=>'请登录后再访问'));
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdpositiondetail');
            $ret = $this->Jdpositiondetail->dropPositionDetail($pdid);
            BLH_Utilities::outputSuccess(array('data'=>'删除成功'));
        }else{
            BLH_Utilities::outputSuccess(array('data'=>'该用户被拒绝访问'));
        }
    }

    /**
     * 获取职位关键词列表
     */
    public function jd_position_keyword_list($type)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $cookie_key_kw_pdid = (int)BLH_Utilities::get_cookie(self::$cookie_key_kw_pdid);
        $get = $this->input->get();
        $pdid = isset($get['pdid']) ? (int)$get['pdid'] : ($cookie_key_kw_pdid > 0 ? $cookie_key_kw_pdid : 0);
        $level = isset($get['level']) ? (int)$get['level'] : 0;
        $state = isset($get['state']) ? htmlspecialchars($get['state']) : '';
        $page = max(1, (isset($get['page'])?$get['page']:1));
        $pagenum = max(50, (isset($get['pagenum'])?$get['pagenum']:1));
        $pagesize = $pagenum > 0 ? max(50, $pagenum) : 50;
        $keyword_type = $type == 'describe' ? '岗位职责' : '任职资格';
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdpositionduty');
            $jdPositionDutyList = $this->Jdpositionduty->fetchAllJdDuty($page, $pagesize, $pdid, $level, $type, TRUE, $state);
            $jdPositionDutyTotal = $this->Jdpositionduty->allJdDutyTotal($pdid, $level, $type, TRUE, $state);
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$jdPositionDutyTotal));
            //获取职位列表
            $this->load->model('Jdpositiondetail');
            $jdPositionDetailList = $this->Jdpositiondetail->fetchAllJdPosition(1, 500);
            if ($pdid > 0)
            {
                //写入Cookie
                BLH_Utilities::set_cookie(self::$cookie_key_kw_pdid, $pdid, 60*60*24*30);
            }
            $params = array(
                'title' => $this->common_data['title'],
                'type' => $type,
                'pdid' => $pdid,
                'level' => $level,
                'state' => $state,
                'keyword_type' => $keyword_type,
                'jd_keyword_list' => $jdPositionDutyList,
                'jd_position_list' => $jdPositionDetailList,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_jd_position_keyword_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 获取/添加/更新某条关键词的记录
     */
    public function jd_position_keyword_detail($type, $kid = 0)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $keyword_type = $type == 'describe' ? '岗位职责' : '任职资格';
            $this->load->model('Jdpositionduty');
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                $kid = isset($post_data['kid']) ? (int)$post_data['kid'] : 0;
                $pdid = isset($post_data['pdid']) ? (int)$post_data['pdid'] : 0;
                $level = isset($post_data['level']) ? (int)$post_data['level'] : 1;
                $keyword = isset($post_data['keyword']) ? $post_data['keyword'] : '';
                $state = isset($post_data['state']) && !empty($post_data['state']) ? $post_data['state'] : '';
                if (empty($keyword))
                {
                    BLH_Utilities::showmessage('请填写关键词', APP_SITE_URL . "/admin/jd_position_keyword_detail/{$type}");
                }
                if ($kid <= 0)
                {
                    if ($pdid > 0)
                    {
                        //写入Cookie
                        BLH_Utilities::set_cookie(self::$cookie_key_kw_pdid_detail, $pdid, 60*60*24*30);
                    }
                    //创建关键词
                    $this->Jdpositionduty->createPositionDuty($pdid, $level, $type, $keyword, (int)$post_data['sortId'], $state);
                }else{
                    //更新关键词
                    $this->Jdpositionduty->updatePositionDuty($kid, $keyword, $level, (int)$post_data['sortId'], $state);
                }
                BLH_Utilities::showmessage('提交成功', APP_SITE_URL . '/admin/jd_position_keyword_list/' . $type);
            }
            if ($kid > 0)
            {
                $jdPositionKeyWordDetailInfo = $this->Jdpositionduty->fetchJdDutyDetail($kid, $type, TRUE);
                $actionName = '编辑';
            }else{
                $cookie_key_kw_pdid_detail = (int)BLH_Utilities::get_cookie(self::$cookie_key_kw_pdid_detail);
                $jdPositionKeyWordDetailInfo = array();
                $jdPositionKeyWordDetailInfo['pdid'] = $cookie_key_kw_pdid_detail > 0 ? $cookie_key_kw_pdid_detail : 0;
                $actionName = '添加';
            }
            //获取职位列表
            $this->load->model('Jdpositiondetail');
            $jdPositionDetailList = $this->Jdpositiondetail->fetchAllJdPosition(1, 500);
            $params = array(
                'title' => $this->common_data['title'],
                'actionName' => $actionName,
                'type' => $type,
                'keyword_type' => $keyword_type,
                'keywordinfo' => $jdPositionKeyWordDetailInfo,
                'jd_position_list' => $jdPositionDetailList,
            );
            $this->load->view('admin/jd_position_keyword_detail', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 获取职位描述列表
     */
    public function jd_position_describe_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $get = $this->input->get();
        $pdid = isset($get['pdid']) ? (int)$get['pdid'] : 0;
        $kid = isset($get['kid']) ? (int)$get['kid'] : 0;
        $level = isset($get['level']) ? (int)$get['level'] : 0;
        $page = max(1, (isset($get['page'])?$get['page']:1));
        $pagenum = max(100, (isset($get['pagenum'])?$get['pagenum']:1));
        $pagesize = $pagenum > 0 ? max(100, $pagenum) : 100;
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jddescribe');
            $jdPositionDescribeList = $this->Jddescribe->fetchAllJdDescribe($page, $pagesize, $kid, $level, $pdid, TRUE);
            $jdPositionDescribeTotal = $this->Jddescribe->allJdDescribeTotal($kid, $level, $pdid, TRUE);
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$jdPositionDescribeTotal));
            //获取职位列表
            $this->load->model('Jdpositiondetail');
            $jdPositionDetailList = $this->Jdpositiondetail->fetchAllJdPosition(1, 500);
            //获取关键词列表
            $this->load->model('Jdpositionduty');
            $jdPositionKeyWordList = $this->Jdpositionduty->fetchAllJdDuty(1, 500);
            $params = array(
                'title' => $this->common_data['title'],
                'kid' => $kid,
                'pdid' => $pdid,
                'level' => $level,
                'jd_describe_list' => $jdPositionDescribeList,
                'jd_position_list' => $jdPositionDetailList,
                'jd_keyword_list' => $jdPositionKeyWordList,
                'page' => $Pager->show(),
            );
            $this->load->view('admin/admin_jd_position_describe_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 获取/添加/更新某条描述的记录
     */
    public function jd_position_describe_detail($id = 0, $kid = 0, $pdid = 0)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jddescribe');
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                $id = isset($post_data['id']) ? (int)$post_data['id'] : 0;
                $kid = isset($post_data['kid']) ? (int)$post_data['kid'] : 0;
                $pdid = isset($post_data['pdid']) ? (int)$post_data['pdid'] : 0;
                $level = isset($post_data['level']) ? (int)$post_data['level'] : 1;
                $content = isset($post_data['content']) ? $post_data['content'] : '';
                $state = isset($post_data['state']) && !empty($post_data['state']) ? $post_data['state'] : '';
                if (empty($kid))
                {
                    BLH_Utilities::showmessage('请选择该描述所属的关键词', APP_SITE_URL . "/admin/jd_position_describe_detail/");
                }
                if (empty($content))
                {
                    BLH_Utilities::showmessage('请填写职位描述', APP_SITE_URL . "/admin/jd_position_describe_detail/");
                }
                if ($id <= 0)
                {
                    //创建关键词
                    $this->Jddescribe->createJdDescribe($kid, $content, (int)$post_data['sortId'], $state);
                }else{
                    //更新关键词
                    $this->Jddescribe->updateJdDescribe($id, $kid, $content, (int)$post_data['sortId'], $state);
                }
                BLH_Utilities::showmessage('提交成功', APP_SITE_URL . '/admin/jd_position_describe_list/');
            }
            if ($id > 0)
            {
                $jdPositionKeyWordDetailInfo = $this->Jddescribe->fetchJdDescribeById($id, $kid, $pdid, TRUE);
                $actionName = '编辑';
            }else{
                $jdPositionKeyWordDetailInfo = array();
                $actionName = '添加';
            }
            //获取职位列表
            //$this->load->model('Jdpositiondetail');
            //$jdPositionDetailList = $this->Jdpositiondetail->fetchAllJdPosition(1, 500);
            //获取关键词列表
            $this->load->model('Jdpositionduty');
            $jdPositionKeyWordList = $this->Jdpositionduty->fetchAllJdDuty(1, 500);
            $params = array(
                'title' => $this->common_data['title'],
                'id' => $id,
                'kid' => $kid,
                'pdid' => $pdid,
                'actionName' => $actionName,
                'info' => $jdPositionKeyWordDetailInfo,
                //'jd_position_list' => $jdPositionDetailList,
                'jd_keyword_list' => $jdPositionKeyWordList,
            );
            $this->load->view('admin/jd_position_describe_detail', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 职位列表批量导入
     */
    public function jd_position_import_batch()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //判断权限
        //校验用户身份
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (empty($post_data['content']))
                {
                    BLH_Utilities::showmessage('必填项不能为空，请重试');
                }
                $this->load->model('Jdpositiondetail');
                $content_arr = explode("\n", $post_data['content']);
                $content_arr_new = BLH_Utilities::filter($content_arr);
                $ret = array();
                $sortId = 0;
                foreach ($content_arr_new as $item)
                {
                    //创建职位
                    $positionName = trim($item);
                    $insertRet = $this->Jdpositiondetail->createPositionDetail(0, $positionName, $sortId, 'new');
                    //$ret[] = '[' . $positionName . ']' . (!$insertRet ? '职位列表导入失败' : '职位列表导入成功');
                }
                BLH_Utilities::showmessage('职位列表导入完毕', APP_SITE_URL.'/admin/jd_position_import_batch');//join('<br />', $ret)
            }else{
                $params = array(
                    'title' => $this->common_data['title'],
                );
                $this->load->view('admin/admin_jd_position_import_batch', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * JD列表批量导入
     */
    public function jd_list_import_batch()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //判断权限
        //校验用户身份
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (empty($post_data['content']))
                {
                    BLH_Utilities::showmessage('必填项不能为空，请重试');
                }
                $this->load->model('Jdjobbase');
                $this->load->model('Jdjobarea');
                $content_arr = explode("\n", $post_data['content']);
                $content_arr_new = $content_arr;//BLH_Utilities::filter($content_arr);
                $ret = array();
                $sortId = 0;
                foreach ($content_arr_new as $item)
                {
                    $lineInfo = explode(" ", $item, 5);
                    $jobClassId = isset($lineInfo[0]) ? (int)$lineInfo[0] : 0;
                    $areaName = isset($lineInfo[1]) ? trim($lineInfo[1]) : '';
                    $companyName = isset($lineInfo[2]) ? trim($lineInfo[2]) : '';
                    $companySite = isset($lineInfo[3]) ? trim($lineInfo[3]) : '';
                    $jdUrl = isset($lineInfo[4]) ? trim($lineInfo[4]) : '';
                    $areaId = 1;//默认北京
                    if (!empty($areaName))
                    {
                        //获取所有地区列表
                        $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new', 'areaName');
                        //查不到则默认北京
                        $areaId = $jobAreaList && isset($jobAreaList[$areaName]['areaId']) ? $jobAreaList[$areaName]['areaId'] : 1;
                    }
                    //获取企业邮箱
                    $email = '';
                    if (!empty($companySite))
                    {
                        $companySite = trim($companySite);
                        $parseData = parse_url($companySite);
                        $domain_url = isset($parseData['host']) ? $parseData['host'] : '';
                        if ($domain_url)
                        {
                            list($www, $domain, $suffix) = explode('.', $domain_url, 3);
                            $email = sprintf('%s@%s.%s', 'hr', $domain, $suffix);
                        }
                    }
                    //创建JD信息
                    $jdParams = array(
                        'companyName' => trim($companyName),
                        'companySite' => trim($companySite),
                        'jdUrl' => trim($jdUrl),
                        'email' => $email,
                        'jobClassId' => (int)$jobClassId,
                        'areaId' => (int)$areaId,
                        'jdPushStatus' => 1, //营销中
                    );
                    $insertRet = $this->Jdjobbase->createJdBaseInfo($jdParams);
                    //$ret[] = '[' . $positionName . ']' . (!$insertRet ? 'JD列表导入失败' : 'JD列表导入成功');
                }
                BLH_Utilities::showmessage('JD列表导入完毕', APP_SITE_URL.'/admin/jd_list_import_batch');
            }else{
                $params = array(
                    'title' => $this->common_data['title'],
                );
                $this->load->view('admin/admin_jd_list_import_batch', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    //=================================================
    /**
     * JD-基础信息录入
     */
    public function jd_base_insert()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $message = array();
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (!isset($post_data['editPosts']) OR empty($post_data['entry']))
                {
                    BLH_Utilities::showmessage('非法请求');
                }
                $data = $post_data['entry'];
                //外部介绍信息
                if (!empty($data['describeContent'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['describeContent'] = stripslashes($data['describeContent']);
                    } else {
                        $data['describeContent'] = $data['describeContent'];
                    }
                }
                //内部介绍信息
                if (!empty($data['demandContent'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['demandContent'] = stripslashes($data['demandContent']);
                    } else {
                        $data['demandContent'] = $data['demandContent'];
                    }
                }
                $data['abilityFeatureString'] = (isset($data['abilityFeature']) && is_array($data['abilityFeature']) && !empty($data['abilityFeature'])) ? join('|', $data['abilityFeature']) : '';
                $this->load->model('Jdjobbase');
                $lastId = $this->Jdjobbase->createJdBaseInfo($data, TRUE);
                if ($lastId > 0)
                {
                    if (!empty($data['abilityFeature']))
                    {
                        //录入JD-能力特征-关联表
                        $this->load->model('Jdjobabilitymap');
                        $this->Jdjobabilitymap->deleteJdAbilityMap($lastId);
                        foreach ($data['abilityFeature'] as $abilityFeatureId)
                        {
                            $this->Jdjobabilitymap->createJdAbilityMap($lastId, $abilityFeatureId);
                        }
                    }
                    $message[] = '供需信息-录入成功';
                }else{
                    $message[] =  '供需信息-录入失败';
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/jd_base_list');
            }else{
                //获取职位分类列表
                $this->load->model('Jdjobclass');
                $jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
                //获取职位程度列表
                $this->load->model('Jdjoblevel');
                $jobLevelList = $this->Jdjoblevel->fetchAllJdJobLevelList(0, 0, TRUE, 'new');
                //获取地区列表
                $this->load->model('Jdjobarea');
                $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new');
                //获取公司类型列表
                $this->load->model('Jdjobcompanytype');
                $jobCompanyTypeList = $this->Jdjobcompanytype->fetchAllJdJobCompanyTypeList(0, 0, TRUE, 'new', 1);
                //获取行业类别列表
                $this->load->model('Jdjobvocationtype');
                $jobVocationTypeList = $this->Jdjobvocationtype->fetchAllJdJobVocationTypeList(0, 0, TRUE, 'new');
                //获取能力特征词列表
                $this->load->model('Jdjobabilityfeature');
                $jobAbilityFeatureList = $this->Jdjobabilityfeature->fetchAllJdJobAbilityFeatureList(0, 0, TRUE, 'new', 1);
                $params = array(
                    'title' => $this->common_data['title'],
                    'jobClassList' => $jobClassList,
                    'jobLevelList' => $jobLevelList,
                    'jobAreaList' => $jobAreaList,
                    'jobCompanyTypeList' => $jobCompanyTypeList,
                    'jobVocationTypeList' => $jobVocationTypeList,
                    'jobAbilityFeatureList' => $jobAbilityFeatureList,
                );
                $this->load->view('admin/admin_jd_base_insert', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 获取JD-基本信息列表
     */
    public function jd_base_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $get = $this->input->get();
        $isPush = isset($get['isPush']) ? (int)$get['isPush'] : '-1';
        $jdPushStatus = isset($get['jdPushStatus']) ? (int)$get['jdPushStatus'] : '1';//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $state = isset($get['state']) ? ($get['state'] == -1 ? '' : htmlspecialchars($get['state'])) : 'new';
        $jobClassId = isset($get['jobClassId']) ? (int)$get['jobClassId'] : '0';
        $jobId = isset($get['id']) ? (int)$get['id'] : '0';
        $page = max(1, (isset($get['page']) ? (int)$get['page'] : 1));
        $pagesize = isset($get['pagenum']) ? min(100, (int)$get['pagenum']) : 50;
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdjobbase');
            $isForceNew = $get ? FALSE : TRUE;
            $jdBaseList = $this->Jdjobbase->fetchAllJdBaseList($page, $pagesize, TRUE, $state, $isPush, $jdPushStatus, $isForceNew, $jobClassId, $jobId);
            $jdBaseTotal = $this->Jdjobbase->allJdBaseListTotal(TRUE, $state, $isPush, $jdPushStatus, $isForceNew, $jobClassId, 0, $jobId);
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$jdBaseTotal));

            $jobClassList = $jobLevelList = $jobAreaList = $jobCompanyTypeList = $jobVocationTypeList = array();
            //获取职位分类列表
            $this->load->model('Jdjobclass');
            $jobClassListInit = $this->Jdjobclass->fetchAllJdJobClassListNew();
            if (!empty($jdBaseList))
            {
                $jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
                //获取职位程度列表
                $this->load->model('Jdjoblevel');
                $jobLevelList = $this->Jdjoblevel->fetchAllJdJobLevelList(0, 0, TRUE, 'new');
                //获取地区列表
                $this->load->model('Jdjobarea');
                $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new');
                //获取公司类型列表
                $this->load->model('Jdjobcompanytype');
                $jobCompanyTypeList = $this->Jdjobcompanytype->fetchAllJdJobCompanyTypeList(0, 0, TRUE, 'new');
                //获取行业类别列表
                $this->load->model('Jdjobvocationtype');
                $jobVocationTypeList = $this->Jdjobvocationtype->fetchAllJdJobVocationTypeList(0, 0, TRUE, 'new');
            }

            $params = array(
                'title' => $this->common_data['title'],
                'isPush' => $isPush,
                'jdPushStatus' => $jdPushStatus,
                'jobClassId' => $jobClassId,
                'state' => $state,
                'jdBaseList' => $jdBaseList,
                'jobClassList' => $jobClassList,
                'jobClassListInit' => $jobClassListInit,
                'jobLevelList' => $jobLevelList,
                'jobAreaList' => $jobAreaList,
                'jobCompanyTypeList' => $jobCompanyTypeList,
                'jobVocationTypeList' => $jobVocationTypeList,
                'total' => $jdBaseTotal,
                'pageShow' => $Pager->show(),
                'page' => $page,
            );
            $this->load->view('admin/admin_jd_base_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * JD-基础信息更新
     */
    public function jd_base_edit()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $get = $this->input->get();
            $jdId = isset($get['id']) ? (int)$get['id'] : 0;
            $currentPage = isset($get['page']) ? (int)$get['page'] : 1;
            $message = array();
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (!isset($post_data['editPosts']) OR empty($post_data['entry']))
                {
                    BLH_Utilities::showmessage('非法请求');
                }
                $data = $post_data['entry'];
                //岗位职责-外部介绍信息/题主说
                if (!empty($data['describeContent'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['describeContent'] = stripslashes($data['describeContent']);
                    } else {
                        $data['describeContent'] = $data['describeContent'];
                    }
                }
                //任职描述-内部介绍信息-小编说
                if (!empty($data['demandContent'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['demandContent'] = stripslashes($data['demandContent']);
                    } else {
                        $data['demandContent'] = $data['demandContent'];
                    }
                }
                $data['abilityFeatureString'] = (isset($data['abilityFeature']) && is_array($data['abilityFeature']) && !empty($data['abilityFeature'])) ? join('|', $data['abilityFeature']) : '';
                $this->load->model('Jdjobbase');
                $lastId = $this->Jdjobbase->updateJdBaseInfo($data);
                if ($lastId > 0)
                {
                    if (!empty($data['abilityFeature']))
                    {
                        //录入JD-能力特征-关联表
                        $this->load->model('Jdjobabilitymap');
                        $this->Jdjobabilitymap->deleteJdAbilityMap($lastId);
                        foreach ($data['abilityFeature'] as $abilityFeatureId)
                        {
                            $this->Jdjobabilitymap->createJdAbilityMap($lastId, $abilityFeatureId);
                        }
                    }
                    $message[] = '供需信息-更新成功';
                }else{
                    $message[] =  '供需信息-更新失败';
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/jd_base_list?page='.$currentPage);//'/admin/jd_base_edit?id='.$data['id']
            }else{
                //获取该JD的基本信息
                $this->load->model('Jdjobbase');
                $jdInfo = $this->Jdjobbase->fetchJdBaseInfoById($jdId);
                //获取职位分类列表
                $this->load->model('Jdjobclass');
                $jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
                //获取职位程度列表
                $this->load->model('Jdjoblevel');
                $jobLevelList = $this->Jdjoblevel->fetchAllJdJobLevelList(0, 0, TRUE, 'new');
                //获取地区列表
                $this->load->model('Jdjobarea');
                $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new');
                //获取公司类型列表
                $this->load->model('Jdjobcompanytype');
                $jobCompanyTypeList = $this->Jdjobcompanytype->fetchAllJdJobCompanyTypeList(0, 0, TRUE, 'new', 1);
                //获取行业类别列表
                $this->load->model('Jdjobvocationtype');
                $jobVocationTypeList = $this->Jdjobvocationtype->fetchAllJdJobVocationTypeList(0, 0, TRUE, 'new');
                //获取能力特征词列表
                $this->load->model('Jdjobabilityfeature');
                $jobAbilityFeatureList = $this->Jdjobabilityfeature->fetchAllJdJobAbilityFeatureList(0, 0, TRUE, 'new', 1);

                $params = array(
                    'title' => $this->common_data['title'],
                    'jdInfo' => $jdInfo,
                    'jobClassList' => $jobClassList,
                    'jobLevelList' => $jobLevelList,
                    'jobAreaList' => $jobAreaList,
                    'jobCompanyTypeList' => $jobCompanyTypeList,
                    'jobVocationTypeList' => $jobVocationTypeList,
                    'jobAbilityFeatureList' => $jobAbilityFeatureList,
                    'page' => $currentPage,
                );
                $this->load->view('admin/admin_jd_base_edit', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 简历列表批量导入
     */
    public function resume_import_batch()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        //判断权限
        //校验用户身份
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data =& $_POST;//$this->input->post(NULL, TRUE);
                if (empty($post_data['content']))
                {
                    BLH_Utilities::showmessage('必填项不能为空，请重试');
                }
                $this->load->model('Jdjobresumebase');
                $content_arr = explode("\n", $post_data['content']);
                $content_arr_new = $content_arr;
                $ret = $fileList = $jsjIds = $errorRet = array();
                $sortId = 0;
                foreach ($content_arr_new as $lineNum => $line)
                {
                    $item = explode("\t", $line);
                    // 有折行的特殊处理
                    if (count($item) == 1) {
                        isset($fileList[$lineNum - 1][16]) && $fileList[$lineNum - 1][16] .= $line;
                        continue;
                    }
                    if (empty($item) || !is_numeric($item[0])) {
                        continue;
                    }
                    // 记录金数据ID
                    $jsjIds[] = $item[0];
                    // 根据金数据ID获取简历数量，防止重复导入
                    $resumeCnt = $this->Jdjobresumebase->fetchResumeCntFromJsjid($item[0]);
                    if ($resumeCnt > 0) {
                        continue;
                    }
                    $fileList[] = $item;
                }
                if (empty($fileList)) {
                    BLH_Utilities::showmessage('没有可导入的简历数据');
                }

                foreach ($fileList as $key => $item) {
                    $resume_data = array();
                    $resume_data['jsjId'] = isset($item[0]) && !empty($item[0]) ? (int)$item[0] : 0; //金数据ID
                    $resume_data['userGender'] = isset(self::$gender_config_map[$item[1]]) && !empty(self::$gender_config_map[$item[1]]) ? (int)self::$gender_config_map[$item[1]] : 1;
                    $resume_data['degree'] = isset($item[2]) && !empty($item[2]) ? $item[2] : ''; //学历
                    $resume_data['graduateSchool'] = isset($item[3]) && !empty($item[3]) ? $item[3] : ''; //毕业院校
                    $resume_data['professional'] = isset($item[4]) && !empty($item[4]) ? $item[4] : ''; //专业
                    $resume_data['userAge'] = isset($item[5]) && !empty($item[5]) ? (int)$item[5] : ''; //出生年份
                    $resume_data['monthlySalary'] = isset($item[6]) && !empty($item[6]) ? $item[6] : '';//当前月薪
                    $resume_data['nowState'] = isset(self::$state_config_map[$item[7]]) && !empty(self::$state_config_map[$item[7]]) ? self::$state_config_map[$item[7]] : 0;//当前状态(1:在职0:离职)
                    // 职能细分
                    if (isset($item[8]) && !empty($item[8])) {
                        if (false !== strpos($item[8], '-')) {
                            list($jobClassId, $jobPartition) = explode('-', $item[8], 2);
                            $resume_data['jobPartition'] = !empty($jobPartition) ? $jobPartition : '';
                            $resume_data['jobClassId'] = !empty($jobClassId) ? (int)$jobClassId : 0;
                        }else{
                            $resume_data['jobPartition'] = $item[8];
                        }
                    }
                    $resume_data['onceCompany'] = isset($item[9]) && !empty($item[9]) ? $item[9] : '';//曾任职企业
                    $resume_data['isInnovate'] = isset(self::$innovate_config_map[$item[10]]) && !empty(self::$innovate_config_map[$item[10]]) ? self::$innovate_config_map[$item[10]] : 0;//是否考虑过创业公司
                    $resume_data['professionTag'] = isset($item[11]) && !empty($item[11]) ? $item[11] : '';//行业标签
                    $resume_data['subordinate'] = isset($item[12]) && strlen($item[12]) > 0 ? $item[12] : '';//下属
                    // 地区-城市
                    if (isset($item[13]) && !empty($item[13])) {
                        if (false !== strpos($item[13], '-')) {
                            list($areaId, $areaName) = explode('-', $item[13], 2);
                            $resume_data['areaName'] = !empty($areaName) ? $areaName : '';
                            $resume_data['areaId'] = !empty($areaId) ? (int)$areaId : 0;
                        }else{
                            $resume_data['areaName'] = $item[13];
                        }
                    }
                    $resume_data['resumeUrl'] = isset($item[14]) && !empty($item[14]) ? $item[14] : '';//简历url
                    $resume_data['recommendCostRate'] = isset($item[15]) && !empty($item[15]) ? str_replace('%', '', trim($item[15])) : 0;//推荐费比例
                    $resume_data['isFindJob'] = 1; //待企业反馈意见
                    $resume_data['entryMemo'] = (isset($item[16]) && !empty($item[16]) && $item[16] != '空') ? $item[16] : '';//录入备注
                    $lastId = $this->Jdjobresumebase->createJdResumeBaseInfo($resume_data);
                    if ($lastId == false) {
                        $errorRet[] = 1;
                    }
                }
                BLH_Utilities::showmessage((empty($errorRet) ? '简历列表导入完毕' : '简历列表导入失败，请重试'), APP_SITE_URL.'/admin/resume_import_batch');
            }else{
                $params = array(
                    'title' => $this->common_data['title'],
                );
                $this->load->view('admin/admin_resume_import_batch', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 简历-基础信息录入
     */
    public function jd_resume_insert()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $message = array();
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (!isset($post_data['editPosts']) OR empty($post_data['entry']))
                {
                    BLH_Utilities::showmessage('非法请求');
                }
                $data = $post_data['entry'];
                $data['abilityFeatureString'] = (isset($data['abilityFeature']) && is_array($data['abilityFeature']) && !empty($data['abilityFeature'])) ? join('|', $data['abilityFeature']) : '';
                $data['pertainFeatureString'] = (isset($data['pertainFeature']) && is_array($data['pertainFeature']) && !empty($data['pertainFeature'])) ? join('|', $data['pertainFeature']) : '';

                //原始简历
                /*if (!empty($data['resumeInit'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['resumeInit'] = stripslashes($data['resumeInit']);
                    } else {
                        $data['resumeInit'] = $data['resumeInit'];
                    }
                }*/
                $this->load->model('Jdjobresumebase');
                $lastId = $this->Jdjobresumebase->createJdResumeBaseInfo($data, TRUE);
                if ($lastId > 0)
                {
                    //录入简历-能力特征-关联表
                    if (!empty($data['abilityFeature']))
                    {
                        $this->load->model('Jdjobresumeabilitymap');
                        $this->Jdjobresumeabilitymap->deleteJdResumeAbilityMap($lastId);
                        foreach ($data['abilityFeature'] as $abilityFeatureId)
                        {
                            $this->Jdjobresumeabilitymap->createJdResumeAbilityMap($lastId, $abilityFeatureId);
                        }
                    }
                    //录入简历-附属能力特征-关联表
                    if (!empty($data['pertainFeature']))
                    {
                        $this->load->model('Jdjobresumepertainmap');
                        $this->Jdjobresumepertainmap->deleteJdResumePertainMap($lastId);
                        foreach ($data['pertainFeature'] as $pertainFeatureId)
                        {
                            $this->Jdjobresumepertainmap->createJdResumePertainMap($lastId, $pertainFeatureId);
                        }
                    }
                    $message[] = '简历信息-录入成功';
                }else{
                    $message[] =  '简历信息-录入失败';
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/jd_resume_list');
            }else{
                //获取职位分类列表
                $this->load->model('Jdjobclass');
                $jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
                //获取职位程度列表
                $this->load->model('Jdjoblevel');
                $jobLevelList = $this->Jdjoblevel->fetchAllJdJobLevelList(0, 0, TRUE, 'new');
                //获取地区列表
                $this->load->model('Jdjobarea');
                $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new');
                //获取公司类型列表
                $this->load->model('Jdjobcompanytype');
                $jobCompanyTypeList = $this->Jdjobcompanytype->fetchAllJdJobCompanyTypeList(0, 0, TRUE, 'new');
                //获取能力特征词列表
                $this->load->model('Jdjobabilityfeature');
                $jobAbilityFeatureList = $this->Jdjobabilityfeature->fetchAllJdJobAbilityFeatureList(0, 0, TRUE, 'new');
                //获取附属特征词列表
                $this->load->model('Jdjobresumepertainfeature');
                $jobResumePertainFeatureList = $this->Jdjobresumepertainfeature->fetchAllJdJobResumePertainFeatureList(0, 0, TRUE, 'new');
                $params = array(
                    'title' => $this->common_data['title'],
                    'jobClassList' => $jobClassList,
                    'jobLevelList' => $jobLevelList,
                    'jobAreaList' => $jobAreaList,
                    'jobCompanyTypeList' => $jobCompanyTypeList,
                    'jobAbilityFeatureList' => $jobAbilityFeatureList,
                    'jobResumePertainFeatureList' => $jobResumePertainFeatureList,
                );
                $this->load->view('admin/admin_jd_resume_base_insert', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 获取简历-基本信息列表
     */
    public function jd_resume_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $get = $this->input->get();
        $isFindJob = isset($get['isFindJob']) ? (int)$get['isFindJob'] : 1;
        $state = isset($get['state']) ? ($get['state'] == -1 ? '' : htmlspecialchars($get['state'])) : 'new';
        $page = max(1, (isset($get['page']) ? (int)$get['page'] : 1));
        $pagesize = isset($get['pagenum']) ? min(100, (int)$get['pagenum']) : 50;
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdjobresumebase');
            $isForceNew = $get ? FALSE : TRUE;
            $jdResumeBaseList = $this->Jdjobresumebase->fetchAllJdResumeBaseList($page, $pagesize, TRUE, $state, $isFindJob, $isForceNew);
            $jdResumeBaseTotal = $this->Jdjobresumebase->allJdResumeBaseListTotal(TRUE, $state, $isFindJob);
            //分页显示
            $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($class_file);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$jdResumeBaseTotal));

            $jobClassList = $jobLevelList = $jobAreaList = $jobCompanyTypeList = array();
            if (!empty($jdResumeBaseList))
            {
                //获取职位分类列表
                $this->load->model('Jdjobclass');
                $jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
                //获取职位程度列表
                $this->load->model('Jdjoblevel');
                $jobLevelList = $this->Jdjoblevel->fetchAllJdJobLevelList(0, 0, TRUE, 'new');
                //获取地区列表
                $this->load->model('Jdjobarea');
                $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new');
                //获取公司类型列表
                $this->load->model('Jdjobcompanytype');
                $jobCompanyTypeList = $this->Jdjobcompanytype->fetchAllJdJobCompanyTypeList(0, 0, TRUE, 'new');
            }

            $params = array(
                'title' => $this->common_data['title'],
                'isFindJob' => $isFindJob,
                'state' => $state,
                'jdResumeBaseList' => $jdResumeBaseList,
                'jobClassList' => $jobClassList,
                'jobLevelList' => $jobLevelList,
                'jobAreaList' => $jobAreaList,
                'jobCompanyTypeList' => $jobCompanyTypeList,
                'nowStateMap' => array_flip(self::$state_config_map),
                'isFindJobConfig' => self::$isFindJobConfig,
                'total' => $jdResumeBaseTotal,
                'pageShow' => $Pager->show(),
                'page' => $page,
            );
            $this->load->view('admin/admin_jd_resume_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 简历-基础信息更新
     */
    public function jd_resume_edit()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $get = $this->input->get();
            $resumeId = isset($get['id']) ? (int)$get['id'] : 0;
            $currentPage = isset($get['page']) ? (int)$get['page'] : 1;
            $message = array();
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (!isset($post_data['editPosts']) OR empty($post_data['entry']))
                {
                    BLH_Utilities::showmessage('非法请求');
                }
                $data = $post_data['entry'];
                $data['abilityFeatureString'] = (isset($data['abilityFeature']) && is_array($data['abilityFeature']) && !empty($data['abilityFeature'])) ? join('|', $data['abilityFeature']) : '';
                $data['pertainFeatureString'] = (isset($data['pertainFeature']) && is_array($data['pertainFeature']) && !empty($data['pertainFeature'])) ? join('|', $data['pertainFeature']) : '';

                //原始简历
                /*if (!empty($data['resumeInit'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['resumeInit'] = stripslashes($data['resumeInit']);
                    } else {
                        $data['resumeInit'] = $data['resumeInit'];
                    }
                }*/
                $this->load->model('Jdjobresumebase');
                $lastId = $this->Jdjobresumebase->updateJdResumeBaseInfo($data);
                if ($lastId > 0)
                {
                    //录入简历-能力特征-关联表
                    if (!empty($data['abilityFeature']))
                    {
                        $this->load->model('Jdjobresumeabilitymap');
                        $this->Jdjobresumeabilitymap->deleteJdResumeAbilityMap($lastId);
                        foreach ($data['abilityFeature'] as $abilityFeatureId)
                        {
                            $this->Jdjobresumeabilitymap->createJdResumeAbilityMap($lastId, $abilityFeatureId);
                        }
                    }
                    //录入简历-附属能力特征-关联表
                    if (!empty($data['pertainFeature']))
                    {
                        $this->load->model('Jdjobresumepertainmap');
                        $this->Jdjobresumepertainmap->deleteJdResumePertainMap($lastId);
                        foreach ($data['pertainFeature'] as $pertainFeatureId)
                        {
                            $this->Jdjobresumepertainmap->createJdResumePertainMap($lastId, $pertainFeatureId);
                        }
                    }
                    $message[] = '简历信息-更新成功';
                }else{
                    $message[] =  '简历信息-更新失败';
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/jd_resume_list?page='.$currentPage);//'/admin/jd_resume_edit?id=' . $data['id']
            }else{
                //获取该简历的基本信息
                $this->load->model('Jdjobresumebase');
                $resumeInfo = $this->Jdjobresumebase->fetchJdBaseInfoById($resumeId, TRUE);
                //获取职位分类列表
                $this->load->model('Jdjobclass');
                $jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
                //获取职位程度列表
                $this->load->model('Jdjoblevel');
                $jobLevelList = $this->Jdjoblevel->fetchAllJdJobLevelList(0, 0, TRUE, 'new');
                //获取地区列表
                $this->load->model('Jdjobarea');
                $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, TRUE, 'new');
                //获取公司类型列表
                $this->load->model('Jdjobcompanytype');
                $jobCompanyTypeList = $this->Jdjobcompanytype->fetchAllJdJobCompanyTypeList(0, 0, TRUE, 'new');
                //获取能力特征词列表
                $this->load->model('Jdjobabilityfeature');
                $jobAbilityFeatureList = $this->Jdjobabilityfeature->fetchAllJdJobAbilityFeatureList(0, 0, TRUE, 'new');
                //获取附属特征词列表
                $this->load->model('Jdjobresumepertainfeature');
                $jobResumePertainFeatureList = $this->Jdjobresumepertainfeature->fetchAllJdJobResumePertainFeatureList(0, 0, TRUE, 'new');
                if (!empty($resumeInfo['resumeInit']))
                {
                    if ($resumeInfo['initResumeId'] > 0)
                    {
                        $resumeInfo['resumeInit'] = html_entity_decode($resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = str_replace(array('<br />', '<br>'), array(), $resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = str_replace(array("\n", "\r\n"), '<br />', $resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = preg_replace('/(.*?)([\d+]{0,})(\<br \/\>{1,})/i', '${1}\n${2}|', $resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = str_replace(array('|\n', '|'), '', $resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = str_replace('--\n', '--', $resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = str_replace(array("\n", "\r\n"), '<br />', $resumeInfo['resumeInit']);
                        $resumeInfo['resumeInit'] = str_replace('\n', '<br />', $resumeInfo['resumeInit']);
                    }
                }
                $resumeInfo['resumeInit'] OR $resumeInfo['resumeInit'] = htmlspecialchars_decode($resumeInfo['resumeInit']);
                $params = array(
                    'title' => $this->common_data['title'],
                    'resumeInfo' => $resumeInfo,
                    'jobClassList' => $jobClassList,
                    'jobLevelList' => $jobLevelList,
                    'jobAreaList' => $jobAreaList,
                    'jobCompanyTypeList' => $jobCompanyTypeList,
                    'jobAbilityFeatureList' => $jobAbilityFeatureList,
                    'jobResumePertainFeatureList' => $jobResumePertainFeatureList,
                    'isFindJobConfig' => self::$isFindJobConfig,
                    'page' => $currentPage,
                );
                $this->load->view('admin/admin_jd_resume_base_edit', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * 简历-基础信息删除
     */
    public function jd_resume_del($id)
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->print_err(1, '无操作权限', 1, true);
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            if(!$id)
            {
                $this->print_err(1, '参数错误', 1, true);
            }
            $this->load->model('Jdjobresumebase');
            $data = $this->Jdjobresumebase->fetchJdBaseInfoById($id);
            if(!$data){
                $this->print_err(1, '你所删除的简历不存在或已删除', 1, true);
            }
            $flag = $this->Jdjobresumebase->dropJdResumeBaseInfo($id);
            if($flag){
                $this->print_err(0, '删除成功', 1, true);
            }else{
                $this->print_err(1, '删除失败', 1, true);
            }
        }
        $this->print_err(1, '无操作权限', 1, true);
    }

    /**
     * 获取JD-对接列表
     */
    public function jd_supply_list()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        $get = $this->input->get();
        $state = isset($get['state']) ? ($get['state'] == -1 ? '' : htmlspecialchars($get['state'])) : 'new';
        $supplyId = isset($get['id']) ? (int)$get['id'] : 0;
        $needId = isset($get['nid']) ? (int)$get['nid'] : 0;
        $page = max(1, (isset($get['page']) ? (int)$get['page'] : 1));
        $pagesize = isset($get['pagenum']) ? min(100, (int)$get['pagenum']) : 50;
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $this->load->model('Jdsupply');
            $isForceNew = $get ? FALSE : TRUE;
            $jdSupplyList = $this->Jdsupply->fetchAllJdSupplyList($page, $pagesize, TRUE, $state, $isForceNew, $supplyId, $needId);
            $jdSupplyTotal = $this->Jdsupply->allJdSupplyListTotal(TRUE, $state, $isForceNew, $supplyId, $needId);
            //分页显示
            $classFile = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
            BLH_Utilities::require_only($classFile);
            $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$jdSupplyTotal));

            $params = array(
                'title' => $this->common_data['title'],
                'state' => $state,
                'jdSupplyList' => $jdSupplyList,
                'total' => $jdSupplyTotal,
                'pageShow' => $Pager->show(),
                'page' => $page,
                'id' => $supplyId,
                'nid' => $needId,
            );
            $this->load->view('admin/admin_jd_supply_list', $params);
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

    /**
     * JD-对接信息更新
     */
    public function jd_supply_edit()
    {
        //未登录/登录失效后跳转到登录页面
        if (!$this->auth(true))
        {
            $this->load->library('session');
            $this->session->sess_destroy();
            $this->jsJumpUrl(APP_SITE_URL . '/admin/login');
        }
        //判断权限
        $this->load->library('session');
        $user_data = $this->session->userdata('user_data');
        if (isset($user_data->id) && !empty($user_data->id) && $user_data->blhRole == self::UNION_ROLE_SYSTEM)
        {
            $get = $this->input->get();
            $supplyId = isset($get['id']) ? (int)$get['id'] : 0;
            $currentPage = isset($get['page']) ? (int)$get['page'] : 1;
            $message = array();
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $post_data = $this->input->post(NULL, TRUE);
                if (!isset($post_data['editPosts']) OR empty($post_data['entry']))
                {
                    BLH_Utilities::showmessage('非法请求');
                }
                $data = $post_data['entry'];
                // 可提供的资源
                if (!empty($data['resource'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['resource'] = stripslashes($data['resource']);
                    } else {
                        $data['resource'] = $data['resource'];
                    }
                }
                // 联系方式
                if (!empty($data['contact'])) {
                    if (get_magic_quotes_gpc()) {
                        $data['contact'] = stripslashes($data['contact']);
                    } else {
                        $data['contact'] = $data['contact'];
                    }
                }
                $this->load->model('Jdsupply');
                $lastId = $this->Jdsupply->updateBaseInfo($data);
                if ($lastId > 0)
                {
                    $message[] = '对接信息-更新成功';
                }else{
                    $message[] =  '对接信息-更新失败';
                }
                BLH_Utilities::showmessage(join(',', $message), APP_SITE_URL.'/admin/jd_supply_list?page='.$currentPage);//'/admin/jd_supply_edit?id='.$data['id']
            }else{
                //获取该JD的基本信息
                $this->load->model('Jdsupply');
                $supplyInfo = $this->Jdsupply->fetchInfoById($supplyId, true);
                // 提问人的用户ID
                $supplyInfo['asker_name'] = '';
                if (!empty($supplyInfo['asker_uid'])) {
                    $this->load->model('Userinfo');
                    $askerUserInfo = $this->Userinfo->fetch_user_data($supplyInfo['asker_uid']);
                    $supplyInfo['asker_name'] = !empty($askerUserInfo['nickname']) ? $askerUserInfo['nickname'] : '';
                }
                // 需求ID
                $supplyInfo['need_title'] = '';
                if (!empty($supplyInfo['need_id'])) {
                    $this->load->model('Jdjobbase');
                    $needInfo = $this->Jdjobbase->fetchJdBaseInfoById($supplyInfo['need_id']);
                    $supplyInfo['need_title'] = !empty($needInfo['companyName']) ? $needInfo['companyName'] : '';
                }

                $params = array(
                    'title' => $this->common_data['title'],
                    'supplyInfo' => $supplyInfo,
                    'page' => $currentPage,
                );
                $this->load->view('admin/admin_jd_supply_edit', $params);
            }
        }else{
            BLH_Utilities::showmessage('该用户被拒绝访问');
        }
    }

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
