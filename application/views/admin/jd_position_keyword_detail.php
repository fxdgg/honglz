<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
<script src="<?php echo JS_PATH;?>bootstrap.js"></script>
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
<form action='<?php echo APP_SITE_URL;?>/admin/jd_position_keyword_detail/<?php echo $type;?>' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="2">
        <div align="center" class="black_14_bold">关键词详情-<?php echo $keyword_type;?>-<?php echo $actionName;?></div></td>
  </tr>
  <?php if (isset($keywordinfo['kid']) && $keywordinfo['kid'] > 0):?>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>关键词ID：</b></td>
    <td width="55%"><?php echo isset($keywordinfo['kid'])?$keywordinfo['kid']:0;?><input type="hidden" name="kid" id="kid" value="<?php echo isset($keywordinfo['kid'])?$keywordinfo['kid']:0;?>" style="width: 180px;" /></td>
  </tr>
  <?php endif;?>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right"><b>职位名称：</b></td>
    <td width="55%">
        <select id="pdid" name="pdid" data-placeholder="请选择职位" style="width:180px;" class="chzn-select game">
            <option value="0">请选择职位</option>
            <?php if (!empty($jd_position_list)): ?>
                <?php foreach ($jd_position_list as $item): 
                    $selected = (isset($keywordinfo['pdid']) && $item['pdid'] == $keywordinfo['pdid']) ? 'selected="selected"' : '';
                    echo '<option value="' . $item['pdid'] . '" '.$selected.'>' . $item['positionName'] . '</option>';
                endforeach; ?>
            <?php endif; ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>等级：</b></td>
    <td width="55%">
        <select id="level" name="level" style="width:180px;">
            <option value="1" <?php if (isset($keywordinfo['level']) && $keywordinfo['level'] == '1'){?>selected="selected" <?php }?>>初级</option>
            <option value="2" <?php if (isset($keywordinfo['level']) && $keywordinfo['level'] == '2'){?>selected="selected" <?php }?>>中级</option>
            <option value="3" <?php if (isset($keywordinfo['level']) && $keywordinfo['level'] == '3'){?>selected="selected" <?php }?>>高级</option>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right"><b>关键词：</b></td>
    <td width="55%"><input type="text" name="keyword" id="keyword" value="<?php echo isset($keywordinfo['keyword'])?$keywordinfo['keyword']:'';?>" style="width: 180px;" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right"><b>排序：</b></td>
    <td width="55%"><input type="text" name="sortId" id="sortId" value="<?php echo isset($keywordinfo['sortId'])?$keywordinfo['sortId']:'';?>" style="width: 180px;" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>状态：</b></td>
    <td width="55%">
        <select id="state" name="state">
            <!--<option value="0">不操作</option>-->
            <option value="new" <?php if (isset($keywordinfo['state']) && $keywordinfo['state'] == 'new'){?>selected="selected" <?php }?>>显示</option>
            <option value="delete" <?php if (isset($keywordinfo['state']) && $keywordinfo['state'] == 'delete'){?>selected="selected" <?php }?>>隐藏</option>
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

<script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>
</body>
</html>