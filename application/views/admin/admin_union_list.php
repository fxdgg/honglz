<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
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
</head>
<body bgcolor="#DDEEF2">
<table width="98%" border="0" cellspacing="1" cellpadding="2"
    align="center" bgcolor="#BBDDE5" class="margin_top_10">
    <tr>
        <td bgcolor="#FFFFFF" height="31" colspan="19">
        <div align="center" class="black_14_bold">社团列表</div>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" height="28" colspan="19">
        <div style="margin-left: 12px;"></div>
        </td>
    </tr>
    <tr height="22" bgcolor="#EEEEEE">
        <!--<th width="2%" height="25" align="center"><input type="checkbox"
            id="checkall" onclick="check_all(this)" title="全选/不选 本页所有角色" /></th>-->
        <th width="3%" height="25" align="center"><b>社团ID</b></th>
        <th width="4%" align="center"><b>社团名称</b></th>
        <th width="4%" align="center"><b>社团简称</b></th>
        <th width="4%" align="center"><b>社团状态</b></th>
        <th width="5%" align="center"><b>社团创建时间</b></th>
    </tr>
    <?php foreach ($union_list as $data){?>
    <tr height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'"
        onMouseOut="this.className='out'">
        <!--<td align="center"><input type="checkbox" id="check" name="check"
            value="<{ $data.roleid }>,<{ $data.charname }>" /></td>-->
        <td align="center"><a href="<?php echo APP_SITE_URL;?>/admin/union_detial_info/<?php echo $data['unionId'];?>"><?php echo $data['unionId'];?></a></td>
        <td align="center"><?php echo $data['unionName'];?></td>
        <td align="center"><?php echo $data['unionNick'];?></td>
        <?php if($data['unionStatus'] == UnionManage::UNION_STATUS_CLOSE){?>
        <td align="center" style="color:#f00000">关闭</td>
        <?php }elseif($data['unionStatus'] == UnionManage::UNION_STATUS_UNAUTH_TMP){?>
        <td align="center">非认证且临时</td>
        <?php }elseif($data['unionStatus'] == UnionManage::UNION_STATUS_UNAUTH_VALID){?>
        <td align="center">非认证且生效</td>
        <?php }elseif($data['unionStatus'] == UnionManage::UNION_STATUS_AUTH_VALID){?>
        <td align="center" style="color:#f00000">认证且生效</td>
        <?php }else{?>
        <td align="center">未知</td>
        <?php }?>
        <td align="center"><?php echo $data['createTime'];?></td>
    </tr>
    <?php }?>
    <tr>
        <td bgcolor="#FFFFFF" height="25" colspan="19">
        <div align="center"><?php echo $page;?></div>
        </td>
    </tr>
</table>
</body>
</html>