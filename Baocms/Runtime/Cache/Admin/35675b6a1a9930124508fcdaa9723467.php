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
        <li class="li2 li3">SEO模板</li>
    </ul>
</div>
<div class="main-jsgl">
    <p class="attention"><span>注意：</span>标签的设计规则是 控制器加方法名，这样会自动的匹配上的</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin: 10px 20px;">
            <div class="left">
                <?php echo BA('seo/create','','添加模版');?>
            </div>
        </div>
        <form target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="seo_id" /></td>
                        <td class="w50">ID</td>
                        <td>标签</td>
                        <td>说明</td>
                        <td>SEO标题</td>
                        <td>SEO关键字</td>
                        <td>SEO描述</td>
                        <td class="w120">操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_seo_id" type="checkbox" name="seo_id[]" value="<?php echo ($var["seo_id"]); ?>"/></td>
                            <td><?php echo ($var["seo_id"]); ?></td>
                            <td><?php echo ($var["seo_key"]); ?></td>
                            <td><?php echo ($var["seo_explain"]); ?></td>
                            <td><?php echo ($var["seo_title"]); ?></td>
                            <td><?php echo ($var["seo_keywords"]); ?></td>
                            <td><?php echo ($var["seo_desc"]); ?></td>

                            <td>
                                <?php echo BA('seo/edit',array("seo_id"=>$var["seo_id"]),'编辑','','remberBtn');?>
                                <?php echo BA('seo/delete',array("seo_id"=>$var["seo_id"]),'删除','act','remberBtn');?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr">
                <div class="left">
                    <?php echo BA('seo/delete','','批量关闭','list','a2');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>