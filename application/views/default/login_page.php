<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>登录</title>
    <link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
    <style>
        .inputimg {
            background: url(<?php echo IMAGE_PATH;?>login.jpg) no-repeat;
        }
    </style>
    <script type="text/javascript" src="<?php echo JS_PATH;?>common.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH;?>admin.js"></script>
    <script>
        var app_url = "<?php echo APP_SITE_URL;?>";
        $(document).keydown(function(event) {
            if (event.keyCode == 13) {
                chklogin();
            }
        });
        function flush_check_code() {
            var check_img = $('#check_img').get(0);
            var split_check_img_src = check_img.src.split('?');
            var url = split_check_img_src[0];
            var query = split_check_img_src[1];
            check_img.src = url + "?time=" + (new Date().getTime());
        }
    </script>
</head>
<body bgcolor="#BED8E5">
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="frm_table">
    <tr>
        <td width="296"></td>
        <td align="center">
            <table width="411" border="0" cellspacing="0" cellpadding="0"
                   align="center" style="margin-top: 120px;">
                <tr>
                    <td height="88" colspan="2"><img src="<?php echo IMAGE_PATH;?>bg_01.jpg"
                                                     width="411" height="88" /></td>
                </tr>
                <tr>
                    <td width="100%" height="160" bgcolor="#FFFFFF">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="26" colspan="2"></td>
                            </tr>
                            <tr>
                                <td width="41%" height="30">
                                    <div align="right"><strong>邮箱：</strong></div>
                                </td>
                                <td align="left" width="59%"><input type="text"
                                                                    name="account" id="account" value="" maxlength="50"
                                                                    style="width: 180px;"
                                                                    onblur="if(this.value!=''){showmsg('loginmsg','');}" />
                                    <input type="hidden" name="r" id="r" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td height="30">
                                    <div align="right"><strong>密 码：</strong></div>
                                </td>
                                <td align="left">
                                    <input type="password" name="password" id="password" value="" style="width: 180px;"
                                           onblur="if(this.value!=''){showmsg('loginmsg','');}" />&nbsp;
                                    <input name="remeber" id="remeber" type="checkbox" title="记住密码" checked="checked"/>
                                </td>
                            </tr>
                            <tr>
                                <td height="30" colspan="2">
                                    <div align="center"><input type="image"
                                                               src="<?php echo IMAGE_PATH;?>login.jpg" name="login" id="login" width="41"
                                                               height="21" onclick="chkuserlogin();" /> &nbsp;&nbsp;<img
                                                src="<?php echo IMAGE_PATH;?>cancel.jpg" width="41" height="21"
                                                style="cursor: pointer;" onclick="cancel();" /></div>
                                </td>
                            </tr>
                            <tr>
                                <td height="30" colspan="2">
                                    <div align="center" id="loginmsg" style="color: #FF0000;"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!--<td width="164"><img src="<?php echo IMAGE_PATH;?>bg_02.jpg" width="164" height="180" /></td>-->
                </tr>
            </table>
        </td>
        <td width="296"></td>
    </tr>
</table>
<script>
    var account = "";
    if (account = getCookie("cookie_account"))
        document.getElementById("account").value = account;
    var password = "";
    if (password = getCookie("cookie_password"))
        document.getElementById("password").value = password;
    var rember;
    if (getCookie("cookie_remeber") != null)
        document.getElementById("remeber").checked = true;
</script>
</body>
</html>