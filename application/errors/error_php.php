<?php 
$error = array('status'=>false,'errcode'=>500,'errmsg'=>array('Severity'=>$severity,'message'=>$message,'filepath'=>$filepath,'line'=>$line));
echo json_encode($error);exit;
?>