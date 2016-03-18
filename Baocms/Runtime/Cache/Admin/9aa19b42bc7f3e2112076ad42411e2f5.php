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
        <li class="li1">会员</li>
        <li class="li2">会员管理</li>
        <li class="li2 li3">第三方登录</li>
    </ul>
</div>
<p class="attention"><span>注意：</span>联合登录需要在第三方平台申请</p>
<form  target="baocms_frm" action="<?php echo U('setting/connect');?>" method="post">
    <div class="mainScAdd">
        <div class="tableBox">
            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >

                <tr>
                    <td class="lfTdBt">开启调试：</td>
                    <td class="rgTdBt">
                        <label><input type="radio" name="data[debug]" <?php if(($CONFIG["connect"]["debug"]) == "1"): ?>checked="checked"<?php endif; ?> value="1"  />开启</label>
                        <label><input type="radio" name="data[debug]"  <?php if(($CONFIG["connect"]["debug"]) == "0"): ?>checked="checked"<?php endif; ?>  value="0"  />关闭</label>
                        <code>调试模式是为了方便通过腾讯和微博等变态的审核！</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">&nbsp;</td>
                    <td class="rgTdBt">腾讯QQ</td>
                </tr>
                <tr>
                    <td class="lfTdBt">APP_ID：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[qq_app_id]" value="<?php echo ($CONFIG["connect"]["qq_app_id"]); ?>"  class="scAddTextName w300 " />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">APP_KEY：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[qq_app_key]" value="<?php echo ($CONFIG["connect"]["qq_app_key"]); ?>"  class="scAddTextName w300 " />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">&nbsp;</td>
                    <td class="rgTdBt">微博</td>
                </tr>
                <tr>
                    <td class="lfTdBt">APP_ID：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[wb_app_id]" value="<?php echo ($CONFIG["connect"]["wb_app_id"]); ?>"  class="scAddTextName w300 " />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">APP_KEY：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[wb_app_key]" value="<?php echo ($CONFIG["connect"]["wb_app_key"]); ?>"  class="scAddTextName w300 " />
                    </td>
                </tr>


                <tr>
                    <td class="lfTdBt">&nbsp;</td>
                    <td class="rgTdBt">微信</td>
                </tr>
                <tr>
                    <td class="lfTdBt">APP_ID：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[wx_app_id]" value="<?php echo ($CONFIG["connect"]["wx_app_id"]); ?>"  class="scAddTextName w300 " />
                        <code>微信登录这个只启用在PC端，看过腾讯的API 这个需要在开发者中心申请！手机端的使用的是微信配置里面的2者不同，很悲剧</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">APP_KEY：</td>
                    <td class="rgTdBt">
                        <input type="text" name="data[wx_app_key]" value="<?php echo ($CONFIG["connect"]["wx_app_key"]); ?>"  class="scAddTextName w300 " />
                    </td>
                </tr>
            </table>
        </div>
        <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>
    </div>
</form>

</div>
</body>
</html>