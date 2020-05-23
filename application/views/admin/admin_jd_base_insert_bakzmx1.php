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
    <td width="15%" align="right" height="25" class="tilte"><b>公司名称：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companyName" name="entry[companyName]" value="" style="width:500px" /><span id="msg_companyName" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>公司网址：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companySite" name="entry[companySite]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>接受简历的邮箱：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="email" name="entry[email]" value="hr@" /><span id="msg_email" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位分类：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="chosegame" name="entry[jobClassId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
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
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>地区：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="areaId" name="entry[areaId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
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
  <tr><td><br><br><br><br><br><br></td></tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作经验：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="workExperience" name="entry[workExperience]" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="2" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>月薪上限：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="monthlySalary" name="entry[monthlySalary]" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="6" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">岗位职责：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!-- <textarea id="editor_id_1" name="entry[describeContent]" style="width:300px;height:100px;"></textarea> -->
        <textarea id="editor_id_1" name="entry[describeContent]" style="width:500px;height:100px;"></textarea><span id="msg_editor_id_1" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">任职描述：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!-- <textarea id="editor_id_2" name="entry[demandContent]" style="width:300px;height:100px;"></textarea> -->
        <textarea id="editor_id_2" name="entry[demandContent]" style="width:500px;height:100px;"></textarea><span id="msg_editor_id_2" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>联系方式：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="contact" name="entry[contact]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>年假制度：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="annualVacation" name="entry[annualVacation]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>所在团队人数：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="teamNumber" name="entry[teamNumber]" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="3" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位程度：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="jobLevelId" name="entry[jobLevelId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
        	<option value='0'>---请选择---</option>
	        <?php 
	            if (is_array($jobLevelList) && !empty($jobLevelList))
	            {
	                foreach ($jobLevelList as $item)
	                {
	                    echo '<option value="'.$item['jobLevelId'].'" />'.$item['jobLevelName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>公司类型：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="companyTypeId" name="entry[companyTypeId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
        	<option value='0'>---请选择---</option>
	        <?php 
	            if (is_array($jobCompanyTypeList) && !empty($jobCompanyTypeList))
	            {
	                foreach ($jobCompanyTypeList as $item)
	                {
	                    echo '<option value="'.$item['companyTypeId'].'" />'.$item['companyTypeName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>行业类别：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="vocationTypeId" name="entry[vocationTypeId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
        	<option value='0'>---请选择---</option>
	        <?php 
	            if (is_array($jobVocationTypeList) && !empty($jobVocationTypeList))
	            {
	                foreach ($jobVocationTypeList as $item)
	                {
	                    echo '<option value="'.$item['vocationTypeId'].'" />'.$item['vocationTypeName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>融资阶段：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="financeStage" name="entry[financeStage]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作时长：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="workDuration" name="entry[workDuration]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>平均年薪：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="avgYearlySalary" name="entry[avgYearlySalary]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>员工保险：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="employeeInsurance" name="entry[employeeInsurance]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>加班状况：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="overtimeStatus" name="entry[overtimeStatus]" value="" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>能力特征词：</b></td>
    <td width="35%" colspan="3">
        <!-- <input type="checkbox" id="checkall" onclick="reverse_check('abilityFeature[]')" title="全选/不选" />全选/反选<br /> -->
        <?php 
            if (is_array($jobAbilityFeatureList) && !empty($jobAbilityFeatureList))
            {
                $key = 0;
                foreach ($jobAbilityFeatureList as $item)
                {
                    $skip = ($key > 0 && $key % 5 == 0) ? '<br />' : '&nbsp;&nbsp;';
                    echo '<input type="checkbox" id="abilityFeatureId_'.$key.'" name="entry[abilityFeature][]" value="'.$item['abilityFeatureId'].'" />&nbsp;'.$item['abilityFeatureName'].$skip;
                    ++$key;
                }
            }
        ?>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF" >
    <td colspan='4' align="center">
        <input type="submit" name="editPosts" id="editPosts" class="createBn" value="" />
    </td>
  </tr>
</table>
</form>
</body>
</html>