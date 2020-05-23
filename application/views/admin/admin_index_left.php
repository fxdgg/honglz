<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo JS_PATH;?>admin.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
<title>网站后台管理系统</title>
<style>
body,input,table,form,div,li,ul,select,textarea {
	margin: 0px;
	font-size: 12px;
	font-family: Tahoma;
}

.divnone {
	display: none;
}

.divblock {
	display: block;
}

.liimg {
	margin-left: -27px;
	*margin-left: 13px;
	_margin-left: 13px;
	/*background: url(<?php echo IMAGE_PATH;?>menu_arrow.gif) no-repeat 0px 3px;*/
	padding-left: 13px;
	_padding-left: 13px;
	*padding-left: 13px;
	list-style-type: none;
}

.openitem {
	cursor: pointer;
	background: url(<?php echo IMAGE_PATH;?>menu_minus.gif) no-repeat 0px 3px;
	padding-left: 13px;
	color: #335B64;
	font-weight: bold;
	padding-top: 1px;
}

.closeitem {
	cursor: pointer;
	background: url(<?php echo IMAGE_PATH;?>menu_plus.gif) no-repeat 0px 3px;
	padding-left: 13px;
	color: #335B64;
	font-weight: bold;
	padding-top: 1px;
}

a {
	text-decoration: none;
	color: #335B64;
}

a:hover {
	text-decoration: underline;
	color: #FF8000;
}

a:active {
	text-decoration: none;
	color: #FF8000;
}

.exit_item {
	/*background: url(<?php echo IMAGE_PATH;?>menu_arrow.gif) no-repeat 0px 3px;*/
	list-style-type: none;
	padding-left: 13px;
	_padding-left: 0px;
	*padding-left: 0px;
}

.menu_checked{
	color:#FF8000;
	font-weight:bolder;
}
</style>
</head>
<body bgcolor="#80BDCB">
<table border="0" cellpadding="0" cellspacing="0" width="180"
	align="center" bgcolor="#FFFFFF"
	style="border: 1px solid #000000; margin-top: 5px;">
	<tr>
		<td>
		<div style="margin-left: 5px;"><?php foreach($menus as $key => $item){?> 
		<?php if ($item['has']==0 || $item['isview']==0 || ($item['server_type']!='all'&&$item['server_type']!=$menuType)){ }else{?>
		<div class="openitem" id="show<?php echo $key;?>" onclick="open_close_item('show<?php echo $key;?>');open_close_li('item<?php echo $key;?>');"><?php echo $item['name'];?></div>
		<div id="item<?php echo $key;?>">
		<ul class='sub_item' data='<?php echo $key;?>'>
			<?php foreach ($item['children'] as $item){?> <?php if ($item['has']==0 || $item['isview']==0 || ($item['server_type']!='all'&&$item['server_type']!=$menuType)){?> <?php }else{?>
			<li class="liimg"><a href="<?php echo $item['path'];?>" target="right"><?php echo $item['name'];?></a></li>
			<?php }}?>
		</ul>
		</div>
		<?php }}?> <?php if ($viewManagerMenu==1){?>
		<div class="openitem" id="show0"
			 onclick="open_close_item('show0');open_close_li('item0');">权限管理</div>
		<div id="item0">
		<ul>
			<li class="liimg"><a href="/permission/group" target="right">群组管理</a></li>
			<li class="liimg"><a href="/permission/user" target="right">用户管理</a></li>
			<li class="liimg"><a href="/permission/menu" target="right">菜单管理</a></li>
			<li class="liimg"><a href="/permission/purview" target="right">权限管理</a></li>
			<li class="liimg"><a href="/permission/platform" target="right">平台管理</a></li>
			<li class="liimg"><a href="/permission/server" target="right">服务器管理</a></li>
		</ul>
		</div>
		<?php }?>

<!--
<{if $server_id >= '10000'}>
		<div class="openitem" id="show1001"
			 onclick="open_close_item('show1001');open_close_li('item1001');">总服新增功能</div>
		<div id="item1001">
		<ul>
			<li class="liimg"><a href="/center/day-pay-series/charge-by-day" target="right">每日充值明细相关</a></li>
			<li class="liimg"><a href="/center/day-pay-series/daily-recharge-details" target="right">每日充值相关</a></li>
			<li class="liimg"><a href="/center/day-pay-series/daily-recharge-month" target="right">每月充值相关</a></li>
			<li class="liimg"><a href="/center/day-pay-series/real-time-compare" target="right">实时对比</a></li>
			<li class="liimg"><a href="/platform/execute-query/index" target="right">全服SQL执行</a></li>
		</ul>
		</div>
<{elseif $server_id == '0'}>
		<div class="openitem" id="show1000"
			 onclick="open_close_item('show1000');open_close_li('item1000');">平台新增功能</div>
		<div id="item1000">
		<ul>
			<li class="liimg"><a href="/platform/cycle-message/show-list" target="right">循环消息</a></li>
			<li class="liimg"><a href="/platform/day-pay-stat/show-list" target="right">每日充值汇总</a></li>
			<li class="liimg"><a href="/platform/day-summary/show-list" target="right">每日汇总</a></li>
			<li class="liimg"><a href="/platform/inter-person/show-list" target="right">内部人员管理</a></li>
			<li class="liimg"><a href="/platform/mall-goods/show-list" target="right">商城物品管理</a></li>
			<li class="liimg"><a href="/platform/mall-privilege/show-list" target="right">商城特惠物品</a></li>
			<li class="liimg"><a href="/platform/monster/show-list" target="right">怪物掉落设置</a></li>
			<li class="liimg"><a href="/platform/reward-send/index" target="right">奖励发放</a></li>
			<li class="liimg"><a href="/platform/role-suggest/show-list" target="right">玩家建议</a></li>
			<li class="liimg"><a href="/platform/festival-activity/show-list" target="right">节日活动</a></li>
			<li class="liimg"><a href="/platform/favorable-activity/show-list" target="right">优惠活动</a></li>
		</ul>
		</div>
<{else}>
		<div class="openitem" id="show1002"
			 onclick="open_close_item('show1002');open_close_li('item1002');">单服新增功能</div>
		<div id="item1002">
		<ul>
			<li class="liimg"><a href="/pay/role-recharge/show-list" target="right">玩家储值</a></li>
			<li class="liimg"><a href="/pay/charge-list/show-list" target="right">充值排行全服</a></li>
			<li class="liimg"><a href="/pay/level-collect/show-list" target="right">充值等级汇总</a></li>
			<li class="liimg"><a href="/pay/prorate/show-list" target="right">充值金额段比例</a></li>
			<li class="liimg"><a href="/pay/day-pay/show-list" target="right">每日充值</a></li>
			<li class="liimg"><a href="/searchuser/user-unlock/show-list" target="right">玩家解锁</a></li>
			<li class="liimg"><a href="/searchuser/mall-buy-item/show-list" target="right">商城消费查询</a></li>
			<li class="liimg"><a href="/searchuser/mall-buy-item/show-item-list" target="right">商城道具查询</a></li>
		</ul>
		</div>
<{/if}>
-->

		<div class="openitem" id="show-1"
			onclick="open_close_item('show-1');open_close_li('item-1');">个人中心</div>
		<div id="item-1">
		<ul>
				<!--<li class="liimg"><a href="/permission/individual/change-psw" target="right">修改密码</a></li>-->
				<li class="liimg"><a href="<?php echo APP_SITE_URL;?>/admin/logout" target="right">安全退出</a></li>
				<!--<li class="liimg"><a href="/default/index/test" target="right">测试</a></li>-->
		</ul>
		</div>
		</td>
	</tr>
</table>
</body>
<script>
$(function(){
   $('.sub_item').each(function(){
		if ($.trim($(this).html()) == '')
		{
			var idstr = $(this).attr('data');
			$('#show'+idstr).hide();
		}
   });
   
   $('.liimg').click(function(){
	   $('.liimg').find('a').removeClass('menu_checked');
	   $(this).find('a').addClass('menu_checked');
   });
});
</script>
</html>