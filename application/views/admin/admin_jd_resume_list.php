<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap-page.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<style type="text/css">
.jin_button {
    width: 38px;
    height: 20px;
}

.top_button {
    width: 63px;
    height: 22px;
}

.over {
    background-color: #F0F0F0;
}

.out {
    background-color: #FFFFFF;
}
.chzn-container-multi .chzn-choices .search-choice{
    margin: 5px 0 3px 5px;
}
table tr.tr-title td{
    cursor: pointer;
}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
<!-- <script src="<?php echo JS_PATH;?>bootstrap.js"></script> -->
<script>

</script>
</head>
<body bgcolor="#DDEEF2">
<div class="info">
	<div class="infoTop">
		 <h1>简历基本信息列表</h1>
	</div>
</div>
<form method="get" class="form-inline">
     <div class="search-inline mt10" style="height:auto">
		<em><input id="jdDomain" type="hidden" value="<?php echo APP_SITE_URL;?>" /></em>
        <a href="<?php echo APP_SITE_URL;?>/admin/jd_resume_insert">添加简历基本信息</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<!--<label>选择职位：</label>
		<span style="margin-top:20px;line-height:40px">
        <select id="pdid" name="pdid" data-placeholder="请选择职位" style="width:180px;" class="chzn-select game">
            <option value="0">请选择职位</option>
            <?php if (!empty($jd_position_list)): ?>
                <?php foreach ($jd_position_list as $item):
                    $selected = (isset($pdid) && $item['pdid'] == $pdid) ? 'selected="selected"' : '';
                    echo '<option value="' . $item['pdid'] . '" '.$selected.'>' . $item['positionName'] . '</option>';
                endforeach; ?>
            <?php endif; ?>
        </select>
		</span>-->
		<label>简历状态：</label>
    	<select name="isFindJob" style="width:200px;">
			<option value="-1">不限</option>
            <?php if (!empty($isFindJobConfig)): ?>
                <?php foreach ($isFindJobConfig as $key => $name):
                    $selected = (isset($isFindJob) && $isFindJob == $key) ? 'selected="selected"' : '';
                    echo '<option value="' . $key . '" '.$selected.'>' . $name . '</option>';
                endforeach; ?>
            <?php endif; ?>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label>状态：</label>
    	<select name="state" style="width:100px;">
			<option value="-1">不限</option>
			<option value="new" <?php if (isset($state) && $state=='new'){?> selected <?php }?> selected = "selected" >正常</option>
			<option value="delete" <?php if (isset($state) && $state=='delete'){?> selected <?php }?> >已删除</option>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<input type="submit" value="查询" class="btn btn-primary">
    </div>
</form>

<table cellspacing="1" cellpadding="2" width="100%" class="table table-striped table-hover table-condensed  table-bordered" bgcolor="#BBDDE5">
    <thead>
    <tr height="22" bgcolor="#EEEEEE">
        <th width="2%" align="center"><b>操作</b></th>
        <th width="1%" height="25" align="center"><b>简历ID</b></th>
        <th width="1%" height="25" align="center"><b>金数据ID</b></th>
        <th width="3%" align="center"><b>职位类型</b></th>
        <th width="1%" height="25" align="center"><b>性别</b></th>
        <th width="1%" height="25" align="center"><b>年龄</b></th>
        <th width="3%" align="center"><b>地区</b></th>
		<th width="2%" height="25" align="center"><b>毕业院校</b></th>
		<th width="2%" height="25" align="center"><b>专业</b></th>
		<th width="3%" align="center"><b>标签</b></th>
        <th width="3%" align="center"><b>学历</b></th>
		<!-- <th width="2%" align="center"><b>月薪上限</b></th> -->
        <th width="1%" height="25" align="center"><b>姓名</b></th>
        <th width="3%" align="center"><b>手机</b></th>
        <th width="1%" height="25" align="center"><b>邮箱</b></th>
        <th width="3%" align="center"><b>当前状态</b></th>
         <th width="1%" height="25" align="center"><b>录入时间</b></th>
		<!-- <th width="3%" align="center"><b>职位程度</b></th> -->
        <!-- <th width="3%" align="center"><b>公司类型</b></th> -->
        <!--<th align="center"><b>面试纪要</b></th>-->
    </tr>
    </thead>
    <tbody>
    <?php if($jdResumeBaseList){foreach ($jdResumeBaseList as $data){?>
    <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
        <td align="center"><a target="_blank" href="<?php echo APP_SITE_URL;?>/r/s/<?php echo urlencode(BLH_Base62::encode($data['id']));?>">浏览</a>&nbsp;&nbsp;<a href="<?php echo APP_SITE_URL;?>/admin/jd_resume_edit?id=<?php echo $data['id'];?>&page=<?php echo $page;?>">编辑</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="delResume(<?php echo $data['id'];?>);">删除</a>&nbsp;&nbsp;<a href="<?php echo htmlspecialchars_decode($data['resumeSource']);?>" target="_blank">来源</a></td>
        <td align="center"><?php echo $data['id'];?></td>
        <td align="center"><?php echo $data['jsjId'];?></td>
        <td align="center"><?php echo (isset($jobClassList[$data['jobClassId']]['jobClassName']) ? $jobClassList[$data['jobClassId']]['jobClassName'] : '暂无');?></td>
        <!--<td align="center"><?php echo $data['userGender'] == 1 ? '男' : '女';?></td>-->
        <td align="center"><?php if ( $data['userGender'] == 1 ) echo'男'; else if ( $data['userGender'] == 2 ) echo'女';?></td>
		<td align="center"><?php echo $data['userAge'];?></td>
        <td align="center"><?php echo (isset($jobAreaList[$data['areaId']]['areaName']) ? $jobAreaList[$data['areaId']]['areaName'] : '暂无');?></td>
		<td align="center"><?php echo $data['graduateSchool'];?></td>
		<td align="center"><?php echo $data['professional'];?></td>
        <!-- <td align="center"><?php echo $data['monthlySalary'];?></td> -->
        <td align="center"><?php echo $data['abilityFeature'];?></td>
		<td align="center"><?php echo $data['degree'];?></td>
		<td align="center"><?php echo $data['userName'];?></td>
        <td align="center"><?php echo $data['mobile'];?></td>
        <td align="center"><?php echo $data['email'];?></td>
        <td align="center"><?php echo isset($nowStateMap[$data['nowState']]) ? $nowStateMap[$data['nowState']] : '未知';?></td>
        <td align="center"><?php echo $data['createTime'];?></td>
		<!-- <td align="center"><?php echo (isset($jobLevelList[$data['jobLevelId']]['jobLevelName']) ? $jobLevelList[$data['jobLevelId']]['jobLevelName'] : '暂无');?></td> -->
        <!-- <td align="center"><?php echo (isset($jobCompanyTypeList[$data['companyTypeId']]['companyTypeName']) ? $jobCompanyTypeList[$data['companyTypeId']]['companyTypeName'] : '暂无');?></td> -->
		<!--<td align="center"><?php echo $data['interviewRecord'] ;?></td>-->
        <!--<td align="center"><?php echo $data['isFindJob'] == 1 ? '是' : '否';?></td>-->
    </tr>
    <?php }}?>
    </tbody>
	<thead>
    <tr>
        <td bgcolor="#FFFFFF" height="25" colspan="14">
            <div align="center"><?php echo $pageShow;?></div>
        </td>
    </tr>
	</thead>
</table>
<script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    //$(".chzn-select").chosen();
	function delResume(id) {
		 if( confirm('确定要删除吗') ) {
				var url = "<?php echo APP_SITE_URL?>/admin/jd_resume_del/"+id;
				$.ajax({
	                type: "post",
	                url:url,
					dataType:"json",
	                success: function(msg)
	                {
	                	if (msg.code == 0)
	                	{
						    $("#tr_"+id).hide();
	                	}
	                    alert(msg.data);
	                }
	            });
		}
	}
</script>
</body>
</html>