<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<frameset rows="60,*" cols="*" border="0" frameborder="0" framespacing="no">
	<frame src="<?php echo APP_SITE_URL;?>/admin/index_top" name="top" scrolling="no" frameborder="no" noresize="noresize" id="top" />
	<frameset rows="*" cols="200,*" border="0" frameborder="0" framespacing="no">
		<frame src="<?php echo APP_SITE_URL;?>/admin/index_left" name="left" scrolling="yes" frameborder="no" noresize="noresize" id="left" />
		<frame src="<?php if (!empty($ref)){?><?php echo $ref;?><?php }else{?><?php echo APP_SITE_URL;?>/admin/index_right <?php }?>" name="right" scrolling="yes" frameborder="no" noresize="noresize" id="right" />
	</frameset>
</frameset>
</head>
<body>
</body>
</html>