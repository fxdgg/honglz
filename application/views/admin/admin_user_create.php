<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>common.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
<style>
.bg1{
background-image:url(<?php echo IMAGE_PATH;?>/bg01.gif);
color:#FFFFFF;
font-size:14px;
font-weight:bold;
width:100px;
height:20px;
padding-top:5px;
margin-left:3px;
}
.bg2{
background-image:url(<?php echo IMAGE_PATH;?>bg02.gif);
color:#000000;
font-size:12px;
font-weight:bold;
width:100px;
height:17px;
padding-top:8px;
margin-left:3px;
}
.zhezhao {background-color:#666666; position: absolute;z-index:5000; top:0px; left:0px;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}
</style>
</head>

<body bgcolor="#DDEEF2">
<form action='<?php echo APP_SITE_URL;?>/admin/create_user_welcome/<?php echo $userid;?>' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
    	<div align="center" class="black_14_bold">创建舶来用户</div></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>用户UID：</b></td>
    <td width="35%"><?php echo $userid;?></td>
    <td width="15%" align="right"><b>邮箱：</b></td>
    <td width="35%"><input type="text" name="email" id="email" value="" maxlength="50" style="width: 180px;" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right"><b>昵称：</b></td>
    <td width="35%"><input type="text" name="nickname" id="nickname" value="" maxlength="50" style="width: 180px;" /></td>
    <td height="25%" align="right"><b>登录密码：</b></td>
    <td><input type="password" name="password" id="password" value="" style="width: 180px;" /></td>
  </tr>
   
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>性别：</b></td>
    <td>
		<select id="sex" name="sex">
			<option value="1">男</option>
			<option value="2">女</option>
		</select>
	</td>
    <td align="right" height="25"><b>所在区域：</b></td>
 	<td><input type="text" name="area" id="area" value="" style="width: 180px;" /></td>
  </tr>
  
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>所在公司：</b></td>
    <td><input type="text" name="company" id="company" value="" style="width: 180px;" /></td>
    <td align="right"><b>职位：</b></td>
    <td><input type="text" name="position" id="position" value="" style="width: 180px;" /></td>
  </tr>
  
  <tr bgcolor="#FFFFFF" style="display:none;">
      <td height="30" colspan="2">
	      <div align="center" id="loginmsg" style="color: #FF0000;"></div>
	  </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan='4' align="center">
    	<input type="submit" name="editUser" id="editUser" width="41" height="21" value="提交"/>
    </td>
  </tr>
</table>
</form>

<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td align="right" height="25">&nbsp;</td>
  </tr>
</table>
</body>
</html>