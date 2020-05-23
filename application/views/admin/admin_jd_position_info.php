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
<form action='<?php echo APP_SITE_URL;?>/admin/jd_position_detail_info' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="2">
        <div align="center" class="black_14_bold">职位详情-<?php echo $actionName;?></div></td>
  </tr>
  <?php if (isset($jdinfo['pdid']) && $jdinfo['pdid'] > 0):?>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>职位ID：</b></td>
    <td width="35%"><?php echo isset($jdinfo['pdid'])?$jdinfo['pdid']:0;?><input type="hidden" name="pdid" id="pdid" value="<?php echo isset($jdinfo['pdid'])?$jdinfo['pdid']:0;?>" style="width: 180px;" /></td>
  </tr>
  <?php endif;?>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right"><b>职位名称：</b></td>
    <td width="35%"><input type="text" name="positionName" id="positionName" value="<?php echo isset($jdinfo['positionName'])?$jdinfo['positionName']:'';?>" style="width: 180px;" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right"><b>职位排序：</b></td>
    <td width="35%"><input type="text" name="sortId" id="sortId" value="<?php echo isset($jdinfo['sortId'])?$jdinfo['sortId']:'';?>" style="width: 180px;" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>状态：</b></td>
    <td width="35%">
        <select id="state" name="state">
            <!--<option value="0">不操作</option>-->
            <option value="new" <?php if (isset($jdinfo['state']) && $jdinfo['state'] == 'new'){?>selected="selected" <?php }?>>显示</option>
            <option value="delete" <?php if (isset($jdinfo['state']) && $jdinfo['state'] == 'delete'){?>selected="selected" <?php }?>>隐藏</option>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan='2' align="center">
        <input type="submit" name="editUser" id="editUser" class="btn btn-primary" width="41" height="21" value="提交"/>
    </td>
  </tr>
</table>
</form>
</body>
</html>