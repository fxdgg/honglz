<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

/**
 * 按企业汇总jd信息，来推荐简历
*/
class Cron_company_email extends BLH_Controller
{
	/**
	 * 是否测试环境
	 * @var string
	 */
	private static $isDev = TRUE;

	/**
	 * JD-常用的数据库-测试
	 * @var string
	 */
	private $jd_db_name_dev = 'dev';

	/**
	 * 每次处理的数据量
	 * @var int
	 */
	private $count = 1000;

	/**
	 * 性别对应关系
	 * @var array
	 */
	private static $gender_config_map = array(
		1 => '男',
		2 => '女',
	);

	/**
	 * 当前状态对应关系
	 * @var array
	 */
	private static $state_config_map = array(
		0 => '离职',
		1 => '在职',
	);

	/**
	 * 是否考虑创业企业对应关系
	 * @var array
	 */
	private static $innovate_config_map = array(
		0 => '不确定',
		1 => '愿意考虑创业企业',
		2 => '不考虑创业企业',
	);

    public function __construct()
    {
        $this->common_data = array('title'=>'简历推荐_继也招聘_不一样的招聘快感' , 'keywords'=>'职位描述,招聘,HR,人力资源,JD,奔跑吧JD', 'description'=>'只需点点鼠标，改改文字，即可快速生成内容详实的职位描述。涵盖全行业全职位，职位描述的内容依据大数据挖掘不断优化！', 'webSiteUrl'=>APP_SITE_URL);
        parent::__construct(false);
        $this->tplName = HLZ_APP_PATH . '/application/views/default/company_resume_email.php';
        $this->companyResumeCss = dirname(HLZ_APP_PATH) . '/www/bootstrap/css/company_resume.css';
    }

    public function index()
    {

    }

	/**
	* 每天定时把符合JD要求的简历信息发送到企业指定的邮箱中
	*/
    public function email_jd_data()
    {
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process start ========================\n";
        $this->load->model('Jdjobbase');
        //查询符合条件的JD列表
		$allJdListForEmail = $this->Jdjobbase->fetchAllJdListByCompany();
		echo '<pre>allJdListForEmail=>';var_dump($allJdListForEmail);echo PHP_EOL;//exit;
		if (!empty($allJdListForEmail))
		{
            $this->load->model('Jdjobresumebase');
            // 获取所有的职位列表
            $this->load->model('Jdjobclass');
            $allJobClassList = $this->Jdjobclass->fetchAllJdJobClassList(0, 0);
			$jobClassIdAreaList = $resumeListClassAreaList = array();
			foreach ($allJdListForEmail as $jdItem) {
                $companyResumeList = array();
                echo "\n======================== [JD-Id=>".$jdItem['id']."]-[".$jdItem['companyName']."] ========================\n";
                echo '<pre>jdItem=>';var_dump($jdItem);echo "\n";
                if (empty($jdItem['companyName'])) {
                    echo "---------------------[JD-Id=>{$jdItem['id']}]-企业名称为空，无法推送邮件------------------------\n";
                    continue;
                }
                if (empty($jdItem['emailGroup'])) {
                    echo "---------------------[JD-Id=>{$jdItem['id']}]-企业邮箱为空，无法推送邮件------------------------\n";
                    continue;
                }
                // 检查邮箱是否合法
                $emailGroup = array_unique(BLH_Utilities::filter($jdItem['emailGroup']));
                foreach ($emailGroup as $key => $emailValue) {
                    if (!$this->_check_email($emailValue)) {
                        echo "---------------------[JD-Id=>{$jdItem['id']}]-[{$emailValue}]-企业邮箱不合法，无法推送邮件------------------------\n";
                        unset($emailGroup[$key]);
                        continue;
                    }
                }
                if (!empty($jdItem['jobClassIdAreaGroup'])) {
                    $jobClassIdAreaList = array_unique(BLH_Utilities::filter($jdItem['jobClassIdAreaGroup']));
                }
                // 公司ID
                $companyResumeList['baseData']['companyId'] = $jdItem['id'];
                // 公司名称
                $companyResumeList['baseData']['companyName'] = $jdItem['companyName'];
                // 公司邮箱
                $companyResumeList['baseData']['companyEmail'] = join(',', $emailGroup);
                // 推荐的职位汇总
                $tmp = $tmpJobClassId = $existsId = array();
                foreach ($jobClassIdAreaList as $jobClassId_AreaId) {
                    list($jobClassId, $jobAreaId) = explode('_', $jobClassId_AreaId);
                    if ($jobClassId > 0 && !empty($allJobClassList[$jobClassId]['jobClassName'])) {
                        $tmp[] = $allJobClassList[$jobClassId]['jobClassName'];
                        $tmpJobClassId[$jobClassId] = 1;
                    }
                }
                echo '<pre>$jobClassIdAreaList=>';var_dump($jobClassIdAreaList);
                $companyResumeList['baseData']['jobDesc'] = '';//!empty($tmp) ? join('，', $tmp) : '';
                $companyResumeList['baseData']['jobCnt'] = 0;//!empty($tmp) ? count($tmp) : 0;
                $companyResumeList['baseData']['jobClassIdList'] = $tmpJobClassId;

                $tmpDesc = array();
                $companyResumeList['resumeData'] = array();
                // 遍历该企业对应的职业ID、地区ID
                foreach ($jobClassIdAreaList as $jobClassId_AreaId) {
                    list($jobClassId, $jobAreaId) = explode('_', $jobClassId_AreaId);
                    echo "---------------------[JD-Id=>{$jdItem['id']}]-[JD-jobClassId=>{$jobClassId}]-[JD-jobAreaId=>{$jobAreaId}]-START------------------------\n";
                    if ($jobClassId <= 0 OR $jobAreaId <= 0)
                    {
                        echo "没有该[jobClassId-jobAreaId]对应的简历数据，直接跳过\n";
                        continue;
                    }
                    //根据jobClassId、areaId获取相应的简历信息
                    if (!isset($resumeListClassAreaList[$jobClassId][$jobAreaId]))
                    {
                        $resumeListClassAreaList[$jobClassId][$jobAreaId] = $allResumeListForEmail = $this->Jdjobresumebase->fetchAllJdResumeBaseListForCompany(0, 0, 2, 'new', -1, 0, $jobClassId, $jobAreaId);
                    }else{
                        $allResumeListForEmail = $resumeListClassAreaList[$jobClassId][$jobAreaId];
                    }
                    echo '<pre>$allResumeListForEmail=>';var_dump($allResumeListForEmail);
                    if (!empty($allResumeListForEmail))
					{
                        $companyResumeList['resumeData'][$jobClassId]['jobClassId'] = $jobClassId;
                        $companyResumeList['resumeData'][$jobClassId]['jobClassName'] = !empty($allJobClassList[$jobClassId]['jobClassName']) ? $allJobClassList[$jobClassId]['jobClassName'] : '';
                        $companyResumeList['baseData']['jobCnt']++;
						foreach ($allResumeListForEmail as $jobClassIdResume => $listResume)
						{
							echo "---------------------[resumeId=>{$listResume['id']}]-[JobClassId=>{$jobClassId}]-PROCESS------------------------\n";
                            $listResume['sex'] = !empty(self::$gender_config_map[$listResume['userGender']]) ? self::$gender_config_map[$listResume['userGender']] : '男';
                            $listResume['age'] = $this->_birthday($listResume['userAge'] . '-01-01');
                            $companyResumeList['resumeData'][$jobClassId]['resumeList'][] = $listResume;
                            // 记录职位名称
                            $tmpDesc[$listResume['jobClassName']] = $listResume['jobClassName'];
                            // 记录简历ID
                            $existsId[] = $listResume['id'];
                        }
                        $this->renderResumeList($companyResumeList);
                    }
                }
                $companyResumeList['baseData']['jobDesc'] = !empty($tmpDesc) ? join('，', $tmpDesc) : '';
                echo '<pre>$companyResumeList=>';var_dump($companyResumeList);
                if (!empty($companyResumeList['resumeData'])) {
                    //收件人邮箱帐号
                    $email_account = $companyResumeList['baseData']['companyEmail'];
                    //获取发送邮件的配置
                    $email_config = $this->config->item('email_config');
                    //邮件标题
                    $email_title = sprintf($email_config['resume_self_config']['jd_email_title'], '推荐');
                    //邮件内容
                    $email_body = $this->fetchTplContent($companyResumeList);
                    echo '$email_account=>'.$email_account.'|$email_title=>'.$email_title.'|$email_body=>'.$email_body.PHP_EOL;
                    $ret = FALSE;
                    if (!empty($email_title) && !empty($email_body))
                    {
                        $ret = $this->sendEmail($email_account, $email_title, $email_body, $email_config['email_account_config']['FromName']);
                        $ret && $mailSendRet = TRUE;
                        // 邮件发送成功
						if (!empty($existsId) && $ret)
						{
							//更新该条简历记录的推送JD的状态，设置为1
        					$updateRet = $this->Jdjobresumebase->updateResumeBatch($existsId, -1, 1);
							echo "批量更新简历为已推送给企业的状态=>".json_encode($existsId).'|更新结果=>'.json_encode($updateRet)."\n";
						}
                        usleep(2000);
                    }
                    echo $ret ? 'JDID=>'.$companyResumeList['baseData']['companyId'].'|'.$email_account . "|推送给企业的简历列表邮件-成功\n" : 'JDID=>'.$companyResumeList['baseData']['companyId'].'|'.$email_account . "|推送给企业的简历列表邮件-失败\n";
                }else{
                    echo "[{$companyResumeList['baseData']['companyName']}]-暂无可以推送的简历\n";
                }
           }
        }
        echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process end ========================\n";
    }

    private function fetchTplContent($data) {
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

    // 生成推荐人信息
    private function renderResumeList(&$companyResumeList) {
        $companyResumeList['resumeDesc'] = '';
        $loop = 0;
        foreach ($companyResumeList['resumeData'] as $jobClassId => $jobItem) {
            $companyResumeList['resumeDesc'] .= '<h3>'.$jobItem['jobClassName'].'</h3>';
            if (!empty($jobItem['resumeList'])) {
                $tmp = '';
                foreach ($jobItem['resumeList'] as $item) {
                    //求职者信息(男，30岁，吉林大学，计算机科学与技术，本科)
                    $userTmp = array();
                    $userTmp['sex'] = !empty($item['sex']) ? $item['sex'] : '男';
                    $userTmp['userAge'] = !empty($item['age']) ? $item['age'].'岁' : '';
                    $userTmp['graduateSchool'] = !empty($item['graduateSchool']) ? $item['graduateSchool'] : '';
                    $userTmp['professional'] = !empty($item['professional']) ? $item['professional'] : '';
                    $userTmp['degree'] = !empty($item['degree']) ? $item['degree'] : '';
                    // 下属
                    $subordinate = !empty($item['subordinate']) ? $item['subordinate'] : '无';
                    // 当前月薪
                    $monthlySalary = !empty($item['monthlySalary']) ? $item['monthlySalary'] : '未知';
                    // 状态
                    $nowState = !empty(self::$state_config_map[$item['nowState']]) ? self::$state_config_map[$item['nowState']] : '离职';
                    // 是否考虑创业企业
                    $isInnovate = !empty(self::$innovate_config_map[$item['isInnovate']]) ? self::$innovate_config_map[$item['isInnovate']] : '不确定';
                    // 推荐费
                    $recommendCostRate = !empty($item['recommendCostRate']) ? $item['recommendCostRate'] . '%' : '面议';
                    // 简历链接-start
                    $resumeUrlStart = !empty($item['resumeUrl']) ? '<a href="'.$item['resumeUrl'].'" target="_blank">' : '';
                    // 简历链接-end
                    $resumeUrlEnd = !empty($item['resumeUrl']) ? '</a>' : '';
                    $tmp .= sprintf('<div class="list"><h4>%s候选人%d <span>（%s）</span>%s</h4>
                            <table><tr><td>行业：<span>%s</span></td><td>曾就职于：<span>%s</span></td><td>下属：<span>%s</span></td>
                            <td class="last">目前月薪：<span>%s</span></td></tr><tr><td>状态：<span>%s</span></td><td>创业意愿：<span>%s</span></td>
                            <td>所在地：<span>%s</span></td><td class="last">推荐费：<span>%s</span></td></tr></table></div></table>',
                        $resumeUrlStart, $item['jsjId'], join('，', $userTmp), $resumeUrlEnd, $item['professionTag'], $item['onceCompany'], $subordinate, $monthlySalary, $nowState, $isInnovate, $item['areaName'], $recommendCostRate);
                }
                $companyResumeList['resumeDesc'] .= $tmp;
                ++$loop;
            }
        }
    }

    //邮件格式验证的函数
    private function _check_email($email) {
        if(!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $email))
        {
            return false;
        }else{
            return true;
        }
    }

    //根据出生日期计算年龄
    private function _birthday($birthday) {
        $age = strtotime($birthday);
        if($age === false){
            return false;
        }
        list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
        $now = strtotime("now");
        list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
        $age = $y2 - $y1;
        if((int)($m2.$d2) < (int)($m1.$d1)) {
            $age -= 1;
        }
        return $age;
    }
}