<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
<style>
.bg1{
background-image:url(<?php echo IMAGE_PATH;?>/bg01.gif);
color:#FFFFFF;
font-size:14px;
font-weight:bold;
width:100px;
height:20px;
padding-top:5px;
margin-left:3px;
}
.bg2{
background-image:url(<?php echo IMAGE_PATH;?>bg02.gif);
color:#000000;
font-size:12px;
font-weight:bold;
width:100px;
height:17px;
padding-top:8px;
margin-left:3px;
}
.zhezhao {background-color:#666666; position: absolute;z-index:5000; top:0px; left:0px;filter:alpha(opacity=95);-moz-opacity:0.95;opacity:0.95;}
</style>
</head>

<body bgcolor="#DDEEF2">
<form action='<?php echo APP_SITE_URL;?>/admin/user_show_info/<?php echo $userinfo['id'];?>' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold">用户基本信息</div></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>用户UID：</b></td>
    <td width="35%"><?php echo $userinfo['id'];?></td>
    <td width="15%" align="right"><b>微博UID：</b></td>
    <td width="35%"><?php echo $userinfo['sinaUserId'];?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right"><b>账号：</b></td>
    <td width="35%"><?php echo $userinfo['email'];?></td>
    <td align="right" height="25"><b>昵称：</b></td>
    <td><?php echo (!empty($userinfo['nickname']) ? $userinfo['nickname'] : '暂无');?></td>
  </tr>
   
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>性别：</b></td>
    <td><?php if($userinfo['sex'] == '1'){?>男<?php }elseif($userinfo['sex'] == '2'){?>女<?php }else{?>未知<?php }?></td>
    <td align="right" height="25"><b>所在区域：</b></td>
    <td><?php echo (!empty($userinfo['area']) ? $userinfo['area'] : '暂无');?></td>
  </tr>
  
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>管理员：</b></td>
    <td><?php if($userinfo['blhRole'] == '0'){?>否<?php }elseif($userinfo['blhRole'] == '1'){?>社团管理员<?php }elseif($userinfo['blhRole'] == '2'){?>舶来管理员<?php }elseif($userinfo['blhRole'] == '3'){?>系统管理员<?php }else{?>未知<?php }?></td>
    <!--<td align="right" height="25"><b>虚拟用户：</b></td>
    <td><?php if($userinfo['usertype'] == '1'){?>是<?php }elseif($userinfo['usertype'] == '0'){?>否<?php }else{?>未知<?php }?></td>-->
    <td align="right" height="25"><b>行业：</b></td>
    <td><?php echo (!empty($userinfo['vocation']) ? $userinfo['vocation'] : '暂无');?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>社团ID：</b></td>
    <td><?php echo (!empty($userinfo['unionIdMuti']) ? $userinfo['unionIdMuti'] : 0);?></td>
    <td align="right"><b>社团名称：</b></td>
    <td><?php echo (!empty($userinfo['unionNameMuti']) ? $userinfo['unionNameMuti'] : '暂无');?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>荔枝币：</b></td>
    <td style="color:#f00000"><?php echo (!empty($userinfo['lizhi']) ? $userinfo['lizhi'] : 0);?></td>
    <td align="right"><b>社团状态：</b></td>
    <td style="color:#f00000">
        <?php 
            $unionRoleDisplayArr = array();
            $unionRoleList = explode(',',$userinfo['unionRoleMuti']);
            foreach($unionRoleList as $unionRoleItem)
            {
                if (isset(UserUnion::$userUnionRoleConfig[$unionRoleItem]) && !empty(UserUnion::$userUnionRoleConfig[$unionRoleItem]))
                {
                    $unionRoleDisplayArr[] = UserUnion::$userUnionRoleConfig[$unionRoleItem];
                }else{
                    $unionRoleDisplayArr[] = '未知';
                }
                /*if($unionRoleItem == '0'){$unionRoleDisplayArr[] = '被踢出或退出';}
                elseif($unionRoleItem == '1'){$unionRoleDisplayArr[] = '普通成员';}
                elseif($unionRoleItem == '2'){$unionRoleDisplayArr[] = '管理员';}
                elseif($unionRoleItem == '3'){$unionRoleDisplayArr[] = '二级管理员';}
                else{$unionRoleDisplayArr[] = '未知';}*/
            }
            echo join(',', $unionRoleDisplayArr);
        ?>
    </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>所在公司：</b></td>
    <td><?php echo (!empty($userinfo['company']) ? $userinfo['company'] : '暂无');?></td>
    <td align="right"><b>职位：</b></td>
    <td><?php echo (!empty($userinfo['position']) ? $userinfo['position'] : '暂无');?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="right" height="25"><b>手机号：</b></td>
    <td><?php echo (!empty($userinfo['cellphone']) ? $userinfo['cellphone'] : '暂无');?></td>
    <td align="right" height="25"><b>QQ：</b></td>
    <td><?php echo (!empty($userinfo['qq']) ? $userinfo['qq'] : '暂无');?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>微博：</b></td>
    <td><?php echo (!empty($userinfo['weibo']) ? $userinfo['weibo'] : '暂无');?></td>
    <td align="right"><b>微信：</b></td>
    <td><?php echo (!empty($userinfo['weixin']) ? $userinfo['weixin'] : '暂无');?></td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td align="right"><b>出生日期：</b></td>
    <td><?php echo (!empty($userinfo['birthday']) ? $userinfo['birthday'] : '暂无');?></td>
    <td align="right"><b>婚姻：</b></td>
    <td><?php if($userinfo['isMarried'] == '1'){?>已婚<?php }elseif($userinfo['isMarried'] == '0'){?>未婚<?php }else{?>未知<?php }?></td>
  </tr>
  
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>工龄：</b></td>
    <td><?php echo (!empty($userinfo['workDate']) ? $userinfo['workDate'] : '暂无');?></td>
    <td align="right"><b>毕业院校：</b></td>
    <td><?php echo (!empty($userinfo['gradeSchool']) ? $userinfo['gradeSchool'] : '暂无');?></td>
  </tr>
  
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>最近登录时间：</b></td>
    <td><?php echo date('Y-m-d H:i:s',$userinfo['lastActivity']);?></td>
    <td align="right"><b>注册时间：</b></td>
    <td><?php echo $userinfo['createTime'];?></td>
  </tr>
  
  <tr bgcolor="#FFFFFF">
    <td align="right"><b>用户状态：</b></td>
    <td><select id="status" name="status">
            <option value="-1">不操作</option>
            <option value="0" <?php if ($userinfo['status'] == '0'){?>selected="selected" <?php }?>>注册(无邀请码)</option>
            <option value="1" <?php if ($userinfo['status'] == '1'){?>selected="selected" <?php }?>>正常</option>
            <option value="2" <?php if ($userinfo['status'] == '2'){?>selected="selected" <?php }?>>禁用</option>
        </select>
    </td>
    <?php if (!$isAdmin) {?>
    <td align="right"><b>社团状态：</b></td>
    <td><select id="is_kick" name="is_kick">
            <?php if($userinfo['unionRoleMuti'] == '0'){?>
            <option value="0">不操作</option>
            <option value="1">正常+发送邮件</option>
            <?php }else{?>
            <option value="0">不操作</option>
            <option value="2">禁用+发送邮件</option>
            <?php }?>
        </select>
    </td>
    <?php }else{echo '<td align="right"></td><td></td>';}?>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan='4' align="center">
        <input type="submit" name="editUser" id="editUser" width="41" height="21" value="提交"/>
    </td>
  </tr>
</table>
</form>

<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td align="right" height="25">&nbsp;</td>
  </tr>
</table>
</body>
</html>