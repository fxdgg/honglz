<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>admin.js"></script>
<script type="text/javascript">
	function choose_server() {
		var choose_server = $("#choose_server");
		var host = choose_server.val();
		var myform = $('<form></form>');
		myform.attr("method", "post");
		myform.attr("target", "_top");
		myform.attr("action", "http://" + host
				+ "/admin/login_check_jump_login");
		var account = $('<input/>');
		account.attr({
			name : "account",
			value : "<?php echo $account;?>"
		});
		account.appendTo(myform);
		var key = $('<input/>');
		key.attr({
			name : "key",
			value : "<?php echo $key;?>"
		});
		key.appendTo(myform);
		var time = $('<input/>');
		time.attr({
			name : "time",
			value : "<?php echo $time;?>"
		});
		time.appendTo(myform);
		var sign = $('<input/>');
		sign.attr({
			name : "sign",
			value : "<?php echo $sign;?>"
		});
		sign.appendTo(myform);
		//必须的
		myform.appendTo($("body"));
		myform.submit();
	}
</script>
<style>
body,input,table,form,div,li,ul,select {
	margin: 0px;
	font-size: 12px;
	font-family: Tahoma;
}

a {
	text-decoration: none;
}
</style>
</head>
<body onload="loop();">
<table border="0" cellpadding="0" cellspacing="0" width="100%"
	bgcolor="#278296">
	<tr>
		<td width="50%">
			<span style="padding-left: 5px; font-size: 24px; color: #FFFFFF; font-weight: bold;"><?php echo $title;?><?php echo (!empty($special_tips) ? '-'.$special_tips : '');?></span>
		</td>
		<td rowspan="2" valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center" height="80"><span id="t"
					style="color: #ffffff; font-size: 13px;"></span></td>
				<td height="10%" align="right"><span style="color:#00ffff;font-size:13px;">你好，<?php echo $account;?></span>&nbsp;&nbsp;&nbsp; &nbsp; <span id="t"
					style="color: #ffffff; font-size: 13px;">语言选择:</span>
					<select id="chooseLang">
						<option value="zh_CN"<?php if ($lang == 'zh_CN'){?> selected="selected" <?php }?>>简体</option>
						<option value="zh_TW"<?php if ($lang == 'zh_TW'){?> selected="selected" <?php }?>>繁体</option>
					</select> <span style="padding-right: 10px;"> <a
					href="<?php echo APP_SITE_URL;?>/admin/logout"><font color="#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;安全退出</font></a>
				&nbsp;&nbsp;&nbsp;&nbsp; </td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
