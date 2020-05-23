<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title;?></title>
    <link rel="shortcut icon" href="<?php echo IMAGE_PATH;?>favicon.ico" />
    <meta charset="utf-8">
	<meta name="Keywords" content="职位描述,招聘,HR,人力资源,JD,奔跑吧JD" />
	<meta name="Description" content="只需点点鼠标，改改文字，即可快速生成内容详实的职位描述。涵盖全行业全职位，职位描述的内容依据大数据挖掘不断优化！" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>jd_css.css" type="text/css"/>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH;?>jquery.zclip.js"></script>
    <style type="text/css">
        .line{margin-bottom:20px;}
        /* 复制提示 */
        .copy-tips{position:fixed;z-index:999;bottom:50%;left:50%;margin:0 0 -20px -80px;background-color:rgba(0, 0, 0, 0.2);filter:progid:DXImageTransform.Microsoft.Gradient(startColorstr=#30000000, endColorstr=#30000000);padding:6px;}
        .copy-tips-wrap{padding:10px 20px;text-align:center;border:1px solid #F4D9A6;background-color:#FFFDEE;font-size:14px;}
    </style>
    <script type="text/javascript" >
    <!--
    $(document).ready(function(){
    /* 定义所有class为copy标签，点击后可复制被点击对象的文本 */
        $(".copy").zclip({
            path: "<?php echo JS_PATH;?>ZeroClipboard.swf",
            copy: function(){
                return $("#jd_content").text();
            },
            beforeCopy:function(){/* 按住鼠标时的操作 */
                $(this).css("color","orange");
            },
            afterCopy:function(){/* 复制成功后的操作 */
                var $copysuc = $("<div class='copy-tips'><div class='copy-tips-wrap'>☺ 复制成功</div></div>");
                $("body").find(".copy-tips").remove().end().append($copysuc);
                $(".copy-tips").fadeOut(3000);
            }
        });
    });
    -->
    </script>
</head>
<body class="mainPage jdDetails">
    <div class="header">
        <div class="con">
            <!--<h1 class="logo"><a href="<?php echo $webSiteUrl;?>"><img src="<?php echo IMAGE_PATH;?>logo_s.png" alt=""/></a></h1>-->
            <div class="steps step4" style="display:none;">
                <span class="step s1">职位信息<span>1</span></span>
                <span class="step s2">岗位职责<span>2</span></span>
                <!--<span class="step s3">任职资格<span>3</span></span>-->
            </div>
        </div>
    </div>

    <form method="post" id="userform" name="userform" class="horizontal" enctype='multipart/form-data' onsubmit="return true;" action="<?php echo APP_SITE_DOMAIN.$_SERVER['REQUEST_URI'];?>">
    <div class="main " id="jd_content"> 
        <div class="maintip3">
            <h2><?php echo (isset($data['baseInfo']['company']) ? $data['baseInfo']['company'] : '') . ' 招聘';?></h2>
        	<div class="positionName"><?php echo $displayPosLevel;?></div>
        	<!--<div class="contactEmail">联系邮箱：<?php echo isset($data['baseInfo']['email']) ? $data['baseInfo']['email'] : '';?></div>-->
        </div>
        <div class="details details2">
            <?php
                $tr_content = '';
                if (isset($data['describe']) && !empty($data['describe']))
                {
                    foreach ($data['describe'] as $key => $value)
                    {
                        $p = $key+1;
                        $tr_content .= '<h3>' . $value['keyword'] . '：</h3>';
                        $value['content'] = str_replace(array('"'),'',$value['content']);
                        /*$tr_content .= '<p id="p_' . $p . '">
                            <textarea style="width:800px;height:75px;" name="content[\"'.$value['id'].'\"]" id="content_' . $p . '" readonly="true">' . $value . '</textarea>
                        </p>';*/
                        $tr_content .= '<p id="p_' . $p . '">' . $value['content'] . '</p>';
                    }
                }
                echo $tr_content;
            ?>
        </div>
    </div>
    <div class="footer" style="display:none;">
        <div class="con">
            <p> <?php echo $pageBaseList['welcome_msg'];?><br/><!--<?php echo $pageBaseList['contact_us'];?> <br/>--><?php echo $pageBaseList['qq_group'];?> </p>
            <div class="bns">
                <a href="#" class="white_bn copy">一键复制</a><!--<a href="#" class="blue_bn">保存</a>-->
            </div>
        </div>
    </div>
    </form>
    <span style="display:none;"><script src="http://s4.cnzz.com/stat.php?id=1254156333&web_id=1254156333" language="JavaScript"></script></span>
</body>
</html>