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
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
<script src="<?php echo JS_PATH;?>bootstrap.js"></script>
<script>
    function delPosition(id)
    {
        if( confirm('您确定要删除该记录吗') ){
              var url = "<?php echo APP_SITE_URL;?>/admin/jd_position_detail_del/"+id;
              $.ajax({
                type: "get",
                url:url,
                dataType:"json",
                success: function(msg)
                {
                    $("#tr_"+id).hide();
                   alert(msg.data);
                }
            });
        }
    }
</script>
</head>
<body bgcolor="#DDEEF2">
<div class="info">
	<div class="infoTop">
		 <h1>职位列表</h1>
	</div>
</div>
<form method="get" class="form-inline" >
     <div class="search-inline mt10" style="height:auto">
		<em><input id="jdDomain" type="hidden" value="<?php echo APP_SITE_URL;?>" /></em>
        <a href="<?php echo APP_SITE_URL;?>/admin/jd_position_detail_info">添加职位</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

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
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	<input type="submit" value="查询" class="btn btn-primary">
    </div>
</form>

<table cellspacing="1" cellpadding="2" width="100%" class="table table-striped table-hover table-condensed  table-bordered" bgcolor="#BBDDE5">
    <thead>
    <tr height="22" bgcolor="#EEEEEE">
        <th width="2%" align="center"><b>操作</b></th>
        <th width="3%" height="25" align="center"><b>职位ID</b></th>
        <th width="6%" align="center"><b>职位名称</b></th>
        <th width="3%" align="center"><b>排序</b></th>
        <th width="3%" align="center"><b>状态</b></th>
    </tr>
    </thead>
    <tbody>
    <?php if($jd_position_list){foreach ($jd_position_list as $data){?>
    <tr id="tr_<?php echo $data['pdid'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
        <td align="center"><a href="<?php echo APP_SITE_URL;?>/admin/jd_position_detail_info/<?php echo $data['pdid'];?>">编辑</a><!--&nbsp;&nbsp;<a href="#" onclick="delPosition(<?php echo $data['pdid'];?>);">删除</a>--></td>
        <td align="center"><?php echo $data['pdid'];?></td>
        <td align="center"><?php echo $data['positionName'];?></td>
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