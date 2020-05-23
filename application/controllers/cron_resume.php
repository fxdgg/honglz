<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

class Cron_resume extends CI_Controller
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
	 * JD-常用的数据库-线上
	 * @var string
	 */
	private $jd_db_name_production = 'production';

	/**
	 * 简历-tbl_keyword用的数据库
	 * @var string
	 */
	private $jd_db_name_keyword = 'jobanalyses';

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
		'男' => 1,
		'女' => 2,
	);

    public function index()
    {
        self::$isDev = defined('ENVIRONMENT') && ENVIRONMENT == 'development' ? TRUE : FALSE;
    }

	/**
	* 每天定时把简历的信息导入到对应的表
	*/
    public function import_resume_data()
    {
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process start ========================\n";
        $this->load->model('Jdjobresumebase');
        $this->load->model('Jdjobanalyses');
        $existsEmail = array();
        do{
        	$this->Jdjobanalyses->set_database($this->jd_db_name_keyword);
        	$allAnalysesResumeTotal = $this->Jdjobanalyses->fetchAllAnalysesResumeTotal();
        	if ($allAnalysesResumeTotal <= 0)
        	{
        		echo "需要更新的简历总数=>{$allAnalysesResumeTotal}|暂无需要更新的简历数据\n";
        		break;
        	}
        	$loop = floor(($allAnalysesResumeTotal + $this->count - 1) / $this->count);
        	echo '需要更新的简历总数=>'.$allAnalysesResumeTotal.'|count=>'.$this->count.'|loop=>'.$loop."\n";
        	if ($loop >= 1)
			{
				for ($page = 1; $page <= $loop; $page++)
				{
					$allAnalysesResumeList = $this->Jdjobanalyses->fetchAllAnalysesResumeList($page, $this->count);
					if (!empty($allAnalysesResumeList))
					{
						$existsId = array();
						foreach ($allAnalysesResumeList as $resumeId => $item)
						{
							echo "---------------------[id=>{$item['id']}]-START------------------------\n";
							if (!isset($item['name']) OR empty($item['name']) OR empty($item['email'])) continue;
							if (isset($existsEmail[$item['email']]))
							{
								$existsId[] = $item['id'];
								continue;
							}
	        				$this->Jdjobanalyses->set_database(self::$isDev ? $this->jd_db_name_dev : $this->jd_db_name_production);
							//插入简历记录
							$resume_data = array();
							$resume_data['userName'] = trim($item['name']);
							/*//查询是否存在该简历，防止重复导致冲突
							$keywordData = $this->Jdjobresumebase->fetchJdPositionDetailByPltk($position_id, self::$level, self::$type_map[$item['type']], $keyword);
							if (!empty($keywordData))
							{
								$keywordNewId =& $keywordData['kid'];
								unset($keywordData);
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|已存在的简历ID=>".$keywordNewId."\n";
							}else{*/
								$gender = 1;$workNum = 0;
								if (!empty($item['other']))
								{
									$item['other'] = str_replace("\n", '', $item['other']);
									list($workExperienceString, $genderString, $userAgeString) = explode('|', $item['other'], 3);
									$gender = FALSE !== strpos($genderString, '男') ? 1 : 2;//isset(self::$gender_config_map[$genderString]) ? self::$gender_config_map[$genderString] : 1;
									$userAge = (int)$userAgeString;
									if (!empty($workExperienceString))
									{
										list($workNum_1, $workNum_2) = explode('-', $workExperienceString);
										$workNum = ((int)$workNum_1 + (int)$workNum_2) / 2;
									}
								}
								$item['phone'] = str_replace('（手机）', '', $item['phone']);
								$resume_data['graduateSchool'] = $item['school']; //毕业院校
								$resume_data['professional'] = $item['professional']; //专业
								$resume_data['degree'] = $item['education']; //学历
								$resume_data['userAge'] = $userAge; //年龄
								$resume_data['mobile'] = floor(floatval($item['phone']));
								$resume_data['email'] = $item['email'];
								$resume_data['workExperience'] = floor($workNum);
								$resume_data['nowCompany'] = $item['company'];
								$resume_data['userGender'] = $gender;
								$lastId = $this->Jdjobresumebase->createJdResumeBaseInfo($resume_data, TRUE);
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|新生成的简历ID=>".$lastId."\n";
							//}
							if ($lastId > 0)
							{
								$existsId[] = $item['id'];
								$existsEmail[$item['email']] = 1;
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|插入简历成功\n";
							}else{
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|插入简历失败\n";
							}
							echo "---------------------[id=>{$item['id']}]-END------------------------\n";
							unset($item);
						}
						if (!empty($existsId))
						{
							//更新该条记录的状态，设置为1
        					$this->Jdjobanalyses->set_database($this->jd_db_name_keyword);
        					$updateRet = $this->Jdjobanalyses->updateAnalysesResumeBatch($existsId, 1);
							echo "[page=>{$page}]|[count=>{$this->count}]|录入简历成功|更新为已更新状态=>".var_export($updateRet, TRUE)."\n";
						}
					}else{
						echo "[page=>{$page}]|[count=>{$this->count}]|暂无需要更新的简历数据\n";
					}
					unset($allAnalysesResumeList);
					usleep(100);
				}
			}else{
				echo "暂无需要更新的简历数据\n";
			}
        }while($allAnalysesResumeTotal > 0);
		echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process end ========================\n";
    }
}