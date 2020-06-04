<?php
/**
 * JD-能力特征列表
 */
class Jdjobabilityfeature extends CI_Model{
    public $_table = 'tbl_jd_job_ability_feature';

	/**
	 * 性别对应关系
	 * @var array
	 */
	public static $ability_feature_field_config = array(
		1 => 'graduateSchool',
		2 => 'nowCompany|onceCompany',
	);

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取-分页
     */
    public function fetchAllJdJobAbilityFeatureList($page = 1, $pagesize = 100, $isAdmin = FALSE, $state='', $type = 0)
    {
        $this->db->select('tjjaf.id,tjjaf.abilityFeatureId,tjjaf.abilityFeatureName,tjjaf.abilityFeatureDescribe,tjjaf.sortId,tjjaf.state');
        !$isAdmin && $this->db->where('tjjaf.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjaf.state', $state);
        $isAdmin && strlen($type) > 0 && $this->db->where('tjjaf.type', (int)$type);
        $this->db->order_by('tjjaf.sortId DESC, tjjaf.id ASC');
        $page > 0 && $pagesize > 0 && $this->db->limit($pagesize, ($page-1)*$pagesize);
        $query = $this->db->get($this->_table . ' tjjaf');
        $res = $query->result_array('abilityFeatureId');
        return !empty($res) ? $res : array();
    }

    public function allJdJobAbilityFeatureListTotal($isAdmin = FALSE, $state='')
    {
        $this->db->select('tjjaf.id,tjjaf.abilityFeatureId,tjjaf.abilityFeatureName,tjjaf.state');
        !$isAdmin && $this->db->where('tjjaf.state', 'new');
        $isAdmin && !empty($state) && $this->db->where('tjjaf.state', $state);
        $query = $this->db->get($this->_table . ' tjjaf');
        $total = count($query->result_array());
        return $total;
    }

    /**
     * 获取列表通过ID数组
     * @param $id
     * @return array
     */
    public function fetchListByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        $this->db->select('abilityFeatureId,abilityFeatureName');
        $this->db->where('type', 1);
        $this->db->where('state', 'new');
        $this->db->where_in('id', $ids);
        $this->db->order_by('sortId DESC');
        $query = $this->db->get($this->_table);
        // echo __FUNCTION__.'|last_query=>'.$this->db->last_query().'<br />';
        $res = $query->result_array('abilityFeatureId');
        return !empty($res) ? $res : array();
    }
}