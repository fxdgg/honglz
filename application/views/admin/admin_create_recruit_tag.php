<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script>
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
<form action='<?php echo APP_SITE_URL;?>/admin/create_recruit_tag' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="2">
        <div align="center" class="black_14_bold">创建招聘分类</div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>分类类型：</b></td>
    <td width="35%" colspan="3">
    <?php
          $category_str = '';
          if (!empty($category_type))
          {
              $category_str .= '<select id="type" name="type" style="width:120px;">';
              foreach ($category_type as $category_type => $category_name)
              {
                  $category_str .= '<option value="'.$category_type.'">'.$category_name.'</option>';
              }
              $category_str .= '</select>';
          }
          echo $category_str;
    ?>
    </td>
  </tr>
  <tr id="group_div_id_1" bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>行业分类：</b></td>
    <td width="35%">
        <input type="text" name="cname" id="cname" value="" style="width: 180px; height: 20px;" />
    </td>
  </tr>
   
  <tr bgcolor="#FFFFFF">
    <td colspan='4' align="center">
        <input type="submit" name="editPosts" id="editPosts" width="41" height="21" value="提交"/>
    </td>
  </tr>
</table>

<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td align="right" height="25">&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>