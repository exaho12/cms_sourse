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
        <li class="li2">微信管理</li>
        <li class="li2 li3">自定义菜单</li>
    </ul>
</div>
<div class="main-cate">
    <p class="attention"><span>注意：</span>微信自定义菜单需要开通服务号！最多3个BUTTON  每组BUTTON 最多5个子菜单！不填可以留空！</p>
    <div class="tableBox">
        <form  target="baocms_frm" action="<?php echo U('setting/weixinmenu');?>" method="post">
            <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF; text-align:center;">
                <tr bgcolor="#ffaa7b" height="48px;" style="color:#FFF; font-size:16px; line-height:48px;">
                    <td style="text-align: center;">项目</td>
                    <td>名称</td>
                    <td>链接地址</td>
                </tr>
                <tr bgcolor="#f1f1f1" height="48px" style="font-size:14px; color:#545454; text-align:left; line-height:48px;">
                    <td width="140" align="center">BUTTON1</td>
                    <td colspan="2" class="rgTdBt">
                        <input class="manageInput" name="data[button][1]" type="text" value="<?php echo ($CONFIG['weixinmenu']['button'][1]); ?>"  />
                    </td>

                </tr>
                <?php $__FOR_START_21962__=1;$__FOR_END_21962__=6;for($i=$__FOR_START_21962__;$i < $__FOR_END_21962__;$i+=1){ ?><tr height="48px" style="font-size:14px; color:#545454; text-align:center; line-height:48px;">
                        <td width="140" >子菜单<?php echo ($i); ?></td>
                        <td class="rgTdBt">
                            <input class="manageInput" name="data[child][1][<?php echo ($i); ?>][name]" type="text" value="<?php echo ($CONFIG['weixinmenu']['child'][1][$i]['name']); ?>"  />
                        </td>
                        <td class="rgTdBt">
                            <input class="manageInput" name="data[child][1][<?php echo ($i); ?>][url]" type="text" value="<?php echo ($CONFIG['weixinmenu']['child'][1][$i]['url']); ?>"  />
                        </td>
                    </tr><?php } ?>    
                <tr bgcolor="#f1f1f1" height="48px" style="font-size:14px; color:#545454; text-align:left; line-height:48px;">
                    <td width="140" align="center">BUTTON2</td>
                    <td colspan="2" class="rgTdBt">
                        <input class="manageInput" name="data[button][2]" type="text" value="<?php echo ($CONFIG['weixinmenu']['button'][2]); ?>"  />
                    </td>
                </tr>
                <?php $__FOR_START_17211__=1;$__FOR_END_17211__=6;for($i=$__FOR_START_17211__;$i < $__FOR_END_17211__;$i+=1){ ?><tr height="48px" style="font-size:14px; color:#545454; text-align:center; line-height:48px;">
                        <td width="140" >子菜单<?php echo ($i); ?></td>
                        <td class="rgTdBt">
                            <input class="manageInput" name="data[child][2][<?php echo ($i); ?>][name]" type="text" value="<?php echo ($CONFIG['weixinmenu']['child'][2][$i]['name']); ?>"  />
                        </td>
                        <td class="rgTdBt">
                            <input class="manageInput" name="data[child][2][<?php echo ($i); ?>][url]" type="text" value="<?php echo ($CONFIG['weixinmenu']['child'][2][$i]['url']); ?>"  />
                        </td>
                    </tr><?php } ?>    
                <tr bgcolor="#f1f1f1" height="48px" style="font-size:14px; color:#545454; text-align:left; line-height:48px;">
                    <td width="140" align="center">BUTTON3</td>
                    <td colspan="2" class="rgTdBt">
                        <input class="manageInput" name="data[button][3]" type="text" value="<?php echo ($CONFIG['weixinmenu']['button'][3]); ?>"  />
                    </td>
                </tr>
                <?php $__FOR_START_19032__=1;$__FOR_END_19032__=6;for($i=$__FOR_START_19032__;$i < $__FOR_END_19032__;$i+=1){ ?><tr height="48px" style="font-size:14px; color:#545454; text-align:center; line-height:48px;">
                        <td width="140" >子菜单<?php echo ($i); ?></td>
                        <td class="rgTdBt">
                            <input class="manageInput" name="data[child][3][<?php echo ($i); ?>][name]" type="text" value="<?php echo ($CONFIG['weixinmenu']['child'][3][$i]['name']); ?>"  />
                        </td>
                        <td class="rgTdBt">
                            <input class="manageInput" name="data[child][3][<?php echo ($i); ?>][url]" type="text" value="<?php echo ($CONFIG['weixinmenu']['child'][3][$i]['url']); ?>"  />
                        </td>
                    </tr><?php } ?>    
            </table>
            <div class="smtQr"><input type="submit" value="确定保存" class="smtQrIpt" /></div>
        </form>
    </div>
</div>

</div>
</body>
</html>