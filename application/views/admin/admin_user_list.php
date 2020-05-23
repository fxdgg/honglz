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
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
</head>
<body bgcolor="#DDEEF2">
<table width="98%" border="0" cellspacing="1" cellpadding="2"
    align="center" bgcolor="#BBDDE5" class="margin_top_10">
    <tr>
        <td bgcolor="#FFFFFF" height="31" colspan="21">
        <div align="center" class="black_14_bold">用户列表</div>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" height="28" colspan="21">
        <div style="margin-left: 12px;"></div>
        </td>
    </tr>
    <tr height="22" bgcolor="#EEEEEE">
        <!--<th width="2%" height="25" align="center"><input type="checkbox"
            id="checkall" onclick="check_all(this)" title="全选/不选 本页所有角色" /></th>-->
        <th width="3%" height="25" align="center"><b>用户UID</b></th>
        <!--<th width="4%" align="center"><b>微博UID</b></th>-->
        <th width="4%" align="center"><b>账号</b></th>
        <th width="4%" align="center"><b>昵称</b></th>
        <!--<th width="4%" align="center"><b>性别</b></th>-->
        <!--<th width="4%" align="center"><b>荔枝币</b></th>-->
        <th width="6%" align="center"><b>地区</b></th>
        <!--<th width="6%" align="center"><b>管理员</b></th>-->
        <!--<th width="6%" align="center"><b>虚拟用户</b></th>-->
        <!--<th width="10%" align="center"><b>所在社团</b></th>-->
        <!--<th width="10%" align="center"><b>所在公司</b></th>-->
        <!--<th width="8%" align="center"><b>职位</b></th>-->
        <!--<th width="6%" align="center"><b>手机号</b></th>-->
        <!--<th width="4%" align="center"><b>微博</b></th>-->
        <!--<th width="4%" align="center"><b>微信</b></th>-->
        <!--<th width="5%" align="center"><b>出生日期</b></th>-->
        <!--<th width="5%" align="center"><b>婚姻</b></th>-->
        <th width="5%" align="center"><b>职位</b></th>
        <th width="5%" align="center"><b>工龄</b></th>
        <th width="5%" align="center"><b>毕业院校</b></th>
        <th width="6%" align="center"><b>用户状态</b></th>
        <!--<th width="8%" align="center"><b>社团状态</b></th>-->
        <th width="6%" align="center"><b>最近登录时间</b></th>
        <th width="6%" align="center"><b>注册时间</b></th>
    </tr>
    <?php foreach ($user_list as $data){?>
    <tr height="22" bgcolor="#FFFFFF" onMouseOver="this.className='over'" onMouseOut="this.className='out'">
        <!--<td align="center"><input type="checkbox" id="check" name="check"
            value="<{ $data.roleid }>,<{ $data.charname }>" /></td>-->
        <td align="center"><a href="<?php echo APP_SITE_URL;?>/admin/user_show_info/<?php echo $data['id'];?>"><?php echo $data['id'];?></a></td>
        <!--<td align="center"><?php echo $data['sinaUserId'];?></td>-->
        <td align="center"><?php echo $data['email'];?></td>
        <td align="center"><?php echo $data['nickname'];?></td>
        <?php if($data['sex'] == '1'){?>
        <!--<td align="center">男</td>-->
        <?php }elseif($data['sex'] == '2'){?>
        <!--<td align="center">女</td>-->
        <?php }else{?>
        <!--<td align="center">未知</td>-->
        <?php }?>
        <!--<td align="center"><?php echo (!empty($data['lizhi']) ? $data['lizhi'] : 0);?></td>-->
        <td align="center"><?php echo (!empty($data['area']) ? $data['area'] : '暂无');?></td>
        <!--<?php if($data['blhRole'] == '0'){?>
        <td align="center">否</td>
        <?php }elseif($data['blhRole'] == '1'){?>
        <td align="center">社团管理员</td>
        <?php }elseif($data['blhRole'] == '2'){?>
        <td align="center">舶来管理员</td>
        <?php }elseif($data['blhRole'] == '3'){?>
        <td align="center">系统管理员</td>
        <?php }else{?>
        <td align="center">未知</td>
        <?php }?>-->
        <!--<?php if($data['usertype'] == '1'){?>
        <td align="center">是</td>
        <?php }elseif($data['usertype'] == '0'){?>
        <td align="center">否</td>
        <?php }else{?>
        <td align="center">未知</td>
        <?php }?>-->
        <!--<td align="center"><?php echo (!empty($data['unionNameMuti']) ? $data['unionNameMuti'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['company']) ? $data['company'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['position']) ? $data['position'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['cellphone']) ? $data['cellphone'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['weibo']) ? $data['weibo'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['weixin']) ? $data['weixin'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['birthday']) ? $data['birthday'] : '暂无');?></td>
        <?php if($data['isMarried'] == '1'){?>
        <td align="center">已婚</td>
        <?php }elseif($data['isMarried'] == '0'){?>
        <td align="center">未婚</td>
        <?php }else{?>
        <td align="center">未知</td>
        <?php }?>-->
        <td align="center"><?php echo (!empty($data['vocation']) ? $data['vocation'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['workDate']) ? $data['workDate'] : '暂无');?></td>
        <td align="center"><?php echo (!empty($data['gradeSchool']) ? $data['gradeSchool'] : '暂无');?></td>
        <?php if($data['status'] == '-1'){?>
        <td align="center">未注册</td>
        <?php }elseif($data['status'] == '0'){?>
        <td align="center">未验证</td>
        <?php }elseif($data['status'] == '1'){?>
        <td align="center">正常</td>
        <?php }elseif($data['status'] == '2'){?>
        <td align="center">被禁用</td>
        <?php }else{?>
        <td align="center">未知</td>
        <?php }?>
        <!--<?php if($data['unionRoleMuti'] == '0'){?>
        <td align="center">被踢出</td>
        <?php }elseif($data['status'] == '1'){?>
        <td align="center">普通成员</td>
        <?php }elseif($data['status'] == '2'){?>
        <td align="center">管理员</td>
        <?php }elseif($data['status'] == '3'){?>
        <td align="center">二级管理员</td>
        <?php }else{?>
        <td align="center">未知</td>
        <?php }?>-->
        <td align="center"><?php echo date('Y-m-d H:i:s',$data['lastActivity']);?></td>
        <td align="center"><?php echo $data['createTime'];?></td>
    </tr>
    <?php }?>
    <tr>
        <td bgcolor="#FFFFFF" height="25" colspan="21">
            <div align="center"><?php echo $page;?></div>
        </td>
    </tr>
</table>
</body>
</html>