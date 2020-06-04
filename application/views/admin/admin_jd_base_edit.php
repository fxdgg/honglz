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
        /*if(editor_id_1 == '')
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
        document.getElementById('msg_email').innerHTML = "";*/
        document.userform.submit();
        return true;
    }
</script>
</head>

<body bgcolor="#DDEEF2">
<form id="userform" name="userform" action='<?php echo APP_SITE_URL;?>/admin/jd_base_edit' method='post' onSubmit="return submitForm();" >
<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold" id="welcome_title_divid">供需信息更新<input type="hidden" id="id" name="entry[id]" value="<?php echo $jdInfo['id'];?>" style="width:500px" /></div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位及要求：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companyName" name="entry[companyName]" value="<?php echo $jdInfo['companyName'];?>" style="width:500px" /><span id="msg_companyName" class="warning"></span>
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
    	                	$selected = (isset($jdInfo['areaId']) && $jdInfo['areaId'] == $item['areaId']) ? 'selected' : '';
    	                    echo '<option value="'.$item['areaId'].'" '.$selected.' />'.$item['areaName'].'</option>';
    	                }
    	            }
    	        ?>
            </select>
    		</span>
        </div>
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
    	                	$selected = (isset($jdInfo['jobClassId']) && $jdInfo['jobClassId'] == $item['jobClassId']) ? 'selected' : '';
    	                    echo '<option value="'.$item['jobClassId'].'" '.$selected.' >'.$item['jobClassName'].'</option>';
    	                }
    	            }
    	        ?>
            </select>
    		</span>
        </div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">外部介绍信息（题主说）：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!-- <textarea id="editor_id_1" name="entry[describeContent]" style="width:300px;height:100px;"></textarea> -->
        <textarea id="editor_id_1" name="entry[describeContent]" style="width:500px;height:500px;"><?php echo $jdInfo['describeContent'];?></textarea><span id="msg_editor_id_1" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">内部介绍信息（小编说）：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!-- <textarea id="editor_id_2" name="entry[demandContent]" style="width:300px;height:100px;"></textarea> -->
        <textarea id="editor_id_2" name="entry[demandContent]" style="width:500px;height:500px;"><?php echo $jdInfo['demandContent'];?></textarea><span id="msg_editor_id_2" class="warning"></span>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>拉勾主页：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companySite" name="entry[companySite]" value="<?php echo $jdInfo['companySite'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>boss主页：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="companySite" name="entry[jdUrl]" value="<?php echo $jdInfo['jdUrl'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>接受简历的邮箱：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="email" name="entry[email]" value="<?php echo $jdInfo['email'];?>" /><span id="msg_email" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>是否推送邮件：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[jdPushStatus]" value="0" <?php if(isset($jdInfo['jdPushStatus']) && $jdInfo['jdPushStatus'] == 0) echo 'checked'; ?> />&nbsp;否，暂停发送
        <input type="radio" name="entry[jdPushStatus]" value="1" <?php if(isset($jdInfo['jdPushStatus']) && $jdInfo['jdPushStatus'] == 1) echo 'checked'; ?> />&nbsp;是，营销中&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[jdPushStatus]" value="2" <?php if(isset($jdInfo['jdPushStatus']) && $jdInfo['jdPushStatus'] == 2) echo 'checked'; ?> />&nbsp;是，推荐中&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    </td>
  </tr>
  <tr><td><br><br><br><br><br><br></td></tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作经验：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="workExperience" name="entry[workExperience]" value="<?php echo $jdInfo['workExperience'];?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="2" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>月薪上限：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="monthlySalary" name="entry[monthlySalary]" value="<?php echo $jdInfo['monthlySalary'];?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="6" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>联系方式：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="contact" name="entry[contact]" value="<?php echo $jdInfo['contact'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>年假制度：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="annualVacation" name="entry[annualVacation]" value="<?php echo $jdInfo['annualVacation'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>所在团队人数：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="teamNumber" name="entry[teamNumber]" value="<?php echo $jdInfo['teamNumber'];?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="3" style="width:500px" />
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
	                	$selected = (isset($jdInfo['jobLevelId']) && $jdInfo['jobLevelId'] == $item['jobLevelId']) ? 'selected' : '';
	                    echo '<option value="'.$item['jobLevelId'].'" '.$selected.' />'.$item['jobLevelName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>公司类型（供求行业）：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="companyTypeId" name="entry[companyTypeId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
        	<option value='0'>---请选择---</option>
	        <?php
	            if (is_array($jobCompanyTypeList) && !empty($jobCompanyTypeList))
	            {
	                foreach ($jobCompanyTypeList as $item)
	                {
	                	$selected = (isset($jdInfo['companyTypeId']) && $jdInfo['companyTypeId'] == $item['companyTypeId']) ? 'selected' : '';
	                    echo '<option value="'.$item['companyTypeId'].'" '.$selected.' />'.$item['companyTypeName'].'</option>';
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
	                	$selected = (isset($jdInfo['vocationTypeId']) && $jdInfo['vocationTypeId'] == $item['vocationTypeId']) ? 'selected' : '';
	                    echo '<option value="'.$item['vocationTypeId'].'" '.$selected.' />'.$item['vocationTypeName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>融资阶段：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="financeStage" name="entry[financeStage]" value="<?php echo $jdInfo['financeStage'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作时长：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="workDuration" name="entry[workDuration]" value="<?php echo $jdInfo['workDuration'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>平均年薪：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="avgYearlySalary" name="entry[avgYearlySalary]" value="<?php echo $jdInfo['avgYearlySalary'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>员工保险：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="employeeInsurance" name="entry[employeeInsurance]" value="<?php echo $jdInfo['employeeInsurance'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>加班状况：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="overtimeStatus" name="entry[overtimeStatus]" value="<?php echo $jdInfo['overtimeStatus'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>能力特征词（标签）：</b></td>
    <td width="35%" colspan="3">
        <!-- <input type="checkbox" id="checkall" onclick="reverse_check('abilityFeature[]')" title="全选/不选" />全选/反选<br /> -->
        <?php
            if (is_array($jobAbilityFeatureList) && !empty($jobAbilityFeatureList))
            {
            	$abilityFeatureArray = array();
            	if (!empty($jdInfo['abilityFeature']))
            	{
            		$abilityFeatureArray = explode('|', $jdInfo['abilityFeature']);
            	}
                $key = 0;
                foreach ($jobAbilityFeatureList as $item)
                {
                    $skip = ($key > 0 && $key % 5 == 0) ? '<br />' : '&nbsp;&nbsp;';
                    $checked = (!empty($abilityFeatureArray) && in_array($item['abilityFeatureId'], $abilityFeatureArray)) ? 'checked' : '';
                    echo '<input type="checkbox" id="abilityFeatureId_'.$key.'" name="entry[abilityFeature][]" value="'.$item['abilityFeatureId'].'" '.$checked.' />&nbsp;'.$item['abilityFeatureName'].$skip;
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
<script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>
</body>
</html>