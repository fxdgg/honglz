<!DOCTYPE html>
<html>
<head>
<title><?php echo $title;?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="utf-8" />
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<meta http-equiv="expires" content="Mon, 23 Jan 1978 20:52:30 GMT">
<meta name="robots" content="all" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="http://i.admin.game.weibo.cn/css/bootstrap.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://i.admin.game.weibo.cn/css/bootstrap-responsive.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://i.admin.game.weibo.cn/css/bootstrap-page.css" type="text/css" media="all" />
<script type="text/javascript" src="http://i.admin.game.weibo.cn/js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="http://i.admin.game.weibo.cn/js/jquery-form-2.36.js"></script>
<script type="text/javascript" src="http://i.admin.game.weibo.cn/js/bootstrap.js"></script>
</head>
<body>
<div>
<link rel="stylesheet" href="http://i.admin.game.weibo.cn/css/chosen/chosen.css">
<script type="text/javascript">
function submitForm(){
    //post 提交
    var appkey = $("#chosegame").val();
    var title = $("#title").val();
    var content = $("#content").val();
    var desc = $("#desc").val();
    //var link_url = $("#link_url").val();
    var position = $("#position").val();
    if(appkey == 0)
    {
        alert('请选择所属游戏');
        return false;
    }
    else if(title == ''){
        alert('请填写礼包标题');
        return false;
    }
    else if(content == ''){
        alert('请填写礼包内容');
        return false;
    }
    else if(desc == ''){
        alert('请填写礼包说明');
        return false;
    }
    else if(position == '' || parseInt(position) < 0){
        alert('请填写正确的礼包排序数值');
        return false;
    }
}
</script>


<style type="text/css">
.chzn-container-multi .chzn-choices .search-choice{
    margin: 5px 0 3px 5px;
}
table tr.tr-title td{
    cursor: pointer;
}
</style>

<form method="post" id="userform" name="userform" class="horizontal" enctype='multipart/form-data' onsubmit="return submitForm();" action="<?php echo APP_SITE_URL;?>/jdtools/main/<?php echo $step;?>">
    <table>
        <tr>
            <td>公司名：</td>&nbsp;&nbsp;&nbsp;<td colspan='5'><input size="50"  type="text" name="title" id="title" value="" style="width:500px" /><span>&nbsp;&nbsp;&nbsp;</span></td>
        </tr>
        <tr>
            <td>职位：</td>&nbsp;&nbsp;&nbsp;
                <td colspan= '5'>
                <select id= "chosegame" name="appkey" data-placeholder="选择职位" style="width:200px;" class="chzn-select game"  >
                    <option value="0"></option>  
                    <option value="1" >产品经理（移动）</option> 
                    <option value='2' >产品经理（网站）</option>
                    <option value='3' >产品经理（社区）</option>
                    <option value='4' >产品经理（反作弊）</option>
                    <option value='5' >产品经理（策略）</option>
                    <option value='6' >产品经理（商业）</option>
                    <option value='7' >工程师（Android）</option>
                    <option value='8' >工程师（IOS）</option>
                    <option value='9' >工程师（C++）</option>
                    <option value='10' >工程师（PHP）</option>
                    <option value='11' >设计师（美术）</option>
                    <option value='12' >设计师（UI/UE）</option>
                    <option value='13' >设计师（3D）</option>
                    <option value='14' >运营经理（移动）</option>
                    <option value='15' >运营经理（社区）</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>职级：</td>
            <td>
                <select id="level" name="level" style="width:200px;">
                    <option value="0">初级</option>  
                    <option value="1" >中级</option> 
                    <option value='2' >高级</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="submit" name="submit" class="btn btn-primary" value="确定"  /></td>
        </tr>
    </table>
</form>

<script src="http://i.admin.game.weibo.cn/js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>
</div><!--end maincontent-->
</body>
</html>