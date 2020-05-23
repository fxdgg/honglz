<?php
/**
 * 用户-充值订单表
 *
 */
class UserOrder extends CI_Model
{
    public $_table = 'tbl_user_order';

    function __construct()
    {
        parent::__construct();
    }
	/**
	 * 充值订单逻辑
	 * @param int $userid
	 * @param int $pay_fee
	 */
    public function addOrder($userid, $pay_fee, $returnLastId = FALSE)
    {
    	$data = array(
    		'userid' => $userid,
    		'pay_fee' => $pay_fee,
    	);
    	$ret = $this->db->insert($this->_table, $data);
	    return $returnLastId ? $this->db->insert_id() : $ret;
    }

}