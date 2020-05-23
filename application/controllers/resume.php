<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 简历模块
 *
 */
class Resume extends BLH_Controller{
    private $common_data;
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = TRUE;
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
        $this->common_data = array('title'=>'简历浏览_鸡蛋招聘_不一样的招聘快感' , 'keywords'=>'职位描述,招聘,HR,人力资源,JD,奔跑吧JD', 'description'=>'只需点点鼠标，改改文字，即可快速生成内容详实的职位描述。涵盖全行业全职位，职位描述的内容依据大数据挖掘不断优化！', 'webSiteUrl'=>APP_SITE_URL);
        parent::__construct(false);
    }

    /**
     * 简历浏览-长链接
     */
    public function show()
    {
        $get = $this->input->get();
        $resumeString = isset($get['id']) ? $get['id'] : '';
    	if (empty($resumeString))
    	{
    		BLH_Utilities::showmessage('参数错误');
    	}
    	//简历ID
    	$resumeId = BLH_Utilities::uc_authcode($resumeString);
    	if (strlen($resumeId) <= 0)
    	{
    		//$resumeStringDecode = urldecode(urlencode(urlencode($resumeString)));
            $resumeStringDecode = str_replace(' ', '+', $resumeString);
    		$resumeId = BLH_Utilities::uc_authcode($resumeStringDecode);
    	}
        $this->showPage($resumeId);
    }

    /**
     * 简历浏览-短链接
     */
    public function s($resumeString = '', $qid = 0)
    {
        if (empty($resumeString))
        {
            BLH_Utilities::showmessage('参数错误');
        }
        //简历ID
        $resumeId = BLH_Base62::decode($resumeString);
        if (strlen($resumeId) <= 0)
        {
            $resumeId = 0;
        }
        $this->showPage($resumeId);
    }

    private function showPage($resumeId)
    {
        if ($resumeId <= 0)
        {
            BLH_Utilities::showmessage('该简历不存在');
        }
        $this->load->model('Jdjobresumebase');
		$resumeInfo = $this->Jdjobresumebase->fetchJdBaseInfoById($resumeId, TRUE);
        if (empty($resumeInfo) OR empty($resumeInfo['state']))
        {
            BLH_Utilities::showmessage('该简历不存在');
        }
    	if ($resumeInfo['state'] == 'delete')
        {
            BLH_Utilities::showmessage('该简历已删除');
        }
		//$resumeInfo['resumeInit'] = preg_replace('/\n\<br \/\>/i', '', $resumeInfo['resumeInit']);
		//$resumeInfo['resumeInit'] = str_replace('&lt;br /&gt;', '', $resumeInfo['resumeInit']);
		//$resumeInfo['resumeInit'] = strip_tags($resumeInfo['resumeInit']);
		$resumeInfo['resumeInit'] = html_entity_decode($resumeInfo['resumeInit']);
		//$resumeInfo['resumeInit'] = str_replace('<br>', '', $resumeInfo['resumeInit']);
		$resumeInfo['resumeInit'] = str_replace(array('<br />', '<br>'), array(), $resumeInfo['resumeInit']);
		if (!empty($resumeInfo['resumeInit']))
		{
			if ($resumeInfo['initResumeId'] > 0)
			{
				$resumeInfo['resumeInit'] = str_replace(array("\n", "\r\n"), '<br />', $resumeInfo['resumeInit']);
				$resumeInfo['resumeInit'] = preg_replace('/(.*?)([\d+]{0,})(\<br \/\>{1,})/i', '${1}\n${2}|', $resumeInfo['resumeInit']);
				$resumeInfo['resumeInit'] = str_replace(array('|\n', '|'), '', $resumeInfo['resumeInit']);
				$resumeInfo['resumeInit'] = str_replace('--\n', '--', $resumeInfo['resumeInit']);
				$resumeInfo['resumeInit'] = str_replace(array("\n", "\r\n"), '<br />', $resumeInfo['resumeInit']);
				$resumeInfo['resumeInit'] = str_replace('\n', '<br />', $resumeInfo['resumeInit']);
			}
		}
		$resumeInfo['resumeInit'] OR $resumeInfo['resumeInit'] = htmlspecialchars_decode($resumeInfo['resumeInit']);
		//项目经历
		if (!empty($resumeInfo['project']))
		{
            $resumeInfo['project'] = str_replace('&lt;br /&gt;', '', $resumeInfo['project']);
            $resumeInfo['project'] = str_replace(array("\n", "\r\n", "<p>\n<br />\n</p>"), '', $resumeInfo['project']);
            $resumeInfo['project'] = $this->_formatOutput($resumeInfo['project']);
			$resumeInfo['project'] = html_entity_decode($resumeInfo['project']);
		}
		//工作经历
		if (!empty($resumeInfo['work_experience']))
		{
            $resumeInfo['work_experience'] = str_replace('&lt;br /&gt;', '', $resumeInfo['work_experience']);
			$resumeInfo['work_experience'] = str_replace(array("\n", "\r\n", "<p>\n<br />\n</p>"), '', $resumeInfo['work_experience']);
            // Edit By 20160308 16:07
            $resumeInfo['work_experience'] = str_replace(array("<br /><br /><br /><br /><br /><br />"), '<br />', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = str_replace(array("<br /><br /><br /><br /><br />"), '<br />', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = str_replace(array("<br /><br /><br /><br />"), '<br />', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = str_replace(array("<br /><br /><br />"), '<br />', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = str_replace(array("<br /><br />"), '<br />', $resumeInfo['work_experience']);

            $resumeInfo['work_experience'] = str_replace(array("<br /></p><br /><p>"), '', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = str_replace(array("<div><br />"), '', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = str_replace(array("<br /></div>"), '', $resumeInfo['work_experience']);
            $resumeInfo['work_experience'] = $this->_formatOutput($resumeInfo['work_experience']);
			$resumeInfo['work_experience'] = html_entity_decode($resumeInfo['work_experience']);
		}
		//自我介绍
		if (!empty($resumeInfo['self_introduction']))
		{
            $resumeInfo['self_introduction'] = str_replace('&lt;br /&gt;', '', $resumeInfo['self_introduction']);
            $resumeInfo['self_introduction'] = str_replace(array("\n", "\r\n", "<p>\n <br />\n</p>"), '', $resumeInfo['self_introduction']);
			$resumeInfo['self_introduction'] = html_entity_decode($resumeInfo['self_introduction']);
		}
		//获取职位分类列表
		$this->load->model('Jdjobclass');
		$jobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0, TRUE, 'new');
        //获取用户职位名称
        $userJobClassName = !empty($jobClassList[$resumeInfo['jobClassId']]['jobClassName']) ? $jobClassList[$resumeInfo['jobClassId']]['jobClassName'] : '';
        if (!empty($jobClassList[$resumeInfo['jobClassId']]['jobClassName'])) {
            $tmpClassNameArray = explode('-', $jobClassList[$resumeInfo['jobClassId']]['jobClassName'], 2);
            if (!empty($tmpClassNameArray[1])) {
                $userJobClassName = $tmpClassNameArray[1];
            }
        }
        //简历标题
        $tmpTitle = array();
        !empty($resumeInfo['jsjId']) && $tmpTitle[] = $resumeInfo['jsjId'];
        !empty($resumeInfo['userName']) && $tmpTitle[] = $resumeInfo['userName'];
        //!empty($jobClassList[$resumeInfo['jobClassId']]['jobClassName']) && $tmpTitle[] = $jobClassList[$resumeInfo['jobClassId']]['jobClassName'];
        !empty($userJobClassName) && $tmpTitle[] = $userJobClassName;
        $resumeTitle = join('-', $tmpTitle);
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
            'keywords' => $this->common_data['keywords'],
            'description' => $this->common_data['description'],
            'webSiteUrl' => $this->common_data['webSiteUrl'],
            'resumeInfo' => $resumeInfo,
            'jobClassList' => $jobClassList,
            'jobLevelList' => $jobLevelList,
            'jobAreaList' => $jobAreaList,
            'jobCompanyTypeList' => $jobCompanyTypeList,
            'jobAbilityFeatureList' => $jobAbilityFeatureList,
            'jobResumePertainFeatureList' => $jobResumePertainFeatureList,
            'resumeTitle' => $resumeTitle,
            'userJobClassName' => $userJobClassName,
        );
        $this->render('default/job_resume_show.php', $params);
        exit;
    }

    public function test() {
        $data = array('baseData'=>array('companyId'=>1,'companyName'=>'公司名','jobDesc'=>'数据分析','jobCnt'=>1),'resumeDesc'=>'简历描述');
        $this->fetchTplContent($data);
        echo $this->tplContent;
        exit;
    }

    private function fetchTplContent($data) {
        $this->common_data = array('title'=>'简历推荐_继也招聘_不一样的招聘快感' , 'keywords'=>'职位描述,招聘,HR,人力资源,JD,奔跑吧JD', 'description'=>'只需点点鼠标，改改文字，即可快速生成内容详实的职位描述。涵盖全行业全职位，职位描述的内容依据大数据挖掘不断优化！', 'webSiteUrl'=>APP_SITE_URL);
        $this->tplName = HLZ_APP_PATH . '/application/views/default/company_resume_email.php';
        $this->companyResumeCss = dirname(HLZ_APP_PATH) . '/www/bootstrap/css/company_resume.css';
        $this->tplContent = file_get_contents($this->tplName);
        $this->cssContent = file_get_contents($this->companyResumeCss);
        $this->tplContent = str_replace('{css_content}', $this->cssContent, $this->tplContent);
        $this->tplContent = str_replace('{companyId}', $data['baseData']['companyId'], $this->tplContent);
        $this->tplContent = str_replace('{companyName}', $data['baseData']['companyName'], $this->tplContent);
        $this->tplContent = str_replace('{jobDesc}', $data['baseData']['jobDesc'], $this->tplContent);
        $this->tplContent = str_replace('{jobCnt}', $data['baseData']['jobCnt'], $this->tplContent);
        $this->tplContent = str_replace('{resumeDesc}', $data['resumeDesc'], $this->tplContent);
        $this->tplContent = str_replace('{image_path}', IMAGE_PATH, $this->tplContent);
        $this->tplContent = str_replace('{css_path}', CSS_PATH, $this->tplContent);
        $this->tplContent = str_replace('{app_site_domain}', APP_SITE_DOMAIN, $this->tplContent);
        $this->tplContent = str_replace('{title}', $this->common_data['title'], $this->tplContent);
        $this->tplContent = str_replace('{keywords}', $this->common_data['keywords'], $this->tplContent);
        $this->tplContent = str_replace('{description}', $this->common_data['description'], $this->tplContent);
        return $this->tplContent;
    }
    private function _formatOutput($content)
    {
        if (empty($content))
        {
            return $content;
        }
        $rexConfig = array(
            '/(19|20)(\d{2})(.*?)-(.*?)\<br \/\>/',
            '/(19|20)(\d{2})(.*?)\<br \/\>/',
        );
        foreach ($rexConfig as $rex)
        {
            preg_match($rex, $content, $match);
            if (isset($match[0]) && !empty($match[0]))
            {
                return preg_replace($rex, '<b>$0</b>', $content);
            }
        }
        return $content;
    }
}
