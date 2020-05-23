<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
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
    KindEditor.ready(function(K) {
        window.editor_1 = K.create('#editor_id_1');
    });

    function submitForm(){
        //post 提交
        var userName = $("#userName").val();
        var nowCompany = $("#nowCompany").val();
        /*if(userName == '')
        {
            var field = 'userName';
            document.getElementById('msg_'+field).innerHTML = "请填写名称";
        	var objName = eval("document.all."+field);
            objName.focus();
            return false;
        }
        document.getElementById('msg_userName').innerHTML = "";
        if(nowCompany == '')
        {
            var field = 'nowCompany';
            document.getElementById('msg_'+field).innerHTML = "请填写现任职的公司";
        	var objName = eval("document.all."+field);
            objName.focus();
            return false;
        }
        document.getElementById('msg_nowCompany').innerHTML = "";*/
        document.userform.submit();
        return true;
    }
</script>
</head>

<body bgcolor="#DDEEF2">
<form id="userform" name="userform" action='<?php echo APP_SITE_URL;?>/admin/jd_resume_edit' method='post' onSubmit="return submitForm();" >
<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold" id="welcome_title_divid">简历基础信息更新<input type="hidden" id="id" name="entry[id]" value="<?php echo $resumeInfo['id'];?>" style="width:500px" /></div>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>姓名：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="userName" name="entry[userName]" value="<?php echo $resumeInfo['userName'];?>" style="width:500px" /><span id="msg_userName" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>性别：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[userGender]" value="1" <?php if(isset($resumeInfo['userGender']) && $resumeInfo['userGender'] == 1) echo 'checked'; ?> />&nbsp;&nbsp;男&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[userGender]" value="2" <?php if(isset($resumeInfo['userGender']) && $resumeInfo['userGender'] == 2) echo 'checked'; ?> />&nbsp;&nbsp;女
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>出生年份：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="userAge" name="entry[userAge]" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" value="<?php echo (empty($resumeInfo['userAge']) OR $resumeInfo['userAge'] == '0000-00-00 00:00:00') ? '1985-01-01' : date('Y-m-d', strtotime($resumeInfo['userAge']));?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>毕业院校：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="graduateSchool" name="entry[graduateSchool]" value="<?php echo $resumeInfo['graduateSchool'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>所学专业：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="professional" name="entry[professional]" value="<?php echo $resumeInfo['professional'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>学历：</b></td>
    <td width="35%" colspan="3" class="inputClass">
    	<select id="degree" name="entry[degree]">
    		<option value="大专" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '大专') echo 'selected'; ?>>大专</option>
    		<option value="本科" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '本科') echo 'selected'; ?>>本科</option>
    		<option value="硕士" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '硕士') echo 'selected'; ?>>硕士</option>
    		<option value="博士" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '博士') echo 'selected'; ?>>博士</option>
    	</select>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>手机号：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="mobile" name="entry[mobile]" value="<?php echo !empty($resumeInfo['mobile']) ? $resumeInfo['mobile'] : '';?>"  onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="11" style="width:500px"/>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>邮箱：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="email" name="entry[email]" value="<?php echo $resumeInfo['email'];?>" />
    </td>
  </tr>
  <!-- <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作经验：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="workExperience" name="entry[workExperience]" value="<?php echo !empty($resumeInfo['workExperience']) ? $resumeInfo['workExperience'] : '';?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="2" style="width:500px" />
    </td>
  </tr> -->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>月薪上限：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="monthlySalary" name="entry[monthlySalary]" value="<?php echo !empty($resumeInfo['monthlySalary']) ? $resumeInfo['monthlySalary'] : '';?>" style="width:500px" />如：3000-5000
    </td>
  </tr>
  <!-- <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>现任职于：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="nowCompany" name="entry[nowCompany]" value="<?php echo $resumeInfo['nowCompany'];?>" /><span id="msg_nowCompany" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>曾任职于：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="onceCompany" name="entry[onceCompany]" value="<?php echo $resumeInfo['onceCompany'];?>" style="width:500px" />
    </td>
  </tr> -->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>是否有管理经验：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[isManageExperience]" value="1" <?php if(isset($resumeInfo['isManageExperience']) && $resumeInfo['isManageExperience'] == 1) echo 'checked'; ?> />&nbsp;是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[isManageExperience]" value="0" <?php if(isset($resumeInfo['isManageExperience']) && $resumeInfo['isManageExperience'] == 0) echo 'checked'; ?> />&nbsp;否
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>现在状态：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[nowState]" value="1" <?php if(isset($resumeInfo['nowState']) && $resumeInfo['nowState'] == 1) echo 'checked'; ?> />&nbsp;在职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[nowState]" value="0" <?php if(isset($resumeInfo['nowState']) && $resumeInfo['nowState'] == 0) echo 'checked'; ?> />&nbsp;离职
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>简历来源：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="resumeSource" name="entry[resumeSource]" value="<?php echo $resumeInfo['resumeSource'];?>" style="width:500px" />
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
	                	$selected = (isset($resumeInfo['jobClassId']) && $resumeInfo['jobClassId'] == $item['jobClassId']) ? 'selected' : '';
	                    echo '<option value="'.$item['jobClassId'].'" '.$selected.' >'.$item['jobClassName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <!-- <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位程度：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="jobLevelId" name="entry[jobLevelId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
        	<option value='0'>---请选择---</option>
	        <?php 
	            if (is_array($jobLevelList) && !empty($jobLevelList))
	            {
	                foreach ($jobLevelList as $item)
	                {
	                	$selected = (isset($resumeInfo['jobLevelId']) && $resumeInfo['jobLevelId'] == $item['jobLevelId']) ? 'selected' : '';
	                    echo '<option value="'.$item['jobLevelId'].'" '.$selected.' />'.$item['jobLevelName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr> -->
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
	                	$selected = (isset($resumeInfo['areaId']) && $resumeInfo['areaId'] == $item['areaId']) ? 'selected' : '';
	                    echo '<option value="'.$item['areaId'].'" '.$selected.' />'.$item['areaName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr>
  <!-- <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>公司类型：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="companyTypeId" name="entry[companyTypeId]" style="width:500px;" data-placeholder="请选择" class="chzn-select-self">
        	<option value='0'>---请选择---</option>
	        <?php 
	            if (is_array($jobCompanyTypeList) && !empty($jobCompanyTypeList))
	            {
	                foreach ($jobCompanyTypeList as $item)
	                {
	                	$selected = (isset($resumeInfo['companyTypeId']) && $resumeInfo['companyTypeId'] == $item['companyTypeId']) ? 'selected' : '';
	                    echo '<option value="'.$item['companyTypeId'].'" '.$selected.' />'.$item['companyTypeName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr> -->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>是否找工作：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[isFindJob]" value="1" <?php if(isset($resumeInfo['isFindJob']) && $resumeInfo['isFindJob'] == 1) echo 'checked'; ?> />&nbsp;是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[isFindJob]" value="0" <?php if(isset($resumeInfo['isFindJob']) && $resumeInfo['isFindJob'] == 0) echo 'checked'; ?> />&nbsp;否
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>能力特征词：</b></td>
    <td width="35%" colspan="3">
        <?php 
            if (is_array($jobAbilityFeatureList) && !empty($jobAbilityFeatureList))
            {
            	$abilityFeatureArray = array();
            	if (!empty($resumeInfo['abilityFeature']))
            	{
            		$abilityFeatureArray = explode('|', $resumeInfo['abilityFeature']);
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
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>附属特征词：</b></td>
    <td width="35%" colspan="3">
        <?php 
            if (is_array($jobResumePertainFeatureList) && !empty($jobResumePertainFeatureList))
            {
            	$pertainFeatureArray = array();
            	if (!empty($resumeInfo['pertainFeature']))
            	{
            		$pertainFeatureArray = explode('|', ($resumeInfo['pertainFeature']));
            	}
                $key = 0;
                foreach ($jobResumePertainFeatureList as $item)
                {
                    $skip = ($key > 0 && $key % 5 == 0) ? '<br />' : '&nbsp;&nbsp;';
                    $checked = (!empty($pertainFeatureArray) && in_array($item['pertainFeatureId'], $pertainFeatureArray)) ? 'checked' : '';
                    echo '<input type="checkbox" id="pertainFeatureId_'.$key.'" name="entry[pertainFeature][]" value="'.$item['pertainFeatureId'].'" '.$checked.' />&nbsp;'.$item['pertainFeatureName'].$skip;
                    ++$key;
                }
            }
        ?>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">完整简历：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <textarea id="editor_id_1" name="entry[resumeInit]" style="width:300px;height:500px;"><?php echo $resumeInfo['resumeInit'];?></textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">面试纪要：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
    	<textarea id="editor_id_2" name="entry[interviewRecord]" style="width:500px;height:100px;"><?php echo $resumeInfo['interviewRecord'];?></textarea>
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