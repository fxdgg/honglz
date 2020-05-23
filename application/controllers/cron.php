<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

class Cron extends CI_Controller
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
	 * JD-tbl_keyword用的数据库
	 * @var string
	 */
	private $jd_db_name_keyword = 'jd';

	/**
	 * 每次处理的数据量
	 * @var int
	 */
	private $count = 1000;

	/**
	 * 默认的关键词，对应的职位等级-中级
	 * @var int
	 */
	private static $level = 2;

	/**
	 * 类型对应关系
	 * @var array
	 */
	private static $type_map = array(
		0 => 'demand',//任职资格、任职要求
		1 => 'describe',//岗位职责
	);

    public function index()
    {
        $this->load->library('cron_schedule');
        $this->cron_schedule->dispatch();
        self::$isDev = defined('ENVIRONMENT') && ENVIRONMENT == 'development' ? TRUE : FALSE;
    }

	/**
	* 每天定时把JD的关键词、描述导入到对应的表
	* type:类型（0:任职要求1:岗位职责）
	*/
    public function import_keyword_describe($type = 0)
    {
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process start ========================\n";
        $this->load->model('keyword');
    	$this->load->model('Jdpositionduty');
    	$this->load->model('Jddescribe');
    	$this->load->model('Jddescribelog');
        do{
        	$this->keyword->set_database($this->jd_db_name_keyword);
        	$allKeywordTotal = $this->keyword->fetchAllKeywordTotal($type);
        	if ($allKeywordTotal <= 0)
        	{
        		echo "[type=>{$type}]|需要更新的关键词总数=>{$allKeywordTotal}|暂无需要更新的关键词数据\n";
        		break;
        	}
        	$loop = floor(($allKeywordTotal + $this->count - 1) / $this->count);
        	echo '[type=>'.$type.']|需要更新的关键词总数=>'.$allKeywordTotal.'|count=>'.$this->count.'|loop=>'.$loop."\n";
        	if ($loop >= 1)
			{
				for ($page = 1; $page <= $loop; $page++)
				{
					$allKeywordList = $this->keyword->fetchAllKeywordList($type, $page, $this->count);
					if (!empty($allKeywordList))
					{
						foreach ($allKeywordList as $keyword => $item)
						{
							echo "---------------------[kid=>{$item['id']}]-START------------------------\n";
							if (!isset($item['keyword']) OR empty($item['keyword'])) continue;
	        				$this->keyword->set_database(self::$isDev ? $this->jd_db_name_dev : $this->jd_db_name_production);
							//插入关键词记录
							$keyword = trim($item['keyword']);
							$position_id = (int)$item['position_id'];
							//查询是否存在该关键词，防止重复导致冲突
							$keywordData = $this->Jdpositionduty->fetchJdPositionDetailByPltk($position_id, self::$level, self::$type_map[$item['type']], $keyword);
							if (!empty($keywordData))
							{
								$keywordNewId =& $keywordData['kid'];
								unset($keywordData);
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|已存在的关键词ID=>".$keywordNewId."\n";
							}else{
								//计算该关键词的权值（关键词权重：被选择的关键词的次数 + 某position_id中的keyword的个数 * 3 + total中的keyword的个数）
								$sortIdNum_1 = $this->Jddescribelog->fetchKeywordDescribeCnt($keyword, 'keyword');
								$sortIdNum_2 = $this->Jdpositionduty->fetchKeywordCnt($keyword, $position_id);
								$sortIdNum_3 = $this->Jdpositionduty->fetchKeywordCnt($keyword);
								//计算后该关键词的排序编号，也就是权值
								$sortIdKeyword = $sortIdNum_1 + ($sortIdNum_2 * 3) + $sortIdNum_3;
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|关键词=>".$keyword.'|职位ID=>'.$position_id.'|被选择的关键词的次数=>'.$sortIdNum_1.'|某position_id中的keyword的个数 * 3=>'.($sortIdNum_2 * 3).'|total中的keyword的个数=>'.$sortIdNum_3.'|计算后的该关键词的权值=>'.$sortIdKeyword."\n";
								$keywordNewId = $this->Jdpositionduty->createPositionDuty($position_id, self::$level, self::$type_map[$item['type']], $keyword, $sortIdKeyword, 'new', TRUE);
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|新生成的关键词ID=>".$keywordNewId."\n";
								unset($sortIdNum_1, $sortIdNum_2, $sortIdNum_3, $sortIdKeyword);
							}
							if ($keywordNewId > 0)
							{
								//插入描述记录
								$content = trim($item['content']);
								//计算该描述的权值（描述权重：被选择的描述的次数）
								$sortIdDescribe = $this->Jddescribelog->fetchKeywordDescribeCnt($content, 'content');
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|描述=>".$content.'|被选择的描述的次数=>'.$sortIdDescribe."\n";
								$ret = $this->Jddescribe->createJdDescribe($keywordNewId, $content, $sortIdDescribe, 'new');
								if ($ret)
								{
									//更新该条记录的状态，设置为1
		        					$this->keyword->set_database($this->jd_db_name_keyword);
		        					$updateKeywordRet = $this->keyword->updateKeywordData($item['id'], 1);
									echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|录入描述成功|更新为已更新状态=>".var_export($updateKeywordRet, TRUE)."\n";
								}else{
									echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|录入描述失败|更新状态失败\n";
								}
							}else{
								echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|插入关键词失败\n";
							}
							echo "---------------------[kid=>{$item['id']}]-END------------------------\n";
							unset($item);
						}
					}else{
						echo "[type=>{$type}]|[page=>{$page}]|[count=>{$this->count}]|暂无需要更新的关键词数据\n";
					}
					unset($allKeywordList);
					usleep(100);
				}
			}else{
				echo "[type=>{$type}]|暂无需要更新的关键词数据\n";
			}
        }while($allKeywordTotal > 0);
		echo "\n======================== [".date('Y-m-d H:i:s')."][".__FILE__."] process end ========================\n";
    }
}