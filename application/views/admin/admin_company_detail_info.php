<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
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
<form action='<?php echo APP_SITE_URL;?>/admin/company_detail_info/<?php echo $company_info['companyId'];?>' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
    	<div align="center" class="black_14_bold">公司-社团关系信息</div></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>公司ID：</b></td>
    <td width="35%"><?php echo $company_info['companyId'];?></td>
    <td width="15%" align="right"><b>公司名称：</b></td>
    <td width="35%"><input type="text" name="companyName" id="companyName" value="<?php echo $company_info['companyName'];?>" style="width: 180px;" /><font color="#f00000">*必填</font></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>公司简称：</b></td>
    <td width="35%"><input type="text" name="companyNick" id="companyNick" value="<?php echo $company_info['companyNick'];?>" style="width: 180px;" /><font color="#f00000">*必填</font></td>
    <td width="15%" align="right"><b>公司首拼音：</b></td>
    <td width="35%"><input type="text" name="companySimple" id="companySimple" value="<?php echo $company_info['companySimple'];?>" style="width: 180px;"/><font color="#f00000">*必填</font></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>社团ID：</b></td>
    <td width="35%"><?php echo $company_info['unionId'];?></td>
    <td width="15%" align="right"><b>社团名称：</b></td>
    <td width="35%"><?php echo $company_info['unionName'];?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right"><b>公司状态：</b></td>
    <td><select id="unionRole" name="unionRole">
			<option value="-1">不操作</option>
			<option value="1" <?php if ($company_info['unionRole'] == CompanyUnion::UNION_ROLE_AUTH){?>selected="selected" <?php }?>>已认证社团</option>
			<option value="2" <?php if ($company_info['unionRole'] == CompanyUnion::UNION_ROLE_UNAUTH){?>selected="selected" <?php }?>>未认证社团</option>
			<!-- <option value="0" <?php if ($company_info['unionRole'] == CompanyUnion::UNION_ROLE_QUIT){?>selected="selected" <?php }?>>已退出社团</option> -->
		</select>
	</td>
    <td width="15%" align="right"><b>社团状态：</b></td>
	<?php if($company_info['unionStatus'] == UnionManage::UNION_STATUS_CLOSE){?>
	<td width="35%" style="color:#f00000">关闭</td>
	<?php }elseif($company_info['unionStatus'] == UnionManage::UNION_STATUS_UNAUTH_TMP){?>
	<td width="35%">非认证且临时</td>
	<?php }elseif($company_info['unionStatus'] == UnionManage::UNION_STATUS_UNAUTH_VALID){?>
	<td width="35%">非认证且生效</td>
	<?php }elseif($company_info['unionStatus'] == UnionManage::UNION_STATUS_AUTH_VALID){?>
	<td width="35%" style="color:#f00000">认证且生效</td>
    <?php }else{?>
    <td width="35%">未知</td>
	<?php }?>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="right" height="25"><b>创建时间：</b></td>
    <td><?php echo (!empty($company_info['createTime']) ? $company_info['createTime'] : '暂无');?></td>
    <td align="right" height="25"><b>更新时间：</b></td>
    <td><?php echo (!empty($company_info['updateTime']) ? $company_info['updateTime'] : '暂无');?></td>
  </tr>
  
  <tr bgcolor="#FFFFFF">
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