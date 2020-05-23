<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * JD生成相关模块
 *
 */
class Jdtools extends BLH_Controller{
    private $common_data;
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = TRUE;
    /**
     * 职级
     * @var array
     */
    public static $levelList = array('1'=>'初级', '2'=>'中级', '3'=>'高级');
    /**
     * 每个职位固定的关键词列表
     * @var array
     */
    public static $commonKeywordList = array(
    	//8359 => array('kid'=>8359, 'keyword'=>'岗位职责'),
    	//20266 => array('kid'=>20266, 'keyword'=>'任职要求'),
    	//-1 => array('kid'=>-1, 'keyword'=>'岗位职责'),
    	//-2 => array('kid'=>-2, 'keyword'=>'任职要求'),
    	22340 => array('kid'=>22340, 'keyword'=>'联系方式'),
    	8386 => array('kid'=>8386, 'keyword'=>'公司概述'),
    	8358 => array('kid'=>8358, 'keyword'=>'工作概述'),
    	8404 => array('kid'=>8404, 'keyword'=>'关于环境'),
    	19505 => array('kid'=>19505, 'keyword'=>'关于福利'),
    	17985 => array('kid'=>17985, 'keyword'=>'关于团队'),
    	8442 => array('kid'=>8442, 'keyword'=>'关于融资'),
    	16238 => array('kid'=>16238, 'keyword'=>'特别说明'),
    );
    /**
     * 可根据职位变换的关键词列表
     * @var array
     */
    public static $changeKeywordList = array(-1 => array('kid'=>-1, 'keyword'=>'岗位职责'), -2 => array('kid'=>-2, 'keyword'=>'任职要求') );
    /**
     * 可随机获取的关键词列表
     * @var array
     */
    public static $randomKeywordList = array('岗位职责'=>-1, '任职要求'=>-2);
    /**
     * 页面基础信息
     * @var array
     */
    public static $pageBaseList = array(
    	'copyright' => 'Copyright © 2015 zp0.cc',
    	'welcome_msg' => '欢迎使用 鸡蛋招聘',
    	'contact_us' => '联系我们：zp0@5tbang.com',
    	'qq_group' => 'QQ群（与同行HR交流）：367058210',
    );
    /**
     * cookie名称-UID
     * @var string
     */
    public static $cookie_key_uid = 'jd_i';
    /**
     * cookie名称-邮箱
     * @var string
     */
    public static $cookie_key_email = 'jd_e';
    /**
     * cookie名称-公司名称
     * @var string
     */
    public static $cookie_key_company = 'jd_c';

    public function __construct()
    {
        $this->common_data = array('title'=>'鸡蛋招聘_不一样的招聘快感' , 'webSiteUrl'=>APP_SITE_URL);
        parent::__construct(false);
    }

    /**
     * JD生成的统一入口
     * http://115.28.47.162/hlztest.php/jdtools/main/1
     */
    public function main($step = 1, $random_id = '')
    {
        if (method_exists($this, 'jd_' . $step) && in_array($step, array(1,2,6)))
        {
            $this->{'jd_' . $step}($step, $random_id);
        }
        BLH_Utilities::showmessage('拒绝访问');
    }

    /**
     * JD生成-步骤1
     */
    private function jd_1($step = 1)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $data = $this->input->post(NULL, true);
            if (!isset($data['company']) OR empty($data['company']))
            {
                BLH_Utilities::showmessage('公司名是必填项，不能为空！', $_SERVER['PHP_SELF'], 0, 1500);
            }
            if (!isset($data['email']) OR empty($data['email']))
            {
                BLH_Utilities::showmessage('邮箱是必填项，不能为空！', $_SERVER['PHP_SELF'], 0, 1500);
            }
            if (!isset($data['position']) OR empty($data['position']))
            {
                BLH_Utilities::showmessage('职位是必填项，不能为空！', $_SERVER['PHP_SELF'], 0, 1500);
            }
            if (!isset($data['level']) OR empty($data['level']))
            {
                //BLH_Utilities::showmessage('职级是必填项，不能为空！', $_SERVER['PHP_SELF'], 0, 1500);
                $data['level'] = 2;//职级默认中级
            }
            //获取所查询的职位ID，精确查询
            $this->load->model('Jdpositiondetail');
            $positionInfo = $this->Jdpositiondetail->fetchJdPositionDetailByName($data['position'], FALSE);
            if (empty($positionInfo) OR $positionInfo['pdid'] <= 0)
            {
            	BLH_Utilities::showmessage('['.$data['position'] . ']职位不存在，请选择其他职位！', $_SERVER['PHP_SELF'], 0, 1500);
            }
            //list($pid, $pname) = explode('-', $data['position']);
            $pname = $data['position'];
            $pid = $positionInfo['pdid'];
            $this->load->model('Userinfo');
            //$userId = BLH_Utilities::get_cookie(self::$cookie_key_uid);
            $getUserInfoByEmail = $this->Userinfo->fetch_user_by_email($data['email']);
            $userId = !empty($getUserInfoByEmail['id']) ? $getUserInfoByEmail['id'] : 0;
            $user_data = array();
            $user_data['nickname'] = !empty($getUserInfoByEmail['nickname']) ? $getUserInfoByEmail['nickname'] : 'JD生成';
            $user_data['company'] = htmlspecialchars($data['company']);
            $user_data['email'] = $data['email'];//($userId > 0 ? $data['email'] : BLH_Utilities::random(6, 1)) . $this->jd_email_suffix;
            $user_data['position'] = $user_data['career'] = $user_data['vocation'] = htmlspecialchars($pname);
            $user_data['memo'] = intval($data['level']);
            $user_data['blhRole'] = isset($getUserInfoByEmail['blhRole']) ? $getUserInfoByEmail['blhRole'] : Userinfo::USER_ROLE_WELCOME;
            $user_data['status'] = isset($getUserInfoByEmail['status']) ? $getUserInfoByEmail['status'] : $this->Userinfo->USER_STATUS_OK;
            $user_data['lastActivity'] = !empty($getUserInfoByEmail['lastActivity']) ? $getUserInfoByEmail['lastActivity'] : SYS_TIME;
            if (empty($userId))
            {
                //录入用户表
                if (!isset($user_data['passwd']) OR !isset($user_data['passwdconf']))
                {
                    $user_data['passwd'] = $user_data['passwdconf'] = $this->default_passwd_from_union;
                }
                $user_data['email'] = $data['email'];//BLH_Utilities::random(6, 1) . $this->jd_email_suffix;//htmlspecialchars($data['email']);
                $userId = $this->Userinfo->addNew($user_data);
                if(!$userId OR $userId <= 0)
                {
                    BLH_Utilities::showmessage('JD生成失败，请重试！', $_SERVER['PHP_SELF'], 0, 1500);
                }else{
                    //$user_data['email'] = $userId . $this->jd_email_suffix;
                    //更新用户邮箱
                    //$this->Userinfo->editEmail($userId, $user_data['email']);
                }
            }
            //写入Cookie
            BLH_Utilities::set_cookie(self::$cookie_key_uid, $userId, 60*60*24*365);
            BLH_Utilities::set_cookie(self::$cookie_key_email, $user_data['email'], 60*60*24*365);
            BLH_Utilities::set_cookie(self::$cookie_key_company, $data['company'], 60*60*24*365);
            //记录用户所选的信息(开启缓存的处理)
            if (self::$enableCache)
            {
                $cache_data = array(
                    'baseInfo' => array(
                    	'userId' => $userId,
                    	'email' => $user_data['email'],
                        'company' => $user_data['company'],
                        'position_id' => $pid,
                        'position' => $user_data['position'],
                        'level' => intval($data['level']),
                		'create_time' => date('Y-m-d H:i:s'),
                    ),
                    'describe' => array(),//岗位描述
                    'demand' => array(),//任职要求
                );
                $this->Userinfo->setCacheData(sprintf("JD_TOOLS|INFO|%s", $userId), $cache_data, YEAR_TIMESTAMP);
            }
            //设置登录状态
            $this->Userinfo->afterLogin($userId, '', (object)$user_data);
            
            BLH_Utilities::redirect(APP_SITE_URL . '/j/m/' . ($step+1).'?i='.$pid.'&l='.intval($data['level']));
            exit;
        }
        //获取所有职位
        $this->load->model('Jdpositiondetail');
        $positionList = $this->Jdpositiondetail->fetchJdPositionDetail();
		//获取Cookie里的邮箱、公司名称
        $cookie_email = BLH_Utilities::get_cookie(self::$cookie_key_email);
        $cookie_company = BLH_Utilities::get_cookie(self::$cookie_key_company);

        $params = array(
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'pageBaseList' => self::$pageBaseList,
            'step' => $step,
            'next_step' => $step + 1,
            'positionList' => $positionList,
            'levelList' => self::$levelList,
            'cookie_email' => $cookie_email,
            'cookie_company' => $cookie_company,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

    /**
     * JD生成-步骤2
     */
    private function jd_2($step = 2)
    {
        if(!$this->auth(TRUE, TRUE))
        {
            BLH_Utilities::showmessage('您尚未选择职位，禁止访问！');
        }
        $get = $this->input->get(NULL, true);
        //获取职位ID
        $id = isset($get['i']) ? (int)$get['i'] : 1;
        //获取职位等级
        $level = isset($get['l']) ? (int)$get['l'] : 1;
        //关键词ID
        $kid = isset($get['k']) ? (int)$get['k'] : 0;
        $displayPosLevel = '';
        if (isset(self::$levelList[$level]))
        {
            //获取所有职位
            $this->load->model('Jdpositiondetail');
            $positionDetail = $this->Jdpositiondetail->fetchJdPositionDetail($id);
            //$displayPosLevel = self::$levelList[$level] . $positionDetail['positionName'];
			$displayPosLevel = $positionDetail['positionName'];
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $post = $this->input->post(NULL, true);
            if (!empty($post['content']))
            {
                /*foreach ($post['content'] as $key =>& $val)
                {
                    $content_val = htmlspecialchars($val);
                    preg_match('/^（.*）(.*)/is', $content_val, $matches);
                    if (isset($matches[1]))
                    {
                        $val = $matches[1];
                    }
                }*/
            	$cache_content_list = array();
                //记录用户编辑过的JD描述信息
                $this->load->model('Jddescribelog');
                $this->load->model('Jdpositionduty');
                foreach ($post['content'] as $key2 =>& $val2)
                {
                	if (!empty($post['keyword_' . $key2]))
                	{
                		$val2 = str_replace('（' . $post['keyword_' . $key2] . '）', '', $val2);
                	}
                    if (!empty($val2) && !empty($post['change_' . $key2]) && $post['change_' . $key2] == 1)
                    {
                    	$describe_kid = !empty($post['describe_kid_' . $key2]) ? $post['describe_kid_' . $key2] : 0;
                    	$keywordInfo = $this->Jdpositionduty->fetchJdDutyDetailByKid($describe_kid);
                    	$describe_keyword = !empty($keywordInfo['keyword']) ? $keywordInfo['keyword'] : '';
                        $this->Jddescribelog->createJdDescribeLog($this->_userid, $id, $level, $describe_kid, $describe_keyword, $key2, $val2);
                    }
                    $cache_content_list[$key2] = array('keyword'=>$post['keyword_' . $key2], 'content'=>$val2);
                }
                //记录用户所选的信息(开启缓存的处理)
                if (self::$enableCache)
                {
                    $cache_key = sprintf("JD_TOOLS|INFO|%s", $this->_userid);
                    $cache_data = $this->Jddescribelog->getCacheData($cache_key);
                    if (!empty($cache_data['baseInfo']))
                    {
                    	$cache_data['baseInfo']['create_time'] = date('Y-m-d H:i:s');
                        $cache_data['describe'] = $cache_content_list;
                        $this->Jddescribelog->setCacheData($cache_key, $cache_data, YEAR_TIMESTAMP);
                    }
                    //记录静态页的数据
			        $random_id = BLH_Utilities::random(8, 1);
			        $cache_key = sprintf("JD_TOOLS|STATIC|PAGE|%d", $random_id);
			        $this->Jddescribelog->setCacheData($cache_key, $cache_data, YEAR_TIMESTAMP);
                }
            }
            BLH_Utilities::redirect(APP_SITE_URL . '/j/m/6/'.$random_id.'.html');
            exit;
        }

        //获取该职位对应的【岗位描述关键词】
        $this->load->model('Jdpositionduty');
        $list_init = $this->Jdpositionduty->fetchJdPositionDutyList($id, $level, 'describe', 0, FALSE, array_keys(self::$randomKeywordList));
        $keyword_content = '';
        $first_kid = 0;
        $search_kid = array();
        //哪些关键词需要过滤掉
        $isSkipRandomKeyword = array();
        if (empty($list_init))
        {
        	$list_init = self::$changeKeywordList;
        	//$isSkipRandomKeyword = !empty(self::$randomKeywordList) ? array_flip(self::$randomKeywordList) : array();
        }else{
        	if (self::$randomKeywordList)
        	{
        		$isExistKeyword = FALSE;
        		foreach (self::$randomKeywordList as $key => $value)
        		{
        			foreach ($list_init as $value_init)
        			{
        				if ($value_init['keyword'] == $key)
        				{
        					$isExistKeyword = TRUE;
        					break;
        				}
        			}
        			!$isExistKeyword && $list_init[$value] = self::$changeKeywordList[$value];
        		}
        	}
        }
        $isSkipRandomKeywordJson = '';//join('|', array_keys($isSkipRandomKeyword));
        //echo '<pre>$list_init=>';var_dump($list_init);
        $list = $list_init + self::$commonKeywordList;
        //echo '<pre>$list=>';var_dump($list);
        if ($list)
        {
        	$exists = array();
            $list_5 = array_keys($list);//array_slice(array_keys($list), 0, 10);
            if (!empty($list_5))
            {
                $search_kid =& $list_5;
            }
            foreach ($list as $item)
            {
            	if (isset($exists[$item['keyword']])) continue;
            	$exists[$item['keyword']] = 1;
                $first_kid <= 0 && $first_kid = $item['kid'];
                $keyword_content .= "<li id=\"li_id_".$item['kid']."\"><span onclick=\"getKeywordContent(this,{$id},{$level},{$item['kid']},'{$isSkipRandomKeywordJson}');\" title='{$item['keyword']}'>{$item['keyword']}</span></li>";
            }
        }
        !empty($search_kid) OR $search_kid = $kid > 0 ? array($kid) : array($first_kid);
        //根据关键词ID获取描述列表
        $this->load->model('Jddescribe');
        $describeList = $this->Jddescribe->fetchJdDescribeByKidKeys($search_kid);
        $describeData = array();
        if (!empty($describeList))
        {
        	foreach ($list as $key => $item)
            {
            	if (isset($describeList[$key]))
            	{
            		$describeKeyItem =& $describeList[$key];
            		$isRandom = FALSE;
	                foreach ($describeKeyItem as $value)
	                {
	                	if (!empty($value['keyword']) && isset(self::$randomKeywordList[$value['keyword']]))
	                	{
	                		$isRandom = TRUE;
	                	}
	                	break;
	                }
	                $describeData[$key] = $isRandom ? $describeKeyItem[array_rand($describeKeyItem)] : $describeKeyItem[0];
            	}else{
            		$describeData[$key] = array('id'=>$item['kid'], 'kid'=>$item['kid'], 'keyword'=>$item['keyword'], 'content'=>'');
            	}
            }
            /*foreach ($describeList as $key => $item)
            {
                //$itemSorted = BLH_Utilities::sortByCol($item, 'sortId', SORT_ASC);
                $isRandom = FALSE;
                foreach ($item as $value)
                {
                	if (!empty($value['keyword']) && isset(self::$randomKeywordList[$value['keyword']]))
                	{
                		$isRandom = TRUE;
                	}
                	break;
                }
                $describeData[$key] = $isRandom ? $item[array_rand($item)] : $item[0];
                //该职位没有该关键词时，仍然显示关键词，但描述为空
            	//if (!empty($isSkipRandomKeyword) && isset($isSkipRandomKeyword[$key]))
            	//{
            	//	$describeData[$key]['content'] = '';
            	//}
            }*/
        }
        //echo '<pre>$describeList=>';var_dump($describeList);
        //echo '<pre>$describeData=>';var_dump($describeData);
        $params = array(
            'id' => $id,
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'pageBaseList' => self::$pageBaseList,
            'lstep' => $step - 1,
            'step' => $step,
            'level' => $level,
            'keyword_content' => $keyword_content,
            'describeList' => $describeData,
            'displayPosLevel' => $displayPosLevel,
        	'randomKeywordList' => self::$randomKeywordList,
        	'isSkipRandomKeywordJson' => $isSkipRandomKeywordJson,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

    /**
     * JD生成-步骤2、3-切换描述、获取某关键词的1条描述
     */
    public function random_jd_content()
    {
        $data = array();
        $post = $this->input->post(NULL, true);
        if (!empty($post['kid']))
        {
            //关键词ID
            $kid = (int)$post['kid'];
            //描述ID，更换描述时使用
            $aid = isset($post['aid']) ? (int)$post['aid'] : 0;
	        //哪些关键词需要过滤掉
	        $jn = isset($post['jn']) ? htmlspecialchars($post['jn']) : '';
            //根据关键词ID获取描述列表
            $this->load->model('Jddescribe');
            $describeList = $this->Jddescribe->fetchJdDescribeByKidKeys(array($kid));
	        //哪些关键词需要过滤掉
	        //$isSkipRandomKeyword = !empty($jn) ? explode('|', trim($jn)) : array();
	        //$isSkipRandomKeywordFlip = !empty($isSkipRandomKeyword) ? array_flip($isSkipRandomKeyword) : array();
            $describeData = array();
            if (!empty($describeList))
            {
                foreach ($describeList as $key => $item)
                {
                    //$itemSorted = BLH_Utilities::sortByCol($item, 'sortId', SORT_ASC);
                    $isRandom = FALSE;
	                foreach ($item as $value)
	                {
	                	if (!empty($value['keyword']) && isset(self::$randomKeywordList[$value['keyword']]))
	                	{
	                		$isRandom = TRUE;
	                	}
	                	break;
	                }
	                $data = $isRandom ? $item[array_rand($item)] : $item[0];
	                //该职位没有该关键词时，仍然显示关键词，但描述为空
	            	/*if (!empty($isSkipRandomKeywordFlip) && isset($isSkipRandomKeywordFlip[$key]))
	            	{
	            		$data['content'] = '';
	            	}*/
                    break;
                }
            }
        }
        !empty($data['content']) && $data['content'] = str_replace(array("\r\n", "\r", "\n"), '', $data['content']);
        $data['isRandom'] = !empty($data['keyword']) && isset(self::$randomKeywordList[$data['keyword']]) ? 1 : 0;
        echo json_encode($data);
    }

    /**
     * JD生成-记录用户搜索的职位关键词
     */
    public function record_jd_position()
    {
        $data = array();
        $post = $this->input->post(NULL, true);
        if (!empty($post['p']))
        {
            //用户搜索的职位关键词
            $positionName = htmlspecialchars($post['p']);
            $userId = BLH_Utilities::get_cookie(self::$cookie_key_uid);
            //根据关键词ID获取描述列表
            $this->load->model('jdsearchpositionlog');
            $data['newId'] = $this->jdsearchpositionlog->createJdSearchPositionLog($userId, $positionName, TRUE);
        }
        echo json_encode($data);
    }

    /**
     * JD生成-获取用户搜索的职位列表
     */
    public function search_jd_position()
    {
        $data = array('status'=>FALSE, 'result'=>array());
        $post = $this->input->post(NULL, true);
        if (!empty($post['p']))
        {
            //用户搜索的职位关键词
            $positionName = htmlspecialchars($post['p']);
	        //获取所查询的职位
	        $this->load->model('Jdpositiondetail');
	        $data['result'] = $this->Jdpositiondetail->fetchJdPositionDetailByName($positionName);
	        if (empty($data['result']))
	        {
	        	//记录用户搜索不到的职位关键词
	        	$userId = BLH_Utilities::get_cookie(self::$cookie_key_uid);
	            //根据关键词ID获取描述列表
	            $this->load->model('jdsearchpositionlog');
	            $this->jdsearchpositionlog->createJdSearchPositionLog($userId, $positionName, TRUE);
	        }else{
	        	$data['status'] = TRUE;
	        }
        }
        echo json_encode($data);
    }

    /**
     * JD生成-步骤3
     */
    private function jd_3($step = 3)
    {
        if(!$this->auth(TRUE, TRUE))
        {
            BLH_Utilities::showmessage('您尚未选择职位，禁止访问！');
        }
        $get = $this->input->get(NULL, true);
        //获取职位ID
        $id = isset($get['i']) ? (int)$get['i'] : 1;
        //获取职位等级
        $level = isset($get['l']) ? (int)$get['l'] : 1;
        //关键词ID
        $kid = isset($get['k']) ? (int)$get['k'] : 0;
        $displayPosLevel = '';
        if (isset(self::$levelList[$level]))
        {
            //获取所有职位
            $this->load->model('Jdpositiondetail');
            $positionDetail = $this->Jdpositiondetail->fetchJdPositionDetail($id);
            $displayPosLevel = self::$levelList[$level] . $positionDetail['positionName'];
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $post = $this->input->post(NULL, true);
            if (!empty($post['content']))
            {
                /*foreach ($post['content'] as $key =>& $val)
                {
                    $content_val = htmlspecialchars($val);
                    preg_match('/^（.*）(.*)/is', $content_val, $matches);
                    if (isset($matches[1]))
                    {
                        $val = $matches[1];
                    }
                }*/
            	$cache_content_list = array();
                //记录用户编辑过的JD描述信息
                $this->load->model('Jddescribelog');
                $this->load->model('Jdpositionduty');
                foreach ($post['content'] as $key2 =>& $val2)
                {
                	if (!empty($post['keyword_' . $key2]))
                	{
                		$val2 = str_replace('（' . $post['keyword_' . $key2] . '）', '', $val2);
                	}
                    if (!empty($val2) && !empty($post['change_' . $key2]) && $post['change_' . $key2] == 1)
                    {
                    	$describe_kid = !empty($post['describe_kid_' . $key2]) ? $post['describe_kid_' . $key2] : 0;
                    	$keywordInfo = $this->Jdpositionduty->fetchJdDutyDetailByKid($describe_kid);
                    	$describe_keyword = !empty($keywordInfo['keyword']) ? $keywordInfo['keyword'] : '';
                        $this->Jddescribelog->createJdDescribeLog($this->_userid, $id, $level, $describe_kid, $describe_keyword, $key2, $val2);
                    }
                    $cache_content_list[$key2] = array('keyword'=>$post['keyword_' . $key2], 'content'=>$val2);
                }
                //记录用户所选的信息(开启缓存的处理)
                if (self::$enableCache)
                {
                    $cache_key = sprintf("JD_TOOLS|INFO|%s", $this->_userid);
                    $cache_data = $this->Jddescribelog->getCacheData($cache_key);
                    if (!empty($cache_data['baseInfo']))
                    {
                    	$cache_data['baseInfo']['create_time'] = date('Y-m-d H:i:s');
                        $cache_data['demand'] = $cache_content_list;
                        $this->Jddescribelog->setCacheData($cache_key, $cache_data, YEAR_TIMESTAMP);
                    }
                }
            }
            BLH_Utilities::redirect(APP_SITE_URL . '/j/m/' . ($step+1).'?i='.$id.'&l='.$level);
            exit;
        }
        //获取该职位对应的【岗位描述关键词】
        $this->load->model('Jdpositionduty');
        $list = $this->Jdpositionduty->fetchJdPositionDutyList($id, $level, 'demand');
        $keyword_content = '';
        $first_kid = 0;
        $search_kid = array();
        if ($list)
        {
            $list_5 = array_keys($list);//array_slice(array_keys($list), 0, 6);
            if (!empty($list_5))
            {
                $search_kid =& $list_5;
            }
            foreach ($list as $item)
            {
                $first_kid <= 0 && $first_kid = $item['kid'];
                $keyword_content .= '<li id="li_id_'.$item['kid'].'"><span onclick=\'getKeywordContent(this,'.$id.','.$level.','.$item['kid'].');\' title="' . $item['keyword'] . '">' . $item['keyword'] . '</span></li>';
            }
        }
        //$search_kid = $kid > 0 ? $kid : $first_kid;
        !empty($search_kid) OR $search_kid = $kid > 0 ? array($kid) : array($first_kid);
        //根据关键词ID获取描述列表
        $this->load->model('Jddescribe');
        $describeList = $this->Jddescribe->fetchJdDescribeByKidKeys($search_kid);
        $describeData = array();
        if (!empty($describeList))
        {
            foreach ($describeList as $key => $item)
            {
                //$itemSorted = BLH_Utilities::sortByCol($item, 'sortId', SORT_ASC);
                $describeData[$key] = $item[array_rand($item)];
            }
        }
        
        $params = array(
            'id' => $id,
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'pageBaseList' => self::$pageBaseList,
            'lstep' => $step - 1,
            'step' => $step,
            'level' => $level,
            'keyword_content' => $keyword_content,
            'describeList' => $describeData,
            'displayPosLevel' => $displayPosLevel,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

    /**
     * JD生成-步骤3
     */
    private function jd_3_bak($step = 3)
    {
        if(!$this->auth(TRUE, TRUE))
        {
            BLH_Utilities::showmessage('您尚未选择职位，禁止访问！');
        }
        $get = $this->input->get(NULL, true);
        //获取职位ID
        $id = isset($get['i']) ? (int)$get['i'] : 1;
        //获取职位等级
        $level = isset($get['l']) ? (int)$get['l'] : 1;
        //关键词ID
        $kid = isset($get['k']) ? (int)$get['k'] : 0;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $post = $this->input->post(NULL, true);
            if (!empty($post['content']))
            {
                //记录用户编辑过的JD描述信息
                $this->load->model('Jddescribelog');
                foreach ($post['content'] as $key => $val)
                {
                    $content_val = htmlspecialchars($val);
                    if (!empty($content_val) && !empty($post['change_' . $key]) && $post['change_' . $key] == 1)
                    {
                        $this->Jddescribelog->createJdDescribeLog($this->_userid, $key, $val);
                    }
                }
                //记录用户所选的信息(开启缓存的处理)
                if (self::$enableCache)
                {
                    $cache_key = sprintf("JD_TOOLS|INFO|%s", $this->_userid);
                    $cache_data = $this->Jddescribelog->getCacheData($cache_key);
                    if (!empty($cache_data['baseInfo']) && isset($cache_data['demand']))
                    {
                        $cache_data['demand'] = $post['content'];
                        $this->Jddescribelog->setCacheData($cache_key, $cache_data, YEAR_TIMESTAMP);
                    }
                }
            }
            BLH_Utilities::redirect(APP_SITE_URL . '/j/m/' . ($step+1).'?i='.$id.'&l='.$level.'&k='.$kid);
            exit;
        }
        //获取该职位对应的【任职能力关键词】
        $this->load->model('Jdpositionduty');
        $list = $this->Jdpositionduty->fetchJdPositionDutyList($id, $level, 'demand');
        $keyword_content = '';
        $first_kid = 0;
        $search_kid = array();
        if ($list)
        {
            $list_5 = array_slice(array_keys($list), 0, 5);
            if (!empty($list_5))
            {
                $search_kid =& $list_5;
            }
            foreach ($list as $item)
            {
                $first_kid <= 0 && $first_kid = $item['kid'];
                $keyword_content .= '<li><a href="' . APP_SITE_URL . '/j/m/'.$step.'?i='.$id.'&l='.$level.'&k=' . $item['kid'] . '" title="' . $item['keyword'] . '">' . $item['keyword'] . '</a></li>';
            }
        }
        empty($search_kid) && $search_kid = $kid > 0 ? array($kid) : array($first_kid);
        //根据关键词ID获取描述列表
        $this->load->model('Jddescribe');
        $describeList = $this->Jddescribe->fetchJdDescribeByKidKeys($search_kid);
        
        $params = array(
            'id' => $id,
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'step' => $step,
            'keyword_content' => $keyword_content,
            'describeList' => $describeList,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

    /**
     * JD生成-步骤4
     */
    private function jd_4($step = 4)
    {
        if(!$this->auth(TRUE, TRUE))
        {
            BLH_Utilities::showmessage('您尚未选择职位，禁止访问！');
        }
        $get = $this->input->get(NULL, true);
        //获取职位ID
        $id = isset($get['i']) ? (int)$get['i'] : 1;
        //获取职位等级
        $level = isset($get['l']) ? (int)$get['l'] : 1;
        $displayPosLevel = $staticPageLevel = '';
        if (isset(self::$levelList[$level]))
        {
            //获取所有职位
            $this->load->model('Jdpositiondetail');
            $positionDetail = $this->Jdpositiondetail->fetchJdPositionDetail($id);
            $displayPosLevel = self::$levelList[$level] . '  ' . $positionDetail['positionName'];
            $staticPageLevel = self::$levelList[$level] . $positionDetail['positionName'];
        }
        $this->load->model('Jddescribe');
        $cache_key = sprintf("JD_TOOLS|INFO|%s", $this->_userid);
        $cache_data = $this->Jddescribe->getCacheData($cache_key);

        $random_id = BLH_Utilities::random(8, 1);
        $cache_key = sprintf("JD_TOOLS|STATIC|PAGE|%d", $random_id);
        $this->Jddescribe->setCacheData($cache_key, $cache_data, YEAR_TIMESTAMP);
        $params = array(
            'id' => $id,
            'lstep' => $step - 1,
            'step' => $step,
            'rstep' => $step + 1,
            'level' => $level,
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'pageBaseList' => self::$pageBaseList,
            'data' => $cache_data,
            'levelList' => self::$levelList,
            'displayPosLevel' => $displayPosLevel,
            'staticPageLevel' => $random_id,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

    /**
     * JD生成-步骤5
     */
    private function jd_5($step = 5, $random_id = 0)
    {
        $this->load->model('Jddescribe');
        $cache_key = sprintf("JD_TOOLS|STATIC|PAGE|%d", $random_id);
        $cache_data = $this->Jddescribe->getCacheData($cache_key);
        if (empty($cache_data))
        {
        	BLH_Utilities::showmessage('不存在的JD');
        }
        $cache_level = isset($cache_data['baseInfo']['level']) ? $cache_data['baseInfo']['level'] : '';
        $cache_position_name = isset($cache_data['baseInfo']['position']) ? $cache_data['baseInfo']['position'] : '';
		//$displayPosLevel = self::$levelList[$cache_level] . $cache_position_name;
		$displayPosLevel = $cache_position_name;

		$params = array(
            'lstep' => $step - 1,
            'step' => $step,
            'rstep' => $step + 1,
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'pageBaseList' => self::$pageBaseList,
            'data' => $cache_data,
            'levelList' => self::$levelList,
            'displayPosLevel' => $displayPosLevel,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

    /**
     * JD生成-步骤6
     */
    private function jd_6($step = 6, $random_id = 0)
    {
        $this->load->model('Jddescribe');
        $cache_key = sprintf("JD_TOOLS|STATIC|PAGE|%d", $random_id);
        $cache_data = $this->Jddescribe->getCacheData($cache_key);
        if (empty($cache_data))
        {
        	BLH_Utilities::showmessage('不存在的JD');
        }
        $cache_level = isset($cache_data['baseInfo']['level']) ? $cache_data['baseInfo']['level'] : '';
        $cache_position_name = isset($cache_data['baseInfo']['position']) ? $cache_data['baseInfo']['position'] : '';
		//$displayPosLevel = self::$levelList[$cache_level] . $cache_position_name;
		$displayPosLevel = $cache_position_name;

        $params = array(
            'lstep' => $step - 1,
            'step' => $step,
            'rstep' => $step + 1,
            'title' => $this->common_data['title'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'pageBaseList' => self::$pageBaseList,
            'data' => $cache_data,
            'levelList' => self::$levelList,
            'displayPosLevel' => $displayPosLevel,
        );
        $this->render('default/jd_' . $step . '.php', $params);
        exit;
    }

}
