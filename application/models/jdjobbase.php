<?php
/**
 * JD-基础表
 */
class Jdjobbase extends CI_Model{
    public $_table = 'tbl_jd_job_base';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取JD-基础表的某条JD信息
     * @param int $id JDID
     */
    public function fetchJdBaseInfoById($id, $isAdmin = FALSE)
    {
        $this->db->select();
        $this->db->where('tjjb.id', $id);
        !$isAdmin && $this->db->where('tjjb.state', 'new');
        $query = $this->db->get($this->_table . ' tjjb');
        $res = $query->row_array();
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 获取按企业分组的所有JD列表
     *
     */
    public function fetchAllJdListByCompany($state = 'new')
    {
        $this->db->select(array_merge(array('tjjb.id','tjjb.companyName'),array('GROUP_CONCAT(`tjjb`.`email`) AS emailGroup'),array('GROUP_CONCAT(CONCAT_WS("_", tjjb.jobClassId, tjjb.areaId)) AS jobClassIdAreaGroup')));
        $this->db->where('tjjb.jdPushStatus > ', 0);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $this->db->where('tjjb.jobClassId > ', 0);
        $this->db->where('tjjb.areaId > ', 0);
        !empty($state) && $this->db->where('tjjb.state', $state);
        $this->db->group_by('tjjb.companyName');
        $query = $this->db->get($this->_table . ' tjjb');
        $list = $query->result_array();
        echo __FUNCTION__.'|last_query=>'.$this->db->last_query().PHP_EOL;
        return !empty($list) ? $list : array();
    }

    /**
     * 获取JD-基础表的列表-分页
     */
    public function fetchAllJdBaseList($page = 1, $pagesize = 50, $isAdmin = FALSE, $state = '', $isPush = '-1', $jdPushStatus = '-1', $isForceNew = FALSE, $jobClassId = 0, $jobId = 0)
    {
        $this->db->select('tjjb.*,tjjc.jobClassName,tjjl.jobLevelName,tjja.areaName,tjjct.companyTypeName,tjjvt.vocationTypeName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjjb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_level tjjl', 'tjjl.jobLevelId = tjjb.jobLevelId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjjb.areaId', 'LEFT');
        $this->db->join('tbl_jd_job_company_type tjjct', 'tjjct.companyTypeId = tjjb.companyTypeId', 'LEFT');
        $this->db->join('tbl_jd_job_vocation_type tjjvt', 'tjjvt.vocationTypeId = tjjb.vocationTypeId', 'LEFT');
        (!$isAdmin OR $isForceNew) && $this->db->where('tjjb.state', 'new');
        $isPush != -1 && $this->db->where('tjjb.isPush', $isPush);
        $jdPushStatus == -2 && $this->db->where('tjjb.jdPushStatus > ', 0);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $jdPushStatus >= 0 && $this->db->where('tjjb.jdPushStatus', $jdPushStatus);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $jobClassId > 0 && $this->db->where('tjjb.jobClassId', $jobClassId);//职位分类编号
        $jobId > 0 && $this->db->where('tjjb.id', $jobId);
        $isAdmin && !empty($state) && $this->db->where('tjjb.state', $state);
        $this->db->order_by('tjjb.updateTime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjb');
        $res = $query->result_array();
        // echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    public function allJdBaseListTotal($isAdmin = FALSE, $state = '', $isPush = '-1', $jdPushStatus = '-1', $isForceNew = FALSE, $jobClassId = 0, $areaId = 0, $jobId = 0)
    {
        $this->db->select('tjjb.*,tjjc.jobClassName,tjjl.jobLevelName,tjja.areaName,tjjct.companyTypeName,tjjvt.vocationTypeName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjjb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_level tjjl', 'tjjl.jobLevelId = tjjb.jobLevelId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjjb.areaId', 'LEFT');
        $this->db->join('tbl_jd_job_company_type tjjct', 'tjjct.companyTypeId = tjjb.companyTypeId', 'LEFT');
        $this->db->join('tbl_jd_job_vocation_type tjjvt', 'tjjvt.vocationTypeId = tjjb.vocationTypeId', 'LEFT');
        (!$isAdmin OR $isForceNew) && $this->db->where('tjjb.state', 'new');
        $isPush != -1 && $this->db->where('tjjb.isPush', $isPush);
        $jdPushStatus == -2 && $this->db->where('tjjb.jdPushStatus > ', 0);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $jdPushStatus >= 0 && $this->db->where('tjjb.jdPushStatus', $jdPushStatus);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $isAdmin && !empty($state) && $this->db->where('tjjb.state', $state);
        $jobClassId > 0 && $this->db->where('tjjb.jobClassId > ', 0);
        $areaId > 0 && $this->db->where('tjjb.areaId > ', 0);
        $jobId > 0 && $this->db->where('tjjb.id', $jobId);
        $query = $this->db->get($this->_table . ' tjjb');
        $total = count($query->result_array());
        //echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        return $total;
    }

    /**
     * 获取JD-基础表的列表-分页-用某个字段做键值
     */
    public function fetchAllJdBaseListByKey($page = 1, $pagesize = 50, $isAdmin = FALSE, $state = '', $isPush = '-1', $jdPushStatus = '-1', $isForceNew = FALSE, $field_key = 'jobClassId_areaId', $jobClassId = 0, $areaId = 0)
    {
        $this->db->select('tjjb.*,tjjc.jobClassName,tjjl.jobLevelName,tjja.areaName,tjjct.companyTypeName,tjjvt.vocationTypeName');
        $this->db->join('tbl_jd_job_class tjjc', 'tjjc.jobClassId = tjjb.jobClassId', 'LEFT');
        $this->db->join('tbl_jd_job_level tjjl', 'tjjl.jobLevelId = tjjb.jobLevelId', 'LEFT');
        $this->db->join('tbl_jd_job_area tjja', 'tjja.areaId = tjjb.areaId', 'LEFT');
        $this->db->join('tbl_jd_job_company_type tjjct', 'tjjct.companyTypeId = tjjb.companyTypeId', 'LEFT');
        $this->db->join('tbl_jd_job_vocation_type tjjvt', 'tjjvt.vocationTypeId = tjjb.vocationTypeId', 'LEFT');
        (!$isAdmin OR $isForceNew) && $this->db->where('tjjb.state', 'new');
        $isPush != -1 && $this->db->where('tjjb.isPush', $isPush);
        $jdPushStatus == -2 && $this->db->where('tjjb.jdPushStatus > ', 0);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $jdPushStatus >= 0 && $this->db->where('tjjb.jdPushStatus', $jdPushStatus);//JD的邮件推送状态(0:暂停发送1:营销中2:推荐中)
        $isAdmin && !empty($state) && $this->db->where('tjjb.state', $state);
        $jobClassId > 0 && $this->db->where('tjjb.jobClassId > ', 0);
        $areaId > 0 && $this->db->where('tjjb.areaId > ', 0);
        $this->db->order_by('tjjb.updateTime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjb');
        $res = !empty($field_key) ? $query->result_array($field_key, '', 'LIST') : $query->result_array();
        //echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 录入JD-基本信息
     */
    public function createJdBaseInfo(&$params, $returnLastId = FALSE)
    {
        $data = array(
            'type' => isset($params['type']) ? intval($params['type']) : 0,
            'creatorUid' => !empty($params['creatorUid']) ? intval($params['creatorUid']) : 0,
            'companyName' => isset($params['companyName']) ? htmlspecialchars($params['companyName']) : '',
            'companySite' => isset($params['companySite']) ? htmlspecialchars($params['companySite']) : '',
            'jdUrl' => isset($params['jdUrl']) ? $params['jdUrl'] : '',
            'workExperience' => isset($params['workExperience']) ? (int)$params['workExperience'] : 0,
            'monthlySalary' => isset($params['monthlySalary']) ? (int)$params['monthlySalary'] : 0,
            'describeContent' => isset($params['describeContent']) ? $params['describeContent'] : '',
            'demandContent' => isset($params['demandContent']) ? $params['demandContent'] : '',
            'email' => isset($params['email']) ? htmlspecialchars($params['email']) : '',
            'contact' => isset($params['contact']) ? htmlspecialchars($params['contact']) : '',
            'teamNumber' => isset($params['teamNumber']) ? (int)$params['teamNumber'] : 0,
            'jobClassId' => isset($params['jobClassId']) ? (int)$params['jobClassId'] : 0,
            'jobLevelId' => isset($params['jobLevelId']) ? (int)$params['jobLevelId'] : 0,
            'areaId' => isset($params['areaId']) ? (int)$params['areaId'] : 0,
            'companyTypeId' => isset($params['companyTypeId']) ? (int)$params['companyTypeId'] : 0,
            'vocationTypeId' => isset($params['vocationTypeId']) ? (int)$params['vocationTypeId'] : 0,
            'isPush' => isset($params['isPush']) ? (int)$params['isPush'] : 0,
            'jdPushStatus' => isset($params['jdPushStatus']) ? (int)$params['jdPushStatus'] : 0,
            'financeStage' => isset($params['financeStage']) ? htmlspecialchars($params['financeStage']) : '',
            'workDuration' => isset($params['workDuration']) ? htmlspecialchars($params['workDuration']) : '',
            'avgYearlySalary' => isset($params['avgYearlySalary']) ? htmlspecialchars($params['avgYearlySalary']) : '',
            'employeeInsurance' => isset($params['employeeInsurance']) ? htmlspecialchars($params['employeeInsurance']) : '',
            'overtimeStatus' => isset($params['overtimeStatus']) ? htmlspecialchars($params['overtimeStatus']) : '',
            'abilityFeature' => isset($params['abilityFeatureString']) ? htmlspecialchars($params['abilityFeatureString']) : '',
            'createTime' => date('Y-m-d H:i:s'),
            'state' => 'new',
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }

    /**
     * 更新JD-基本信息
     */
    public function updateJdBaseInfo(&$params)
    {
        if (isset($params['companyName'])) $data['companyName'] = $params['companyName'];
        if (isset($params['companySite'])) $data['companySite'] = $params['companySite'];
        if (isset($params['workExperience'])) $data['workExperience'] = (int)$params['workExperience'];
        if (isset($params['monthlySalary'])) $data['monthlySalary'] = (int)$params['monthlySalary'];
        if (isset($params['describeContent'])) $data['describeContent'] = htmlspecialchars($params['describeContent']);
        if (isset($params['demandContent'])) $data['demandContent'] = htmlspecialchars($params['demandContent']);
        if (isset($params['email'])) $data['email'] = htmlspecialchars($params['email']);
        if (isset($params['contact'])) $data['contact'] = htmlspecialchars($params['contact']);
        if (isset($params['teamNumber'])) $data['teamNumber'] = (int)$params['teamNumber'];
        if (isset($params['jobClassId'])) $data['jobClassId'] = (int)$params['jobClassId'];
        if (isset($params['jobLevelId'])) $data['jobLevelId'] = (int)$params['jobLevelId'];
        if (isset($params['areaId'])) $data['areaId'] = (int)$params['areaId'];
        if (isset($params['companyTypeId'])) $data['companyTypeId'] = (int)$params['companyTypeId'];
        if (isset($params['vocationTypeId'])) $data['vocationTypeId'] = (int)$params['vocationTypeId'];
        if (isset($params['isPush'])) $data['isPush'] = (int)$params['isPush'];
        if (isset($params['jdPushStatus'])) $data['jdPushStatus'] = (int)$params['jdPushStatus'];
        if (isset($params['financeStage'])) $data['financeStage'] = htmlspecialchars($params['financeStage']);
        if (isset($params['workDuration'])) $data['workDuration'] = htmlspecialchars($params['workDuration']);
        if (isset($params['avgYearlySalary'])) $data['avgYearlySalary'] = htmlspecialchars($params['avgYearlySalary']);
        if (isset($params['employeeInsurance'])) $data['employeeInsurance'] = htmlspecialchars($params['employeeInsurance']);
        if (isset($params['overtimeStatus'])) $data['overtimeStatus'] = htmlspecialchars($params['overtimeStatus']);
        if (isset($params['abilityFeatureString'])) $data['abilityFeature'] = htmlspecialchars($params['abilityFeatureString']);
        if (isset($params['jdUrl'])) $data['jdUrl'] = htmlspecialchars($params['jdUrl']);
        if (isset($params['state'])) $data['state'] = $params['state'];
        $data['updateTime'] = !empty($params['updateTime']) ? $params['updateTime'] : date('Y-m-d H:i:s');
        $this->db->where('id', $params['id']);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 删除JD-基本信息
     * @param $id
     */
    public function dropJdBaseInfo($id)
    {
        $data = array('state'=>'delete');
        $this->db->where('id', $id);
        $this->db->update($this->_table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * 删除JD-基本信息-物理删除
     * @param $id
     */
    public function deleteJdBaseInfo($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->_table);
    }

    /**
     * 获取供需列表-按行业分组
     */
    public function fetchDemandListGroup($page = 1, $pagesize = 20, $type = 1)
    {
        $this->db->select(array('GROUP_CONCAT(`id` ORDER BY updateTime DESC) as groupIds'));
        $this->db->where('type', $type);
        $this->db->where('state', 'new');
        $this->db->group_by('companyTypeId');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table);
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        $res = $query->result_array();
        return !empty($res) ? $res : array();
    }

    /**
     * 获取供需列表-分页
     */
    public function fetchDemandList($ids = [], $page = 1, $pagesize = 20, $timeRange = [], $condition = [], $type = 1)
    {
        $this->db->select('id,type,companyName,describeContent,demandContent,companyTypeId,abilityFeature,createTime,updateTime');
        !empty($ids) && $this->db->where_in('id', $ids);
        $this->db->where('type', $type);
        $this->db->where('state', 'new');
        if (!empty($timeRange)) {
            $this->db->ar_where[] = " AND `createTime` BETWEEN '{$timeRange[0]}' AND '{$timeRange[1]}'";
        }
        // 行业分类
        if (!empty($condition['companyTypeId'])) {
            $this->db->where('companyTypeId', (int)$condition['companyTypeId']);
        }
        $this->db->order_by('updateTime DESC');
        $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table);
        $res = $query->result_array();
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }

    /**
     * 获取供需列表-总数
     */
    public function fetchDemandTotal($ids = [], $timeRange = [], $condition = [], $type = 1) {
        $this->db->select('*');
        !empty($ids) && $this->db->where_in('id', $ids);
        $this->db->where('type', $type);
        $this->db->where('state', 'new');
        if (!empty($timeRange)) {
            $this->db->ar_where[] = " AND `createTime` BETWEEN '{$timeRange[0]}' AND '{$timeRange[1]}'";
        }
        // 行业分类
        if (!empty($condition['companyTypeId'])) {
            $this->db->where('companyTypeId', (int)$condition['companyTypeId']);
        }
        $query = $this->db->get($this->_table);
        $total = count($query->result_array());
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        return $total;
    }

    /**
     * 获取某条信息
     * @param int $id 编号
     */
    public function fetchInfoById($id, $type = 1)
    {
        $this->db->select();
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->where('state', 'new');
        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        $res = $query->row_array();
        //echo 'last_query=>'.$this->db->last_query().'<br />';
        return !empty($res) ? $res : array();
    }
}