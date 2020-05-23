<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends BLH_Controller {

    public function __construct()
    {
        parent::__construct(FALSE);
    }
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index($uid = '')
    {
        $r = parse_url('http://www.localgravity.com');
        echo '<pre>$r=>';var_dump($r);
        exit;
    	/*$email_account = '394253152@qq.com';
		$email_title = 'title_20150924';
		$email_body = 'body_20150924';
    	$FromName = '鸡蛋招聘';
    	$ret = $this->sendEmail($email_account, $email_title, $email_body, $FromName);
    	echo 'ret=><pre>';var_dump($ret);
    	exit;*/
    	$id = 1;
    	$string_en = BLH_Utilities::uc_authcode($id, 'ENCODE');
    	$string_de = BLH_Utilities::uc_authcode($string_en);
		echo '$str_en=>'.$string_en;echo '<br />';
		echo '$str_de=>'.$string_de;echo '<br />';
    	
    	exit;
		
        $this->load->library('BLH_McryptDes');
    	$string_en2 = BLH_McryptDes::getInstance('ejAT6rSo')->encrypt($id);
    	$string_de2 = BLH_McryptDes::getInstance('ejAT6rSo')->decrypt($string_en2);
		echo '$string_en2=>'.$string_en2;echo '<br />';
		echo '$string_de2=>'.$string_de2;echo '<br />';
    	
    	exit;
        $max_num = 50;
        $num = 51;
        
        $string = '（任职要求）1. 了解互联网软件产品整体实现过程，包括从需求分析到产品发布；2. 了解交互逻辑、对产品设计有独到的见解；有创新能力，勇于尝试新的产品方法和工具，并提成合理化建议，推进产品模型的改进；3. 本科学历或以上学历，1年以上互联网相关行业工作经验；4. 具备独立撰写产品文档的能力，熟练掌握产品需求分析，熟练使用office软件，了解Axure等产品原型设计软件，会使用思维流程分析软件；4. 较强的项目管理能力，善于跨部门组织、沟通和协调资源，良好的团队合作意识。5. 主动思考、积极向上，有较强的逻辑分析能力和学习能力，喜爱知乎网，爱折腾优先。6. 有（XXX）经历者优先。';
        //$jsonData = json_decode($string, TRUE);
        //echo '<pre>jsonData=>';var_dump($jsonData);
        $ret = preg_match('/^（.*）(.*)/', $string, $matches);
        echo '<pre>$matches=>';var_dump($matches, $ret);
        
        exit;
        echo 'data=>';echo $num % $max_num;
        echo '<br />';
        
        exit;
        $json_string = '[
            {"id":"1","email":"1@1.cn","sex":"0","nickname":"","blhRole":"0","company":"","position":"",
                 "cellphone":"","qq":"","weibo":"","weixin":"","other":"","iconUrl":"","invitedby":"3",
                 "inviteIntro":"\u8d50\u6211\u4e00\u4e2a\u6fc0\u6d3b\u7801\u5427","inviteCount":"0",
                 "publicemail":"1","publicsex":"0","publiccompany":"0","publicposition":"0","publiccellphone":"0",
                 "publicqq":"0","publicweibo":"1","publicweixin":"0","publicother":"0","status":"1",
                 "usertype":"0","createTime":"2013-11-15 15:00:21","updateTime":"2013-11-15 15:00:21",
                 "disableReason":"","career":"","area":"","birthday":"0000-00-00 00:00:00","isMarried":"0",
                 "signature":""
            },
            {"id":"2","email":"2@wer.rtt","sex":"0","nickname":"","blhRole":"0","company":"","position":"",
                "cellphone":"","qq":"","weibo":"","weixin":"","other":"","iconUrl":"","invitedby":"0",
                "inviteIntro":"","inviteCount":"0","publicemail":"1","publicsex":"0","publiccompany":"0",
                "publicposition":"0","publiccellphone":"0","publicqq":"0","publicweibo":"1","publicweixin":"0",
                "publicother":"0","status":"0","usertype":"0","createTime":"2013-11-15 14:25:26",
                "updateTime":"2013-11-15 14:25:26","disableReason":"","career":"","area":"",
                "birthday":"0000-00-00 00:00:00","isMarried":"0","signature":""
            },
            {"id":"3","email":"3@5.cn","sex":"0","nickname":"\u5b54\u660e","blhRole":"0",
                "company":"\u5927\u8700\u56fd","position":"\u4e1e\u76f8","cellphone":"24565326","qq":"54346785",
                "weibo":"","weixin":"","other":"","iconUrl":"","invitedby":"3",
                "inviteIntro":"\u8d50\u6211\u4e00\u4e2a\u6fc0\u6d3b\u7801\u5427","inviteCount":"0",
                "publicemail":"1","publicsex":"0","publiccompany":"0","publicposition":"0","publiccellphone":"0",
                "publicqq":"0","publicweibo":"0","publicweixin":"0","publicother":"0","status":"1",
                "usertype":"0","createTime":"2013-11-14 13:54:09","updateTime":"2013-11-14 22:06:09",
                "disableReason":"","career":"","area":"\u897f\u5ddd","birthday":"1234-11-12 00:00:00",
                "isMarried":"0","signature":"\u7d2f\u6b7b\u7b97\u7403"
            },
            {"id":"4","email":"4@4.cn","sex":"0",
                "nickname":"","blhRole":"0","company":"","position":"","cellphone":"","qq":"","weibo":"",
                "weixin":"","other":"","iconUrl":"","invitedby":"0","inviteIntro":"","inviteCount":"0",
                "publicemail":"1","publicsex":"0","publiccompany":"0","publicposition":"0","publiccellphone":"0",
                "publicqq":"0","publicweibo":"1","publicweixin":"0","publicother":"0","status":"0",
                "usertype":"0","createTime":"2013-11-14 13:48:07","updateTime":"2013-11-14 13:48:06",
                "disableReason":"","career":"","area":"","birthday":"0000-00-00 00:00:00","isMarried":"0",
                "signature":""
            },
            {"id":"5","email":"5@3.cn","sex":"0","nickname":"","blhRole":"0","company":"",
                "position":"","cellphone":"","qq":"","weibo":"","weixin":"","other":"","iconUrl":"",
                "invitedby":"0","inviteIntro":"","inviteCount":"0","publicemail":"1","publicsex":"0",
                "publiccompany":"0","publicposition":"0","publiccellphone":"0","publicqq":"0","publicweibo":"1",
                "publicweixin":"0","publicother":"0","status":"0","usertype":"0",
                "createTime":"2013-11-14 13:45:04","updateTime":"2013-11-14 13:45:04","disableReason":"",
                "career":"","area":"","birthday":"0000-00-00 00:00:00","isMarried":"0","signature":""
            },
            {"id":"6","email":"6@123.com","sex":"0","nickname":"","blhRole":"0","company":"","position":"",
                "cellphone":"","qq":"","weibo":"","weixin":"","other":"","iconUrl":"","invitedby":"0",
                "inviteIntro":"","inviteCount":"0","publicemail":"1","publicsex":"0","publiccompany":"0",
                "publicposition":"0","publiccellphone":"0","publicqq":"0","publicweibo":"1","publicweixin":"0",
                "publicother":"0","status":"0","usertype":"0","createTime":"2013-11-13 17:52:39",
                "updateTime":"2013-11-13 17:52:39","disableReason":"","career":"","area":"",
                "birthday":"0000-00-00 00:00:00","isMarried":"0","signature":""
            },
            {"id":"7","email":"7@3.cn",
                "sex":"0","nickname":null,"blhRole":"0","company":"","position":null,"cellphone":"","qq":"",
                "weibo":null,"weixin":"","other":"","iconUrl":null,"invitedby":"3",
                "inviteIntro":"\u8d50\u6211\u4e00\u4e2a\u6fc0\u6d3b\u7801\u5427","inviteCount":"0",
                "publicemail":"1","publicsex":"0","publiccompany":"0","publicposition":"0","publiccellphone":"0",
                "publicqq":"0","publicweibo":"1","publicweixin":"0","publicother":"0","status":"1","usertype":"0",
                "createTime":"2013-11-11 15:08:24","updateTime":"2013-11-11 15:08:24","disableReason":null,
                "career":null,"area":null,"birthday":null,"isMarried":"0","signature":null
            },
            {"id":"8","email":"8@123.com","sex":"0","nickname":"","blhRole":"0","company":"","position":"",
                "cellphone":"","qq":"","weibo":"","weixin":"","other":"","iconUrl":"","invitedby":"0",
                "inviteIntro":"","inviteCount":"0","publicemail":"1","publicsex":"0","publiccompany":"0",
                "publicposition":"0","publiccellphone":"0","publicqq":"0","publicweibo":"1","publicweixin":"0",
                "publicother":"0","status":"0","usertype":"0","createTime":"2013-11-13 17:52:39",
                "updateTime":"2013-11-13 17:52:39","disableReason":"","career":"","area":"",
                "birthday":"0000-00-00 00:00:00","isMarried":"0","signature":""
            }
        ]';
        $data = json_decode($json_string, TRUE);
        /*$a = array('baseInfo'=>array(
            'id' => 1,'name'=>'aaa'
        ));
        $d = array('baseInfo'=>array());
        $b = array('id'=>1, 'name'=>'bbb','sex'=>1,'fff'=>'1,2,3,4,5');
        $c = array_merge($d['baseInfo'], $b);
        echo '<pre>c=>';var_dump($c);
        exit;*/
        //echo 'lang=>'.$this->lang->line('union_create_limited');
        //exit;
        $encryption_key = '*&^JK_%^#@.,564';
        $t = 'a:5:{s:10:"session_id";s:32:"b79bb0aaa23097ed79987196d0ce87b4";s:10:"ip_address";s:14:"101.40.142.193";s:10:"user_agent";s:43:"mobile: MI 2, SdkVersion:19, systemName:4.4";s:13:"last_activity";i:1398778755;s:9:"user_data";s:0:"";}0370546e723e3107d3e6446c5b5345d8';
        
        $hash    = substr($t, strlen($t)-32); // get last 32 chars
        $session = substr($t, 0, strlen($t)-32);
        $data = unserialize($session);
        $session_id = $data['session_id'];//'1a0d7b176de34fa60650fa597a8e4d6a';//
        $md5 = md5($session_id.$encryption_key);
        
        echo 'key=>'.$encryption_key.'<br />';
        echo 'md5=>'.$md5.'<br />';
        echo 'hash=>'.$hash.'<br />';
        echo 't=>';var_dump($data);
        echo '<br />';
        
        $iconUrl = 'http://115.28.47.162/hlz.php/pics/p/6/535faa67e5873.jpg';
        $iconUrl = str_replace(APP_SITE_URL, '', $iconUrl);
        echo 'iconUrl=>'.$iconUrl.'<br />';
        exit;
        $ret = array(
            'status' => TRUE,
            'count' => count($data),
            'list' => $data,
        );
        //echo json_encode($ret);
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        /*$sendUserId = 15030;
        $receiveUserid = 15041;
        $sendUserUnionId = 9;
        $this->load->model('UserChat');
        $userChatData = $this->UserChat->getUserChatBySendUid($sendUserId, $receiveUserid, $sendUserUnionId);
        echo '$userChatData=>';var_dump($userChatData);exit;*/
        //$this->load->library('session');
        //$user_data_session = $this->session->all_userdata();
        //dump('user_data_session=>', $user_data_session);
        //Redis测试
        /*$this->load->model('Redis_model');
        $key = 'test_1';
        $value = array('qwertyuiop', 'abcdefg', 'hijklmn');
        $this->Redis_model->open();
        $ret2 = $this->Redis_model->set($key, json_encode($value), 20);
        $ret = $this->Redis_model->get($key);
        vdump('ret2=>', $ret2);
        vdump('ret=>', $ret);
        vdump('ret_decode=>', json_decode($ret,true));*/
        
        //Memcache测试
        /*$this->load->model('Memcached_model');
        $this->Memcached_model->open();
        $key = 'test_2';
        $value = array('111', '333', '222');
        $ret2 = $this->Memcached_model->set($key, json_encode($value), 20);
        $ret = $this->Memcached_model->get($key);
        vdump('ret2=>', $ret2);
        vdump('ret=>', $ret);
        vdump('ret_decode=>', json_decode($ret,true));*/
        if ((int)$uid <= 0) exit("params is failed!");
        $third_userid = $uid;//'1646270114';//'1646270114';//'1764636634';//'1942332501';//'1921463427'-cr;//'1188687973'-grb
        //$weiboObj = BLH_Utilities::getWeibo($this);
        //$ret = $weiboObj->getUserInfo($third_userid);
//        $ret = $this->send_weibo_api('getUserInfo', array($third_userid), TRUE);
        //通过关键词搜索用户
        //$ret = $this->send_weibo_api('searchUser', array($third_userid, '百度', FALSE, TRUE, FALSE), TRUE);//account_profile_basic、account_career、account_education
        //获取推荐好友
        //$ret = $this->send_weibo_api('getInterested', array($third_userid,1,100), TRUE);
        //$ret = $this->send_weibo_api('getSuggestUsersHot', array($third_userid, 'tech'), TRUE);
//        $ret = $this->send_weibo_api('getSearchSuggestCompany', array($third_userid, '百度'), TRUE);
//        $ret = $this->send_weibo_api('get_tags', array($third_userid), TRUE);
        $weiboUserInfo = $this->send_weibo_api('getUserInfo', array($third_userid), TRUE);//account_profile_basic、getUserInfo
        echo '<pre>微博用户基本信息=>';
        var_dump($weiboUserInfo);
        echo "#####################[<b>{$weiboUserInfo['screen_name']}</b>]的公司数据###########################<br />";
        $weiboUserJobs = $this->send_weibo_api('account_career', array($third_userid), TRUE);
        if (!empty($weiboUserJobs) && !isset($weiboUserJobs['error_code']) && empty($weiboUserJobs['error']))
        {
            foreach ($weiboUserJobs as $jobItem)
            {
                $endTime = $jobItem['end'] == 9999 ? '至今' : $jobItem['end'];
                echo "公司名[{$jobItem['company']}] | 职位[{$jobItem['department']}] | 入职时间[{$jobItem['start']}] | 离职时间[{$endTime}]<br />";
            }
        }
        //获取当前登陆用户好友分组列表
        //mxz: friend:3541724653193573 特别关注:3667080299766486 good blog:3541724862933559 互联网:201008230003520803 回龙观:201008240003563469 
        $groupListFriends = $this->send_weibo_api('getGroupListOfFriends', array($third_userid, 'private'), TRUE);//getGroupListOfFriends getUsersByGroupShowBatch
        //$list_id = '3541724653193573,3667080299766486,201008240003563469';//'201008230003520803';
        //$uids = '1646270114,1646270114,1646270114';
        $group_lists = $user_info = array();
        if (!empty($groupListFriends['lists']))
        {
            foreach($groupListFriends['lists'] as $groupItem)
            {
                if ($groupItem['member_count'] >= 100) continue;
                $group_lists[$groupItem['idstr']] = $groupItem['name'];
                empty($user_info) && $user_info = $groupItem['user'];
            }
            $list_ids = array_keys($group_lists);
            echo "#####################[<b>{$weiboUserInfo['screen_name']}</b>]的微博分组好友列表###########################<br />";
            //获取某一好友分组下的成员列表
            $groupMemberLists = $this->send_weibo_api('getUsersByGroupMembersShowBatchBackend', array($third_userid, $list_ids), TRUE);
            if (!empty($groupMemberLists['result']))
            {
                foreach ($groupMemberLists['result'] as $groupValue)
                {
                    echo "<hr />------------------[分组名称-<b>{$groupValue['gname']}</b>]------------------<br />";
                    if (!empty($groupValue['members']))
                    {
                        $userBatchInfo = $this->send_weibo_api('getShowBatch', array($third_userid, $groupValue['members']), TRUE);
                        if (!empty($userBatchInfo))
                        {
                            foreach ($userBatchInfo as $userKey => $userItem)
                            {
                                echo "[{$userKey}][{$userItem['idstr']}] | [{$userItem['screen_name']}] | [{$userItem['location']}] | [{$userItem['description']}] | <a href='http://weibo.com/{$userItem['profile_url']}' target='_blank'>微博主页</a><br />";
                                
                            }
                        }
                    }else{
                        echo "分组为空...<br />";
                    }
                }
            }
        }
        
        /*string(5) "职业信息ret=>"
            array(1) {
              [0]=>
              array(8) {
                ["city"]=>
                int(8)
                ["company"]=>
                string(11) "Microdreams"
                ["department"]=>
                string(0) ""
                ["end"]=>
                int(9999)
                ["id"]=>
                int(9280539)
                ["province"]=>
                int(11)
                ["start"]=>
                int(2011)
                ["visible"]=>
                int(2)
              }
            }
         string(5) "教育信息ret=>"
            array(1) {
              [0]=>
              array(12) {
                ["area"]=>
                int(0)
                ["city"]=>
                int(0)
                ["department"]=>
                string(15) "外国语学院"
                ["department_id"]=>
                int(0)
                ["id"]=>
                int(139853174)
                ["is_verified"]=>
                string(0) ""
                ["province"]=>
                int(37)
                ["school"]=>
                string(15) "泰山医学院"
                ["school_id"]=>
                int(246661)
                ["type"]=>
                int(1)
                ["visible"]=>
                int(2)
                ["year"]=>
                int(2005)
              }
            }*/
//      $this->load->library('BLH_TauthClient');
//      $r = BLH_TauthClient::getToken($this);
//      vdump('r=>', $r);
        /*r=>
        array(1) {
          ["token"]=>
          string(6) "159352"
        }*/
        /*ini_set('display_errors', 1);
        $this->config->load('redis');
        $this->load->library('BLH_Redis');
        $this->BLH_Redis->load($this->config->item('hostname', 'redis'), $this->config->item('port', 'redis'));
        echo '<pre>';var_dump('$this->redis=>', $this->BLH_Redis);
        exit;
        $ret = $this->BLH_Redis->set('test', 123456, 60);
        $ret2 = $this->BLH_Redis->get('test');
        var_dump('$ret=>',$ret);
        var_dump('$ret2=>',$ret2);*/
        //$this->load->view('welcome_message');
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
