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
     /* for IE */  
    text-overflow: ellipsis;
    /* for Firefox,mozilla */ 
    -moz-text-overflow: ellipsis;
    overflow: hidden;  
    white-space: nowrap;  
    text-align: left  
}


.mytable tr td {
    /* for IE */  
    text-overflow: ellipsis;
    /* for Firefox,mozilla */ 
    -moz-text-overflow: ellipsis;
    overflow: hidden;  
    white-space: nowrap;  
    text-align: left  
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
         <h1>对接列表</h1>
    </div>
</div>
<form method="get" class="form-inline">
     <div class="search-inline mt10" style="height:auto">
         <label>对接ID：</label>
         <input id="id" name="id" type="text" value="<?php if (isset($id) && !empty($id)) {echo $id;} ?>" />
         <label>需求ID：</label>
         <input id="nid" name="nid" type="text" value="<?php if (isset($nid) && !empty($nid)) {echo $nid;} ?>" />
         <label>状态：</label>
        <select name="state" style="width:100px;">
            <option value="-1">不限</option>
            <option value="new" <?php if (isset($state) && $state=='new'){?> selected <?php }?> selected = "selected" >正常</option>
            <option value="delete" <?php if (isset($state) && $state=='delete'){?> selected <?php }?> >已删除</option>
        </select>
        <input type="submit" value="查询" class="btn btn-primary">
    </div>
</form>

<table width='100%' cellspacing='1' cellpadding='2' class="table table-striped table-hover table-condensed  table-bordered" style='table-layout: fixed;overflow: hidden;' bgcolor="#BBDDE5">
    <thead>
    <tr height="22" bgcolor="#EEEEEE">
        <th width="5%" align="center"><b>操作</b></th>
        <th width="5%" height="25" align="center"><b>对接ID</b></th>
        <th width="5%" height="25" align="center"><b>需求ID</b></th>
        <th width="20%" height="25" align="center"><b>资源概述</b></th>
        <th width="20%" height="25" align="center"><b>状态</b></th>
        <th width="15%" align="center" style="overflow: hidden;"><b  style="overflow: hidden;">创建时间</b></th>
        <th width="15%" align="center" style="overflow: hidden;"><b  style="overflow: hidden;">更新时间</b></th>
    </tr>
    </thead>
    <tbody>
    <?php if($jdSupplyList){foreach ($jdSupplyList as $data){?>
    <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
        <td align="center"><a href="<?php echo APP_SITE_URL;?>/admin/jd_supply_edit?id=<?php echo $data['id'];?>&page=<?php echo $page;?>">编辑</a><!--&nbsp;&nbsp;<a href="#" onclick="delPosition(<?php echo $data['id'];?>);">删除</a>--></td>
        <td align="center"><?php echo $data['id'];?></td>
        <td align="center"><a href="<?php echo APP_SITE_URL;?>/admin/jd_base_list?id=<?php echo $data['need_id'];?>"><?php echo $data['need_id'];?></a>
        <td align="center" title="<?php echo mb_substr($data['resource'], 0, 50);?>"><div style="overflow:hidden;"><?php echo mb_substr($data['resource'], 0, 50);?></div></td>
        <td align="center"><?php echo $data['state']=='new'?'正常':'已删除';?></td>
        <td align="center" ><div style="overflow:hidden;"><?php echo (!empty($data['ctime']) ? $data['ctime'] : '');?></div></td>
        <td align="center" ><div style="overflow:hidden;"><?php echo (!empty($data['utime']) ? $data['utime'] : '');?></div></td>
    </tr>
    <?php }}?>
    </tbody>
    <thead>
    <tr>
        <td bgcolor="#FFFFFF" height="25" colspan="7">
            <div align="center"><?php echo $pageShow;?></div>
        </td>
    </tr>
    </thead>
</table>
<script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    //$(".chzn-select").chosen();
</script>
</body>
</html>