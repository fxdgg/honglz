<?php
/**
 * 社团表
 *
 */
class UnionManage extends CI_Model
{
    public $_table = 'tbl_union';
    /**
     * 社团-被关闭
     * @var int
     */
    const UNION_STATUS_CLOSE = 0;
    /**
     * 社团-非认证且临时
     * @var int
     */
    const UNION_STATUS_UNAUTH_TMP = 1;
    /**
     * 社团-非认证且生效
     * @var int
     */
    const UNION_STATUS_UNAUTH_VALID = 2;
    /**
     * 社团-认证且生效
     * @var int
     */
    const UNION_STATUS_AUTH_VALID = 3;
    /**
     * 是否开启缓存
     * @var boolean
     */
    public static $enableCache = TRUE;
    
    function __construct()
    {
        parent::__construct();
    }
    /**
     * 获取社团列表
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp
     * @param string $unionNameTag
     */
    public function getUnionList($page=1, $pagesize=100, $timestamp=0, $unionNameTag='', $unionCodeTag='')
    {
        $this->db->select('tu.unionId,tu.unionName,tu.unionNick,tu.unionStatus,tu.unionDesc,tu.unionIntro,tu.unionSmallLogo,tu.unionBigLogo,tu.createTime,tcu.companyId,tcu.companyName,tcu.companyNick,tcu.unionRole');
        $this->db->join('tbl_company_union tcu', 'tu.unionId = tcu.unionId', 'INNER');
        if (!empty($unionNameTag) OR !empty($unionCodeTag))
        {
            $this->db->where_in('tu.unionStatus', array(self::UNION_STATUS_UNAUTH_VALID, self::UNION_STATUS_AUTH_VALID));//self::UNION_STATUS_UNAUTH_TMP, 
        }else{
            $this->db->where('tu.unionStatus', self::UNION_STATUS_AUTH_VALID);
        }
        !empty($unionNameTag) && $this->db->like('tu.unionName', $unionNameTag, 'both');//both after before
        //根据社团邀请码进行搜索
        if (!empty($unionCodeTag))
        {
            $this->db->where('tu.unionCode', $unionCodeTag);
            $this->db->limit(1, 0);
        }else{
            $this->db->order_by('tcu.companySimple' ,'ASC');
            $this->db->limit($pagesize, ($page-1)*$pagesize);
        }
        $query = $this->db->get($this->_table . ' tu');
        $result = $query->result_array();
        return $result;
    }
    public function searchUnionListByUnionName($searchName)
    {
        //开启缓存的处理
        if (self::$enableCache)
        {
            $cache_key = sprintf("%s|%s|%s", __CLASS__, __FUNCTION__, md5($searchName));
            $result = $this->getCacheData($cache_key);
            if (!empty($result))
            {
                return $result;
            }
        }
        $this->db->select('tu.unionId,tu.unionName,tu.unionNick,tu.unionStatus,tu.unionDesc,tu.createTime,tcu.companyId,tcu.companyName,tcu.companyNick,tcu.unionRole');
        $this->db->join('tbl_company_union tcu', 'tu.unionId = tcu.unionId', 'LEFT');
        $this->db->where_in('tu.unionStatus', array(self::UNION_STATUS_UNAUTH_TMP, self::UNION_STATUS_UNAUTH_VALID, self::UNION_STATUS_AUTH_VALID));
        $this->db->like('tu.unionName', $searchName, 'both');//both after before
        $this->db->order_by('tcu.companySimple' ,'ASC');
        $query = $this->db->get($this->_table . ' tu');
        $result = $query->result_array();
        if (self::$enableCache && !empty($result))
        {
            $this->setCacheData($cache_key, $result, HOUR_TIMESTAMP);
        }
        return $result;
    }
    /**
     * 获取某社团的详细信息
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp
     */
    public function getUnionDetailInfo($unionId)
    {
        $this->db->select('tu.unionId,tu.unionName,tu.unionNick,tu.unionStatus,tu.createTime,tcu.companyId,tcu.companyName,tcu.companyNick,tcu.unionRole');
        $this->db->join('tbl_company_union tcu', 'tu.unionId = tcu.unionId', 'LEFT');
        $this->db->where('tu.unionId', $unionId);
        $this->db->where_in('tu.unionStatus', array(self::UNION_STATUS_UNAUTH_TMP, self::UNION_STATUS_UNAUTH_VALID, self::UNION_STATUS_AUTH_VALID));
        $this->db->order_by('tcu.companySimple' ,'ASC');
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tu');
        $result = $query->row_array();
        return $result;
    }
    /**
     * 仅获取社团的基本信息列表
     * @param $page
     * @param $pagesize
     */
    public function getOnlyUnionListAdmin($page=1, $pagesize=100, $unionStatus = -1)
    {
        $this->db->select('unionId,unionName,unionNick,unionStatus,createTime');
        $unionStatus != -1 && $this->db->where('unionStatus', $unionStatus);
        $this->db->order_by('createTime' ,'DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table);
        $result = $query->result_array();
        return $result;
    }
    /**
     * 管理后台-获取所有的社团列表
     * @param $unionStatus
     */
    public function getAllUnionListAdmin($unionStatus = -1)
    {
        $this->db->select('unionId,unionName,unionNick,unionStatus,unionDesc,createTime');
        ($unionStatus > 0 && is_numeric($unionStatus)) && $this->db->where('unionStatus', $unionStatus);
        (is_array($unionStatus) && !empty($unionStatus)) && $this->db->where_in('unionStatus', $unionStatus);
        //$this->db->order_by('createTime' ,'DESC');
        $query = $this->db->get($this->_table);
        $result = $query->result_array();
        return $result;
    }
    /**
     * 仅获取社团的基本信息列表总数
     * @param $page
     * @param $pagesize
     */
    public function getOnlyUnionListAdminTotal($unionStatus = -1)
    {
        $unionStatus != -1 && $this->db->where('tu.unionStatus', $unionStatus);
        $cnt = $this->db->count_all_results($this->_table);
        return $cnt;
    }
    /**
     * 获取某社团的信息
     * @param int $unionId
     * @param string $source(union:接口请求 admin:后台管理员请求)
     */
    public function getUnionById($unionId, $source='union')
    {
        $this->db->select('unionId,unionName,unionNick,unionStatus,createTime');
        //用户在社团里的状态(0:被关闭1:非认证且临时2:非认证且生效3:认证且生效)
        if ($source=='union') $this->db->where('unionStatus != ', self::UNION_STATUS_CLOSE);
        $this->db->where('unionId', $unionId);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $result = $query->row_array();
        return !empty($result) ? $result : FALSE;
    }
    /**
     * 获取某社团的信息
     * @param int $unionName
     */
    public function getUnionByName($unionName)
    {
        $this->db->select('unionId,unionName,unionNick');
        $this->db->where('unionStatus != ', self::UNION_STATUS_CLOSE);
        $this->db->where('unionName', $unionName);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $result = $query->result_array();
        if(count($result) == 1)
        {
            return (array)$result[0];
        }
        return FALSE;
    }
    /**
     * 创建社团所需的流程
     * @param $unionName
     * @param $companyName
     */
    public function createUnionProcess($userId, $unionName, $companyName, &$data = array(), &$user_data = array())
    {
        //检查该用户目前已创建的社团信息
        $this->load->model('UserUnion');
        $userUnionList = $this->UserUnion->searchUnionListByUserId($userId);
        if (count($userUnionList) >= $this->config->item('union_user_create_limit', 'system_union_config'))
        {
            return BLH_Utilities::genErrorMsg(27003, $this->lang->line('union_create_limited'));
        }
        $unionData = $this->getUnionByName($unionName);
        if (!empty($unionData))
        {
            return BLH_Utilities::genErrorMsg(27004, $this->lang->line('union_has_exist'));
        }
        $this->load->model('CompanyUnion');
        $companyData = $this->CompanyUnion->getCompanyByName($companyName);
        if (!empty($companyData))
        {
            return BLH_Utilities::genErrorMsg(27005, $this->lang->line('union_company_has_exist'));
        }
        //生成社团邀请码
        $invite_code_length = $this->config->item('invite_code_length');
        $unionCode = BLH_Utilities::random($invite_code_length);
        $this->db->trans_start();
        $unionId = $this->createUnion($unionName, TRUE, $unionCode);
        //创建企业-社团关系的逻辑
        $ret_company = $unionId > 0 && $this->CompanyUnion->createCompanyUnion($companyName, $unionId);
        //创建用户-社团关系的逻辑
        $ret_user = $unionId > 0 && $this->UserUnion->createUnionUser($userId, array($userId,'',$unionId), TRUE, UserUnion::USER_UNION_ROLE_MASTER_ADMIN);
        //更新用户状态为1
        if ($unionId > 0)
        {
            $this->load->model('Userinfo');
            if (!empty($user_data))
            {
                $user_data_db = (array)$user_data;
            }else{
                $user_data_db = $this->Userinfo->info($userId, $userId);
            }
            $user_post = array();
            //更新登录者的信息
            if (empty($user_data_db['nickname']) && !empty($data['nickname']))
            {
                $user_post['nickname'] = $data['nickname'];
            }
            if (empty($user_data_db['sex']) && !empty($data['sex']))
            {
                $user_post['sex'] = $data['sex'];
            }
            if (empty($user_data_db['iconUrl']) && !empty($data['iconUrl']))
            {
                $user_post['iconUrl'] = $data['iconUrl'];
            }
            if (empty($user_data_db['company']) && !empty($data['company']))
            {
                $user_post['company'] = $data['company'];
            }
            if (empty($user_data_db['position']) && !empty($data['position']))
            {
                $user_post['position'] = $data['position'];
            }
            //用户状态(-1:未注册 0:注册但无邀请码 1:正常 2:被禁用)
            $user_post['status'] = $this->Userinfo->USER_STATUS_OK;
            $ret_userinfo = !empty($user_post) && $this->Userinfo->edit($userId, $user_post);
        }
        $this->db->trans_complete();
        if (!$ret_company OR !$ret_user OR !$ret_userinfo)
        {
            return BLH_Utilities::genErrorMsg(27006, $this->lang->line('union_create_failed'));
        }else{
            return BLH_Utilities::outputSuccess(array('unionId'=>$unionId));
        }
    }
    /**
     * 创建社团逻辑
     * @param $userid
     */
    public function createUnion($unionName, $returnLastId = FALSE, $unionCode = '')
    {
        $data = array(
            'unionName' => $unionName,
            'unionStatus' => self::UNION_STATUS_UNAUTH_TMP,
        );
        //社团邀请码
        !empty($unionCode) && $data['unionCode'] = $unionCode;
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }
    /**
     * 更新社团逻辑
     * @param $unionId
     * @param $unionName
     * @param $unionStatus
     */
    public function updateUnion($unionId, $unionName='', $unionStatus = -1,$unionNick='')
    {
        if (!empty($unionName)) $data['unionName'] = $unionName;
        if ($unionStatus != -1) $data['unionStatus'] = $unionStatus;
        if (!empty($unionNick)) $data['unionNick'] = $unionNick;
        $data['updateTime'] = date('Y-m-d H:i:s', SYS_TIME);
        $this->db->where('unionId', $unionId);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    /**
     * 删除社团逻辑
     * @param $userid
     */
    public function dropUnion($unionId)
    {
        $this->db->where('unionId', $unionId);
        return $this->db->delete($this->_table);
    }
    /**
     * 查询符合关闭条件的临时社团逻辑
     */
    public function searchTmpUnionCron()
    {
        //7天内邀请至少5名前同事加入
        $union_invite_timeout = $this->config->item('union_invite_timeout', 'system_union_config');
        $union_invite_total = $this->config->item('union_invite_total', 'system_union_config');
        $this->db->select('tu.unionId,tu.unionName,tu.unionNick,tu.unionStatus,tu.createTime,COUNT(tu.unionId) AS userTotal,GROUP_CONCAT(tuu.userid) AS userIdMuti');
        $this->db->join('tbl_user_union tuu', 'tuu.unionId = tu.unionId', 'LEFT');
        //用户在社团里的状态(0:被关闭1:非认证且临时2:非认证且生效3:认证且生效)
        $this->db->where('tu.unionStatus', UnionManage::UNION_STATUS_UNAUTH_TMP);
        $this->db->where('tu.createTime < DATE_SUB(NOW(), INTERVAL '.$union_invite_timeout.' DAY)');
        $this->db->group_by('tu.unionId');
        $this->db->having('COUNT(tu.unionId) < '.$union_invite_total);
        $query = $this->db->get($this->_table . ' tu');
        $result = $query->result_array();
        return $result;
    }
    /**
     * 定期检查临时社团逻辑
     *  +---------+--------------+-------------+---------------------+-----------+------------+-------------+
        | unionId | unionName | unionStatus | createTime     | userTotal | userIdMuti | unionIdMuti |
        +---------+--------------+-------------+---------------------+-----------+------------+-------------+
        |       4 | 北广传媒 |        1 | 2014-01-20 14:36:30 |         2 | 99,2       | 4,4         |
        |       5 | 悠视互动 |        1 | 2014-01-20 23:45:27 |         1 | 98         | 5           |
        |       6 | 神舟七号 |        1 | 2014-01-22 20:18:54 |         3 | 103,3,102  | 6,6,6       |
        |       7 | 测试1   |        1 | 2014-01-22 20:45:59 |         2 | 106,3      | 7,7         |
        |       8 | 测试2   |        1 | 2014-01-22 20:57:51 |         1 | NULL       | 8           |
        +---------+--------------+-------------+---------------------+-----------+------------+-------------+
     */
    public function checkTmpUnionCron()
    {
        $tmpUnionList = $this->searchTmpUnionCron();
        if (is_array($tmpUnionList) && !empty($tmpUnionList))
        {
            $this->load->model('CompanyUnion');
            $this->load->model('UserUnion');
            $this->db->trans_start();
            foreach ($tmpUnionList as $unionItem)
            {
                if ($unionItem['unionId'] <= 0) continue;
                if (!empty($unionItem['userIdMuti']))
                {
                    $this->load->model('Userinfo');
                    //挨个检查社团里的用户，若有加入的大于1个社团则不操作，否则置为未验证状态
                    $userIdList = explode(',', $unionItem['userIdMuti']);
                    foreach ($userIdList as $userId)
                    {
                        if ($userId <= 0) continue;
                        //检查该用户加入的社团列表
                        $userUnionIds = $this->UserUnion->getMyJoinUnionIdList($userId, TRUE);
                        if (count($userUnionIds) <= 1)
                        {
                            //批量把用户状态设置为未验证状态(用户基本信息模块)
                            $this->Userinfo->updateUserStatus($userId, $this->Userinfo->USER_STATUS_REGISTER);
                        }
                    }
                }
                //关闭该社团(社团模块)
                $this->updateUnion($unionItem['unionId'], '', UnionManage::UNION_STATUS_CLOSE);
                //把企业退出该社团(企业-社团关系模块)
                $this->CompanyUnion->updateCompanyUnionStatus($unionItem['unionId'], CompanyUnion::UNION_ROLE_QUIT);
                //把社团里的用户设置为退出该社团(用户-社团关系模块)
                $this->UserUnion->updateUnionUserRole(0, $unionItem['unionId'], UserUnion::USER_UNION_ROLE_DELETE);
                echo 'currentTime=>'.date('Y-m-d H:i:s')."|unionId=>{$unionItem['unionId']}|unionName=>{$unionItem['unionName']}|createTime=>{$unionItem['createTime']}|userTotal=>{$unionItem['userTotal']}|userIdList=>{$unionItem['userIdMuti']}"."\n";
            }
            $this->db->trans_complete();
        }else{
            echo "没有需要关闭的临时社团\n";
        }
    }

    /**
     * 通过社团邀请码获取某社团的信息
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp
     */
    public function getUnionInfoByCode($unionCode)
    {
        $this->db->select('tu.unionId,tu.unionName,tu.unionNick,tu.unionStatus,tu.createTime');
        $this->db->where('tu.unionCode', $unionCode);
        $this->db->limit(1);
        $query = $this->db->get($this->_table . ' tu');
        $row = $query->row_array();
        return $row;
    }
    
    /**
     * 检查社团邀请码是否存在
     * @param int $page
     * @param int $pagesize
     * @param int $timestamp
     */
    public function isExistUnionByCode($unionCode)
    {
        $this->db->select('unionId');
        $this->db->where('unionCode', $unionCode);
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $row = $query->row_array();
        return !empty($row) ? TRUE : FALSE;
    }
}