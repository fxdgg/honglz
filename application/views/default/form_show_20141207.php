<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript">var NREUMQ=NREUMQ||[];NREUMQ.push(["mark","firstbyte",new Date().getTime()]);</script>
  <title><?php echo $formTitle;?></title>
  <meta name="description" content="<?php echo $formContent;?>">
  <link href="<?php echo CSS_PATH;?>form_1.css" media="screen" rel="stylesheet" />
  <style type="text/css">
    .entry-container {
        background-image: url(<?php echo IMAGE_PATH;?>noisy_grid_1.png);
        background-repeat: repeat;
        background-size: auto;
    }
    .bg-image {

    }
</style>
<style type="text/css">
    .entry-container .banner {
        background-color: #F4F4F4;
        color: #AAAAAA;
    }

    .entry-container form .form-name {
        font-size: 22px; color: #000; font-weight: normal;
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
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script>
</head>
<body class="entry-container bg-image">
  <form accept-charset="UTF-8" action="<?php echo APP_SITE_URL;?>/<?php echo $formScriptName;?>/submit" onsubmit="return submitForm();" class="center" id="new_entry" method="post">
    <div style="margin:0;padding:0;display:inline">
        <input name="unionId" type="hidden" value="<?php echo $unionId;?>" />
        <input id="emailExist" type="hidden" value="0" />
    </div>
      <div class="banner">
          <span class="banner-text banner-content"></span>
              <div class="qrcode-box hide">
      <img src="<?php echo IMAGE_PATH;?>qrcode_<?php echo $formScriptName;?>.jpg" />
      <div class="qrcode-desc">扫一扫分享给好友</div>
    </div>
<div class="corner-qrcode pull-right">
  <img alt="扫一扫分享给好友" src="<?php echo IMAGE_PATH;?>half-qrcode_1.png" title="扫一扫分享给好友" />
</div>


    </div>

  <h1 class="form-name"><?php echo $formTitle;?></h1>
  <div class="form-description"><?php echo $formContent;?></div>

  <fieldset>
    <div class="form-content">


<div class="form-message hide"></div>
    <?php $field_id = 1; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" /><?php if (isset($formConfig[$field_id]['must']) && $formConfig[$field_id]['must'] == 1): ?><span style="color:#f00"><strong>*</strong></span><?php endif;?>
                    <span id="msg_<?php echo $field_id;?>" style="color:#f00"></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 2; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <select id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]">
                        <option value="1">男</option>
                        <option value="2">女</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 3; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="this.value=this.value.replace(/[^\u4E00-\u9FA5]/g,'')" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 4; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($beforeProduct)) { foreach ($beforeProduct as $value) {
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
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
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
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
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
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
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
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" onfocus="WdatePicker({dateFmt:'yyyy-MM'})" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 9; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 10; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 11; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <div class="clearfix radio-group" data-role="controlgroup">
                        <?php if(!empty($nowPosition)) { foreach ($nowPosition as $value) {
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
    <?php $field_id = 12; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
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
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" onblur="return checkMobile('entry_field_<?php echo $field_id;?>', 'entry_field_<?php echo $field_id;?>', <?php echo $field_id;?>);" maxlength=11 />
                    <span id="msg_<?php echo $field_id;?>" style="color:#f00"></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 14; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <textarea class="input-xxlarge" id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 15; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <textarea class="input-xxlarge" id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 16; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength=15 />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 17; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" onblur="return checkMobile('entry_field_<?php echo $field_id;?>', 'entry_field_<?php echo $field_id;?>', <?php echo $field_id;?>);" maxlength=11 />
                    <span id="msg_<?php echo $field_id;?>" style="color:#f00"></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 18; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onblur="return checkEmail('entry_field_<?php echo $field_id;?>', 'entry_field_<?php echo $field_id;?>', <?php echo $field_id;?>);" /><?php if (isset($formConfig[$field_id]['must']) && $formConfig[$field_id]['must'] == 1): ?><span style="color:#f00"><strong>*</strong></span><?php endif;?>
                    <span id="msg_<?php echo $field_id;?>" style="color:#f00"></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 19; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <input id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" type="text" value="" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" />
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    <?php $field_id = 20; if(isset($formConfig[$field_id])):?>
    <div class="field" data-api-code="field_<?php echo $field_id;?>">
        <div class="control-group " >
            <label class="control-label field_title" data-role="collapse_toggle" for="entry_field_<?php echo $field_id;?>"><?php echo $formConfig[$field_id]['name'];?>：</label>
            <div class="field_content">
                <div class="help-block"><p><?php echo $formConfig[$field_id]['help'];?></p></div>
                <div class="controls">
                    <textarea class="input-xxlarge" id="entry_field_<?php echo $field_id;?>" name="entry[field_<?php echo $field_id;?>]" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>

<script>
//<![CDATA[
    $(function() {
        String.locale = 'zh-CN';
        //var rules = $.parseJSON('{}');
        //new GoldenData.FormLogic(rules).run();
    });
    function checkMobile(obj, labelName, fieldId) {
        var objName = eval("document.all."+obj);
        var re = /^1\d{10}$/
        if (re.test(objName.value)) {
        } else {
            document.getElementById('msg_' + fieldId).innerHTML = "请输入正确的手机号";
            objName.focus();
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
            objName.focus();
            return false;
        }
        checkEmailExist(obj, labelName, fieldId);
        
        document.getElementById('msg_' + fieldId).innerHTML = "";
        document.getElementById('emailExist').value = 0;
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
                document.getElementById('msg_18').innerHTML = "";
                document.getElementById('emailExist').value = 0;
                return true;
            }
            document.getElementById('msg_18').innerHTML = "[<?php echo $formConfig[18]['name'];?>]已被注册";
            document.getElementById('emailExist').value = 1;
            return false;
        }
    }
    function submitForm(){
        var form_name = document.getElementById('new_entry');
        var nickname = document.getElementById('entry_field_1').value;
        var email = document.getElementById('entry_field_18').value;
        var emailExist = document.getElementById('emailExist').value;
        if (nickname.length <= 0)
        {
            document.getElementById('msg_1').innerHTML = '[<?php echo $formConfig[1]["name"];?>]不能为空';
            document.getElementById('entry_field_1').focus();
            return false;
        }
        else if (email == ''){
            document.getElementById('msg_18').innerHTML = '[<?php echo $formConfig[18]["name"];?>]不能为空';
            document.getElementById('entry_field_18').focus();
            return false;
        }else if (emailExist == '1'){
            document.getElementById('msg_18').innerHTML = '[<?php echo $formConfig[18]["name"];?>]已被注册';
            document.getElementById('entry_field_18').focus();
            return false;
        }
        document.getElementById('commit').setAttribute("data-disable-with", "提交中...");
        return true;
    }
//]]>
</script>

        <div class="field submit-field ">
          <div class="value">
            <input class="submit" id="commit" name="commit" type="submit" value="提交" />
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
  <footer class='published'>
    <div class="center hide">
      <!--<a class="powered-by" href="https://jinshuju.net/?utm_campaign=jul&amp;utm_medium=bottom_logo&amp;utm_source=published_form&amp;utm_term=H63pRw" target="_blank">
          <i class="powered-logo"></i>
          <p>Powered By <?php echo APP_SITE_NAME_BACKEND;?></p>
</a>-->    </div>
  </footer>

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