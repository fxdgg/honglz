<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demand extends BLH_Controller{
    // 默认的行业ID
    public static $defaultCateTypeId = 4;

    public function __construct()
    {
        parent::__construct(false);
    }

    /**
     * 需求列表
     */
    public function index() {
        if( ! $this->auth(TRUE, TRUE)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $page = (int)$this->input->get('page');
        $pagesize = min(10, (int)$this->input->get('pagenum'));
        if($page <= 0) $page = 1;
        if($pagesize <= 0) $pagesize = 10;

        $data = [
            'status' => true,
            'page'   => $page,
            'title'  => '供需列表',
            'list'   => [],
        ];
        $this->load->model('Jdjobbase');
        $group = $this->Jdjobbase->fetchDemandListGroup();
        if (!empty($group)) {
            $ids = $totalIds = [];
            foreach ($group as $val) {
                if (empty($val['groupIds'])) {
                    continue;
                }
                $groupIdsArr = explode(',', $val['groupIds']);
                $totalIds    = array_merge($totalIds, $groupIdsArr);
                foreach ($groupIdsArr as $key2 => $val2) {
                    // $offset = ($page - 1) * $pagesize;
                    // if ($key2 >= $offset && $key2 < ($offset + 2)) {
                    //     $ids[] = $val2;
                    // }
                    if ($key2 < 2) {
                        $ids[] = $val2;
                    }
                }
            }
            if (!empty($ids)) {
                $data['list'] = $this->Jdjobbase->fetchDemandList($ids, $page, $pagesize);
                $dataTotal = $this->Jdjobbase->fetchDemandTotal($ids);

                //分页显示
                $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
                BLH_Utilities::require_only($class_file);
                BLH_Pager::$pageDisplayConfig = array(
                    'default_display_count' => $pagesize,
                    'system_display_config' => array(min($pagesize, 5),10,20,30),
                );
                $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$dataTotal));
                $data['pageShow'] = $Pager->show();

                $this->load->model('Jdjobabilityfeature');
                $this->load->model('Jdjobcompanytype');
                foreach ($data['list'] as &$item) {
                    // 更新日期
                    $item['updateDate'] = !empty($item['updateTime']) ? date('Y-m-d', strtotime($item['updateTime'])) : date('Y-m-d');

                    // 获取行业
                    $_companyTypeInfo = $this->Jdjobcompanytype->fetchOne($item['companyTypeId']);
                    if (!empty($_companyTypeInfo)) {
                        $item['companyTypeName'] = $_companyTypeInfo['companyTypeName'];
                        // 标题
                        $item['title'] = sprintf('%s / %s', $item['companyName'], $item['companyTypeName']);
                    }else{
                        // 标题
                        $item['title'] = sprintf('%s', $item['companyName']);
                    }

                    // 获取能力特征词
                    $_abilityList = $this->Jdjobabilityfeature->fetchListByIds(explode('|', $item['abilityFeature']));
                    if (!empty($_abilityList)) {
                        $item['abilityFeatureName'] = join(' | ', array_column($_abilityList, 'abilityFeatureName'));
                        // 标题
                        $item['title'] = sprintf('%s （%s）', $item['title'], $item['abilityFeatureName']);
                    }
                }
            }
        }
        $this->load->view('default/demand_index', $data);
    }

    /**
     * 需求详情
     */
    public function info() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $id = (int)$this->input->get('id');
        if (empty($id) || $id <= 0) {
            BLH_Utilities::outputError(-101, '参数不能为空');
        }

        $this->load->model('Jdjobbase');
        $this->load->model('Jdjobabilityfeature');
        $this->load->model('Jdjobcompanytype');
        $data = [
            'status' => true,
            'title'  => '供需详情',
            'data'   => [],
        ];
        $info = $this->Jdjobbase->fetchInfoById($id);

        // 题主说
        if (empty($info['describeContent'])) {
            $info['describeContent'] = '暂无';
        }
        // 小编说
        if (empty($info['demandContent'])) {
            $info['demandContent'] = '暂无';
        }
        // 获取行业
        $_companyTypeInfo = $this->Jdjobcompanytype->fetchOne($info['companyTypeId']);
        if (!empty($_companyTypeInfo)) {
            $info['companyTypeName'] = $_companyTypeInfo['companyTypeName'];
        }else{
            $info['companyTypeName'] = '暂无';
        }

        // 获取能力特征词
        $_abilityList = $this->Jdjobabilityfeature->fetchListByIds(explode('|', $info['abilityFeature']));
        if (!empty($_abilityList)) {
            $info['abilityFeatureName'] = join(' | ', array_column($_abilityList, 'abilityFeatureName'));
        }else{
            $info['abilityFeatureName'] = '暂无';
        }
        $data['data'] = $info;

        $this->load->view('default/demand_info', $data);
    }

    // 发布信息页面
    public function add() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $this->load->view('default/demand_add', ['title'=>'发布信息']);
    }

    // 发布信息
    public function doAdd() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::outputError($this->login_error_data['errcode'], $this->lang->line('user_login_failed'));
        }

        $userId = isset($this->_userid) ? (int)$this->_userid : 0;
        if (empty($userId) || $userId <= 0) {
            BLH_Utilities::outputError(-101, '用户ID不合法');
        }

        $title = $this->input->post('title');
        $content = $this->input->post('content');
        $contact = $this->input->post('contact');
        if (empty($title) || empty($content) || empty($contact)) {
            BLH_Utilities::outputError(-1, '参数不能为空或不合法');
        }

        $params = [
            'type'            => 1,
            'creatorUid'      => $userId,
            'companyName'     => $title,
            'describeContent' => $content,
            'contact'         => $contact,
            'jdPushStatus'    => 1,
            'companyTypeId'   => self::$defaultCateTypeId, // 默认的行业类型(tbl_jd_job_company_type表type=1)
        ];
        $this->load->model('Jdjobbase');
        $jobId = $this->Jdjobbase->createJdBaseInfo($params, true);
        BLH_Utilities::outputSuccess(['status'=>1, 'id'=>$jobId]);
    }

    /**
     * 本周发布列表
     */
    public function week() {
        if( ! $this->auth(TRUE, TRUE)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $page = (int)$this->input->get('page');
        $pagesize = min(10, (int)$this->input->get('pagenum'));
        if($page <= 0) $page = 1;
        if($pagesize <= 0) $pagesize = 10;

        $timeRange = [];
        // 星期中的第几天
        $dateNum = date('N') - 1;
        $timeRange[] = date('Y-m-d 00:00:00', strtotime("-{$dateNum} days", time()));
        $timeRange[] = date('Y-m-d 23:59:59');
        $data = [
            'status' => true,
            'page'   => $page,
            'title'  => '本周列表',
            'list'   => [],
        ];
        $this->load->model('Jdjobbase');
        $data['list'] = $this->Jdjobbase->fetchDemandList([], $page, $pagesize, $timeRange);
        $dataTotal = $this->Jdjobbase->fetchDemandTotal([], $timeRange);

        //分页显示
        $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
        BLH_Utilities::require_only($class_file);
        BLH_Pager::$pageDisplayConfig = array(
            'default_display_count' => $pagesize,
            'system_display_config' => array(min($pagesize, 5),10,20,30),
        );
        $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$dataTotal));
        $data['pageShow'] = $Pager->show();

        $this->load->model('Jdjobabilityfeature');
        $this->load->model('Jdjobcompanytype');
        foreach ($data['list'] as &$item) {
            // 更新日期
            $item['updateDate'] = !empty($item['updateTime']) ? date('Y-m-d', strtotime($item['updateTime'])) : date('Y-m-d');

            // 获取行业
            $_companyTypeInfo = $this->Jdjobcompanytype->fetchOne($item['companyTypeId']);
            if (!empty($_companyTypeInfo)) {
                $item['companyTypeName'] = $_companyTypeInfo['companyTypeName'];
                // 标题
                $item['title'] = sprintf('%s / %s', $item['companyName'], $item['companyTypeName']);
            }else{
                // 标题
                $item['title'] = sprintf('%s', $item['companyName']);
            }

            // 获取能力特征词
            $_abilityList = $this->Jdjobabilityfeature->fetchListByIds(explode('|', $item['abilityFeature']));
            if (!empty($_abilityList)) {
                $item['abilityFeatureName'] = join(' | ', array_column($_abilityList, 'abilityFeatureName'));
                // 标题
                $item['title'] = sprintf('%s （%s）', $item['title'], $item['abilityFeatureName']);
            }
        }
        $this->load->view('default/demand_week', $data);
    }

    /**
     * 全部分类
     */
    public function category() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $ctid = (int)$this->input->get('ctid');
        if (empty($ctid) || $ctid <= 0) {
            $ctid = self::$defaultCateTypeId;
        }
        $page = (int)$this->input->get('page');
        $pagesize = min(10, (int)$this->input->get('pagenum'));
        if($page <= 0) $page = 1;
        if($pagesize <= 0) $pagesize = 10;

        $params = [];
        $params['companyTypeId'] = $ctid;
        $data = [
            'status' => true,
            'page'   => $page,
            'title'  => '全部分类',
            'list'   => [],
        ];
        $this->load->model('Jdjobbase');
        $data['list'] = $this->Jdjobbase->fetchDemandList([], $page, $pagesize, [], $params);
        $dataTotal = $this->Jdjobbase->fetchDemandTotal([], [], $params);

        //分页显示
        $class_file = APPPATH . 'libraries/' . BLH_Utilities::$subclass_prefix . 'Pager' . '.php';
        BLH_Utilities::require_only($class_file);
        BLH_Pager::$pageDisplayConfig = array(
            'default_display_count' => $pagesize,
            'system_display_config' => array(min($pagesize, 5),10,20,30),
        );
        $Pager = new BLH_Pager(array('pagesize'=>$pagesize, 'count'=>$dataTotal));
        $data['pageShow'] = $Pager->show();

        $this->load->model('Jdjobabilityfeature');
        $this->load->model('Jdjobcompanytype');

        // 获取行业列表
        $companyTypeList = $data['companyTypeList'] = $this->Jdjobcompanytype->fetchList();

        foreach ($data['list'] as &$item) {
            // 更新日期
            $item['updateDate'] = !empty($item['updateTime']) ? date('Y-m-d', strtotime($item['updateTime'])) : date('Y-m-d');

            // 获取行业名称
            if (!empty($companyTypeList[$item['companyTypeId']])) {
                $item['companyTypeName'] = $companyTypeList[$item['companyTypeId']]['companyTypeName'];
                // 标题
                $item['title'] = sprintf('%s / %s', $item['companyName'], $item['companyTypeName']);
            }else{
                // 标题
                $item['title'] = sprintf('%s', $item['companyName']);
            }

            // 获取能力特征词
            $_abilityList = $this->Jdjobabilityfeature->fetchListByIds(explode('|', $item['abilityFeature']));
            if (!empty($_abilityList)) {
                $item['abilityFeatureName'] = join(' | ', array_column($_abilityList, 'abilityFeatureName'));
                // 标题
                $item['title'] = sprintf('%s （%s）', $item['title'], $item['abilityFeatureName']);
            }
        }
        $this->load->view('default/demand_category', $data);
    }

    /**
     * 我的信息
     */
    public function my() {
        if (!$this->auth(true, true)) {
            // 尚未登录或登录失效
            BLH_Utilities::showmessage($this->login_error_data['errmsg_zh'], APP_SITE_URL . '/users/login_page');
        }

        $userId = isset($this->_userid) ? (int)$this->_userid : 0;
        if (empty($userId) || $userId <= 0) {
            BLH_Utilities::outputError(-101, '用户ID不合法');
        }

        $this->load->model('Userinfo');
        $userInfo = $this->Userinfo->fetch_user_data($userId);
        // 我的合作者列表
        $partnerList = $this->Userinfo->fetchPartnerList($userId);

        $data = [
            'status'      => true,
            'title'       => '我的信息',
            'userInfo'    => $userInfo,
            'partnerList' => $partnerList,
        ];

        $this->load->view('default/demand_my', $data);
    }

}
