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
    <script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
    <script>
        var app_url = "<?php echo APP_SITE_URL;?>";
        /*
         * 资源提供页清空功能
         */
        function cancel() {
            $('#nid').val('');
            $('#content').val('');
            $('#contact').val('');
        }
        function add() {
            var nid = $('#nid').val();
            var content = $('#content').val();
            var contact = $('#contact').val();
            if (content == "") {
                $('#loginmsg').html("请输入资源信息！");
                return;
            }
            if (contact == "") {
                alert('请输入联系人信息');
                return;
            }
            $.post(app_url+'/supply/doAdd', {
                'nid'     : nid,
                'content' : content,
                'contact' : contact
            }, function(d) {
                var json = JSON.parse(d);
                console.log('json:', json);
                if (json.status != 1) {
                    $('#loginmsg').html(json.errmsg+'('+json.errcode+')');
                    return;
                } else {
                    $('#loginmsg').html('提交成功, 正在努力的跳转, 请稍候...');
                    window.location.href = app_url+'/demand/info?id='+nid;
                }
            });
        }
    </script>
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
        <tr height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div align="center">
                    信息编号：<?php echo $jobId; ?>
                    <input type="hidden" name="nid" id="nid" value="<?php echo $jobId; ?>" />
                </div>
            </td>
        </tr>
        <tr height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div align="center">
                    可以提供什么样的资源？<br />
                    <textarea rows="5" cols="10" name="content" id="content"></textarea>

                </div>
            </td>
        </tr>
        <tr height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
            <td align="center">
                <div align="center">
                    如何联系到您？（微信号、手机号、邮箱等）<br />
                    <input type="text" name="contact" id="contact" value="" maxlength="30" />
                </div>
            </td>
        </tr>
        <tr>
            <td height="30" colspan="1">
                <div align="center">
                    <input type="button" name="addBtn" value="提交" id="login"
                           width="41" height="21" onclick="add();" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" name="cancelBtn" value="取消" width="41" height="21" onclick="cancel();" />
                </div>
            </td>
        </tr>
        <tr>
            <td height="30" colspan="2">
                <div align="center" id="loginmsg" style="color: #FF0000;"></div>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>