<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>嘉宾分享</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<!-- <link rel="stylesheet" type="text/css" href="http://bbs.g.uusee.com/forumdata/cache/style_1_common.css" /> -->
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH;?>scriptstyle_1_viewthread.css" />
<style type="text/css">
#header{height:120px; background-position:center}
#header .wrap{height:75px;}
#header h2{ padding-left:30px; padding-top:25px;}
#menu a:hover{border-color:#00B2E8;background: url(<?php echo IMAGE_PATH;?>menubg_c.gif) repeat-x; height:25px;}
#menu li.current a {background: url(<?php echo IMAGE_PATH;?>menubg_c.gif) repeat-x; height:25px;}
.postmessage .avt_list li{height:70px;}

.online_user_button{margin-bottom:5px; width:85px; cursor:pointer};
</style>
</head>
<body>

	<form action='<?php echo APP_SITE_URL;?>/admin/visitor_posts_reply' method='post'>
		<table id="pid_table" summary="pid_table" align="center" bgcolor="#BBDDE5" cellspacing="0" cellpadding="0">
	   		<tr>
	    		<td bgcolor="#FFFFFF" colspan="4">
	    		<input type="hidden" id="u" name="u" value="<?php echo $posts_list['uid'];?>" />
	    		<input type="hidden" id="rid" name="rid" value="<?php echo $posts_list['rootid'];?>" />
	    		<input type="hidden" id="ru" name="ru" value="0" />
	    		</td>
	  		</tr>
	  		<tr>
	    		<td bgcolor="#FFFFFF" height="31" colspan="4">
	    			<div align="center" class="black_14_bold">帖子发布</div>
	    		</td>
	  		</tr>
	  		<tr bgcolor="#FFFFFF">
	   			<td width="35%" colspan="4" align="center">
	    			<textarea id="content" name="content" style="width:1024px;height:300px;"></textarea>
	    		</td>
	  		</tr>
	  		<tr bgcolor="#FFFFFF">
	    		<td colspan='4' align="center">
	    			<input type="submit" name="editPosts" id="editPosts" class="submitbtn" width="41" height="21" value="提交" />
	    		</td>
	  		</tr>
		</table>
	</form>

	<div id="postlist" class="mainbox viewthread">
		<?php 
			if (!empty($posts_list['root']))
			{
				$userList = !empty($posts_list['users']) ? $posts_list['users'] : array();
				$data =& $posts_list['root'];
				
				$uid = isset($data['userid']) ? $data['userid'] : 0;
				$userInfo = isset($userList[$uid]) ? $userList[$uid] : array();
				$iconUrl = !empty($userInfo['iconUrl']) ? $userInfo['iconUrl'] : '';
				if (!empty($iconUrl))
				{
					if (substr($iconUrl, 0, 7) != 'http://') //自己上传的头像
					{
						$iconUrl = APP_SITE_URL . str_replace('_s', '_m', $iconUrl);
					}else if(strpos($iconUrl, 'sinaimg') !== FALSE){ //微博头像
						$iconUrl = str_replace('/50/', '/180/', $iconUrl);
					}
				}
				empty($iconUrl) && $iconUrl = IMAGE_PATH . 'online_member.gif';
		?>
		<div id="post_<?php echo $data['id'];?>">
			<table id="pid<?php echo $data['id'];?>" summary="pid<?php echo $data['id'];?>" cellspacing="0" cellpadding="0">
				<tr>
					<td class="postauthor" rowspan="2">
	 					<div>
							<div class="avatar">
								<img src="<?php echo $iconUrl;?>" width="120px" height="120px" onerror="this.src='<?php echo IMAGE_PATH;?>noavatar_middle.gif'" />
							</div>
						</div>
						<div class="postinfo" style="margin-top:-10px;">
							<span style="margin-left: 50px; font-weight: 800">
							<?php echo !empty($userInfo['nickname']) ? $userInfo['nickname'] : '昵称';?>
							</span><!-- target="_blank" href="space.php?uid=11603669" http://bbs.g.uusee.com/viewthread.php?tid=114199&extra=page%3D1-->
						</div>
					</td>
					<td class="postcontent">
						<div id="threadstamp"></div>
						<div class="postinfo">
							<div class="posterinfo">
								<div class="authorinfo">
									<img class="authicon" id="authicon<?php echo $data['id'];?>" src="<?php echo IMAGE_PATH;?>online_member.gif" />
									<em id="authorposton<?php echo $data['id'];?>">发表于 <?php echo $data['posttime'];?></em>
								</div>
							</div>
						</div>
						<div class="defaultpost">
							<div id="ad_thread2_0"></div>
							<div id="ad_thread3_0"></div>
							<div id="ad_thread4_0"></div>
							<div class="postmessage firstpost">
								<!-- <div id="threadtitle">
									<h1>标题</h1>
								</div> -->
								<div class="t_msgfontfix">
									<table cellspacing="0" cellpadding="0">
										<tr>
											<td class="t_msgfont" id="postmessage_<?php echo $data['id'];?>">
											<?php echo $data['content'];?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php 
			}else{
				$posts_list['posts'] = array();
			}
		?>
		<?php 
			if (!empty($posts_list['posts']))
			{
				//$userList = !empty($posts_list['users']) ? $posts_list['users'] : array();
				foreach ($posts_list['posts'] as $data){
					$uid = isset($data['userid']) ? $data['userid'] : 0;
					$userInfo = isset($userList[$uid]) ? $userList[$uid] : array();
					$iconUrl = !empty($userInfo['iconUrl']) ? $userInfo['iconUrl'] : '';
					if (!empty($iconUrl))
					{
						if (substr($iconUrl, 0, 7) != 'http://') //自己上传的头像
						{
							$iconUrl = APP_SITE_URL . str_replace('_s', '_m', $iconUrl);
						}else if(strpos($iconUrl, 'sinaimg') !== FALSE){ //微博头像
							$iconUrl = str_replace('/50/', '/180/', $iconUrl);
						}
					}
					empty($iconUrl) && $iconUrl = IMAGE_PATH . 'online_member.gif';
		?>
		<div id="post_<?php echo $data['id'];?>">
			<table id="pid<?php echo $data['id'];?>" summary="pid<?php echo $data['id'];?>" cellspacing="0" cellpadding="0">
				<tr>
					<td class="postauthor" rowspan="2">
	 					<div>
							<div class="avatar">
								<img src="<?php echo $iconUrl;?>" width="120px" height="120px" onerror="this.src='<?php echo IMAGE_PATH;?>noavatar_middle.gif'" />
							</div>
						</div>
						<div class="postinfo">
							<span style="margin-left: 50px; font-weight: 800">
							<?php echo !empty($userInfo['nickname']) ? $userInfo['nickname'] : '昵称';?>
							</span><!-- target="_blank" href="space.php?uid=11603669" http://bbs.g.uusee.com/viewthread.php?tid=114199&extra=page%3D1-->
						</div>
					</td>
					<td class="postcontent">
						<div id="threadstamp"></div>
						<div class="postinfo">
							<div class="posterinfo">
								<div class="authorinfo">
									<img class="authicon" id="authicon<?php echo $data['id'];?>" src="<?php echo IMAGE_PATH;?>online_member.gif" />
									<em id="authorposton<?php echo $data['id'];?>">回复于 <?php echo $data['posttime'];?></em>
								</div>
							</div>
						</div>
						<div class="defaultpost">
							<div id="ad_thread2_0"></div>
							<div id="ad_thread3_0"></div>
							<div id="ad_thread4_0"></div>
							<div class="postmessage firstpost">
								<!-- <div id="threadtitle">
									<h1>标题</h1>
								</div> -->
								<div class="t_msgfontfix">
									<table cellspacing="0" cellpadding="0">
										<tr>
											<td class="t_msgfont" id="postmessage_<?php echo $data['id'];?>">
											<?php echo $data['content'];?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php }}?>
	</div>
</body>
</html>