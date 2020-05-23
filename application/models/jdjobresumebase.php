<?php
/**
 * 简历-基础表
 */
class Jdjobresumebase extends CI_Model{
    public $_table = 'tbl_jd_resume_base';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取简历-基础表的某条简历信息
     * @param int $id 简历ID
     */
    public function fetchJdBaseInfoById($id, $isAdmin = FALSE)
    {
        $this->db->select();
        $this->db->where('tjrb.id', $id);
        !$isAdmin && $this->db->where('tjrb.state', 'new');
        $query = $this->db->get($this->_table . ' tjrb');
        $res = $query->row_array();
        return !empty($res) ? $res : array();
    }

    /**
     * 获取简历-基础表-特征关键词
     * @param int $id 简历ID
     */
    public function fetchResumeAbilityList($id)
    {
        $this->db->select('tjrb.*,tjjaf.abilityFeatureId,tjjaf.abilityFeatureName,tjjaf.abilityFeatureDescribe');
        $this->db->join('tbl_jd_resume_ability_map tjram', 'tjram.resumeId = tjrb.id', 'LEFT');
        $this->db->join('tbl_jd_job_ability_feature tjjaf', 'tjjaf.abilityFeatureId = tjram.abilityFeatureId', 'LEFT');
        $this->db->where('tjrb.id', $id);
        $this->db->where('tjrb.state', 'new');
        $query = $this->db->get($this->_table . ' tjrb');
        $res = $query->result_array();
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 获取简历-基础表的列表-分页
     */
    public function fetchAllJdResumeBaseList($page = 1, $pagesize = 50, $isAdmin = FALSE, $state = '', $isFindJob = '-1', $isForceNew = FALSE)
    {
        $yesDate = date('Y-m-d', strtotime('-2 day'));
        $nowDate = date('Y-m-d');
        $this->db->select('tjrb.*,tjjc.jobClassName,tjjl.jobLevelName,tjja.areaName,tjjct.companyTypeName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjrb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_level tjjl', 'tjjl.jobLevelId = tjrb.jobLevelId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjrb.areaId', 'LEFT');
        $this->db->join('tbl_jd_job_company_type tjjct', 'tjjct.companyTypeId = tjrb.companyTypeId', 'LEFT');
        (!$isAdmin OR $isForceNew) && $this->db->where('tjrb.state', 'new');
        $isFindJob != -1 && $this->db->where('tjrb.isFindJob', $isFindJob);
        ($isAdmin OR $isForceNew) && !empty($state) && $this->db->where('tjrb.state', $state);
        //Edit By 20160308 16:15
        //$this->db->where('tjrb.createTime >= ', $yesDate.' 00:00:00');
        //$this->db->where('tjrb.createTime <= ', $nowDate.' 23:59:59');
        $this->db->ar_where[] = "AND ((tjrb.project != ''";
        $this->db->ar_where[] = "AND tjrb.work_experience != ''";
        $this->db->ar_where[] = "AND tjrb.abilityFeature != ''";
        $this->db->ar_where[] = "AND tjrb.abilityFeature != 4)";
        $this->db->ar_where[] = "OR tjrb.initResumeId = 0)";
        //$this->db->where('tjrb.project != ', '');//项目经验
        //$this->db->where('tjrb.work_experience != ', '');//工作经验
        //$this->db->where('tjrb.abilityFeature != ', '');
        //$this->db->where('tjrb.abilityFeature != ', 4);
        //$this->db->or_where('tjrb.initResumeId = ', 0);
        $this->db->order_by('tjrb.createTime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjrb');
        $res = $query->result_array();
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    public function allJdResumeBaseListTotal($isAdmin = FALSE, $state = '', $isFindJob = '-1')
    {
        $yesDate = date('Y-m-d', strtotime('-2 day'));
        $nowDate = date('Y-m-d');
        $this->db->select('tjrb.*,tjjc.jobClassName,tjjl.jobLevelName,tjja.areaName,tjjct.companyTypeName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjrb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_level tjjl', 'tjjl.jobLevelId = tjrb.jobLevelId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjrb.areaId', 'LEFT');
        $this->db->join('tbl_jd_job_company_type tjjct', 'tjjct.companyTypeId = tjrb.companyTypeId', 'LEFT');
        !$isAdmin && $this->db->where('tjrb.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjrb.state', $state);
        $isFindJob != -1 && $this->db->where('tjrb.isFindJob', $isFindJob);
        //Edit By 20160308 16:15
        //$this->db->where('tjrb.createTime >= ', $yesDate.' 00:00:00');
        //$this->db->where('tjrb.createTime <= ', $nowDate.' 23:59:59');
        /*$this->db->where('tjrb.project != ', '');//项目经验
        $this->db->where('tjrb.work_experience != ', '');//工作经验
        $isAdmin && !empty($state) && $this->db->where('tjrb.state', $state);
        $this->db->where('tjrb.abilityFeature != ', '');
        $this->db->where('tjrb.abilityFeature != ', 4);*/
        $this->db->ar_where[] = "AND ((tjrb.project != ''";
        $this->db->ar_where[] = "AND tjrb.work_experience != ''";
        $this->db->ar_where[] = "AND tjrb.abilityFeature != ''";
        $this->db->ar_where[] = "AND tjrb.abilityFeature != 4)";
        $this->db->ar_where[] = "OR tjrb.initResumeId = 0)";
        $query = $this->db->get($this->_table . ' tjrb');
        $total = count($query->result_array());
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        return $total;
    }

    /**
     * 获取符合条件的简历信息列表发送到ZMX、JD的邮箱
     */
    public function fetchAllJdResumeBaseListForEmail($page = 1, $pagesize = 100, $isFindJob = 2, $state = 'new', $isPush = 0, $isPushJd = 0, $jobClassId = 0, $areaId = 0)
    {
        $this->db->select('tjrb.*,tjjc.jobClassName,tjja.areaName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjrb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjrb.areaId', 'LEFT');
        $this->db->where('tjrb.state', $state);
        $jobClassId > 0 && $this->db->where('tjrb.jobClassId', $jobClassId);
        $areaId > 0 && $this->db->where('tjrb.areaId', $areaId);
        $isFindJob > 0 && $this->db->where('tjrb.isFindJob', $isFindJob);
        $isPush >=0 && $this->db->where('tjrb.isPush', $isPush);
        $isPushJd >=0 && $this->db->where('tjrb.isPushJd', $isPushJd);
        $this->db->where('tjrb.project != ', '');//项目经验
        $this->db->where('tjrb.work_experience != ', '');//工作经验
        $this->db->order_by('tjrb.userAge ASC');
        $page > 0 && $this->db->limit($pagesize, ($page-1) * $pagesize);
        $query = $this->db->get($this->_table . ' tjrb');
        $res = $query->result_array('jobClassId', '', 'LIST');
        echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 获取符合条件的简历信息条数
     */
    public function fetchAllJdResumeBaseTotalForEmail($isFindJob = 2, $state = 'new', $isPush = 0, $isPushJd = 0, $jobClassId = 0, $areaId = 0)
    {
        $this->db->select('tjrb.*,tjjc.jobClassName,tjja.areaName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjrb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjrb.areaId', 'LEFT');
        $this->db->where('tjrb.state', $state);
        $jobClassId > 0 && $this->db->where('tjrb.jobClassId', $jobClassId);
        $areaId > 0 && $this->db->where('tjrb.areaId', $areaId);
        $isFindJob > 0 && $this->db->where('tjrb.isFindJob', $isFindJob);
        $isPush >=0 && $this->db->where('tjrb.isPush', $isPush);
        $isPushJd >=0 && $this->db->where('tjrb.isPushJd', $isPushJd);
        $this->db->where('tjrb.project != ', '');//项目经验
        $this->db->where('tjrb.work_experience != ', '');//工作经验
        $query = $this->db->get($this->_table . ' tjrb');
        $count = $query->num_rows();
        return $count;
    }

    /**
     * 根据金数据ID获取简历数量
     */
    public function fetchResumeCntFromJsjid($jsjId, $state = 'new')
    {
        $this->db->select('tjrb.*');
        $this->db->where('tjrb.jsjId', $jsjId);
        $this->db->where('tjrb.state', $state);
        $query = $this->db->get($this->_table . ' tjrb');
        $count = $query->num_rows();
        return $count;
    }

    /**
     * 获取符合条件的简历数量
     */
    public function fetchResumeCntForCompany($jobClassIds = array(), $areaIds = array(), $isFindJob = 1, $state = 'new')
    {
        $this->db->select('tjrb.*');
        !empty($jobClassIds) && $this->db->where_in('tjrb.jobClassId', $jobClassIds);
        !empty($areaIds) && $this->db->where_in('tjrb.areaId', $areaIds);
        $this->db->where('tjrb.isFindJob', (int)$isFindJob);
        $this->db->where('tjrb.state', $state);
        $query = $this->db->get($this->_table . ' tjrb');
        $count = $query->num_rows();
        echo __FUNCTION__.'|last_query=>'.$this->db->last_query().PHP_EOL;
        return $count;
    }

    /**
     * 获取符合条件的简历信息列表，汇总发送到企业的邮箱
     */
    public function fetchAllJdResumeBaseListForCompany($page = 1, $pagesize = 100, $isFindJob = 1, $state = 'new', $isPush = 0, $isPushJd = 0, $jobClassId = 0, $areaId = 0)
    {
        $this->db->select('tjrb.*,tjjc.jobClassName,tjja.areaName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjrb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjrb.areaId', 'LEFT');
        $this->db->where('tjrb.state', $state);
        $jobClassId > 0 && $this->db->where('tjrb.jobClassId', $jobClassId);
        $areaId > 0 && $this->db->where('tjrb.areaId', $areaId);
        $isFindJob > 0 && $this->db->where('tjrb.isFindJob', $isFindJob);
        $isFindJob < 0 && $this->db->where_not_in('tjrb.isFindJob', array(abs($isFindJob)));
        $isPush >=0 && $this->db->where('tjrb.isPush', $isPush);
        $isPushJd >=0 && $this->db->where('tjrb.isPushJd', $isPushJd);
        $this->db->ar_orderby[] = 'FIELD(tjrb.isFindJob, 1,6,2,3,4)';
        $page > 0 && $this->db->limit($pagesize, ($page-1) * $pagesize);
        $query = $this->db->get($this->_table . ' tjrb');
        $res = $query->result_array('isFindJob', '', 'LIST');
        echo __FUNCTION__.'|last_query=>'.$this->db->last_query().PHP_EOL;
        return !empty($res) ? $res : array();
    }

    /**
     * 录入简历-基本信息
     */
    public function createJdResumeBaseInfo(&$params, $returnLastId = FALSE)
    {
        $data = array(
            'userName' => isset($params['userName']) ? htmlspecialchars(trim($params['userName'])) : '',
            'graduateSchool' => isset($params['graduateSchool']) ? htmlspecialchars(trim($params['graduateSchool'])) : '',
        	'professional' => isset($params['professional']) ? htmlspecialchars(trim($params['professional']))	 : '',
        	'degree' => isset($params['degree']) ? htmlspecialchars(trim($params['degree'])) : '',
            'userAge' => isset($params['userAge']) ? htmlspecialchars(trim($params['userAge'])) : '0000',//0000-00-00 00:00:00
        	'mobile' => isset($params['mobile']) ? floor(floatval($params['mobile'])) : 0,
        	'email' => isset($params['email']) ? htmlspecialchars(trim($params['email']))	 : '',
            'workExperience' => isset($params['workExperience']) ? (int)$params['workExperience'] : 0,
            'monthlySalary' => isset($params['monthlySalary']) ? htmlspecialchars(trim($params['monthlySalary'])) : '',
            'hopeSalary' => isset($params['hopeSalary']) ? htmlspecialchars(trim($params['hopeSalary'])) : '',//期望薪资
            'nowCompany' => isset($params['nowCompany']) ? trim($params['nowCompany']) : '',
            'onceCompany' => isset($params['onceCompany']) ? trim($params['onceCompany']) : '',
            'isManageExperience' => isset($params['isManageExperience']) ? (int)$params['isManageExperience'] : 0,
            'nowState' => isset($params['nowState']) ? (int)$params['nowState'] : 0,
            'resumeSource' => isset($params['resumeSource']) ? htmlspecialchars(trim($params['resumeSource'])) : '',
            'userGender' => isset($params['userGender']) ? (int)$params['userGender'] : 1,
            'jobClassId' => isset($params['jobClassId']) ? (int)$params['jobClassId'] : 0,
            'jobLevelId' => isset($params['jobLevelId']) ? (int)$params['jobLevelId'] : 0,
            'areaId' => isset($params['areaId']) ? (int)$params['areaId'] : 0,
            'companyTypeId' => isset($params['companyTypeId']) ? (int)$params['companyTypeId'] : 0,
            'leaveCause' => isset($params['leaveCause']) ? htmlspecialchars(trim($params['leaveCause'])) : '',//离职原因
            'isFindJob' => isset($params['isFindJob']) ? (int)$params['isFindJob'] : 1,
            'abilityFeature' => isset($params['abilityFeatureString']) ? htmlspecialchars($params['abilityFeatureString']) : '',
            'pertainFeature' => isset($params['pertainFeatureString']) ? htmlspecialchars($params['pertainFeatureString']) : '',
        	'resumeInit' => isset($params['resumeInit']) ? htmlspecialchars($params['resumeInit']) : '',
        	'interviewRecord' => isset($params['interviewRecord']) ? htmlspecialchars($params['interviewRecord']) : '',
        	'project' => isset($params['project']) ? $params['project'] : '',//项目经历
        	'work_experience' => isset($params['work_experience']) ? $params['work_experience'] : '',//工作经历
        	'self_introduction' => isset($params['self_introduction']) ? $params['self_introduction'] : '',//自我介绍
            'initResumeId' => isset($params['initResumeId']) ? (int)$params['initResumeId'] : 0,
            'state' => 'new',
            'resumeUrl' => isset($params['resumeUrl']) ? trim($params['resumeUrl']) : '',//简历url
            'jsjId' => isset($params['jsjId']) ? (int)$params['jsjId'] : 0,//金数据ID
            'jobPartition' => isset($params['jobPartition']) ? htmlspecialchars($params['jobPartition']) : '',//职能细分
            'isInnovate' => isset($params['isInnovate']) ? intval($params['isInnovate']) : 0,//是否考虑过创业企业
            'professionTag' => isset($params['professionTag']) ? htmlspecialchars(trim($params['professionTag'])) : '',//行业标签
            'subordinate' => isset($params['subordinate']) ? htmlspecialchars(trim($params['subordinate'])) : '',//下属
            'recommendCostRate' => isset($params['recommendCostRate']) ? trim($params['recommendCostRate']) : 0,//推荐费比例
            'entryMemo' => isset($params['entryMemo']) ? htmlspecialchars($params['entryMemo']) : '',//录入备注
        );
        $ret = $this->db->insert($this->_table, $data);
        if ($returnLastId) {
            $lastId = $this->db->insert_id();
            //更新简历url
            $updateJobResumeArray = array();
            $updateJobResumeArray['id'] = $lastId;
		    $this->updateJdResumeBaseInfo($updateJobResumeArray);
            return $lastId;
        }
        return $ret;
    }

    /**
     * 更新简历-基本信息
     */
    public function updateJdResumeBaseInfo(&$params)
    {
        if (isset($params['userName'])) $data['userName'] = htmlspecialchars(trim($params['userName']));
        if (isset($params['graduateSchool'])) $data['graduateSchool'] = htmlspecialchars(trim($params['graduateSchool']));
        if (isset($params['professional'])) $data['professional'] = htmlspecialchars(trim($params['professional']));
        if (isset($params['degree'])) $data['degree'] = htmlspecialchars(trim($params['degree']));
        if (isset($params['userAge'])) $data['userAge'] = htmlspecialchars(trim($params['userAge']));
        if (isset($params['mobile'])) $data['mobile'] = floor(floatval($params['mobile']));
        if (isset($params['email'])) $data['email'] = htmlspecialchars(trim($params['email']));
        if (isset($params['workExperience'])) $data['workExperience'] = (int)$params['workExperience'];
        if (isset($params['monthlySalary'])) $data['monthlySalary'] = htmlspecialchars($params['monthlySalary']);
        if (isset($params['hopeSalary'])) $data['hopeSalary'] = htmlspecialchars($params['hopeSalary']);
        if (isset($params['nowCompany'])) $data['nowCompany'] = htmlspecialchars($params['nowCompany']);
        if (isset($params['onceCompany'])) $data['onceCompany'] = htmlspecialchars(trim($params['onceCompany']));
        if (isset($params['isManageExperience'])) $data['isManageExperience'] = (int)$params['isManageExperience'];
        if (isset($params['nowState'])) $data['nowState'] = (int)$params['nowState'];
        if (isset($params['resumeSource'])) $data['resumeSource'] = trim($params['resumeSource']);
        if (isset($params['userGender'])) $data['userGender'] = (int)$params['userGender'];
        if (isset($params['jobClassId'])) $data['jobClassId'] = (int)$params['jobClassId'];
        if (isset($params['jobLevelId'])) $data['jobLevelId'] = (int)$params['jobLevelId'];
        if (isset($params['areaId'])) $data['areaId'] = (int)$params['areaId'];
        if (isset($params['companyTypeId'])) $data['companyTypeId'] = (int)$params['companyTypeId'];
        if (isset($params['leaveCause'])) $data['leaveCause'] = htmlspecialchars($params['leaveCause']);
        if (isset($params['isFindJob'])) $data['isFindJob'] = (int)$params['isFindJob'];
        if (isset($params['abilityFeatureString'])) $data['abilityFeature'] = htmlspecialchars($params['abilityFeatureString']);
        if (isset($params['pertainFeatureString'])) $data['pertainFeature'] = htmlspecialchars($params['pertainFeatureString']);
        if (isset($params['resumeInit'])) $data['resumeInit'] = htmlspecialchars($params['resumeInit']);
        if (isset($params['project'])) {
            $params['project'] = str_replace("<p>\n <br />\n</p>", '', $params['project']);
            $data['project'] = htmlspecialchars(trim($params['project']));
        }
        if (isset($params['work_experience'])) {
            $params['work_experience'] = str_replace("<p>\n <br />\n</p>", '', $params['work_experience']);
            $data['work_experience'] = htmlspecialchars(trim($params['work_experience']));
        }
        if (isset($params['self_introduction'])) {
            $params['self_introduction'] = str_replace("<p>\n <br />\n</p>", '', $params['self_introduction']);
            $data['self_introduction'] = htmlspecialchars(trim($params['self_introduction']));
        }
        if (isset($params['interviewRecord'])) $data['interviewRecord'] = htmlspecialchars($params['interviewRecord']);
        if (isset($params['state'])) $data['state'] = $params['state'];
        if (isset($params['resumeUrl'])) $data['resumeUrl'] = htmlspecialchars($params['resumeUrl']);//urlencode(BLH_Utilities::uc_authcode($params['id'], 'ENCODE'));
        if (isset($params['jsjId'])) $data['jsjId'] = (int)$params['jsjId'];//金数据ID
        if (isset($params['jobPartition'])) $data['jobPartition'] = htmlspecialchars($params['jobPartition']);//职能细分
        if (isset($params['isInnovate'])) $data['isInnovate'] = (int)$params['isInnovate'];//是否考虑过创业企业
        if (isset($params['professionTag'])) $data['professionTag'] = htmlspecialchars(trim($params['professionTag']));//行业标签
        if (isset($params['subordinate'])) $data['subordinate'] = htmlspecialchars(trim($params['subordinate']));//下属
        if (isset($params['recommendCostRate'])) $data['recommendCostRate'] = $params['recommendCostRate'];//推荐费比例
        if (isset($params['entryMemo'])) $data['entryMemo'] = htmlspecialchars($params['entryMemo']);//录入备注
        $data['updateTime'] = !empty($params['updateTime']) ? $params['updateTime'] : date('Y-m-d H:i:s');
        $this->db->where('id', $params['id']);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 删除简历-基本信息
     * @param $id
     */
    public function dropJdResumeBaseInfo($id)
    {
        $data = array('state'=>'delete');
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 删除简历-基本信息-物理删除
     * @param $id
     */
    public function deleteJdResumeBaseInfo($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->_table);
    }

    /**
     * 批量更新已推送的简历记录-批量
     * @param $ids
     * @param $status
     */
    public function updateResumeBatch($ids, $isPush = 1, $isPushJd = -1)
    {
        $isPush >= 0 && $data['isPush'] = $isPush;
        $isPushJd >= 0 && $data['isPushJd'] = $isPushJd;
        $data['emailTime'] = !empty($params['emailTime']) ? $params['emailTime'] : date('Y-m-d H:i:s');
        $this->db->where_in('id', $ids);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
    /**
     * 批量更新[新增简历]的简历状态，更新为[过往简历]-批量
     * @param $ids
     * @param $isFindJob
     */
    public function updateResumeFindJobsBatch($ids, $isFindJob = 6)
    {
        $isFindJob >= 0 && $data['isFindJob'] = (int)$isFindJob;
        $data['updateTime'] = date('Y-m-d H:i:s');
        $this->db->where_in('id', $ids);
        $this->db->where('isFindJob', 1);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }
}