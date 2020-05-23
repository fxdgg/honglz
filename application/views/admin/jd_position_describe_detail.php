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
<form action='<?php echo APP_SITE_URL;?>/admin/jd_position_describe_detail/<?php echo $id;?>/<?php echo $kid;?>/<?php echo $pdid;?>' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="2">
        <div align="center" class="black_14_bold">职位描述详情-<?php echo $actionName;?></div></td>
  </tr>
  <?php if (isset($info['id']) && $info['id'] > 0):?>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>描述ID：</b></td>
    <td width="55%"><?php echo isset($info['id'])?$info['id']:0;?><input type="hidden" name="id" id="id" value="<?php echo isset($info['id'])?$info['id']:0;?>" style="width: 180px;" /></td>
  </tr>
  <?php endif;?>
  <?php if (isset($info['id']) && $info['id'] > 0):?>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>关键词ID：</b></td>
    <td width="55%"><?php echo isset($info['kid'])?$info['kid']:0;?></td>
  </tr>
  <?php endif;?>
  <?php if (isset($info['id']) && $info['id'] > 0):?>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>等级：</b></td>
    <td width="55%">
        <?php 
            switch ($info['level'])
            {
                case 1:
                    echo '低级';
                    break;
                case 2:
                    echo '中级';
                    break;
                case 3:
                    echo '高级';
                    break;
                default:
                    echo '未知';
                    break;
            }
        ?>
    </td>
  </tr>
  <?php endif;?>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right"><b>请选择关键词：</b></td>
    <td width="55%">
        <select id="kid" name="kid" data-placeholder="请选择关键词" style="width:260px;" class="chzn-select game">
            <?php if (!empty($jd_keyword_list)): ?>
                <?php foreach ($jd_keyword_list as $item): 
                    $selected = (isset($kid) && $item['kid'] == $kid) ? 'selected="selected"' : '';
                    echo '<option value="' . $item['kid'] . '" '.$selected.'>' . $item['positionName'] . '-' . $item['keyword'] . '</option>';
                endforeach; ?>
            <?php endif; ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right"><b>职位描述：</b></td>
    <td width="55%">
    	<textarea id="content" name="content" style="width:700px;height:300px;"><?php echo isset($info['content'])?$info['content']:'';?></textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right"><b>排序：</b></td>
    <td width="55%"><input type="text" name="sortId" id="sortId" value="<?php echo isset($info['sortId'])?$info['sortId']:'';?>" style="width: 180px;" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="5%" align="right" height="25"><b>状态：</b></td>
    <td width="55%">
        <select id="state" name="state">
            <!--<option value="0">不操作</option>-->
            <option value="new" <?php if (isset($info['state']) && $info['state'] == 'new'){?>selected="selected" <?php }?>>显示</option>
            <option value="delete" <?php if (isset($info['state']) && $info['state'] == 'delete'){?>selected="selected" <?php }?>>隐藏</option>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan='2' align="center">
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
    <script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(".chzn-select").chosen();
    </script>
</body>
</html>