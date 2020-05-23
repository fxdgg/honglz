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


<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap-responsive.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap-page.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-1.7.1.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-form-2.36.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap.js"></script>
</head>
<body>
<div>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<script type="text/javascript">
function submitForm(){
    //post 提交
    var company = $("#company").val();
    var position = $("#position").val();
    var level = $("#level").val();
    var email = $("#email").val();
    if(company == ''){
        document.getElementById('msg_company').innerHTML = "请填写公司名称";
        return false;
    }
    document.getElementById('msg_company').innerHTML = "";
    if(position == 0)
    {
        document.getElementById('msg_position').innerHTML = "请选择职位";
        return false;
    }
    document.getElementById('msg_position').innerHTML = "";
    if(level == 0)
    {
        document.getElementById('msg_level').innerHTML = "请选择职级";
        return false;
    }
    document.getElementById('msg_level').innerHTML = "";
    if(email == '')
    {
        document.getElementById('msg_email').innerHTML = "请输入正确的邮箱地址";
        return false;
    }
    document.getElementById('msg_email').innerHTML = "";
    return true;
}
function checkEmpty(obj, labelName) {
    var objName = eval("document.all."+obj);
    if (objName.value == '') {
        document.getElementById('msg_' + obj).innerHTML = labelName;
        //objName.focus();
        return false;
    }
    document.getElementById('msg_' + obj).innerHTML = '';
    return true;
}
function checkEmail(obj, labelName) {
    var objName = eval("document.all."+obj);
    var pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
    if (!pattern.test(objName.value)) {
        document.getElementById('msg_mail').innerHTML = "请输入正确的邮箱地址";
        document.getElementById('emailExist').value = 1;
        //objName.focus();
        return false;
    }
    checkEmailExist(obj, labelName);
    return true;
}
var xmlHttpRequest;
//XmlHttpRequest对象
function createXmlHttpRequest(){
    if(window.ActiveXObject){ //如果是IE浏览器
        return new ActiveXObject("Microsoft.XMLHTTP");
    }else if(window.XMLHttpRequest){ //非IE浏览器
        return new XMLHttpRequest();
    }
}

function checkEmailExist(obj, labelName){
    var objName = eval("document.all."+obj);
    var url = "<?php echo APP_SITE_URL;?>/users/email_exist";
    var params = "email="+objName.value;
    //1.创建XMLHttpRequest组建
    xmlHttpRequest = createXmlHttpRequest();
        
    //2.设置回调函数    
    xmlHttpRequest.onreadystatechange = checkEmailExistCallback;
        
    //3.初始化XMLHttpRequest组建
    xmlHttpRequest.open("POST",url,true);
    //post请求要自己设置请求头
    xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    //4.发送请求
    xmlHttpRequest.send(params);
}
//回调函数
function checkEmailExistCallback(){
    if(xmlHttpRequest.readyState == 4 && xmlHttpRequest.status == 200){
        var data = xmlHttpRequest.responseText;
        var dataObj = eval("("+data+")");
        if(dataObj.ret == 0){
            document.getElementById('msg_mail').innerHTML = "";
            document.getElementById('emailExist').value = 0;
            return true;
        }
        document.getElementById('msg_mail').innerHTML = "邮箱已被注册";
        document.getElementById('emailExist').value = 1;
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
<div class="search-inline mt10" style="height:auto;">
    <table style="align:center;">
        <tr>
            <td>公司名：</td>&nbsp;&nbsp;&nbsp;<input id="emailExist" type="hidden" value="0" /><td colspan='5'><input size="50" type="text" name="company" id="company" value="" onblur="return checkEmpty('company', '请填写公司名称');" style="width:270px" /><span>&nbsp;&nbsp;&nbsp;</span><span id="msg_company" style="color:#f00"></span></td>
        </tr>
        <tr>
            <td>邮箱：</td>&nbsp;&nbsp;&nbsp;<td colspan='5'><input size="50" type="text" name="email" id="email" value="" onblur="return checkEmail('email', 'email');" style="width:270px" /><span>&nbsp;&nbsp;&nbsp;</span><span id="msg_mail" style="color:#f00"></span></td>
        </tr>
        <tr>
            <td>职&nbsp;&nbsp;&nbsp;&nbsp;位：</td>&nbsp;&nbsp;&nbsp;
                <td colspan= '5'>
                <select id="position" name="position" data-placeholder="请选择职位" style="width:300px;" class="chzn-select game">
                    <option value="0"></option>
                    <?php if (!empty($positionList)): ?>
                        <?php foreach ($positionList as $item): 
                            echo '<option value="' . $item['pdid'] . '-' . $item['positionName'] . '">' . $item['positionName'] . '</option>';
                        endforeach; ?>
                    <?php endif; ?>
                </select><span id="msg_position" style="color:#f00"></span>
            </td>
        </tr>
        <tr>
            <td>职&nbsp;&nbsp;&nbsp;&nbsp;级：</td>
            <td>
                <select id="level" name="level" data-placeholder="请选择职级" style="width:300px;" class="chzn-select game">
                    <option value="0"></option>
                    <?php if (!empty($levelList)): ?>
                        <?php foreach ($levelList as $key => $level): 
                            echo '<option value="' . $key . '">' . $level . '</option>';
                        endforeach; ?>
                    <?php endif; ?>
                </select><span id="msg_level" style="color:#f00"></span>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="submit" name="submit" class="btn btn-primary" value="确定"  /></td>
        </tr>
    </table>
    </div>
</form>

<script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".chzn-select").chosen();
</script>
</div><!--end maincontent-->
</body>
</html>