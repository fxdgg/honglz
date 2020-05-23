<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 表单相关业务-老友记
*/
class Laoyouji extends BLH_Controller{
    /**
    * 当前页面的配置，勿随意变动ID，不需要的话，注释即可，不用删除
    */
    private static $form_config = array(
        1 => array('field'=>'nickname', 'name'=>'姓名', 'help'=>'必填', 'must'=>1),
        2 => array('field'=>'email', 'name'=>'常用邮箱', 'help'=>'必填，资格审核通过后接收调研结果', 'must'=>1, 'only'=>1),
        3 => array('field'=>'sex', 'name'=>'性别', 'help'=>'', 'other'=>1),
        4 => array('field'=>'area', 'name'=>'城市', 'help'=>'', 'other'=>1),
        5 => array('field'=>'before_product', 'name'=>'在我厂隶属的产品线', 'help'=>'如：LBS，百度贴吧，凤巢等'),
        6 => array('field'=>'before_vocation', 'name'=>'在我厂的职位', 'help'=>'', 'other'=>1),
        7 => array('field'=>'leave_time', 'name'=>'离职时间', 'help'=>'', 'other'=>1),
        8 => array('field'=>'company', 'name'=>'现公司', 'help'=>''),
        14 => array('field'=>'depart', 'name'=>'现公司的职级', 'help'=>'', 'other'=>1),
        9 => array('field'=>'recommend_man', 'name'=>'证明人', 'help'=>'填写一位可以证明你在我厂履历的人，这个人也已离职'),
        10 => array('field'=>'qq', 'name'=>'如何联系（QQ号）', 'help'=>''),
        11 => array('field'=>'cellphone', 'name'=>'如何联系（手机号）', 'help'=>''),
        12 => array('field'=>'weixin', 'name'=>'如何联系（微信号）', 'help'=>''),
        13 => array('field'=>'memo', 'name'=>'向老友记留言', 'help'=>'希望老友记提供的帮助，可以为老友记提供的支持等'),
        14 => array('field'=>'passwd', 'name'=>'密码（必填）', 'help'=>'', 'must'=>1),
        15 => array('field'=>'passwdconf', 'name'=>'确认密码（必填）', 'help'=>'', 'must'=>1),
    );

    /**
    * 当前表单的社团配置
    */
    private static $union_config = array(
        'unionId' => 1, //社团ID，必填
        'unionName' => '老友记', //社团名称，必填
        'formScriptName' => 'laoyouji', //跟文件名一致
        'formTitle' => '2015年<{unionName}>年会筹备（暨：第一次老友记人口普查）', //表单标题，必填
        'formContent' => '<p>2015年的老友记年会即将召开，为了让老友记成员间更好的相互结识，互通有无，我们进行第一次老友记人口普查。</p>
        <p>参与调研的同学将与老友记众多高手一起，在合作、求职、招聘、融资、活动等方面得到协助与支持。除姓名、邮箱必填外，其他项均选填。我们对您的联系方式保密。调研从2014年12月x日开始，持续20天，2014年12月x日结束。参与调研的同学审核通过后将通过邮箱收到调研结果（联系方式不公开），调研结果也会在年会中印成小册子发放。</p>
        <p>欢迎参加调研，欢迎将本调研分享给你身边的朋友！</p>', //表单描述，必填
         'Sex' => array('男', '女'), //原产品，必填
         'City' => array('北京', '上海', '广州', '深圳', '成都', '杭州', '武汉', '南京'), //地区
        'beforeProduct' => array('新浪游戏', '新浪新闻', '新浪微博', '腾讯广点通', '商务搜索部', '网页搜索部'), //原产品，必填
        'beforePosition' => array('研发', '测试', '产品', '设计', '市场', '销售', '商务', '法务', '人力'), //原职位，必填
        'nowPosition' => array('研发', '测试', '产品', '设计', '市场', '销售', '商务', '法务', '人力'), //现职位，必填
        'leavetime' => array('上市前', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014'), //离职时间，必填
        'depart' => array('创始人', '联合创始人', 'VP', '管理者', '高工', '奋斗中'), //现职位，必填
        'submitLocationUrl' => '', //提交表单后跳转到的链接，默认为空，不跳转
    );

    public function __construct()
    {
        parent::__construct(false);
        //设置当前表单的社团ID
        $this->current_union_csrf = rawurlencode(BLH_Utilities::uc_authcode(self::$union_config['unionId'], 'ENCODE', FORM_CSRF_KEY));
    }
    public function index()
    {
        BLH_Utilities::redirect(APP_SITE_URL . '/form/show', 0, true, true, false, true);
    }
    /**
    * 表单展示
    * http://115.28.47.162/hlztest.php/laoyouji/form
    */
    public function form()
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
            'unionId' => $this->current_union_csrf,
            'unionName' => self::$union_config['unionName'],
            'formConfig' => self::$form_config,
            'formScriptName' => self::$union_config['formScriptName'],
            'formTitle' => str_replace(array('<{unionName}>'), array(self::$union_config['unionName']), self::$union_config['formTitle']),
            'formContent' => str_replace(array('<{unionName}>'), array(self::$union_config['unionName']), self::$union_config['formContent']),
            'beforeProduct' => self::$union_config['beforeProduct'],
            'beforePosition' => self::$union_config['beforePosition'],
            'Sex' => self::$union_config['Sex'],
            'City' => self::$union_config['City'],
            'leavetime' => self::$union_config['leavetime'],
            'nowPosition' => self::$union_config['nowPosition'],
            'depart' => self::$union_config['depart'],
            'isView' => $isView,
        );
        $this->load->view('default/form_show', $params);
    }
    /**
    * 表单生成
    */
    public function submit()
    {
        $unionIdCsrf = $this->input->post('unionId');
        $entry = $this->input->post('entry');
        $unionId = BLH_Utilities::uc_authcode(rawurldecode($unionIdCsrf), 'DECODE', FORM_CSRF_KEY);
        if (!is_numeric($unionId) OR $unionId != self::$union_config['unionId'] OR empty($entry))
        {
            BLH_Utilities::showmessage('非法请求！');
        }
        if (!empty(self::$form_config))
        {
            $user_data = $only_data = array();
            foreach (self::$form_config as $field_id => $field_value)
            {
                if (!isset($field_value['field']) OR !isset($field_value['name'])) continue;
                if (isset($field_value['must']) && $field_value['must'] == 1 && empty($entry['field_' . $field_id]))
                {
                    BLH_Utilities::showmessage($field_value['name'] . '是必填项，不能为空！');
                    break;
                }
                if (isset($field_value['other']) && $field_value['other'] == 1)
                {
                    $user_data[$field_value['field']] = !empty($entry['field_' . $field_id]) ? $entry['field_' . $field_id] : $entry['field_' . $field_id . '_other'];
                }else{
                    $user_data[$field_value['field']] = !empty($entry['field_' . $field_id]) ? $entry['field_' . $field_id] : '';
                }
                //性别
                if ($field_value['field'] == 'sex')
                {
                    $user_data[$field_value['field']] = (!empty($entry['field_' . $field_id]) && $entry['field_' . $field_id] == '女') ? 2 : 1;
                }
                //职位/行业
                if ($field_value['field'] == 'vocation' && !empty($user_data[$field_value['field']]))
                {
                    $user_data['position'] = $user_data['career'] = $user_data[$field_value['field']];
                }
                //唯一的字段
                if (isset($field_value['only']) && $field_value['only'] == 1)
                {
                    $only_data[$field_value['field']] = array(
                        'name' => $field_value['name'],
                        'value' => $user_data[$field_value['field']],
                    );
                }
            }
            //检查密码、确认密码
            if (!empty($user_data['passwd']) && !empty($user_data['passwdconf']))
            {
                if($user_data['passwd'] != $user_data['passwdconf'])
                {
                    BLH_Utilities::showmessage('[密码、确认密码]不一致，请重新提交！');
                }
            }
            $this->load->model('Userinfo');
            if (!empty($only_data))
            {
                foreach ($only_data as $field_name => $field_item)
                {
                    if (empty($field_item['value']))
                    {
                        BLH_Utilities::showmessage('[' . $field_item['name'] . ']是必填项，不能为空！');
                        break;
                    }
                    switch ($field_name)
                    {
                        case 'email': //邮箱
                            $check = $this->Userinfo->fetch_user_by_email($field_item['value']);
                            if (is_array($check) && !empty($check))
                            {
                                BLH_Utilities::showmessage('[' . $field_item['value'] . ']已经被注册，请重新提交！');
                                break;
                            }
                            break;
                        default:break;
                    }
                }
            }
            //录入用户表
            if (!isset($user_data['passwd']) OR !isset($user_data['passwdconf']))
            {
                $user_data['passwd'] = $user_data['passwdconf'] = $this->default_passwd_from_union;
            }
            $user_data['status'] = $this->Userinfo->USER_STATUS_OK;
            $user_data['lastActivity'] = SYS_TIME;
            $userId = $this->Userinfo->addNew($user_data);
            if(!$userId OR $userId <= 0)
            {
                BLH_Utilities::showmessage('用户注册失败，请重新提交！');
            }
            //插入社团用户表
            $this->load->model('UserUnion');
            $inviteInfo = array(0, '', self::$union_config['unionId']);
            $ret_union = $this->UserUnion->createUnionUser($userId, $inviteInfo, FALSE, UserUnion::USER_UNION_ROLE_DELETE);
            if (!$ret_union)
            {
                BLH_Utilities::showmessage('加入社团失败，请重新提交！');
            }
            $submitUrl = !empty(self::$union_config['submitLocationUrl']) ? self::$union_config['submitLocationUrl'] : sprintf('%s/%s/form', APP_SITE_URL, self::$union_config['formScriptName']);
            BLH_Utilities::showmessage('提交成功！', $submitUrl);
            /*$params = array(
                'unionId' => $this->current_union_csrf,
                'unionName' => self::$union_config['unionName'],
                'formConfig' => self::$form_config,
                'formScriptName' => self::$union_config['formScriptName'],
                'formTitle' => str_replace(array('<{unionName}>'), array(self::$union_config['unionName']), self::$union_config['formTitle']),
                'formContent' => str_replace(array('<{unionName}>'), array(self::$union_config['unionName']), self::$union_config['formContent']),
            );
            $this->load->view('default/form_submit', $params);*/
        }else{
            BLH_Utilities::showmessage('表单错误！');
        }
    }
}
/*
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4AQAAAAARHwt/AAABsklEQVR4nL2UwWrCQBCGJ4mwXoxeBaOCT+AtgQUDHvoe7QuYnG0SrYdeavQB2vfwUDAYMC8hmEboVdOLQtbtrjdPDkI7ucwuP2Fmvtlf5dehwnX8/3mkqJERMKcsM4S+yvegBbFyvGS39VwJyC7sT6OdyHD1nbaPrGY27utHRK8xX7lQwukVHiSpP6yUtyJD6A9KDagzAfOS3db7/NzdguUmMkPox0wl31USriGPMDwG8bNi8VPqUKAeQh/aOYlH/SbsAGYIvcObZmcaO+GmucbwhugL6qPiYHV8e4zQ6/Bxytx4fmRZgal/ZP8s2x7bJy0DNX9de6tuXBrau6cVpn5lmQMR8z8W78P8jv3Lqb+oCx52+2miY/gxxb/wSAML00+F8IXk8QqA4jEW30nwsDMD1U/185x0JA8rw9WviTvBg2g1VP3c9nqSRznWNUz9L8txdOFhOCXMvnrUI3L+5ezMMO9ZuF5Xzt+EGcXwFa6XyPkvROeY/0v/k+/BZmqM3KeuJua/bk3Z9D7/I5JHlLp0gPO/iAkeD21/hfQ/spY8Mlw/wvXMhl8MoOFRzD797fkXhirT4uaj66cAAAAASUVORK5CYII=
post_data=>array(3) {
  ["unionId"]=>
  string(40) "3785kVDJ+Ks4bWhu19j0IR9mWAW0hM9TdPRaF6jL"
  ["entry"]=>
  array(23) {
    ["field_1"]=>
    string(6) "蝈蝈"
    ["field_2"]=>
    string(1) "1"
    ["field_3"]=>
    string(6) "北京"
    ["field_4"]=>
    string(12) "新浪游戏"
    ["field_4_other"]=>
    string(0) ""
    ["field_5"]=>
    string(15) "商务搜索部"
    ["field_6"]=>
    string(6) "研发"
    ["field_6_other"]=>
    string(0) ""
    ["field_7"]=>
    string(6) "guoguo"
    ["field_8"]=>
    string(10) "2014-12-01"
    ["field_9"]=>
    string(12) "北京腾讯"
    ["field_10"]=>
    string(6) "企鹅"
    ["field_11"]=>
    string(6) "其它"
    ["field_11_other"]=>
    string(12) "技术总监"
    ["field_12"]=>
    string(10) "推荐人a"
    ["field_13"]=>
    string(11) "13800138000"
    ["field_14"]=>
    string(15) "需要的支持"
    ["field_15"]=>
    string(15) "提供的帮助"
    ["field_16"]=>
    string(3) "111"
    ["field_17"]=>
    string(3) "222"
    ["field_18"]=>
    string(10) "gg@1.cn"
    ["field_19"]=>
    string(3) "444"
    ["field_20"]=>
    string(6) "备注"
  }
  ["commit"]=>
  string(6) "提交"
}
*/