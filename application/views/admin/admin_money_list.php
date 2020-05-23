<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.jin_button {
	width: 38px;
	height: 20px;
}

.top_button {
	width: 63px;
	height: 22px;
}

.over {
	background-color: #F0F0F0;
}

.out {
	background-color: #FFFFFF;
}
</style>
</head>
<body bgcolor="#DDEEF2">
<table width="98%" border="0" cellspacing="1" cellpadding="2"
	align="center" bgcolor="#BBDDE5" class="margin_top_10">
	<tr>
		<td bgcolor="#FFFFFF" height="31" colspan="8">
		<div align="center" class="black_14_bold">荔枝币流水表</div>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" height="28" colspan="8">
		<div style="margin-left: 12px;"></div>
		</td>
	</tr>
	<tr height="22" bgcolor="#EEEEEE">
		<!--<th width="2%" height="25" align="center"><input type="checkbox"
			id="checkall" onclick="check_all(this)" title="全选/不选 本页所有角色" /></th>-->
		<th width="6%" height="25" align="center"><b>发起私聊的用户UID</b></th>
		<th width="4%" align="center"><b>接受私聊的用户UID</b></th>
		<th width="4%" align="center"><b>消耗荔枝币</b></th>
		<th width="4%" align="center"><b>私聊开始时间</b></th>
		<th width="4%" align="center"><b>私聊截止有效期</b></th>
		<th width="4%" align="center"><b>所在社团</b></th>
		<th width="4%" align="center"><b>用户的社团状态</b></th>
		<th width="4%" align="center"><b>社团状态</b></th>
	</tr>
	<?php $lizhiSumTotal=0;foreach ($money_list as $data){?>
	<tr height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'"
		onMouseOut="this.className='out'">
		<!--<td align="center"><input type="checkbox" id="check" name="check"
			value="<{ $data.roleid }>,<{ $data.charname }>" /></td>-->
		<td align="center"><?php echo $data['send_userid'];?></a></td>
		<td align="center"><?php echo $data['receive_userid'];?></td>
		<td align="center"><?php echo $data['pay_money'];?></td>
		<td align="center"><?php echo $data['createTime'];?></td>
		<td align="center"><?php echo date('Y-m-d H:i:s', $data['expiresTime']);?></td>
		<td align="center"><?php echo (!empty($data['unionName']) ? $data['unionName'] : '未知');?></td>
		<?php if($data['unionRole'] == '0'){?>
		<td align="center">被踢出</td>
		<?php }elseif($data['unionRole'] == '1'){?>
		<td align="center">普通成员</td>
		<?php }elseif($data['unionRole'] == '2'){?>
		<td align="center">管理员</td>
		<?php }elseif($data['unionRole'] == '3'){?>
		<td align="center">二级管理员</td>
		<?php }else{?>
		<td align="center">未知</td>
		<?php }?>
		<?php if($data['unionStatus'] == '0'){?>
		<td align="center">已关闭</td>
		<?php }elseif($data['unionStatus'] == '1'){?>
		<td align="center">非认证且临时</td>
		<?php }elseif($data['unionStatus'] == '2'){?>
		<td align="center">非认证且生效</td>
		<?php }elseif($data['unionStatus'] == '3'){?>
		<td align="center">认证且生效</td>
        <?php }else{?>
        <td align="center">未知</td>
		<?php }?>
	</tr>
	<?php $lizhiSumTotal += $data['pay_money'];}?>
	<tr bgcolor="#FFFFFF">
		<td height="25">消耗荔枝币总计：</td>
		<td colspan="7" align="left"><?php echo $lizhiSumTotal;?></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" height="25" colspan="19">
		<div align="center"><?php echo $page;?></div>
		</td>
	</tr>
</table>
</body>
</html>