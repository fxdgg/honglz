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

    /*function submitForm(){
        //post 提交
        var companyName = $("#companyName").val();
        var editor_id_1 = $("#editor_id_1").val();
        var editor_id_2 = $("#editor_id_2").val();
        var email = $("#email").val();
        if(companyName == '')
        {
            var field = 'companyName';
            document.getElementById('msg_'+field).innerHTML = "请填写公司名称";
            var objName = eval("document.all."+field);
            objName.focus();
            return false;
        }
        document.getElementById('msg_companyName').innerHTML = "";
        if(editor_id_1 == '')
        {
            var field = 'editor_id_1';
            document.getElementById('msg_'+field).innerHTML = "请填写岗位职责";
            var objName = eval("document.all."+field);
            objName.focus();
            return false;
        }
        document.getElementById('msg_editor_id_1').innerHTML = "";
        if(editor_id_2 == '')
        {
            var field = 'editor_id_2';
            document.getElementById('msg_'+field).innerHTML = "请填写任职描述";
            var objName = eval("document.all."+field);
            objName.focus();
            return false;
        }
        document.getElementById('msg_editor_id_2').innerHTML = "";
        if(email == '')
        {
            var field = 'email';
            document.getElementById('msg_'+field).innerHTML = "请填写邮箱地址";
            var objName = eval("document.all."+field);
            objName.focus();
            return false;
        }
        document.getElementById('msg_email').innerHTML = "";
        document.userform.submit();
        return true;
    }*/
</script>
</head>

<body bgcolor="#DDEEF2">
<form id="userform" name="userform" action='<?php echo APP_SITE_URL;?>/admin/jd_base_insert' method='post' onSubmit="return submitForm();" >
<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold" id="welcome_title_divid">JD基础信息录入</div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位及要求：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companyName" name="entry[companyName]" value="" style="width:500px" /><span id="msg_companyName" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>接受简历的邮箱：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="email" name="entry[email]" value="hr@111.com" /><span id="msg_email" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位分类：</b></td>
    <td width="35%" colspan="3" class="inputClass">
         <div class="search-inline mt10" style="height:auto">
            <span style="margin-top:20px;line-height:40px">
            <select id="chosegame" name="entry[jobClassId]" style="width:500px;" data-placeholder="请选择" class="chzn-select">
                <option value='0'>---请选择---</option>
                <?php
                    if (is_array($jobClassList) && !empty($jobClassList))
                    {
                        foreach ($jobClassList as $item)
                        {
                            echo '<option value="'.$item['jobClassId'].'">'.$item['jobClassName'].'</option>';
                        }
                    }
                ?>
            </select>
            </span>
        </div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>地区：</b></td>
    <td width="35%" colspan="3" class="inputClass">
         <div class="search-inline mt10" style="height:auto">
            <span style="margin-top:20px;line-height:40px">
            <select id="areaId" name="entry[areaId]" style="width:500px;" data-placeholder="请选择" class="chzn-select">
                <option value='0'>---请选择---</option>
                <?php
                    if (is_array($jobAreaList) && !empty($jobAreaList))
                    {
                        foreach ($jobAreaList as $item)
                        {
                            echo '<option value="'.$item['areaId'].'" />'.$item['areaName'].'</option>';
                        }
                    }
                ?>
            </select>
            </span>
        </div>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">外部介绍信息：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
    <textarea id="describeContent" name="entry[describeContent]" style="width:500px;height:100px"></textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">内部介绍信息：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
    <textarea id="demandContent" name="entry[demandContent]" style="width:500px;height:100px"></textarea>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>拉勾主页：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companySite" name="entry[companySite]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>boss主页：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="jdUrl" name="entry[jdUrl]" value="" style="width:500px" />
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位程度：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[jobLevelId]" value="0" />&nbsp;高级别
        <input type="radio" name="entry[jobLevelId]" value="1" checked />&nbsp;资深&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[jobLevelId]" value="2" />&nbsp;执行层
    </td>
  </tr>


  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>是否推送邮件：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[jdPushStatus]" value="0" />&nbsp;否，暂停发送
        <input type="radio" name="entry[jdPushStatus]" value="1" checked />&nbsp;是，营销中&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[jdPushStatus]" value="2" />&nbsp;是，推荐中
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