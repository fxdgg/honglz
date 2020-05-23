<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap-page.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
<!--<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script>-->
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
<script src="<?php echo JS_PATH;?>bootstrap.js"></script>
<script>
    KindEditor.ready(function(K) {
        window.editor_1 = K.create('#editor_id_1');
        window.editor_2 = K.create('#editor_id_2');
        window.editor_3 = K.create('#editor_id_3');
        window.editor_4 = K.create('#editor_id_4');
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
<form id="userform" name="userform" action='<?php echo APP_SITE_URL;?>/admin/jd_resume_insert' method='post' onSubmit="return submitForm();" >
    <table width="100%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
    <tr>
        <td bgcolor="#FFFFFF" colspan="4"></td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" height="31" colspan="4">
            <div align="center" class="black_14_bold" id="welcome_title_divid">简历基础信息录入</div>
        </td>
    </tr>
	<tr>
        <td height="30" >初次填写的事录入：</td>
    </tr>
    <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>简历来源：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="resumeSource" name="entry[resumeSource]" value="" style="width:500px" />
        </td>
    </tr>
    <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>金数据id：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="number" id="jsjId" name="entry[jsjId]" value="" style="width:500px" />
        </td>
    </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>出生年份：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <!--<input type="text" id="userAge" name="entry[userAge]" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" value="1985-01-01" style="width:500px" />-->
            <input type="number" id="userAge" name="entry[userAge]" value="1985" style="width:500px" />
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>性别：</b></td>
        <td width="35%" colspan="3">
            <input type="radio" name="entry[userGender]" value="1" checked />&nbsp;&nbsp;男&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="entry[userGender]" value="2" />&nbsp;&nbsp;女
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>毕业院校：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="graduateSchool" name="entry[graduateSchool]" value="" style="width:500px" />
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>学历：</b></td>
        <td width="35%" colspan="3" class="inputClass">
        	<select id="degree" name="entry[degree]">
        		<option value="大专">大专</option>
        		<option value="本科" selected>本科</option>
        		<option value="硕士">硕士</option>
        		<option value="博士">博士</option>
        	</select>
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>工作过的企业：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="onceCompany" name="entry[onceCompany]" value="" style="width:500px" />
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>下属人数：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="subordinate" name="entry[subordinate]" value="" style="width:500px" />
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>现在状态：</b></td>
        <td width="35%" colspan="3">
            <input type="radio" name="entry[nowState]" value="1" />&nbsp;在职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="entry[nowState]" value="0" checked />&nbsp;离职
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>地区：</b></td>
        <td width="35%" colspan="3" class="inputClass">
             <div class="search-inline mt10" style="height:auto">
        		<span style="margin-top:20px;line-height:40px">
                <select id="areaId" name="entry[areaId]" data-placeholder="请选择" style="width:350px;" class="chzn-select game">
                    <option value="0">---请选择---</option>
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
        <td width="15%" align="right" height="25" class="tilte"><b>金数据简历地址：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="resumeUrl" name="entry[resumeUrl]" value="https://jinshuju.net/f/" style="width:500px" /><font color="red">请补全链接地址</font>
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>所学专业：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="professional" name="entry[professional]" value="" style="width:500px" />
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>是否有管理经验：</b></td>
        <td width="35%" colspan="3">
            <input type="radio" name="entry[isManageExperience]" value="1" />&nbsp;是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="radio" name="entry[isManageExperience]" value="0" checked />&nbsp;否
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>职位分类：</b></td>
        <td width="35%" colspan="3" class="inputClass">
             <div class="search-inline mt10" style="height:auto">
        		<span style="margin-top:20px;line-height:40px">
                <select id="chosegame" name="entry[jobClassId]" data-placeholder="请选择职位" style="width:350px;" class="chzn-select game">
                    <option value="0">请选择职位</option>
                    <?php if (!empty($jobClassList)): ?>
                        <?php foreach ($jobClassList as $item): 
                            echo '<option value="' . $item['jobClassId'] . '">' . $item['jobClassName'] . '</option>';
                        endforeach; ?>
                    <?php endif; ?>
                </select>
        		</span>
               </div>
        </td>
      </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>能力特征词：</b></td>
    <td width="35%" colspan="3">
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
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>附属特征词：</b></td>
    <td width="35%" colspan="3">
        <?php 
            if (is_array($jobResumePertainFeatureList) && !empty($jobResumePertainFeatureList))
            {
                $key = 0;
                foreach ($jobResumePertainFeatureList as $item)
                {
                    $skip = ($key > 0 && $key % 5 == 0) ? '<br />' : '&nbsp;&nbsp;';
                    echo '<input type="checkbox" id="pertainFeatureId_'.$key.'" name="entry[pertainFeature][]" value="'.$item['pertainFeatureId'].'" />&nbsp;'.$item['pertainFeatureName'].$skip;
                    ++$key;
                }
            }
        ?>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">完整简历：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <textarea id="editor_id_1" name="entry[resumeInit]" style="width:300px;height:500px;"></textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">自我介绍：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <textarea id="editor_id_4" name="entry[self_introduction]" style="width:300px;height:500px;"></textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">工作经历：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <textarea id="editor_id_3" name="entry[work_experience]" style="width:300px;height:500px;"></textarea>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">项目经历：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
        <textarea id="editor_id_2" name="entry[project]" style="width:300px;height:500px;"></textarea>
    </td>
  </tr>

	<tr><td height="30" >再次录入时填写（获得联系方式）：</td></tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>姓名：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="userName" name="entry[userName]" value="" style="width:500px" /><span id="msg_userName" class="warning"></span>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>手机号：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="mobile" name="entry[mobile]" value="" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>邮箱：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="email" name="entry[email]" value="" />
    </td>
  </tr>
  <!-- <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作经验：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="workExperience" name="entry[workExperience]" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="2" style="width:500px" />
    </td>
  </tr> -->

  <tr><td height="30" >电话面试时填写：</td></tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>月薪上限：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="monthlySalary" name="entry[monthlySalary]" value="" style="width:500px" />如：3000-5000
    </td>
  </tr>
  <!-- <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>现任职于：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="nowCompany" name="entry[nowCompany]" value="" /><span id="msg_nowCompany" class="warning"></span>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>曾任职于：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="onceCompany" name="entry[onceCompany]" value="" style="width:500px" />
    </td>
  </tr> -->

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
	                    echo '<option value="'.$item['jobLevelId'].'" />'.$item['jobLevelName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr> -->
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
	                    echo '<option value="'.$item['companyTypeId'].'" />'.$item['companyTypeName'].'</option>';
	                }
	            }
	        ?>
        </select>
    </td>
  </tr> -->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">面试纪要：</span></b></td>
    <td width="35%" colspan="3" class="inputClass">
    	<textarea id="editor_id_2" name="entry[interviewRecord]" style="width:500px;height:100px;"></textarea>
    </td>
  </tr>
      <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>简历状态</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[isFindJob]" value="1" checked />&nbsp;运营-整理简历&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[isFindJob]" value="0"  />&nbsp;否
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