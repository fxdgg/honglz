<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

class Cron_jd_email extends BLH_Controller
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

    public function __construct()
    {
        parent::__construct(false);
    }

    public function index()
    {

    }

	/**
	* 每天定时把符合JD要求的简历信息发送到JD指定的邮箱中
	*/
    public function email_jd_data()
    {
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process start ========================\n";
        $this->load->model('Jdjobbase');
        $this->load->model('Jdjobresumebase');
		//获取能力特征词列表
		$this->load->model('Jdjobabilityfeature');
		$jobAbilityFeatureList = $this->Jdjobabilityfeature->fetchAllJdJobAbilityFeatureList(0, 0, TRUE, 'new');
		//查询符合条件的JD条数
    	$allJdTotalForEmail = $this->Jdjobbase->allJdBaseListTotal(FALSE, '', -1, -2, 1, 1);
    	if ($allJdTotalForEmail <= 0)
    	{
    		echo "需要推送邮件的JD总数=>{$allJdTotalForEmail}|暂无需要推送的JD数据\n";
    		break;
    	}
    	$loop = floor(($allJdTotalForEmail + $this->count - 1) / $this->count);
    	echo '需要推送的JD总数=>'.$allJdTotalForEmail.'|count=>'.$this->count.'|loop=>'.$loop."\n";
    	if ($loop >= 1)
		{
            $existsUser = $existJdEmail = array();
			for ($page = 1; $page <= $loop; $page++)
			{
				//查询符合条件的JD列表
				$allJdListForEmail = $this->Jdjobbase->fetchAllJdBaseListByKey($page, $this->count, FALSE, '', -1, -2, FALSE, 'jobClassId_areaId', 1, 1);
				echo '<pre>allJdListForEmail=>';var_dump($allJdListForEmail);echo "\n";
				if (!empty($allJdListForEmail))
				{
					$existsNoJobClassId = array();
					foreach ($allJdListForEmail as $jobClassId_AreaId => $list)
					{
                        echo '<pre>list=>';var_dump($list);echo "\n";
				        $existsId = $existsEmail = $resumeListClassAreaList = array();
						$email_body_yx = $email_body_tj = '';
						list($jobClassId, $jobAreaId) = explode('_', $jobClassId_AreaId);
						echo "---------------------[JD-jobClassId=>{$jobClassId}]-[JD-jobAreaId=>{$jobAreaId}]-START------------------------\n";
						if ($jobClassId <= 0 OR $jobAreaId <= 0)
						{
							echo "没有该[jobClassId-jobAreaId]对应的简历数据，直接跳过\n";
							continue;
						}
						if (isset($existsNoJobClassId[$jobClassId]))
						{
							//没有该jobClassId对应的简历数据，直接跳过
							echo "没有该[jobClassId]对应的简历数据，直接跳过\n";
							continue;
						}
		                foreach ($list as $key => $item)
                    	{
							if (!isset($item['id']) OR empty($item['id']) OR empty($item['email']) OR isset($existJdEmail[$item['email']]))
							{
		        				echo "经过查询|该JD没有配置接收邮件的邮箱或该JD邮箱已处理|{$item['email']}，直接跳过\n";
								continue;
							}
                            echo "企业信息|id=>{$item['id']}|companyName=>{$item['companyName']}|email=>{$item['email']}|jobClassId=>{$item['jobClassId']}|areaId=>{$item['areaId']}\n";
                            $existJdEmail[$item['email']] = 1;
							//根据jobClassId获取相应的简历信息
                            if (!isset($resumeListClassAreaList[$jobClassId][$jobAreaId]))
                            {
							    $resumeListClassAreaList[$jobClassId][$jobAreaId] = $allResumeListForEmail = $this->Jdjobresumebase->fetchAllJdResumeBaseListForEmail(0, $this->count, 2, 'new', -1, 0, $jobClassId, $jobAreaId);
                            }else{
                                $allResumeListForEmail = $resumeListClassAreaList[$jobClassId][$jobAreaId];
                            }
							if (!empty($allResumeListForEmail))
							{
								$existsIdResume = array();
								foreach ($allResumeListForEmail as $jobClassIdResume => $listResume)
								{
									echo "---------------------[RESUME-jobClassId=>{$jobClassIdResume}]-START------------------------\n";
			                    	foreach ($listResume as $keyResume => $itemResume)
			                    	{
										if (!isset($itemResume['id']) OR empty($itemResume['id']))
                                        {
                                            echo "[id=>{$itemResume['id']}]|[page=>{$page}]|[count=>{$this->count}]|简历id不存在\n";
                                            continue;
                                        }
										if (isset($existsEmail[$itemResume['id']]))
										{
                                            echo "[id=>{$itemResume['id']}]|[page=>{$page}]|[count=>{$this->count}]|简历id重复\n";
											$existsId[] = $itemResume['id'];
											continue;
										}
										if ($keyResume == 0)
										{
											//营销邮件内容
											$email_body_yx .= '我们是“鸡蛋招聘”。每日为您推荐有诚意的求职者列表。（&nbsp;'.$itemResume['jobClassName'].'）<br>1.&nbsp;如希望与求职者面谈，请回复邮件告知其编号。我们将协助您与求职者联系。<br>2.&nbsp;目前，我们不收取任何费用，您可完全免费使用。<br>3.&nbsp;如不希望收到这样的邮件，请回复本邮件告知我们即可，打搅了！</b><br><br></div>';
											//$email_body_yx .= '推荐如下人才的简历供您选择。<br>您可以将满足要求的求职者编号回复邮件告知我们。我们代为面试，如求职者乐于到您们公司面试，我们将提供求职者的联系方式，您们直接约定面试时间。<br>如需人工帮助，请联系：周明轩（QQ && 微信：215941423），谢谢</b><br></div>';
											//推荐邮件内容
											//$email_body_tj .= '我们是“鸡蛋招聘”。每日为您推荐有诚意的求职者列表。（&nbsp;'.$itemResume['jobClassName'].'）<br>1.&nbsp;如希望与求职者面谈，请回复邮件告知其编号。我们将协助您与求职者联系。<br>2.&nbsp;目前，我们不收取任何费用，您可完全免费使用。<br>3.&nbsp;如不希望收到这样的邮件，请回复本邮件告知我们即可，打搅了！</b><br><br></div>';
											//$email_body_tj .= '推荐如下人才的简历供您选择。<br>您可以将满足要求的求职者编号回复邮件告知我们。我们代为面试，如求职者乐于到您们公司面试，我们将提供求职者的联系方式，您们直接约定面试时间。<br>如需人工帮助，请联系：周明轩（QQ && 微信：215941423），谢谢</b><br></div>';

											$email_body_tj .= '我们是“鸡蛋招聘”，一家年轻的创业公司，我们专注于用大数据挖掘优秀的人才，不断提升互联网招聘体验。<br><br>今日为您推荐如下&nbsp;'.$itemResume['jobClassName'].'<br>如果您希望与其面谈，请回复邮件告知求职者编号，我们会协助联系并安排其到您处面试（全程免费）。<br><br>如需人工帮助，请联系"IOS研发招聘经理"：周明轩（QQ && 微信：215941423），竭诚为您服务。</b><br></div>';
										}
										$userGender = self::$gender_config_map[$itemResume['userGender']];
										$userResumeUrl = APP_SITE_URL . '/r/s/' . urlencode(BLH_Base62::encode($itemResume['id'])) . '/{qid}';
										//处理能力特征词
										$userAbilityFeatureString = $userAbilityFeatureStringTmp = '';
										if (!empty($itemResume['abilityFeature']))
										{
											$abilityFeatureArray = explode('|', $itemResume['abilityFeature']);
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
															$user_field_value .= !empty($itemResume[$user_field_item]) ? $itemResume[$user_field_item] . ($user_field_cnt > 1 ? '、' : '') : '';
														}
													}
													$symbol_1 = isset(Jdjobabilityfeature::$ability_feature_field_config[$abilityId]) ? '（' : '';
													$symbol_2 = isset(Jdjobabilityfeature::$ability_feature_field_config[$abilityId]) ? '）' : '';
													$userAbilityFeatureStringTmp .= $jobAbilityFeatureList[$abilityId]['abilityFeatureName'] . $symbol_1 . $user_field_value . $symbol_2 . '，';
												}
											}
										}
										!empty($userAbilityFeatureStringTmp) && $userAbilityFeatureString = '<span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);">'.$userAbilityFeatureStringTmp.'</span>';
                                        //设置唯一键值
                                        $existsUserKey = sprintf('%s_%s_%s_%s_%s', $itemResume['userGender'], $itemResume['userAge'], $itemResume['areaId'], $itemResume['graduateSchool'], $userAbilityFeatureStringTmp);
                                        if (isset($existsUser[$existsUserKey]))
                                        {
                                            echo "[id=>{$itemResume['id']}]|[page=>{$page}]|[count=>{$this->count}]|简历信息重复|{$existsUserKey}\n";
                                            continue;
                                        }
										$symbol = $userAbilityFeatureString ? '，' : '';
										//营销邮件内容
										$email_body_yx .= '<div style="font-family: Arial; font-size: 14px; line-height: 30px;"><span style="background-color: window;">>>&nbsp;&nbsp;<b>编号'.$itemResume['id'].'</b>，'.$userGender.'，'.$itemResume['userAge'].'年'.$symbol.'</span>'.$userAbilityFeatureString.'<span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);"></span><a href="'.$userResumeUrl.'" style="background-color: window;">查看简历</a><span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);">&nbsp;</span></div>';
										//推荐邮件内容
										$email_body_tj .= '<div style="font-family: Arial; font-size: 14px; line-height: 30px;"><span style="background-color: window;">>>&nbsp;&nbsp;<b>编号'.$itemResume['id'].'</b>，'.$userGender.'，'.$itemResume['userAge'].'年'.$symbol.'</span>'.$userAbilityFeatureString.'<span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);"></span><a href="'.$userResumeUrl.'" style="background-color: window;">查看简历</a><span style="color: rgb(0, 0, 0); background-color: rgba(0, 0, 0, 0);">&nbsp;</span></div>';
                                        //设置唯一属性
                                        $existsUser[$existsUserKey] = 1;
										$existsId[] = $itemResume['id'];
										$existsEmail[$itemResume['id']] = 1;
										echo "[id=>{$itemResume['id']}]|[page=>{$page}]|[count=>{$this->count}]|生成简历模版成功\n";
			                    	}
			                    }
			                }else{
			                	$existsNoJobClassId[$jobClassId] = 1;
				        		echo "经过查询|没有该jobClassId对应的简历数据，直接跳过\n";
				        		continue;
			                }
			            }

                        reset($list);
		                //挨个给JD发送简历邮件
				        /*if (!empty($existsEmail) && !empty($email_body_yx))
				        {*/
                            $mailSendRet = FALSE;
				        	//获取发送邮件的配置
							$email_config = $this->config->item('email_config');
                            echo '<pre>listSendEmail=>';var_dump($list);echo "\n";
			                foreach ($list as $key => $item)
	                    	{
								if (!isset($item['id']) OR empty($item['id']) OR empty($item['email']))
								{
			        				echo "经过查询|JDID=>{$item['id']}|该JD没有配置接收邮件的邮箱，直接跳过\n";
									continue;
								}
								//收件人邮箱帐号
								$email_account = $item['email'];
								//邮件标题
								$email_title = $item['jdPushStatus'] == 2 ? sprintf($email_config['resume_self_config']['jd_email_title'], '推荐') : sprintf($email_config['resume_self_config']['jd_email_title'], '推荐');
								//邮件内容(状态2代表推荐邮件，否则是营销邮件)
                                $email_body = '<div style="font-family: Arial; font-size: 14px; line-height: 30px;"><b>'.$item['companyName'].'，您好：<br>';
								$email_body .= $item['jdPushStatus'] == 2 ? str_replace('{qid}', $item['id'], $email_body_tj) : str_replace('{qid}', $item['id'], $email_body_yx);
								$email_body .= '<img src="'.APP_SITE_DOMAIN.'/email/jd_'.$item['id'].'.jpg" style="display:none;">';
								$ret = FALSE;
								if (!empty($email_title) && !empty($email_body))
								{
									$ret = $this->sendEmail($email_account, $email_title, $email_body, $email_config['email_account_config']['FromName']);
                                    $ret && $mailSendRet = TRUE;
				                    usleep(100);
								}
								echo $ret ? 'JDID=>'.$item['id'].'|'.$email_account . "|推送给JD的简历列表邮件-成功\n" : 'JDID=>'.$item['id'].'|'.$email_account . "|推送给JD的简历列表邮件-失败\n";
							}

							if (!empty($existsId) && $mailSendRet)
							{
								//更新该条简历记录的推送JD的状态，设置为1
	        					$updateRet = $this->Jdjobresumebase->updateResumeBatch($existsId, -1, 1);
								echo "[page=>{$page}]|[count=>{$this->count}]|批量更新简历为已推送给JD的状态=>".json_encode($existsId).'|更新结果=>'.json_encode($updateRet)."\n";
							}
						/*}else{
	        				echo "经过查询|没有该jobClassId对应的简历数据(不应该走到这里)，直接跳过\n";
						}*/
					}
				}else{
					echo "[page=>{$page}]|[count=>{$this->count}]|暂无需要推送给JD的简历数据\n";
				}
				unset($allJdListForEmail);
				usleep(100);
			}
		}else{
			echo "暂无需要推送给JD的简历数据\n";
		}
		echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process end ========================\n";
    }
}
