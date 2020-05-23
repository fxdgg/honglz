<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

class Cron_resume_self extends BLH_Controller
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
	private $count = 100;

	/**
	 * 性别对应关系
	 * @var array
	 */
	private static $gender_config_map = array(
		1 => '男',
		2 => '女',
	);

    public function __construct()
    {
        parent::__construct(false);
    }

    public function index()
    {

    }

	/**
	* 每天定时把符合条件的简历信息发送到ZMX的邮箱中
	*/
    public function email_resume_data()
    {
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process start ========================\n";
        $this->load->model('Jdjobresumebase');
        $existsEmail = array();
		$email_body = '';
		//获取能力特征词列表
		$this->load->model('Jdjobabilityfeature');
		$jobAbilityFeatureList = $this->Jdjobabilityfeature->fetchAllJdJobAbilityFeatureList(0, 0, TRUE, 'new');
        do{
        	$allResumeTotalForEmail = $this->Jdjobresumebase->fetchAllJdResumeBaseTotalForEmail();
        	if ($allResumeTotalForEmail <= 0)
        	{
        		echo "需要推送邮件的简历总数=>{$allResumeTotalForEmail}|暂无需要推送的简历数据\n";
        		break;
        	}
        	$loop = floor(($allResumeTotalForEmail + $this->count - 1) / $this->count);
        	echo '需要推送的简历总数=>'.$allResumeTotalForEmail.'|count=>'.$this->count.'|loop=>'.$loop."\n";
        	if ($loop >= 1)
			{
				for ($page = 1; $page <= $loop; $page++)
				{
					$allResumeListForEmail = $this->Jdjobresumebase->fetchAllJdResumeBaseListForEmail($page, $this->count);
					if (!empty($allResumeListForEmail))
					{
						$existsId = array();
						foreach ($allResumeListForEmail as $jobClassId => $list)
						{
							echo "---------------------[jobClassId=>{$jobClassId}]-START------------------------\n";
	                    	foreach ($list as $key => $item)
	                    	{
								if (!isset($item['id']) OR empty($item['id'])) continue;
								if (isset($existsEmail[$item['id']]))
								{
									$existsId[] = $item['id'];
									continue;
								}
								if ($key == 0)
								{
									$email_body .= '<div style="font-family: Arial; font-size: 14px; line-height: 23px;"><b>'.$item['jobClassName'].'：</b></div>';
								}
								$userGender = self::$gender_config_map[$item['userGender']];
                                $resumeBaseUrl = !empty($item['resumeUrl']) ? $item['resumeUrl'] : urlencode(BLH_Base62::encode($item['id']));
								$userResumeUrl = APP_SITE_URL . '/r/s/' . $resumeBaseUrl;
								//处理能力特征词
								$userAbilityFeatureString = $userAbilityFeatureStringTmp = '';
								if (!empty($item['abilityFeature']))
								{
									$abilityFeatureArray = explode('|', $item['abilityFeature']);
									if ($abilityFeatureArray)
									{
										foreach ($abilityFeatureArray as $abilityId)
										{
											//配置的能力特征ID，不在能力特征列表中
											if (!isset($jobAbilityFeatureList[$abilityId])) continue;
											$user_field_value = '';
											if (isset(Jdjobabilityfeature::$ability_feature_field_config[$abilityId]))
											{
												$user_field_string = Jdjobabilityfeature::$ability_feature_field_config[$abilityId];
												$user_field_array = explode('|', $user_field_string);
												$user_field_cnt = count($user_field_array);
												foreach ($user_field_array as $user_field_item)
												{
													$user_field_value .= !empty($item[$user_field_item]) ? $item[$user_field_item] . ($user_field_cnt > 1 ? '、' : '') : '';
												}
											}
											$symbol_1 = isset(Jdjobabilityfeature::$ability_feature_field_config[$abilityId]) ? '（' : '';
											$symbol_2 = isset(Jdjobabilityfeature::$ability_feature_field_config[$abilityId]) ? '）' : '';
											$userAbilityFeatureStringTmp .= $jobAbilityFeatureList[$abilityId]['abilityFeatureName'] . $symbol_1 . $user_field_value . $symbol_2 . '，';
										}
									}
								}
								!empty($userAbilityFeatureStringTmp) && $userAbilityFeatureString = '<span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);">'.$userAbilityFeatureStringTmp.'</span>';
								$symbol = $userAbilityFeatureString ? '，' : '';
								$email_body .= '<div style="font-family: Arial; font-size: 14px; line-height: 23px;"><span style="background-color: window;">求职者'.$item['id'].'，'.$userGender.'，'.$item['userAge'].'年，毕业院校（'.$item['graduateSchool'].'），专业（'.$item['professional'].'），学历（'.$item['degree'].'），累计研发年限（'.'xxx）</span></div><div style="font-family: Arial; font-size: 14px; line-height: 23px;"><span style="background-color: window;">工作经历：</span></div><div style="font-family: Arial; font-size: 14px; line-height: 23px;"><div><span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);"></span><a href="'.$userResumeUrl.'" style="background-color: window;">'.$userResumeUrl.'</a><span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);">&nbsp;</span></div>';
								//$email_body .= '<div style="font-family: Arial; font-size: 14px; line-height: 23px;"><span style="background-color: window;">求职者'.$item['id'].'，'.$userGender.'，'.$item['userAge'].'年，意愿工作地（'.$item['areaName'].'）'.$symbol.'</span>'.$userAbilityFeatureString.'</div><div style="font-family: Arial; font-size: 14px; line-height: 23px;"><div><span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);"></span><a href="'.$userResumeUrl.'" style="background-color: window;">'.$userResumeUrl.'</a><span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);">&nbsp;</span></div>';


								$existsId[] = $item['id'];
								$existsEmail[$item['id']] = 1;
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|推送简历成功\n";
	                    	}
							echo "---------------------[jobClassId=>{$jobClassId}]-END------------------------\n";
							unset($list);
						}
						if (!empty($existsId))
						{
							//更新该条记录的推送状态，设置为1
        					$updateRet = $this->Jdjobresumebase->updateResumeBatch($existsId, 1);
							echo "[page=>{$page}]|[count=>{$this->count}]|更新为已推送状态=>".json_encode($existsId)."\n";
						}
					}else{
						echo "[page=>{$page}]|[count=>{$this->count}]|暂无需要推送的简历数据\n";
					}
					unset($allResumeListForEmail);
					usleep(100);
				}
			}else{
				echo "暂无需要推送的简历数据\n";
			}
        }while($allResumeTotalForEmail > 0);

        if (!empty($existsEmail) && !empty($email_body))
        {
        	//获取发送邮件的配置
			$email_config = $this->config->item('email_config');
			$email_account = $email_config['resume_self_config']['email_account'];
			$email_title = $email_config['resume_self_config']['email_title'];
			$ret = FALSE;
			if (!empty($email_title) && !empty($email_body))
			{
				$ret = $this->sendEmail($email_account, $email_title, $email_body, $email_config['email_account_config']['FromName']);
			}
			echo $ret ? $email_account . '|推送简历列表邮件-成功' : $email_account . '|推送简历列表邮件-失败';
        }
		echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process end ========================\n";
    }
}