<?php
/**
 * JD-能力特征-关联表
 */
class Jdjobabilitymap extends CI_Model{
    public $_table = 'tbl_jd_job_ability_map';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 录入信息
     */
    public function createJdAbilityMap($jdId, $abilityFeatureId, $returnLastId = FALSE)
    {
        $data = array(
            'jdId' => $jdId,
            'abilityFeatureId' => $abilityFeatureId,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }

    /**
     * 删除信息-物理删除
     * @param $id
     */
    public function deleteJdAbilityMap($jdId)
    {
        $this->db->where('jdId', $jdId);
        return $this->db->delete($this->_table);
    }
}