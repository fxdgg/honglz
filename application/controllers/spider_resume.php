<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

class Spider_resume extends CI_Controller
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
	 * 简历-t_resume用的数据库
	 * @var string
	 */
	private $jd_db_spider_resume = 'spider_resume';

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
	* 每天定时把抓取的简历信息导入到线上的简历表
	*/
    public function import_data($current_date = '')
    {
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process start ========================\n";
        $this->load->model('Jdjobresumebase');
		$this->load->model('Jdjobarea');
        $this->load->model('Spiderresume');
        $existsResumeName = $jobAreaList = array();
        $initLoop = 0;
        empty($current_date) && $current_date = date('Y-m-d');
        $today_timestamp = strtotime(date('Y-m-d 00:00:00', strtotime($current_date)));
        do{
        	$this->Spiderresume->set_database($this->jd_db_spider_resume);
        	$allSpiderResumeTotal = $this->Spiderresume->fetchAllSpiderResumeTotal($today_timestamp);
        	if ($allSpiderResumeTotal <= 0)
        	{
        		echo "需要导入的简历总数=>{$allSpiderResumeTotal}|暂无需要导入的简历数据\n";
        		break;
        	}
        	$loop = floor(($allSpiderResumeTotal + $this->count - 1) / $this->count);
        	echo '需要导入的简历总数=>'.$allSpiderResumeTotal.'|count=>'.$this->count.'|loop=>'.$loop."\n";
        	if ($loop >= 1)
			{
				for ($page = 1; $page <= $loop; $page++)
				{
					$allSpiderResumeList = $this->Spiderresume->fetchAllSpiderResumeList($page, $this->count, $today_timestamp);
					if (!empty($allSpiderResumeList))
					{
						$existsId = array();
						foreach ($allSpiderResumeList as $resumeId => $item)
						{
							echo "---------------------[id=>{$item['id']}]-START------------------------\n";
							if (!isset($item['content']) OR empty($item['content']))
							{
								$existsId[] = $item['id'];
								continue;
							}
							/*if (isset($existsResumeName[$item['content']]))
							{
								$existsId[] = $item['id'];
								continue;
							}*/
	        				$this->Spiderresume->set_database(self::$isDev ? $this->jd_db_name_dev : $this->jd_db_name_production);
                            if ($initLoop == 0 && empty($jobAreaList))
                            {
		                        $jobAreaList = $this->Jdjobarea->fetchAllJdJobAreaList(0, 0, FALSE);
                            }
							//插入简历记录
							$resume_data = array();
							/*//查询是否存在该简历，防止重复导致冲突
							$keywordData = $this->Jdjobresumebase->fetchJdPositionDetailByPltk($position_id, self::$level, self::$type_map[$item['type']], $keyword);
							if (!empty($keywordData))
							{
								$keywordNewId =& $keywordData['kid'];
								unset($keywordData);
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|已存在的简历ID=>".$keywordNewId."\n";
							}else{*/
                                if (is_array($jobAreaList) && !empty($jobAreaList) && !empty($item['residence']))
	                            {
                	                foreach ($jobAreaList as $areaItem)
                	                {
                                        if (strpos($item['residence'], $areaItem['areaName']) !== false)
                                        {
                                            $resume_data['areaId'] = $areaItem['areaId'];
                                            break;
                                        }
                                    }
                                }
								$resume_data['userGender'] = (int)$item['sex'];
								$resume_data['graduateSchool'] = $item['university']; //毕业院校
								$resume_data['professional'] = $item['major']; //专业
								$resume_data['degree'] = $item['education']; //学历
								$resume_data['userAge'] = (int)$item['birthday']; //出生年月
								$resume_data['nowCompany'] = $item['work_company'];
								$resume_data['abilityFeatureString'] = $item['ability_feature'];
								$resume_data['jobClassId'] = (int)$item['job_class_id'];
								$resume_data['resumeInit'] = $item['content'];
								$resume_data['project'] = $item['project'];//项目经验
								$resume_data['work_experience'] = $item['work_experience'];//工作经验
								$resume_data['self_introduction'] = $item['self_introduction'];//自我介绍
								$resume_data['resumeSource'] = $item['url']; //简历来源
                                $resume_data['initResumeId'] = $item['id'];
								$lastId = $this->Jdjobresumebase->createJdResumeBaseInfo($resume_data, TRUE);
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|新生成的简历ID=>".$lastId."\n";
							//}
							if ($lastId > 0)
							{
								$existsId[] = $item['id'];
								//$existsResumeName[$item['resume_name']] = 1;
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|导入简历成功\n";
							}else{
								echo "[id=>{$item['id']}]|[page=>{$page}]|[count=>{$this->count}]|导入简历失败\n";
							}
							echo "---------------------[id=>{$item['id']}]-END------------------------\n";
							unset($item);
						}
						if (!empty($existsId))
						{
							//更新该条记录的状态，设置为1
        					$this->Spiderresume->set_database($this->jd_db_spider_resume);
        					$updateRet = $this->Spiderresume->updateSpiderResumeBatch($existsId, 1);
							echo "[page=>{$page}]|[count=>{$this->count}]|导入简历成功|更新为已更新状态=>".var_export($updateRet, TRUE)."\n";
						}
					}else{
						echo "[page=>{$page}]|[count=>{$this->count}]|暂无需要导入的简历数据\n";
					}
					unset($allSpiderResumeList);
					usleep(100);
				}
			}else{
				echo "暂无需要导入的简历数据\n";
			}
        }while($allSpiderResumeTotal > 0);
		echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process end ========================\n";
    }
}