<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript">var NREUMQ=NREUMQ||[];NREUMQ.push(["mark","firstbyte",new Date().getTime()]);</script>
  <title><?php echo $formTitle;?></title>
  <meta name="description" content="<?php echo $formContent;?>">
  <?php if ($isView == 'mobile'):?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <link rel="apple-touch-icon-precomposed" href="<?php echo IMAGE_PATH;?>favicon.png">
  <link href="<?php echo CSS_PATH;?>mobile_published_1.css" media="screen" rel="stylesheet" />
  <script src="<?php echo JS_PATH;?>mobile_form_1.js"></script>
  <?php else:?>
  <link href="<?php echo CSS_PATH;?>published_1.css" media="screen" rel="stylesheet" />
  <?php endif;?>
  <style type="text/css">
    <?php if ($isView == 'mobile'):?>
    [data-role=header] {
        background: #659199;
        border-color: #659199;
    }
    .header-image[data-role=header] {
        background-color: #F4F4F4;
        color: #AAAAAA;
    }
    <?php else:?>
    .entry-container {
        /*background-image: url(<?php echo IMAGE_PATH;?>noisy_grid_1.png);*/
        background-repeat: repeat;
        background-size: auto;
    }
    .bg-image {

    }
    <?php endif;?>
</style>
<?php if ($isView == 'pc'):?>
<style type="text/css">
    .entry-container .banner {
        background-color: #3385ff;
        color: #3385ff;
    }

    .entry-container form .form-name {
        font-size: 28px; color: #000; font-weight: bolder;line-height:80px;
    }

    .entry-container form .form-description {
        font-size: 12px; color: #666; font-weight: normal;
    }

    .entry-container form .field label.control-label {
        font-size: 14px; color: #333; font-weight: bold;
    }

    .entry-container form .field .field_content label,
    .entry-container form .field .attachment .status .file-name,
    .entry-container form .field .attachment label {
    font-size: 14px; color: #333; font-weight: bold;
        font-size: 12px;
        font-weight: normal;
    }
    .entry-container form .field .field_content .image-choices label,
    .entry-container form fieldset .goods-items .goods-item .text-wrapper .dimensions .dimension-options label {
          color: inherit;
    }

    .entry-container form .field .help-block{
        font-size: 12px; color: #777; font-weight: normal;
    }

    .entry-container form .field.section-break label {
        font-size: 16px; color: #333; font-weight: bold;
    }

    .entry-container form .field.section-break .help-block {
        font-size: 12px; color: #999; font-weight: normal;
    }

    .entry-container .message {
        font-size: 22px; color: #000; font-weight: normal;
    }
    .hide {
        display:none;
    }
</style>
  <!--[if lte IE 8]>
    <link href="<?php echo CSS_PATH;?>lte-ie8_1.css" media="screen" rel="stylesheet" />
    <script src="<?php echo JS_PATH;?>html5_1.js"></script>
  <![endif]-->
  <!--[if lte IE 7]>
    <link href="<?php echo CSS_PATH;?>lte-ie7_1.css" media="screen" rel="stylesheet" />
  <![endif]-->
  <!--[if IE 6]> <link href="<?php echo CSS_PATH;?>lte-ie6_1.css" media="screen" rel="stylesheet" /> <![endif]-->
  <script src="<?php echo JS_PATH;?>application_1.js"></script>
  <!--[if IE 6]>
    <script src="<?php echo JS_PATH;?>fix-ie6_1.js"></script>
  <![endif]-->
<?php endif;?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script>
</head>
<?php if ($isView == 'pc'):?>
<body class="entry-container bg-image">
    <form accept-charset="UTF-8" action="<?php echo APP_SITE_URL;?>/<?php echo $formScriptName;?>/submit" onsubmit="return submitForm();" class="center" id="new_entry" method="post">
        <div style="margin:0;padding:0;display:inline">
            <input name="unionId" type="hidden" value="<?php echo $unionId;?>" />
            <input id="emailExist" type="hidden" value="0" />
        </div>
        <div style="background-color:#fff">
        <h1 class="form-name"><?php echo $formTitle;?></h1>
        <div class="form-description"><?php echo $formContent;?></div>
        </div>
<?php else:?>
<body ontouchstart="" class="mobile ">
    <div data-role="page" class="page  ">
        <div class="fixed-background"></div>
        <header data-role="header" class="">
            <h1><i class="fontello-pencil2"></i><?php echo $formTitle;?></h1>
        </header>
        <div class="main" data-role="content">
            <div class="main-content">
                <form accept-charset="UTF-8" action="<?php echo APP_SITE_URL;?>/<?php echo $formScriptName;?>/submit" onsubmit="return submitForm();" class="center" id="new_entry" method="post">
                    <div style="margin:0;padding:0;display:inline">
                        <input name="unionId" type="hidden" value="<?php echo $unionId;?>" />
                        <input id="emailExist" type="hidden" value="0" />
                    </div>
                    <div style="backgroud:#CCC">
                    <h1 class="form-name"><?php echo $formTitle;?></h1>
                    <div class="form-description"><?php echo $formContent;?></div>
                    </div>
<?php endif;?>

  <fieldset>
    <div class="form-content">
    <div class="form-message hide"></div>

    <?php $field_id = 1; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <!--<label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?><?php if (isset($formConfig[$field_id]['must']) && $formConfig[$field_id]['must'] == 1): ?><span style="color:#f00"><strong>*</strong></span><?php endif;?></label>-->
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 2; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onblur="return checkEmail('entry_field_<?php echo $field_id;?>', 'entry_field_<?php echo $field_id;?>', <?php echo $field_id;?>);" />
                    <span id="msg_<?php echo $field_id;?>" style="color:#f00"></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 15; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    
    <?php $field_id = 16; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    
    <?php $field_id = 3; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($Sex)) { foreach ($Sex as $value) {
                          echo '<label onclick="" class="radio inline">
                            <input name="entry[field_',$field_id,']" type="radio" value="',$value,'" />',$value,'
                        </label>';
                        }}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 4; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($City)) { foreach ($City as $value) {
                          echo '<label onclick="" class="radio inline">
                            <input name="entry[field_',$field_id,']" type="radio" value="',$value,'" />',$value,'
                        </label>';
                        }}?>
                        <div class="other-choice-area inline">
                            <label onclick="" class="radio inline">
                            <input class="other_choice" data-field-key="field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="radio" value="" />其它</label>
                            <input class="other-choice-input input-medium" data-field-key="field_<?php echo $field_id;?>" id="entry_field_<?php echo $field_id;?>_other" name="entry[field_<?php echo $field_id;?>_other]" type="text" value="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 5; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 6; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($beforePosition)) { foreach ($beforePosition as $value) {
                          echo '<label onclick="" class="radio inline">
                            <input name="entry[field_',$field_id,']" type="radio" value="',$value,'" />',$value,'
                        </label>';
                        }}?>
                        <div class="other-choice-area inline">
                            <label onclick="" class="radio inline">
                            <input class="other_choice" data-field-key="field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="radio" value="" />其它</label>
                            <input class="other-choice-input input-medium" data-field-key="field_<?php echo $field_id;?>" id="entry_field_<?php echo $field_id;?>_other" name="entry[field_<?php echo $field_id;?>_other]" type="text" value="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 7; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($leavetime)) { foreach ($leavetime as $value) {
                          echo '<label onclick="" class="radio inline">
                            <input name="entry[field_',$field_id,']" type="radio" value="',$value,'" />',$value,'
                        </label>';
                        }}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 9; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 8; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

	<?php $field_id = 14; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($depart)) { foreach ($depart as $value) {
                          echo '<label onclick="" class="radio inline">
                            <input name="entry[field_',$field_id,']" type="radio" value="',$value,'" />',$value,'
                        </label>';
                        }}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>


    <?php $field_id = 10; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="13" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 11; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" onblur="return checkMobile('entry_field_<?php echo $field_id;?>', 'entry_field_<?php echo $field_id;?>', <?php echo $field_id;?>);" maxlength="11" />
                    <span id="msg_<?php echo $field_id;?>" style="color:#f00"></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 12; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <?php $field_id = 13; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong><?php echo $formConfig[$field_id]['name'];?>：</strong><span style="color:#777;float:right;position:relative;*top:-21px;"><?php echo $formConfig[$field_id]['help'];?></span>
            <div class="field_content">
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

    <!--<div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <strong>支付餐费</strong><span style="color:#777;float:right;position:relative;*top:-21px;">餐费AA</span>
            <div class="field_content">
                <div class="controls">
                    <a href="http://www.baidu.com" target="_blank">测试地址</a>
                </div>
            </div>
        </div>
    </div>-->



<script>
//<![CDATA[

    $(function() {
        String.locale = 'zh-CN';
        var rules = $.parseJSON('{}');
        new GoldenData.FormLogic(rules).run();
    });
    function checkMobile(obj, labelName, fieldId) {
        var objName = eval("document.all."+obj);
        var re = /^1\d{10}$/
        if (re.test(objName.value)) {
        } else {
            document.getElementById('msg_' + fieldId).innerHTML = "请输入正确的手机号";
            //objName.focus();
            return false;
        }
        document.getElementById('msg_' + fieldId).innerHTML = "";
        return true;
    }
    function checkEmail(obj, labelName, fieldId) {
        var objName = eval("document.all."+obj);
        var pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
        if (!pattern.test(objName.value)) {
            document.getElementById('msg_' + fieldId).innerHTML = "请输入正确的邮箱地址";
            document.getElementById('emailExist').value = 1;
            //objName.focus();
            return false;
        }
        checkEmailExist(obj, labelName, fieldId);
        
        //document.getElementById('msg_' + fieldId).innerHTML = "";
        //document.getElementById('emailExist').value = 0;
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
    
    function checkEmailExist(obj, labelName, fieldId){
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
                document.getElementById('msg_2').innerHTML = "";
                document.getElementById('emailExist').value = 0;
                return true;
            }
            document.getElementById('msg_2').innerHTML = "[<?php echo $formConfig[2]['name'];?>]已被注册";
            document.getElementById('emailExist').value = 1;
            return false;
        }
    }
    function submitForm(){
        var form_name = document.getElementById('new_entry');
        var nickname = document.getElementById('entry_field_1').value;
        var email = document.getElementById('entry_field_2').value;
        var emailExist = document.getElementById('emailExist').value;
        if (nickname.length <= 0)
        {
            document.getElementById('msg_1').innerHTML = '[<?php echo $formConfig[1]["name"];?>]不能为空';
            document.getElementById('entry_field_1').focus();
            return false;
        }
        else if (email == ''){
            document.getElementById('msg_2').innerHTML = '[<?php echo $formConfig[2]["name"];?>]不能为空';
            document.getElementById('entry_field_2').focus();
            return false;
        }else if (emailExist == '1'){
            document.getElementById('msg_2').innerHTML = '[<?php echo $formConfig[2]["name"];?>]已被注册';
            document.getElementById('entry_field_2').focus();
            return false;
        }
        document.getElementById('commit').setAttribute("data-disable-with", "提交中...");
        return true;
    }
//]]>
</script>

        <div class="field submit-field ">
          <div class="value">
            <input class="submit" id="commit" name="commit" type="submit" value="提交信息，去付款！" />
          </div>
        </div>
    </div>
  </fieldset>
</form>
<script>
//<![CDATA[

  $(function() {
      $("[data-role='attachment']").addClass('preview');
  })

//]]>
</script>
<?php if ($isView == 'pc'):?>
  <footer class='published'>
    <div class="center hide">
      <!--<a class="powered-by" href="https://jinshuju.net/?utm_campaign=jul&amp;utm_medium=bottom_logo&amp;utm_source=published_form&amp;utm_term=H63pRw" target="_blank">
          <i class="powered-logo"></i>
          <p>Powered By <?php echo APP_SITE_NAME_BACKEND;?></p>
</a>-->    </div>
  </footer>
<?php else:?>
    </div>
  </div>
</div>
<div class="footer hide">
  <footer>
    <!--<a data-ajax="false" href="https://jinshuju.net/?utm_campaign=jul&amp;utm_medium=bottom_logo&amp;utm_source=published_form&amp;utm_term=H63pRw" target="_blank"><span class='powered-by-text'>POWERED BY </span>金数据</a>
    <div id="form_thumbnail_default" class="hide"><img alt="Weixin thumbnail default" src="https://dn-jinshuju-assets.qbox.me/assets/weixin_thumbnail/weixin_thumbnail_default-90e4f8276e5f93eace992448a7714ef2.jpg" /></div>-->
  </footer>
</div>
<?php endif;?>
<script>
//<![CDATA[
  $(function() {
  GoldenData.fileLoadingImage = "<?php echo IMAGE_PATH;?>loading_1.gif";
  GoldenData.fileCloseImage = "<?php echo IMAGE_PATH;?>close_1.png";
  GoldenData.attachmentImage = "<?php echo IMAGE_PATH;?>attachment_1.png";
  GoldenData.zeroClipboardFlash = "https://dn-jinshuju-assets.qbox.me/assets/ZeroClipboard-f76df783d5cef1abbca4868e17b9069e.swf";
  GoldenData.tinyMCEAsset = "<?php echo CSS_PATH;?>tinymce_content.css";
  });

//]]>
</script>
<script type="text/javascript">
    if (!NREUMQ.f) {
        /*NREUMQ.f=function() {
            NREUMQ.push(["load",new Date().getTime()]);
            var e=document.createElement("script");
            e.type="text/javascript";
            e.src=(("http:"===document.location.protocol)?"http:":"https:") + "//" +
              "js-agent.newrelic.com/nr-100.js";
            document.body.appendChild(e);
            if(NREUMQ.a)NREUMQ.a();
        };
        NREUMQ.a=window.onload;window.onload=NREUMQ.f;*/
    };
    //NREUMQ.push(["nrfj","beacon-2.newrelic.com","9036a76073","1704671","JVwKFRNZVVtTF0kTQARfDRIJU11oUAoUDkZJQAwOFg==",6,275,new Date().getTime(),"","","","",""]);
</script>
</body>
</html>