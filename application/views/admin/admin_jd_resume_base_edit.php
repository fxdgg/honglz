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
<form id="userform" name="userform" action='<?php echo APP_SITE_URL;?>/admin/jd_resume_edit?page=<?php echo $page;?>' method='post' onSubmit="return submitForm();" >
<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold" id="welcome_title_divid" style="margin-left:-200px;">简历基础信息更新<input type="hidden" id="id" name="entry[id]" value="<?php echo $resumeInfo['id'];?>" style="width:500px" /></div>
    </td>
  </tr>

  <tr bgcolor="#FFFFFF" >
    <td colspan='4' align="center">
        <input type="submit" name="editPosts" id="editPosts1" class="createBn" value="" />
    </td>
  </tr>

  	<tr><td height="30" >初次填写的时录入：</td></tr>
<tr>  <td>添加时间：<?php echo $resumeInfo['createTime']; ?> </td></tr>

	<!--<tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>简历状态</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <select id="isFindJob" name="entry[isFindJob]">
        <?php if (!empty($isFindJobConfig)): ?>
            <?php foreach ($isFindJobConfig as $key => $name):
                $selected = (isset($resumeInfo['isFindJob']) && $resumeInfo['isFindJob'] == $key) ? 'selected="selected"' : '';
                echo '<option value="' . $key . '" '.$selected.'>' . $name . '</option>';
            endforeach; ?>
        <?php endif; ?>
        </select>
	</td>
  </tr>-->
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b>姓名：</b></td>
        <td width="35%" colspan="3" class="inputClass">
            <input type="text" id="subordinate" name="entry[userName]" value="<?php echo $resumeInfo['userName'];?>" style="width:500px" />
        </td>
      </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>出生年份：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <!--<input type="text" id="userAge" name="entry[userAge]" onclick="WdatePicker({dateFmt:'yyyy'})" value="<?php echo (empty($resumeInfo['userAge']) OR $resumeInfo['userAge'] == '0000') ? '1985' : date('Y', strtotime($resumeInfo['userAge']));?>" style="width:500px" />-->
        <input type="number" id="userAge" name="entry[userAge]" value="<?php echo (empty($resumeInfo['userAge']) OR $resumeInfo['userAge'] == '0000') ? '1985' : date('Y', strtotime($resumeInfo['userAge']));?>" style="width:500px" />
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
    	                	$selected = (isset($resumeInfo['areaId']) && $resumeInfo['areaId'] == $item['areaId']) ? 'selected' : '';
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
    <td width="15%" align="right" height="25" class="tilte"><b>金数据ID：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="jsjId" name="entry[jsjId]" value="<?php echo $resumeInfo['jsjId'];?>" style="width:500px" />
    </td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>性别：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[userGender]" value="1" <?php if(isset($resumeInfo['userGender']) && $resumeInfo['userGender'] == 1) echo 'checked'; ?> />&nbsp;&nbsp;男&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[userGender]" value="2" <?php if(isset($resumeInfo['userGender']) && $resumeInfo['userGender'] == 2) echo 'checked'; ?> />&nbsp;&nbsp;女
    </td>
  </tr>

  <!--<tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>毕业院校：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="graduateSchool" name="entry[graduateSchool]" value="<?php echo $resumeInfo['graduateSchool'];?>" style="width:500px" />
    </td>
  </tr>-->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>学历：</b></td>
    <td width="35%" colspan="3" class="inputClass">
    	<select id="degree" name="entry[degree]">
    		<option value="大专" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '大专') echo 'selected'; ?>>大专</option>
        <option value="专升本" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '专升本') echo 'selected'; ?>>专升本</option>
    		<option value="本科" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '本科') echo 'selected'; ?>>本科</option>
    		<option value="硕士" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '硕士') echo 'selected'; ?>>硕士</option>
    		<option value="博士" <?php if(isset($resumeInfo['degree']) && $resumeInfo['degree'] == '博士') echo 'selected'; ?>>博士</option>
    	</select>
    </td>
  </tr>
 <!--<tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>工作过的企业：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="onceCompany" name="entry[onceCompany]" value="<?php echo $resumeInfo['onceCompany'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>下属人数：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="subordinate" name="entry[subordinate]" value="<?php echo $resumeInfo['subordinate'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>期望薪资：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="hopeSalary" name="entry[hopeSalary]" value="<?php echo $resumeInfo['hopeSalary'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>离职原因：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="leaveCause" name="entry[leaveCause]" value="<?php echo $resumeInfo['leaveCause'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>金数据简历地址：</b></td>
    <td width="35%" colspan="3" class="inputClass">
        <input type="text" id="resumeUrl" name="entry[resumeUrl]" value="<?php echo $resumeInfo['resumeUrl'];?>" style="width:500px" />
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>现在状态：</b></td>
    <td width="35%" colspan="3">
        <input type="radio" name="entry[nowState]" value="1" <?php if(isset($resumeInfo['nowState']) && $resumeInfo['nowState'] == 1) echo 'checked'; ?> />&nbsp;在职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="entry[nowState]" value="0" <?php if(isset($resumeInfo['nowState']) && $resumeInfo['nowState'] == 0) echo 'checked'; ?> />&nbsp;离职
    </td>
  </tr>-->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25" class="tilte"><b>职位分类：</b></td>
    <td width="35%" colspan="3" class="inputClass">
         <div class="search-inline mt10" style="height:auto">
    		<span style="margin-top:20px;line-height:40px">
            <select id="chosegame" name="entry[jobClassId]" data-placeholder="请选择" style="width:350px;" class="chzn-select game">
                <option value="0">---请选择---</option>
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
    		</span>
           </div>
    </td>
  </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">教育背景及自我介绍：</span></b></td>
        <td width="35%" colspan="3" class="inputClass">
            <textarea id="editor_id_4" name="entry[self_introduction]" style="width:300px;height:500px;"><?php echo $resumeInfo['self_introduction'];?></textarea>
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">工作经历：</span></b></td>
        <td width="35%" colspan="3" class="inputClass">
            <textarea id="editor_id_3" name="entry[work_experience]" style="width:300px;height:500px;"><?php echo $resumeInfo['work_experience'];?></textarea>
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td width="15%" align="right" height="25" class="tilte"><b><span id="content_spanid">项目经历：</span></b></td>
        <td width="35%" colspan="3" class="inputClass">
            <textarea id="editor_id_2" name="entry[project]" style="width:300px;height:500px;"><?php echo $resumeInfo['project'];?></textarea>
        </td>
      </tr>


  <tr bgcolor="#FFFFFF" >
    <td colspan='4' align="center">
        <input type="submit" name="editPosts" id="editPosts2" class="createBn" value="" />
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
