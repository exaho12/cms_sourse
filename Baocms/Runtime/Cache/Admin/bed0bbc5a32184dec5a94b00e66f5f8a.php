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
        <li class="li1">设置</li>
        <li class="li2">模版管理</li>
        <li class="li2 li3">网站风格</li>
    </ul>
</div>
<style>
    .tableBox table td{border-left: 1px solid #dbdbdb; border-top: 1px solid #dbdbdb;}
    .tableBox table td.topnone{border-top:none;}
    .tableBox table td.leftnone{border-left: none;}
</style>
<div class="main-jsgl">
    <div class="jsglNr">
        <form target="baocms_frm" method="post">
            <div class="tableBox" style="margin: 0px;">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="0px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;">
                    <tr>
                        <td class="topnone leftnone">模版名称</td>
                        <td class="topnone">图片</td>
                        <td class="topnone">操作</td>
                    </tr>
                    <?php if(is_array($template)): foreach($template as $key=>$var): ?><tr>
                            <td class="leftnone">
                                <?php echo ($var["name"]); ?>
                            </td>
                            <td>
                                <img class="w100"   src="__ROOT__/themes/<?php echo ($var["theme"]); ?>/<?php echo ($var["photo"]); ?>" />
                            </td>
                            <td>
                        <?php if(!empty($themes[$var['theme']])): echo BA('template/uninstall',array("theme"=>$var["theme"]),'卸载','act','remberBtn');?>
                            <?php if(!$themes[$var['theme']]['is_default']): echo BA('template/df',array("theme"=>$var["theme"]),'设置为默认','act','remberBtn');?>
                                <?php else: ?>    
                                <!--<?php echo BA('template/setting',array(),'配置模版','','remberBtn');?> -->
                                <?php echo BA('template/settings',array("theme"=>$var["theme"]),'配置模版','','remberBtn'); endif; ?>
                            <?php else: ?>
                            <?php echo BA('template/install',array("theme"=>$var["theme"]),'安装','act','remberBtn'); endif; ?>
                        <a target="_blank" href="/index.php?theme=<?php echo ($var['theme']); ?>" class="remberBtn">预览模版</a>
                        </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>