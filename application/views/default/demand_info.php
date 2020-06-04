<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $title;?></title>
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
</head>
<body bgcolor="#DDEEF2">
<div class="info">
    <div class="infoTop">
        <h1>
            <a href="/demand/index">首页</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/demand/category">全部分类</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/demand/week">本周发布</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/demand/my">我的信息</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/demand/add">发布信息</a>&nbsp;&nbsp;&nbsp;&nbsp;
        </h1>
    </div>
</div>
<table width='100%' cellspacing='1' cellpadding='2' class="table table-striped table-hover table-condensed  table-bordered" style='table-layout: fixed;overflow: hidden;' bgcolor="#BBDDE5">
    <tbody>
        <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div style="overflow:hidden;">信息编号：<?php echo $data['id'];?></div>
            </td>
        </tr>
        <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div style="overflow:hidden;">分类：<?php echo $data['companyTypeName'];?></div>
            </td>
        </tr>
        <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div style="overflow:hidden;">标签：<?php echo $data['abilityFeatureName'];?></div>
            </td>
        </tr>
        <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div style="overflow:hidden;">题主说：<?php echo $data['describeContent'];?></div>
            </td>
        </tr>
        <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div style="overflow:hidden;">小编说：<?php echo $data['demandContent'];?></div>
            </td>
        </tr>
        <tr id="tr_<?php echo $data['id'];?>" height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div style="overflow:hidden;"><a href="/supply/add?id=<?php echo $data['id']; ?>" target="_blank">我有资源对接</a></div>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>