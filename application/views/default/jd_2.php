<!DOCTYPE html>
<html>
<head>
<title><?php echo $title;?></title>
<link rel="shortcut icon" href="<?php echo IMAGE_PATH;?>favicon.ico" />
<meta charset="utf-8">
<meta name="Keywords" content="招聘,HR,人力资源,JD,鸡蛋招聘,企业服务" />
<meta name="Description" content="鸡蛋招聘”诞生前，招人是一件费时费力的事；自从有了“鸡蛋招聘”，一切都变得简单。“鸡蛋招聘”，带给你不一样的招聘快感。快速便捷招到合适的人！" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/> 
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-1.7.1.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-ui.min.js"></script>

<script type="text/javascript" >
function getKeywordContent(obj,id,level,kid,jn)
{
    jQuery("#li_id_" + kid).addClass('checked');
    var isExists = jQuery("#exists_" + kid).val();
    if (isExists == undefined || isExists == 0)
    {
        $.ajax({
            'type':'POST',
            'url':'<?php echo APP_SITE_URL;?>/jdtools/random_jd_content',
            'data':"id="+id+"&level="+level+"&kid="+kid+"&jn="+jn,
            'dataType':'json',
            success:function(data)
            {
                //var $jqLastTr = jQuery("#show_table_id tr:last-child");   //获得最后一个tr的对象
                //var lastTrVal = $jqLastTr.find(".td_num").text();   //获得最后一个tr的序号
                if (data.isRandom == 1 && data.content != '')
                {
                    var displayJdContent = "<span class=\"more\" title=\"换个描述\" onclick=\"randomJdContent(this,"+id+","+level+","+kid+","+data.id+",'"+jn+"');\"></span>";
                }else{
                    var displayJdContent = '';
                }
                var randomHtml = '<div id="div_'+data.kid+'" class="msg">'+
                                    '<input id="exists_'+kid+'" name="exists_'+kid+'" type="hidden" value="1" />'+
                                    '<input id="change_'+data.id+'" name="change_'+data.id+'" type="hidden" value="0" />'+
                                    '<input id="moveid_'+data.id+'" type="hidden" value="'+data.id+'" />'+
                                    '<input id="keyword_'+data.id+'" name="keyword_'+data.id+'" type="hidden" value="'+data.keyword+'" />'+
                                    '<textarea style="height:75px;" name="content['+data.id+']" id="content_'+data.id+'" onclick="editTrCommand(this,'+data.id+');" onblur="onBlurCommand(this,'+data.id+')">' + '（' + data.keyword + '）' + data.content + '</textarea>'+
                                    '<div class="fns">'+
                                        displayJdContent+
                                        '<span class="del" title="删除" onclick="removeTrOpra(this,'+data.id+','+data.kid+');"></span>'+
                                    '</div>'+
                                '</div>';
                //jQuery("#show_table_id").append(randomHtml);

                var $txt = jQuery("#div_"+data.kid).html(randomHtml).find('textarea');
                textareaMonitor( $txt );
                textareaMonitor.update( $txt );

        		$('.maincon .msg').removeClass('selected');
        		posMsg($('#div_'+data.kid));
            }
        });
    }else{
    	$('.maincon .msg').removeClass('selected');
    	posMsg($('#div_'+kid));
    }
}
function posMsg( $msg ){
	var offset = $msg.offset();
    if(offset == null) return;
    var pos = $(window).height() - 100 - offset.top-$msg.height();
	$msg.addClass('selected');
	if( pos<0 ){ 
		document.body.scrollTop = document.documentElement.scrollTop = Math.abs(pos);
	} else if( offset.top<document.body.scrollTop+document.documentElement.scrollTop ){
		document.body.scrollTop = document.documentElement.scrollTop = offset.top;
	}
}
//点击换描述
function randomJdContent(obj,id,level,kid,aid,jn)
{
    $.ajax({
        'type':'POST',
        'url':'<?php echo APP_SITE_URL;?>/jdtools/random_jd_content',
        'data':"id="+id+"&level="+level+"&kid="+kid+"&aid="+aid+"&jn="+jn,
        'dataType':'json',
        success:function(data)
        {
            //var currentTrId = jQuery(obj).parent().parent().attr('id');  //获得本身tr的序号
            //var tdNum = jQuery(obj).parent().parent().find(".td_num").text();  //获得本身td_num的序号
            var randomHtml = '<div id="div_'+data.kid+'" class="msg">'+
                                '<input id="exists_'+kid+'" name="exists_'+kid+'" type="hidden" value="1" />'+
                                '<input id="change_'+data.id+'" name="change_'+data.id+'" type="hidden" value="0" />'+
                                '<input id="moveid_'+data.id+'" type="hidden" value="'+data.id+'" />'+
                            	'<input id="describe_kid_'+data.id+'" name="describe_kid_'+data.id+'" type="hidden" value="'+data.kid+'" />'+
                                '<input id="keyword_'+data.id+'" name="keyword_'+data.id+'" type="hidden" value="'+data.keyword+'" />'+
                                '<textarea style="height:75px;" name="content['+data.id+']" id="content_'+data.id+'" onblur="onBlurCommand(this,'+data.id+')">' + '（' + data.keyword + '）' + data.content + '</textarea>'+
                                '<div class="fns">'+
                                    "<span class=\"more\" title=\"换个描述\" onclick=\"randomJdContent(this,"+id+","+level+","+kid+","+data.id+",'"+jn+"');\"></span>"+
                                    '<span class="del" title="删除" onclick="removeTrOpra(this,'+data.id+','+data.kid+');"></span>'+
                                '</div>'+
                            '</div>';
            //jQuery("#"+currentTrId).html(randomHtml);
            

            var $txt = jQuery("#div_"+data.kid).html(randomHtml).find('textarea');
            textareaMonitor( $txt );
            textareaMonitor.update( $txt );
        }
    });
}
//上移指令
function prevMoveTrCommand(obj){
    var $jqFirstTr = jQuery("#show_table_id tr:first-child");   //获得第一个tr的对象
    var firstTrVal = $jqFirstTr.find(".td_num").text();   //获得第一个tr的序号
    var objVal = jQuery(obj).parent().parent().find(".td_num").text();  //获得本身tr的序号
    if(objVal == firstTrVal){  //判断是否在把第一行向上移
        return;
    }else{
        prevMoveTrOpra(obj);   //调用上移操作方法
    }
}
//上移操作
function prevMoveTrOpra(obj){
    var $jqObj = jQuery(obj).parent().parent();  //获得本身tr的信息
    var $trOObjt = jQuery("#hide_tr_id").append($jqObj.html());  //把本身tr放入临时信息
    var $jqSublObj = jQuery(obj).parent().parent().prev();   //获得上一个tr的信息
    $jqSublObj.find(".td_num").text(Number($jqSublObj.find(".td_num").text())+1);    //把上一个tr序号加1
    $jqObj.html("").append($jqSublObj.html());   //把本身tr清空并插入上一个信息
    $trOObjt.find(".td_num").text(Number($trOObjt.find(".td_num").text())-1);    //把本身tr序号减1
    $jqSublObj.html("").append($trOObjt.html());   //把上一个tr清空并插入临时保存的tr信息
    jQuery("#hide_tr_id").html("");   //清空临时tr信息
}
//下移指令
function nextMoveTrCommand(obj){
    var $jqLastTr = jQuery("#show_table_id tr:last-child");   //获得最后一个tr的对象
    var lastTrVal = $jqLastTr.find(".td_num").text();   //获得最后一个tr的序号
    var objVal = jQuery(obj).parent().parent().find(".td_num").text();  //获得本身tr的序号
    if(objVal == lastTrVal){  //判断是否想把最后一行往下移
        return;
    }else{
        nextMoveTrOpra(obj);    //调用下移操作方法
    }
}
//下移操作
function nextMoveTrOpra(obj){
    var $jqObj = jQuery(obj).parent().parent();  //获得本身tr的信息
    var $trOObjt = jQuery("#hide_tr_id").append($jqObj.html());  //把本身tr放入临时信息
    var $jqSiblObj = jQuery(obj).parent().parent().next();   //获得下一个tr的信息
    
    $jqSiblObj.find(".td_num").text(Number($jqSiblObj.find(".td_num").text())-1);    //把下一个tr序号减1
    $jqObj.html("").append($jqSiblObj.html());   //把本身tr清空并插入下一个tr信息
    $trOObjt.find(".td_num").text(Number($trOObjt.find(".td_num").text())+1);    //把本身tr序号加1
    $jqSiblObj.html("").append($trOObjt.html());   //把下一个tr清空并插入临时保存的tr信息
    jQuery("#hide_tr_id").html("");    //清空临时tr信息
}
//编辑
function editTrCommand(obj,pos){
    $("#change_" + pos).val(1);
    //$("#content_" + pos).removeAttr("readonly");
    $("#content_" + pos).css('background-color','#6ECAFF');
}
//删除
function removeTrOpra(obj,aid,kid){
    var currentTrId = jQuery(obj).parent().parent().attr('id');  //获得本身tr的序号
    textareaMonitor.remove( $("#" + currentTrId).find('textarea') );
    $("#" + currentTrId).html('');
    $("#li_id_" + kid).removeClass('checked');
}
//离开光标
function onBlurCommand(obj,pos){
    //$("#content_" + pos).attr("readonly", true);
    $("#content_" + pos).css('background-color','white');
}
function nextBtnSubmit(){
    $("#userform").submit();
}

// textarea监听器
function textareaMonitor (textarea,callback) {
    textarea = $(textarea);
    var guid = textareaMonitor.guid++,
        monitor = $('<div data-role="monitor_' + guid + '">&nbsp;</div>').css({
        position    : 'absolute',
        top         : 0,
        right       : 0,
        width       : textarea.width(),
        fontFamily  : textarea.css('font-family'),
        fontSize    : textarea.css('font-size'),
        fontWeight  : textarea.css('font-weight'),
        padding     : textarea.css('padding'),
        margin      : textarea.css('margin'),
        lineHeight  : textarea.css('line-height'),
        wordBreak   : 'break-all',
        display     : 'none',
        visibility  : 'hidden'
    }).appendTo('body'),
        timer;
    textarea.css('height',monitor.height());
    textarea.attr('data-guid',guid).on('focus',function () {
        var element = this;
        monitor.show();
        timer = setInterval(function () {
            textareaMonitor.update(textarea);
            callback && callback(element,monitor);
        },1)
    }).on('blur',function () {
        clearInterval(timer);
        monitor.hide();
    })
}
textareaMonitor.update = function (textarea) {
    var guid = textarea.data('guid'),
        monitor = $('[data-role=monitor_' + guid + ']'),
        v = htmlEscape(textarea.val()).replace(/\n/g,"<br /><span style='margin-left:-4px;'>&nbsp;</span>");
    monitor.html(v);
    if (monitor.html() == '') {
        monitor.html('&nbsp;');
    };
    textarea.css('height',monitor.height());
}
textareaMonitor.remove = function (textarea) {
    var guid = textarea.data('guid'),
        monitor = $('[data-role=monitor_' + guid + ']');
        monitor.remove();
}

textareaMonitor.guid = 0;

// escape
function htmlEscape (html) {
    html = '' + html;
    var htmlEscapeSymbol = htmlEscape.htmlEscapeSymbol;
    for (var key in htmlEscapeSymbol) {
        html = html.replace(key,htmlEscapeSymbol[key]);
    }
    return html;
}

htmlEscape.htmlEscapeSymbol = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '/': '&#x2F;'
}
</script>
</head>
<body class="mainPage">
    <div class="header">
        <div class="con">
            <h1 class="logo"><a href="<?php echo $webSiteUrl;?>"><img src="<?php echo IMAGE_PATH;?>logo_s.png" alt=""/></a></h1>
            <!--
			<div class="steps step2">
                <span class="step s1">职位信息<span>1</span></span>
                <span class="step s2 on">岗位职责<span>2</span></span>
                <span class="step s3">任职资格<span>3</span></span>
            </div>
			-->
        </div>
    </div>
    <form method="post" id="userform" name="userform" class="horizontal" enctype='multipart/form-data' action="<?php echo APP_SITE_DOMAIN.$_SERVER['REQUEST_URI'];?>">
    <div class="main">
        <div class="sidebar">
            <h3>招聘要求填写项：</h3>
            <ul>
                <?php echo $keyword_content;?>
            </ul>
        </div>
        <div class="maincon">
            <h3 class="title"><?php echo $displayPosLevel;?><span>&nbsp;的招聘要求：</span></h3>
			<div class="msgs">
            <?php
                $tr_content = '';
                if (!empty($describeList))
                {
                    $key = $last_kid = 0;
                    foreach ($describeList as $key_kid => $value)
                    {
                        $p = ++$key;
                        if ($p > 3)
                        {
                            //$last_kid = $value['kid'];
                            //break;
                            $tr_content .= '<div id="div_' . $value['kid'] . '" class="msg"></div>';
                            continue;
                        }
                        $aid = isset($value['id']) ? $value['id'] : 0;
                        $kid = isset($value['kid']) ? $value['kid'] : 0;
                        $isRandom = isset($randomKeywordList[$value['keyword']]) ? 1 : 0;
                        $displayJdContent = $isRandom && !empty($value['content']) ? "<span class=\"more\" title=\"换个描述\" onclick=\"randomJdContent(this,{$id},{$level},{$kid},{$aid},'{$isSkipRandomKeywordJson}');\"></span>" : '';
                        $tr_content .= '<div id="div_' . $kid . '" class="msg">
                            <input id="exists_'.$kid.'" name="exists_'.$kid.'" type="hidden" value="1" />
                            <input id="change_'.$aid.'" name="change_'.$aid.'" type="hidden" value="0" />
                            <input id="moveid_'.$aid.'" type="hidden" value="'.$aid.'" />
                            <input id="describe_kid_'.$aid.'" name="describe_kid_'.$aid.'" type="hidden" value="'.$kid.'" />
                            <input id="keyword_'.$aid.'" name="keyword_'.$aid.'" type="hidden" value="'.$value['keyword'].'" />
                            <textarea style="height:75px;" name="content['.$aid.']" id="content_' . $aid . '" onclick="editTrCommand(this,'.$aid.');" onblur="onBlurCommand(this,'.$aid.')">' . '（'.$value['keyword'].'）' . str_replace(array("\r\n", "\r", "\n"), '', $value['content']) . '</textarea>
                            <div class="fns">
                                '.$displayJdContent.'
                                <span class="del" title="删除" onclick="removeTrOpra(this,'.$aid.','.$kid.');"></span>
                            </div>
                        </div>';
                    }
                    //$tr_content .= '<div id="div_' . $last_kid . '" class="msg"></div>';
                }
                echo $tr_content;
            ?>
			</div>
        </div>
    </div>
    <div class="footer">
        <div class="con">
            <p> <?php echo $pageBaseList['welcome_msg'];?><br/><?php echo $pageBaseList['contact_us'];?> <br/><?php echo $pageBaseList['qq_group'];?> </p>
            <div class="bns">
                <a href="<?php echo APP_SITE_URL;?>" class="white_bn">上一步</a><a href="#" class="blue_bn" onclick="nextBtnSubmit();">为我推荐</a>
            </div>
        </div>
    </div>
    </form>
    <span style="display:none;"><script src="http://s4.cnzz.com/stat.php?id=1254156333&web_id=1254156333" language="JavaScript"></script></span>
</body>
<style>
    textarea{resize:none;overflow: hidden;}
</style>
<script>
$(function(){
	$('.maincon .msg:not(:empty)').each(function(i, $msg){
		var id = 'li_id_'+$msg.id.split('_')[1];
		$('#'+id).addClass('checked');
	});
	$('.maincon .msg').on('click',function(){
		$('.maincon .msg').removeClass('selected');
		$(this).addClass('selected');
	});
	$('.msgs').sortable();

    $('.maincon .msg textarea').each(function() {
            textareaMonitor( $(this) );
            textareaMonitor.update( $(this) );
    });
});

</script>
</html>
