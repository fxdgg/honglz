<?php
if(PHP_SAPI !== 'cli') die("禁止访问");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','30720M');

/**
 * Class Calc_money
 * 计算用户自有金额、合作者金额、实际金额
 */
class Calc_money extends CI_Controller
{
	/**
	 * 是否测试环境
	 * @var string
	 */
	private static $isDev = TRUE;

	/**
	 * 常用的数据库-测试
	 * @var string
	 */
	private $jd_db_name_dev = 'dev';

	/**
	 * 常用的数据库-线上
	 * @var string
	 */
	private $jd_db_name_production = 'production';

	private $allUserTotal = 0;

	/**
	 * 每次处理的数据量
	 * @var int
	 */
	private $count = 100;

    public function index()
    {
        self::$isDev = defined('ENVIRONMENT') && ENVIRONMENT == 'development' ? TRUE : FALSE;
    }

    /**
     * 获取所有用户数量
     */
    public function get_alluser_total()
    {
        $this->load->model('Userinfo');
        $this->allUserTotal = $this->Userinfo->fetchAllUserCount();
    }

	/**
	* 每天定时计算-自有金额-每天0点
     * /usr/bin/php /home/bailaohui/blh5mx/www/index.php calc_money my
	*/
    public function my()
    {
        // 获取所有用户数量
        $this->get_alluser_total();
    	echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."][allUserTotal:{$this->allUserTotal}] process start ========================\n";

    	$this->load->model('Userinfo');
        $this->load->model('Jdsupply');

        if ($this->allUserTotal == 0) {
            echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."] process break ========================\n";
            exit;
        }

        // 按分页获取所有用户列表
        $pageTotal = ceil($this->allUserTotal / $this->count);
        for($page = 1; $page <= $pageTotal; $page ++) {
            // 按页获取所有用户列表
            $partUserList = $this->Userinfo->fetchAllUserByPage($page, $this->count);
            if (empty($partUserList)) {
                continue;
            }

            foreach ($partUserList as $partItem) {
                if (empty($partItem['id'])) {
                    continue;
                }
                $currentUid = (int)$partItem['id'];
                // 获取该用户作为题主的总收入
                $askerInfo = $this->Jdsupply->fetchMySumMoney($currentUid, 'asker_uid');
                // 获取该用户作为答主的总收入
                $answerInfo = $this->Jdsupply->fetchMySumMoney($currentUid, 'answer_uid');
                // 计算该用户的自有金额
                $askerMoney = !empty($askerInfo['money']) ? (int)$askerInfo['money'] : 0;
                $answerMoney = !empty($answerInfo['money']) ? (int)$answerInfo['money'] : 0;
                // if ($askerMoney <= 0 && $answerMoney <= 0) {
                //     continue;
                // }
                // 更新该用户的自有金额
                $myMoney = intval(($askerMoney * 0.4) + ($answerMoney * 0.5));
                $userRet = $this->Userinfo->edit($currentUid, ['my_money'=>$myMoney]);
                $successMsg = $myMoney > 0 ? '-[UPDATE_SUCCESS]' : '';
                // 记录日志
                $msgContent = sprintf('%s_currentUid:%d, askerMoney:%s, answerMoney:%s, myMoney:%s（%s*0.4+%s*0.5）, userRet:%s',
                    __METHOD__, $currentUid, $askerMoney, $answerMoney, $myMoney, $askerMoney, $answerMoney, json_encode($userRet));
                echo "[".date('Y-m-d H:i:s')."]-[{$msgContent}]{$successMsg}\n";
                log_message('debug', $msgContent);
            }
        }

		echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."] process end ========================\n";
    }

    /**
     * 每天定时计算-合作者金额-每天0点半
     * /usr/bin/php /home/bailaohui/blh5mx/www/index.php calc_money partner
     */
    public function partner()
    {
        // 获取所有用户数量
        $this->get_alluser_total();
        echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."][allUserTotal:{$this->allUserTotal}] process start ========================\n";

        $this->load->model('Userinfo');
        $this->load->model('Jdsupply');

        if ($this->allUserTotal == 0) {
            echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."] process break ========================\n";
            exit;
        }

        // 按分页获取所有用户列表
        $pageTotal = ceil($this->allUserTotal / $this->count);
        for($page = 1; $page <= $pageTotal; $page ++) {
            // 按页获取所有用户列表
            $partUserList = $this->Userinfo->fetchAllUserByPage($page, $this->count);
            if (empty($partUserList)) {
                continue;
            }

            foreach ($partUserList as $partItem) {
                if (empty($partItem['id'])) {
                    continue;
                }
                $currentUid = (int)$partItem['id'];
                // 获取该用户作为隶属关系ID的总收入
                $childMyMoneyInfo = $this->Userinfo->fetchMySumMoney($currentUid, 'subjection_uid');
                // 计算该用户的合作者金额
                $childMyMoney = !empty($childMyMoneyInfo['my_money']) ? (int)$childMyMoneyInfo['my_money'] : 0;
                // if ($childMyMoney <= 0) {
                //     continue;
                // }
                // 更新该用户的合作者金额(隶属关系id是该人的自有金额*10%)
                $partnerMoney = intval($childMyMoney * 0.1);
                $userRet = $this->Userinfo->edit($currentUid, ['partner_money'=>$partnerMoney]);
                $successMsg = $partnerMoney > 0 ? '-[UPDATE_SUCCESS]' : '';
                // 记录日志
                $msgContent = sprintf('%s_currentUid:%d, childMyMoney:%s, partnerMoney:%s（%s*0.1）, userRet:%s',
                    __METHOD__, $currentUid, $childMyMoney, $partnerMoney, $childMyMoney, json_encode($userRet));
                echo "[".date('Y-m-d H:i:s')."]-[{$msgContent}]{$successMsg}\n";
                log_message('debug', $msgContent);
            }
        }

        echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."] process end ========================\n";
    }

    /**
     * 每天定时计算-实际金额-每天1点
     * /usr/bin/php /home/bailaohui/blh5mx/www/index.php calc_money actual
     */
    public function actual()
    {
        // 获取所有用户数量
        $this->get_alluser_total();
        echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."][allUserTotal:{$this->allUserTotal}] process start ========================\n";

        $this->load->model('Userinfo');
        $this->load->model('Jdsupply');

        if ($this->allUserTotal == 0) {
            echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."] process break ========================\n";
            exit;
        }

        // 按分页获取所有用户列表
        $pageTotal = ceil($this->allUserTotal / $this->count);
        for($page = 1; $page <= $pageTotal; $page ++) {
            // 按页获取所有用户列表
            $partUserList = $this->Userinfo->fetchAllUserByPage($page, $this->count);
            if (empty($partUserList)) {
                continue;
            }

            foreach ($partUserList as $partItem) {
                if (empty($partItem['id'])) {
                    continue;
                }
                $currentUid = (int)$partItem['id'];

                // 更新该用户的实际金额(自有金额*90% + 合作者金额)
                $actualMoney = intval(((int)$partItem['my_money'] * 0.9) + (int)$partItem['partner_money']);
                $userRet = $this->Userinfo->edit($currentUid, ['actual_money'=>$actualMoney]);
                $successMsg = $actualMoney > 0 ? '-[UPDATE_SUCCESS]' : '';
                // 记录日志
                $msgContent = sprintf('%s_currentUid:%d, myMoney:%s, partnerMoney:%s, actualMoney:%s（%s*0.9+%s）, userRet:%s',
                    __METHOD__, $currentUid, (int)$partItem['my_money'], (int)$partItem['partner_money'], $actualMoney, (int)$partItem['my_money'], (int)$partItem['partner_money'], json_encode($userRet));
                echo "[".date('Y-m-d H:i:s')."]-[{$msgContent}]{$successMsg}\n";
                log_message('debug', $msgContent);
            }
        }

        echo "\n======================== [".date('Y-m-d H:i:s')."][".__METHOD__."] process end ========================\n";
    }
}