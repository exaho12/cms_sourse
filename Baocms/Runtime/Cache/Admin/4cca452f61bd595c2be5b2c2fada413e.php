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
        <li class="li2 li3">邮件模版</li>
    </ul>
</div>
<div class="main-jsgl">
    <p class="attention"><span>注意：</span>只有当邮件模版启用的时候才能正常发送邮件！</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin: 10px 20px;">
            <div class="left">
                <?php echo BA('email/create','','添加内容');?>
            </div>
        </div>
        <form target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="email_id" /></td>
                        <td class="w50">ID</td>
                        <td>标签</td>
                        <td>说明</td>
                        <td class="w80">是否启用</td>
                        <td class="w120">操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_email_id" type="checkbox" name="email_id[]" value="<?php echo ($var["email_id"]); ?>" /></td>
                            <td><?php echo ($var["email_id"]); ?></td>
                            <td><?php echo ($var["email_key"]); ?></td>
                            <td><?php echo ($var["email_explain"]); ?></td>
                            <td><?php if(($var["is_open"]) == "1"): ?>是<?php else: ?>否<?php endif; ?></td>
                        <td>
                            <?php echo BA('email/edit',array("email_id"=>$var["email_id"]),'编辑','','remberBtn');?>

                            <?php if(($var["is_open"]) == "0"): echo BA('email/audit',array("email_id"=>$var["email_id"]),'启用','act','remberBtn');?>
                        <?php else: ?>
                        <?php echo BA('email/delete',array("email_id"=>$var["email_id"]),'关闭','act','remberBtn'); endif; ?>

                        </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr">
                <div class="left">
                    <?php echo BA('email/delete','','批量关闭','list','a2');?>
                    <?php echo BA('email/audit','','批量开启','list','remberBtn');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>