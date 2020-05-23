<!DOCTYPE html>
<html>
<head>
<title><?php echo $title;?></title>
<link rel="shortcut icon" href="<?php echo IMAGE_PATH;?>favicon.ico" />
<meta name="Keywords" content="招聘,HR,人力资源,JD,鸡蛋招聘,企业服务" />
<meta name="Description" content="“鸡蛋招聘”诞生前，招人是一件费时费力的事；自从有了“鸡蛋招聘”，一切都变得简单。“鸡蛋招聘”，带给你不一样的招聘快感。快速便捷招到合适的人！" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="utf-8" />
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<meta http-equiv="expires" content="Mon, 23 Jan 1978 20:52:30 GMT">
<meta name="robots" content="all" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>chosen.css">
<style>
    .main{position: relative;}
    #smartbox{position: absolute;width:100%;font-size:16px;line-height: 28px;display: none;background: #fff;max-height: 220px;overflow-y: auto;}
    #smartbox p{border-bottom: 1px solid #ddd;padding:0 5px;}
    #smartbox p:hover{background-color: #eee}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-1.7.1.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-form-2.36.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap.js"></script>

<script type="text/javascript">
function submitForm(){
    //post 提交
    var company = $("#company").val();
    var position = $("#position").val();
    var level = $("#level").val();
    var email = $("#email").val();
    var emailExist = $("#emailExist").val();
    if(company == '')
    {
        document.getElementById('msg_company').innerHTML = "请填写公司名称";
        return false;
    }
    document.getElementById('msg_company').innerHTML = "";
    if(email == '')
    {
        document.getElementById('msg_email').innerHTML = "请输入正确的邮箱地址";
        return false;
    }
    /*if(emailExist == 1)
    {
        document.getElementById('msg_email').innerHTML = "请填写邮箱或邮箱已被注册";
        return false;
    }
    document.getElementById('msg_email').innerHTML = "";*/
    if(position == 0)
    {
        document.getElementById('msg_position').innerHTML = "请选择职位";
        return false;
    }
    document.getElementById('msg_position').innerHTML = "";
    /*if(level == 0)
    {
        document.getElementById('msg_level').innerHTML = "请选择职级";
        return false;
    }
    document.getElementById('msg_level').innerHTML = "";*/
    document.userform.submit();
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
        document.getElementById('msg_mail').innerHTML = "<strong>·</strong> 请填写邮箱";
        //document.getElementById('emailExist').value = 1;
        //objName.focus();
        return false;
    }
    document.getElementById('msg_mail').innerHTML = "";
    //checkEmailExist(obj, labelName);
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
        document.getElementById('msg_mail').innerHTML = "<strong>·</strong> 邮箱已被注册";
        document.getElementById('emailExist').value = 1;
        return false;
    }
}
</script>
</head>

<body class="index">
<form method="post" id="userform" name="userform" class="horizontal" onSubmit="return submitForm();" action="/j/m/<?php echo $step;?>">
    <div class="main">
        <h1 class="logo"></h1>
            <div class="formLine"> <span class="tilte">公司：</span> <input type="text" name="company" id="company" value="<?php echo $cookie_company;?>" onblur="return checkEmpty('company', '请填写公司名称');" /> <span id="msg_company" class="warning"></span></div>
            <div class="formLine"> <span class="tilte">邮箱：</span> <input id="jdDomain" type="hidden" value="<?php echo APP_SITE_URL;?>" /><input id="emailExist" type="hidden" value="0" /><input type="text" name="email" id="email" value="<?php echo $cookie_email;?>" onblur="return checkEmail('email', 'email');" /> <span id="msg_mail" class="warning"></span></div>
            <div class="formLine pos" style="z-index:2">
                <span class="tilte">职位：</span> 
                <input id="jdTitle" type="text" value="ios研发工程师" name="position" autocomplete="off"/>
            </div>
       <div class="formLine2 level" style="z-index:1;display:none;">
                <span class="tilte">职级：</span> 
                 <select id="level" name="level" data-placeholder="请选择职级" class="chzn-select game">
                    <option value="0"></option>
                    <?php if (!empty($levelList)): ?>
                        <?php foreach ($levelList as $key => $level): 
                            echo '<option value="' . $key . '">' . $level . '</option>';
                        endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div id="smartbox"></div>
            <button class="createBn"></button>
    </div>
    <div class="footer">
        <?php echo $pageBaseList['copyright'];?>
    </div>
</form>
</body>

<!-- <script src="<?php echo JS_PATH;?>chosen.jquery.js" type="text/javascript"></script> -->
<span style="display:none;"><script src="http://s4.cnzz.com/stat.php?id=1254156333&web_id=1254156333" language="JavaScript"></script></span>
<script type="text/javascript">
    // $(".chzn-select").chosen();
	$('.createBn').on('click',function(){
		this.form.submit();
	});

    $(function() {
        var $input = $('#jdTitle'),
            $smartbox = $('#smartbox'),
            tid;

        $input.on('keyup',function() {
            var query = $(this).val();
            tid && clearTimeout(tid);
            tid = setTimeout(function() {
                $.post('<?php echo APP_SITE_URL;?>/jdtools/search_jd_position', {
                    p:query
                }, function(d) {
                    if( d.status ){
                        var html = '';
                        $.each(d.result,function(i,v){
                            html += '<p>'+v.positionName+'</p>';
                        });
                        $smartbox.show().html(html);
                    }else{
                        $smartbox.empty();
                    }
                }, 'json');
            },300);
        });

        $smartbox.on('click','p',function(e) {
            e.stopPropagation();
            $input.val( $(this).text() );
            $smartbox.hide();
        });
        $(document.body).on('click',function() {
            $smartbox.hide()
        });
        //company=1&email=1%40q.com&position=72-%E5%89%8D%E7%AB%AF%E5%B7%A5%E7%A8%8B%E5%B8%88%EF%BC%88HTML5%EF%BC%89&level=0
        //company=1&email=1%40q.com&position=ANDROID%E9%A9%B1%E5%8A%A8%E5%B7%A5%E7%A8%8B%E5%B8%88&level=0
    });
</script>
</div><!--end maincontent-->
</body>
</html>
