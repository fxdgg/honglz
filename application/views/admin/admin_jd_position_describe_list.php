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
<script src="<?php echo JS_PATH;?>bootstrap.js"></script>
<script>
    
</script>
</head>
<body bgcolor="#DDEEF2">
<div class="info">
	<div class="infoTop">
		 <h1>职位描述列表</h1>
	</div>
</div>
<form method="get" class="form-inline">
     <div class="search-inline mt10" style="height:auto">
		<em><input id="jdDomain" type="hidden" value="<?php echo APP_SITE_URL;?>" /></em>
        <a href="<?php echo APP_SITE_URL;?>/admin/jd_position_describe_detail/">添加职位描述</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<label>选择职位：</label>
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
		</span>
		<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span style="margin-top:20px;line-height:40px">
        <select id="kid" name="kid" data-placeholder="请选择关键词" style="width:180px;" class="chzn-select game">
            <option value="0">请选择关键词</option>
            <?php if (!empty($jd_keyword_list)): ?>
                <?php foreach ($jd_keyword_list as $item): 
                    $selected = (isset($kid) && $item['kid'] == $kid) ? 'selected="selected"' : '';
                    echo '<option value="' . $item['kid'] . '" '.$selected.'>' . $item['keyword'] . '</option>';
                endforeach; ?>
            <?php endif; ?>
        </select>
		</span>-->
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label>级别：</label>
    	<select name="level" style="width:100px;">
			<option value="0">不限</option>
			<option value="1" <?php if (isset($level) && $level==1){?> selected <?php }?> >低级</option>
			<option value="2" <?php if (isset($level) && $level==2){?> selected <?php }?> >中级</option>
			<option value="3" <?php if (isset($level) && $level==3){?> selected <?php }?> >高级</option>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<input type="submit" value="查询" class="btn btn-primary">
    </div>
</form>

<table cellspacing="1" cellpadding="2" width="100%" class="table table-striped table-hover table-condensed  table-bordered" bgcolor="#BBDDE5">
    <thead>
    <tr height="22" bgcolor="#EEEEEE">
        <th width="2%" align="center"><b>操作</b></th>
        <th width="1%" height="25" align="center"><b>描述ID</b></th>
        <th width="1%" height="25" align="center"><b>类型</b></th>
        <th width="1%" height="25" align="center"><b>关键词ID</b></th>
        <th width="2%" height="25" align="center"><b>关键词</b></th>
        <th width="2%" align="center"><b>职位名称</b></th>
        <th width="6%" align="center"><b>职位描述</b></th>
        <th width="3%" align="center"><b>级别</b></th>
        <th width="3%" align="center"><b>排序</b></th>
        <th width="3%" align="center"><b>状态</b></th>
    </tr>
    </thead>
    <tbody>
    <?php if($jd_describe_list){foreach ($jd_describe_list as $data){?>
    <tr id="tr_<?php echo $data['kid'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
        <td align="center"><a href="<?php echo APP_SITE_URL;?>/admin/jd_position_describe_detail/<?php echo $data['id'];?>/<?php echo $data['kid'];?>/<?php echo $data['pdid'];?>">编辑</a><!--&nbsp;&nbsp;<a href="#" onclick="delPosition(<?php echo $data['kid'];?>);">删除</a>--></td>
        <td align="center"><?php echo $data['id'];?></td>
        <td align="center"><?php echo (isset($data['type']) && $data['type']=='describe') ? '岗位职责' : ((isset($data['type']) && $data['type']=='demand') ? '任职资格' : '未知');?></td>
        <td align="center"><?php echo $data['kid'];?></td>
        <td align="center"><?php echo $data['keyword'];?></td>
        <td align="center"><?php echo $data['positionName'];?></td>
        <td align="center"><?php echo $data['content'];?></td>
        <td align="center">
            <?php 
                switch ($data['level'])
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
        <td align="center"><?php echo $data['sortId'];?></td>
        <td align="center"><?php echo $data['state']=='new'?'显示':'隐藏';?></td>
    </tr>
    <?php }}?>
    </tbody>
	<thead>
    <tr>
        <td bgcolor="#FFFFFF" height="25" colspan="21">
            <div align="center"><?php echo $page;?></div>
        </td>
    </tr>
	</thead>
</table>
<script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>
</body>
</html>