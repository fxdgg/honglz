<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?>-后台管理系统</title>
<link href="<?php echo CSS_PATH;?>css.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>common.js"></script>
<script src="<?php echo JS_PATH;?>jquery.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>My97DatePicker/WdatePicker.js"></script>
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
<script charset="utf-8" src="<?php echo APP_SITE_DOMAIN;?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo APP_SITE_DOMAIN;?>/editor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function(K) {
        window.editor = K.create('#editor_id');
    });

    function change_item(obj, id)
    {
        if(obj.value==0){
            for (var i=1; i<=2; i++)
            {
                $('#group_div_id_' + i).hide();
            }
            //$('#editor_id').html('');
        }else{
            for (var i=1; i<=2; i++)
            {
                if (i == id)
                {
                    if (id == 2)
                    {
                        $('#content_spanid').html("招聘内容");
                        $('#welcome_title_divid').html("招聘录入");
                    }
                    $('#group_div_id_' + id).show();
                }else{
                    $('#group_div_id_' + i).hide();
                }
            }
            //var tpl="赠送【】一张，价值【】元。\n"+"有效期至"+$('#activity_end_time').val()+"。\n";
            //$('#editor_id').html(tpl);
        }
    }
</script>
</head>

<body bgcolor="#DDEEF2">
<form action='<?php echo APP_SITE_URL;?>/admin/recruit_insert' method='post'>
<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center" bgcolor="#BBDDE5" class="margin_top_10">
   <tr>
    <td bgcolor="#FFFFFF" colspan="4"></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" height="31" colspan="4">
        <div align="center" class="black_14_bold" id="welcome_title_divid">招聘录入</div>
    </td>
  </tr>
  <!--<tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>社团列表：</b></td>
    <td width="35%" colspan="3">
        <input type="checkbox" id="checkall" onclick="reverse_check('check_union_ids[]')" title="全选/不选 所有社团" />全选/反选<br />
        <?php 
            if (is_array($unionManageList) && !empty($unionManageList))
            {
                $unionKey = 0;
                foreach ($unionManageList as $unionId => $unionName)
                {
                    $skip = ($unionKey > 0 && $unionKey % 5 == 0) ? '<br />' : '';
                    echo '<input type="checkbox" id="check_union" name="check_union_ids[]" value="'.$unionId.'" />'.$unionName.$skip;
                    ++$unionKey;
                }
            }
        ?>
    </td>
  </tr>-->
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b>舶来类型：</b></td>
    <td width="35%" colspan="3">
        <select id="group_id" name="group_id" onchange="change_item(this, this.value)">
            <!--<option value="0">文字类</option>
            <option value="1">群福利</option>-->
            <option value="2" selected="selected">招聘</option>
        </select>
    </td>
  </tr>
  <tr id="group_div_id_2" bgcolor="#FFFFFF" style="">
      <td width="15%" align="right" height="25"><b>招聘分类：</b></td>
      <td width="35%" colspan="3">
          <?php
              $category_str = '';
              if (!empty($category_list_config))
              {
                  foreach ($category_list_config as $category_type => $category_data)
                  {
                      $category_str .= '<select id="'.$category_type.'" name="'.$category_type.'" style="width:120px;">
                        <option value="-1">------'.$category_data[0]['typeName'].'------</option>';
                      if (!empty($category_data))
                      {
                          foreach ($category_data as $item_name)
                          {
                              $category_str .= '<option value="'.$item_name['cid'].'">'.$item_name['cname'].'</option>';
                          }
                      }
                      $category_str .= '</select>';
                  }
              }
              echo $category_str;
          ?>
     </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td width="15%" align="right" height="25"><b><span id="content_spanid">招聘内容：</span></b></td>
    <td width="35%" colspan="3">
        <textarea id="editor_id" name="content" style="width:700px;height:300px;"></textarea>
    </td>
  </tr>
   
  <tr bgcolor="#FFFFFF">
    <td colspan='4' align="center">
        <input type="submit" name="editPosts" id="editPosts" width="41" height="21" value="提交"/>
    </td>
  </tr>
</table>

<table width="98%" border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td align="right" height="25">&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>