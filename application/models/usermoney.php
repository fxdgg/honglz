<?php
/**
 * 用户-荔枝币表
 *
 */
class UserMoney extends CI_Model
{
    public $_table = 'tbl_user_money';

    function __construct()
    {
        parent::__construct();
    }
    /**
     * 获取该用户的荔枝币数据
     * @param $userid
     */
    public function getMoney($userid)
    {
       $this->db->select('userid,lizhi');
       $this->db->where('userid', $userid);
       $this->db->limit(1);
       $query = $this->db->get($this->_table);
       $result = $query->row_array();
       return $result;
    }
	/**
	 * 荔枝币增加逻辑
	 * @param int $userid
	 */
    public function addMoney($userid, $lizhi, $returnLastId = FALSE)
    {
    	$lizhi = abs((float)$lizhi);
    	$moneyData = $this->getMoney($userid);
    	if (empty($moneyData))
    	{
	    	$data = array(
	    		'userid' => $userid,
	    		'lizhi' => $lizhi,
	    	);
	    	$ret = $this->db->insert($this->_table, $data);
	    	return $returnLastId ? $this->db->insert_id() : $ret;
    	}
    	else
    	{
    		$updatetime = date('Y-m-d H:i:s', SYS_TIME);
    		$this->db->query("UPDATE `{$this->_table}` SET `lizhi`=`lizhi`+{$lizhi},`updatetime`='{$updatetime}' WHERE `userid` = '{$userid}'");
        	return $this->db->affected_rows() > 0;
    	}
    }
    /**
     * 荔枝币减少逻辑
     * @param int $userid
     * @param int $lizhi
     */
    public function reduceMoney($userid, $lizhi)
    {
    	$moneyData = $this->getMoney($userid);
    	$lizhi = abs((float)$lizhi);
    	if (empty($moneyData) OR $moneyData['lizhi'] < $lizhi)
    	{
    		return FALSE;
    	}
    	$updatetime = date('Y-m-d H:i:s', SYS_TIME);
    	$this->db->query("UPDATE `{$this->_table}` SET `lizhi`=`lizhi`-{$lizhi},`updatetime`='{$updatetime}' WHERE `userid` = '{$userid}'");
        return $this->db->affected_rows() > 0;
    }

}