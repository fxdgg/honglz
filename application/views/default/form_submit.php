
<!DOCTYPE html>
<html>
<head><script type="text/javascript">var NREUMQ=NREUMQ||[];NREUMQ.push(["mark","firstbyte",new Date().getTime()]);</script>
  <title><?php echo $formTitle;?></title>
  <meta name="description" content="<?php echo $formContent;?>">
  <link href="<?php echo CSS_PATH;?>published_1.css" media="screen" rel="stylesheet" />
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

<meta content="authenticity_token" name="csrf-param" />
<meta content="<?php echo $unionId;?>" name="csrf-token" />
</head>
<body class="entry-container bg-image">
  <div class="submit-modal">
  <div class="align-middle">
    <p class="message">提交成功！</p>



    <p>
            <!--请 <a href="/signup?auth_token=zLIMruYK0JuzTe8ZRLSo7g&amp;utm_campaign=jul&amp;utm_medium=link&amp;utm_source=submitted&amp;utm_term=H63pRw">注册</a> 或 <a href="/login?auth_token=zLIMruYK0JuzTe8ZRLSo7g">登录</a> 以便日后查阅您填写的这条数据-->
      
    </p>
  </div>
</div>

  <footer class='published'>
    <div class="center hide">
      <!--<a class="powered-by" href="https://jinshuju.net/?utm_campaign=jul&amp;utm_medium=bottom_logo&amp;utm_source=submitted&amp;utm_term=H63pRw" target="_blank">
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
		NREUMQ.f=function() {
			NREUMQ.push(["load",new Date().getTime()]);
			var e=document.createElement("script");
			e.type="text/javascript";
			e.src=(("http:"===document.location.protocol)?"http:":"https:") + "//" +
			  "js-agent.newrelic.com/nr-100.js";
			document.body.appendChild(e);
			if(NREUMQ.a)NREUMQ.a();
		};
		NREUMQ.a=window.onload;window.onload=NREUMQ.f;
	};
//NREUMQ.push(["nrfj","beacon-2.newrelic.com","9036a76073","1704671","JVwKFRNZVVtTF0kTQARfDRIJU11oUAoUDkZJQBECAlNKRA==",7,41,new Date().getTime(),"","","","",""]);</script></body>
</html>