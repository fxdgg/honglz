<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
<!-- <script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script> -->
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
.inputClass{height:30px;padding-top:20px;position:relative}
.title{float:left;width:60px;}
.inputClass input{
	color:#010101;
	/*border:1px solid #d9d9d9;*/
	/*border-width:0 0 1px;*/
	width:500px;
	height:30px;
	/*position:absolute;*/
	right:5px;
	bottom:3px;
	font-size:18px;
	font-family:'微软雅黑',arial
}
.inputClass select{
	/*opacity:0;
	position:absolute;
	right:0;
	bottom:0;*/
	width:500px;
	height:30px;
	/*z-index:100*/
}
.inputClass radio{
	height:30px;
}
.createBn{width:280px;height:54px;background:url(/bootstrap/img/ScreenLockBut.png) no-repeat;border:none;margin-top:20px;cursor:pointer;}
</style>
<script charset="utf-8" src="<?php echo APP_SITE_DOMAIN;?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo APP_SITE_DOMAIN;?>/editor/lang/zh_CN.js"></script>

<style type="text/css">
select.chzn-select-self {
  height: 28px !important;
  min-height: 28px !important;
}
.chzn-container-multi .chzn-choices .search-choice{
    margin: 5px 0 3px 5px;
}
table tr.tr-title td{
    cursor: pointer;
}
select#chosepackage{
  font-size:12px;
}
.warning{color:red;}
</style>
<script>
    /*KindEditor.ready(function(K) {
        window.editor_1 = K.create('#editor_id_1');
        window.editor_2 = K.create('#editor_id_2');
    });*/

    function submitForm(){
        //post 提交
        document.userform.submit();
        return true;
    }
</script>
</head>

<body bgcolor="#DDEEF2">
<form id="userform" name="userform" action='<?php echo APP_SITE_URL;?>/admin/jd_supply_edit' method='post' onSubmit="return submitForm();" >
<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold" id="welcome_title_divid">对接信息更新<input type="hidden" id="id" name="entry[id]" value="<?php echo $supplyInfo['id'];?>" style="width:500px" /></div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>需求信息：<?php echo sprintf('<a href="/admin/jd_base_list?id=%s">%s（ID：%s）</a>', $supplyInfo['need_id'], mb_substr($supplyInfo['need_title'], 0, 50), $supplyInfo['need_id']);?></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!--<input type="text" readonly="true" id="need_id" name="entry[need_id]" value="<?php echo $supplyInfo['need_id'];?>" style="width:500px" /><span id="msg_need_id" class="warning"></span>-->
    </td>
  </tr>
<tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>需求方用户信息：<?php echo sprintf('%s（ID：%s）', $supplyInfo['asker_name'], $supplyInfo['asker_uid']);?></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!--<input type="text" readonly="true" id="asker_uid" name="entry[asker_uid]" value="<?php echo $supplyInfo['asker_uid'];?>" style="width:500px" /><span id="msg_asker_uid" class="warning"></span>-->
    </td>
</tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">可以提供的资源：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <textarea id="editor_id_1" name="entry[resource]" style="width:500px;height:500px;"><?php echo $supplyInfo['resource'];?></textarea><span id="msg_editor_id_1" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>联系方式：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="contact" name="entry[contact]" value="<?php echo $supplyInfo['contact'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>是否弹出提醒：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[is_alert]" value="0" <?php if(isset($supplyInfo['is_alert']) && $supplyInfo['is_alert'] == 0) echo 'checked'; ?> />&nbsp;否，暂不提醒
        <input type="radio" name="entry[is_alert]" value="1" <?php if(isset($supplyInfo['is_alert']) && $supplyInfo['is_alert'] == 1) echo 'checked'; ?> />&nbsp;是，需要提醒
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>金额（单位分）：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="money" name="entry[money]" value="<?php echo $supplyInfo['money'];?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" style="width:500px" />
    </td>
  </tr>
    <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>状态：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <select id="state" name="entry[state]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
                <option value='new' <?php echo $supplyInfo['state'] === 'new' ? 'selected' : '';?>>正常</option>
                <option value='delete' <?php echo $supplyInfo['state'] === 'delete' ? 'selected' : '';?>>删除</option>
            </select>
        </td>
    </tr>
  <tr bgcolor="#FFFFFF" >
    <td colspan='4' align="center">
        <input type="submit" name="editPosts" id="editPosts" class="createBn" value="" />
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