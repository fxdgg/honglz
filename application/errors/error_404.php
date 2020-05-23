<?php 
$error = array('status'=>false,'errcode'=>404,'errmsg'=>array('heading'=>$heading,'message'=>$message));
echo json_encode($error);exit;
?>