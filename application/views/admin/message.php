<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>提示信息</title>

<style type="text/css">
*{ padding:0; margin:0; font-size:12px}
.showMsg .guery {white-space: pre-wrap; /* css-3 */white-space: -moz-pre-wrap; /* Mozilla, since 1999 */white-space: -pre-wrap; /* Opera 4-6 */white-space: -o-pre-wrap; /* Opera 7 */	word-wrap: break-word; /* Internet Explorer 5.5+ */}
a:link,a:visited{text-decoration:none;color:#0068a6}
a:hover,a:active{color:#ff6600;text-decoration: underline}
.showMsg{border: 1px solid #1e64c8; zoom:1; width:510px; height:185px;position:absolute;top:40%;left:50%;margin:-87px 0 0 -225px}
.showMsg h5{background-image: url(<?php echo IMAGE_PATH;?>msg.png);background-repeat: no-repeat; color:#fff; padding-left:35px; height:25px; line-height:26px;*line-height:28px; overflow:hidden; font-size:14px; text-align:left}
.showMsg .content{ padding:46px 30px 22px 45px; font-size:14px; height:66px;}
.showMsg .bottom{ background:#e4ecf7; margin: 0 1px 1px 1px;line-height:26px; *line-height:30px; height:26px; text-align:center}
.showMsg .ok,.showMsg .guery{background: url(<?php echo IMAGE_PATH;?>msg_bg.png) no-repeat 0px -560px;}
.showMsg .guery{background-position: left -460px;}
</style>
<!--<script type="text/javascript" language="JavaScript" src="<?php echo JS_PATH;?>jquery.js"></script>-->
<!--<script type="text/javascript" language="JavaScript" src="<?php echo JS_PATH;?>common.js"></script>-->
</head>
<body>
<div class="showMsg" style="text-align:center">
	<h5>提示信息</h5><!--max-width:280px-->
    <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline; "><?php echo $msg;?></div>
    <div class="bottom">
    <?php if($url_forward=='goback' || $url_forward==''){?>
	<a href="javascript:history.back();" >[点这里返回上一页]</a><!-- window.location.reload(); history.back(); fusion2.dialog.relogin();-->
	<?php }elseif($url_forward=="close"){?>
	<input type="button" name="close" value=" 关闭 " onClick="window.close();" style=''><!--background:url("<?php echo IMAGE_PATH;?>ser_all_list.jpg") no-repeat scroll 0 0 transparent;border:0 #ff0000 solid;-->
	<?php }elseif ($url_forward=="blank"){?>
	<?php }elseif($url_forward){?>
		<a href="<?php echo $url_forward;?>">如果您的浏览器没有自动跳转，请点击这里</a>
	    <script language="javascript">setTimeout("redirect('<?php echo $url_forward;?>','<?php echo $clientNoticeUrl;?>')",<?php echo $ms;?>);</script> 
    <?php }?>
	<?php if(isset($dialog) && $dialog){?>
        <script type="text/javascript">window.top.location.reload();window.top.art.dialog({id:"<?php echo $dialog;?>"}).close();</script>
    <?php }?>
        </div>
</div>
<script type="text/javascript">
	function redirect(url,ifFlag) {
		if(ifFlag == 'relogin'){
			//top.href = url;
			window.top.location.reload();
			//fusion2.dialog.relogin();
		}else{
			location.href = url;
		}
	}
</script>
</body>
</html>