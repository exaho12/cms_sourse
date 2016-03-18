<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo ($CONFIG["site"]["title"]); ?>管理后台</title>
        <meta name="description" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <meta name="keywords" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <!-- <link href="__TMPL__statics/css/index.css" rel="stylesheet" type="text/css" /> -->
        <link href="__TMPL__statics/css/style.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/land.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/pub.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/main.css" rel="stylesheet" type="text/css" />
        <link href="__PUBLIC__/js/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script> var BAO_PUBLIC = '__PUBLIC__'; var BAO_ROOT = '__ROOT__'; </script>
        <script src="__PUBLIC__/js/jquery.js"></script>
        <script src="__PUBLIC__/js/jquery-ui.min.js"></script>
        <script src="__PUBLIC__/js/my97/WdatePicker.js"></script>
        <script src="/Public/js/layer/layer.js"></script>
        <script src="__PUBLIC__/js/admin.js?v=20150409"></script>
    </head>
    <body>
         <iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
   <div class="main">
<div class="mainBt">
    <ul>
        <li class="li1">微信</li>
        <li class="li2">微信关键字</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <div class="jsglNr">
        <div class="selectNr" style="border-top: none; margin-top: 0px;">
            <div class="left">
                <?php echo BA('weixinkeyword/create','','添加内容');?>  
            </div>
            <div class="right">
                <form method="post" action="<?php echo U('weixinkeyword/index');?>">
                    <input type="text"  class="inptText" name="keyword" value="<?php echo ($keyword); ?>"  /><input type="submit" value="   搜索"  class="inptButton" />
                </form>
            </div>
        </div>
        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td>  <input type="checkbox" class="checkAll" rel="keyword_id" />ID</td>
                        <td>关键字</td>
                        <td>类型</td>
                        <td>内容</td>
                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_keyword_id" type="checkbox" name="keyword_id[]" value="<?php echo ($var["keyword_id"]); ?>" /> <?php echo ($var["keyword_id"]); ?></td>
                            <td><?php echo ($var["keyword"]); ?></td>
                            <td><?php echo ($var["type"]); ?></td>
                            <td><?php echo ($var["contents"]); ?></td>
                            <td>
                                <?php echo BA('weixinkeyword/edit',array("keyword_id"=>$var["keyword_id"]),'编辑','','remberBtn');?>
                                <?php echo BA('weixinkeyword/delete',array("keyword_id"=>$var["keyword_id"]),'删除','act','remberBtn');?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('weixinkeyword/delete','','批量删除','list',' a2');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>