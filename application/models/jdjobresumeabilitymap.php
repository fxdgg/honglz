<?php
/**
 * 简历-能力特征-关联表
 */
class Jdjobresumeabilitymap extends CI_Model{
    public $_table = 'tbl_jd_resume_ability_map';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 录入信息
     */
    public function createJdResumeAbilityMap($resumeId, $abilityFeatureId, $returnLastId = FALSE)
    {
        $data = array(
            'resumeId' => $resumeId,
            'abilityFeatureId' => $abilityFeatureId,
        );
        $ret = $this->db->insert($this->_table, $data);
        return $returnLastId ? $this->db->insert_id() : $ret;
    }

    /**
     * 删除信息-物理删除
     * @param $id
     */
    public function deleteJdResumeAbilityMap($resumeId)
    {
        $this->db->where('resumeId', $resumeId);
        return $this->db->delete($this->_table);
    }
}