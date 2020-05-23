<html>
<head>
<!--<title>简历浏览_鸡蛋招聘_不一样的招聘快感</title>-->
<title>候选人简历</title>
<link rel="shortcut icon" href="<?php echo IMAGE_PATH;?>favicon.ico" />
<meta name="Keywords" content="<?php echo $keywords;?>" />
<meta name="Description" content="<?php echo $description;?>" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="utf-8" />
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<meta http-equiv="expires" content="Mon, 23 Jan 1978 20:52:30 GMT">
<meta name="robots" content="all" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<style>
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background: url(http://cdn.ledongli.cn/cdn-uploader/_1492437799790/20160325141908_4e3432.png) no-repeat center 0/cover fixed;
}
table {
	font-size: 14px;
	font-family: arial, "宋体";
}
td {
	word-break: break-word;
	line-height: 30px;
}
.blue {
	color: #256fb8;
	padding-left: 6px
}
.greenscore {
	color: #0b8e09;
}
.redscore {
	color: #ed0000;
}
.graybutton {
	color: #676767;
}
.font14 {
	font-size: 14px;
}
.font18 {
	font-size: 18px;
}
.font25 {
	font-size: 25px;
}
table.borderline th {
	border-collapse: collapse;
	border: solid #c7c7c7;
	border-width: 1px 1px 0 1px;
	text-indent: 5px;
	text-align: left;
	font-weight: normal;
}
table.borderline td {
	border-collapse: collapse;
	border: solid #c7c7c7;
	border-width: 1px 0 0 1px;
	text-indent: 5px;
}
table.none td {
	border-collapse: collapse;
	border: 0;
}
a.blue {
	color: #256fb8;
	text-decoration: underline;
}
a.boldblue {
	color: #256fb8;
	text-decoration: none;
	font-weight: bold;
}
a.gray {
	color: #676767;
	text-decoration: none;
}
.text_left {
	padding-left: 10px;
	font-size: 12px;
	color: 2B2B2B;
	text-decoration: none;
	font-family: Arial, Helvetica, sans-serif;
}
.cvtitle {
	padding-left: 6px;
	font-size: 15px;
	color: #2670B7;
	text-decoration: none;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
}
hr {
	border: 1px;
	color: #cacaca;
	width: 97%;
	background: #cacaca;
}
.top {
	font-size: 12px;
	color: 2B2B2B;
	text-decoration: none;
	font-family: Arial, Helvetica, sans-serif;
}
.text {
	font-size: 12px;
	color: 2B2B2B;
	text-decoration: none;
	font-family: Arial, Helvetica, sans-serif;
	text-align: left;
}
.bg {
	background-repeat: repeat-x;
}
.red {
	font-size: 12px;
	color: #ff0000;
	text-decoration: none;
	font-family: Arial, Helvetica, sans-serif;
}
.name {
	padding-left: 10px;
	font-size: 24px;
	color: #2B2B2B;
	text-decoration: none;
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
}
.table_set {
	margin: 0px auto;
	line-height: 20px;
	padding: 0 0 0 8px;
}

#box{
	width: 65%;
}

#box2{
	width: 95%;
}

@media screen and (max-width: 400px) {
	#box{
		width: 100%;
	}

	#box2{
		width: 100%;
	}
}
</style>
</head>
<body>
<table id="box" border="0" align="center" cellpadding="0" cellspacing="0" style="background:white;">
	<tr>
		<td style="height: 30px;background: rgb(82, 128, 25);"></td>
	</tr>
	<tr>
		<td style="height: 30px;"></td>
	</tr>
  <tr>
    <td><table id="box2" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top"><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" style="margin:3px auto 0px auto;padding:5px 0px 0px 5px;">
              <tr>
                <td align="left"></td>
              </tr>
            </table>
            <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="30" style="border-bottom:1px dashed rgb(82, 128, 25);"><span style="font-size:25px;line-height:30px;padding-left:6px;padding-top:10px; padding-bottom:20px;color: rgb(82, 128, 25);display: block;"> <b><?php echo $resumeTitle;?></b></td>
                <td align="right" style="border-bottom:1px dashed #88b4e0;"><span style="float:right;"></span></td>
              </tr>
              <tr>
                <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="26" colspan="4"><span class="blue">
                        <!--<b>鸡蛋点评：
                        	<?php
								if (!empty($resumeInfo['abilityFeature']))
								{
									$abilityFeatureArray = explode('|', $resumeInfo['abilityFeature']);
									foreach ($abilityFeatureArray as $abilityId)
									{
										echo isset($jobAbilityFeatureList[$abilityId]['abilityFeatureName']) ? $jobAbilityFeatureList[$abilityId]['abilityFeatureName'] . '&nbsp;&nbsp;' : '';
									}
								}
							?></b></span>-->
						</td>
                    </tr>
                  </table></td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10"></td>
              </tr>
            </table>
            <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td width="100%" colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto;line-height:20px;padding:0 0 0 8px;">
                    <tr>
	                    <!-- <td colspan="2"><span style="color:rgb(82, 128, 25);font-size:14px;"><b>基本信息</b></span></td> -->
		                <td align="left" colspan="2" valign="middle" class="cvtitle" style="color: rgb(82, 128, 25);">基本信息</td>
                    </tr>
                    <tr>
                      <td width="99">性别：</td>
                      <td width="230"><?php echo $resumeInfo['userGender'] == 1 ? '男' : '女';?></td>
                    </tr>
                    <tr>
                      <td width="99">年龄：</td>
                      <td width="230"><?php echo !empty($resumeInfo['userAge']) ? $resumeInfo['userAge'] : '暂无';?></td>
                    </tr>
                    <tr>
                      <td width="99">期望工作地：</td>
                      <td width="230"><?php echo isset($jobAreaList[$resumeInfo['areaId']]['areaName']) ? $jobAreaList[$resumeInfo['areaId']]['areaName'] : '暂无';?></td>
                    </tr>
                    <!--<tr>
                      <td width="99">毕业院校及专业：</td>
                      <td width="230"><?php echo !empty($resumeInfo['graduateSchool']) ? $resumeInfo['graduateSchool'] : '暂无';?></td>
                    </tr>
                    <tr>
														<td width="99">专业：</td>
														<td width="230"><?php echo !empty($resumeInfo['professional']) ? $resumeInfo['professional'] : '暂无';?></td>
													</tr>-->
                    <tr>
                      <td width="99">学历：</td>
                      <td width="230"><?php echo !empty($resumeInfo['degree']) ? $resumeInfo['degree'] : '暂无';?></td>
                    </tr>
                   <!-- <tr>
                      <td>职位：</td>
                      <td><?php echo !empty($userJobClassName) ? $userJobClassName : '暂无';?></td>
                    </tr>

                    <tr>
														<td>手机：</td>
														<td><?php echo !empty($resumeInfo['mobile']) ? $resumeInfo['mobile'] : '与候选人确定意向后，显示手机号码';?></td>
													</tr>


													<tr>
														<td>邮箱：</td>
														<td><?php echo !empty($resumeInfo['email']) ? $resumeInfo['email'] : '与候选人确定意向后，显示邮箱';?></td>
													</tr>-->

                  </table></td>
                <td width="43" align="center" style="background:url(cid:d00ea993-0cc2-45c4-99f8-47ec227aa541) repeat-y;"></td>
              </tr>
            </table>
            <hr size="1" noshade>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="15"></td>
              </tr>
            </table>
            <?php if (!empty($resumeInfo['self_introduction'])):?>
            <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td align="left" valign="middle" class="cvtitle" style="color: rgb(82, 128, 25);">教育背景及自我介绍</td>
              </tr>
              <tr>
                <td height="10" align="left" valign="middle"></td>
              </tr>
              <tr>
                <td align="left" valign="middle">
                	<?php echo !empty($resumeInfo['self_introduction']) ? $resumeInfo['self_introduction'] : '';?>
                </td>
              </tr>
              <tr>
                <td height="20" align="left" valign="middle"></td>
              </tr>
            </table>
            <?php endif;?>
            <?php if (!empty($resumeInfo['work_experience'])):?>
            <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td align="left" valign="middle" class="cvtitle" style="color: rgb(82, 128, 25);">工作经历</td>
              </tr>
              <tr>
                <td height="10" align="left" valign="middle"></td>
              </tr>
              <tr>
                <td align="left" valign="middle">
                	<?php echo !empty($resumeInfo['work_experience']) ? $resumeInfo['work_experience'] : '';?>
                </td>
              </tr>
              <tr>
                <td height="20" align="left" valign="middle"></td>
              </tr>
            </table>
            <?php endif;?>
            <?php if (!empty($resumeInfo['project'])):?>
            <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td align="left" valign="middle" class="cvtitle" style="color: rgb(82, 128, 25);">项目经历</td>
              </tr>
              <tr>
                <td height="10" align="left" valign="middle"></td>
              </tr>
              <tr>
                <td align="left" valign="middle">
                	<?php echo !empty($resumeInfo['project']) ? $resumeInfo['project'] : '';?>
                </td>
              </tr>
              <tr>
                <td height="20" align="left" valign="middle"></td>
              </tr>
            </table>
            <?php endif;?>
        </td>
        </tr>
      </table></td>
  </tr>
</table>
<script src="http://s4.cnzz.com/stat.php?id=1254156333&web_id=1254156333" language="JavaScript" style="display:none"></script>
</html>