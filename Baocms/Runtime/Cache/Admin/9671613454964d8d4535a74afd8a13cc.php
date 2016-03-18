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
        <li class="li1">功能</li>
        <li class="li2">广告管理</li>
        <li class="li2 li3">广告位设置</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <div class="jsglNr">
        <form  target="baocms_frm" method="post">
            <div class="tableBox" style="margin-top: 20px; margin-bottom: 20px;">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
              
                    <?php if(is_array($place)): $pl = 0; $__LIST__ = $place;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($pl % 2 );++$pl;?><tr>
                            <td colspan="4" style="text-align: left; padding-left: 20px; font-size: 16px; font-weight: bold;"><?php echo ($item); ?></td>
                        </tr>
                         <tr>
                         <?php $i=0; ?>
                        <?php if(is_array($adsite)): foreach($adsite as $key=>$var): if($var['site_place'] == $pl): ?><td>
                                        (<?php echo ($var["site_id"]); ?>)<?php echo ($var["site_name"]); ?>
                                        <?php echo BA('ad/index',array("site_id"=>$var["site_id"]),'管理广告','','remberBtn');?>
                                    </td>
                                    <?php $i++; if($i%4==0) echo '</tr><tr>'; endif; endforeach; endif; ?>
                           </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                   
                </table>
              
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>