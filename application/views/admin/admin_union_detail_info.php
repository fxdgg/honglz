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
<form action='<?php echo APP_SITE_URL;?>/admin/union_detial_info/<?php echo $union_info['unionId'];?>' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold">社团基本信息</div></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>社团ID：</b></td>
    <td width="35%" colspan="3"><?php echo $union_info['unionId'];?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>社团名称：</b></td>
    <td width="35%"><input type="text" name="unionName" id="unionName" value="<?php echo $union_info['unionName'];?>" style="width: 180px;" /><font color="#f00000">*必填</font></td>
    <td width="15%" align="right"><b>社团简称：</b></td>
    <td width="35%"><input type="text" name="unionNick" id="unionNick" value="<?php echo $union_info['unionNick'];?>" style="width: 180px;" /><font color="#f00000">*必填</font></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right"><b>社团状态：</b></td>
    <td><select id="unionStatus" name="unionStatus">
            <option value="-1">不操作</option>
            <option value="0" <?php if ($union_info['unionStatus'] == UnionManage::UNION_STATUS_CLOSE){?>selected="selected" <?php }?>>关闭</option>
            <option value="1" <?php if ($union_info['unionStatus'] == UnionManage::UNION_STATUS_UNAUTH_TMP){?>selected="selected" <?php }?>>非认证且临时</option>
            <option value="2" <?php if ($union_info['unionStatus'] == UnionManage::UNION_STATUS_UNAUTH_VALID){?>selected="selected" <?php }?>>非认证且生效</option>
            <option value="3" <?php if ($union_info['unionStatus'] == UnionManage::UNION_STATUS_AUTH_VALID){?>selected="selected" <?php }?>>认证且生效</option>
        </select>
    </td>
    <td align="right" height="25"><b>创建时间：</b></td>
    <td><?php echo (!empty($union_info['createTime']) ? $union_info['createTime'] : '暂无');?></td>
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