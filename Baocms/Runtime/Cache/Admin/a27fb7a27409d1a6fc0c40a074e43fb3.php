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
        <li class="li1">商家</li>
        <li class="li2">商家新闻</li>
        <li class="li2 li3">发布新闻</li>
    </ul>
</div>
<form  target="baocms_frm" action="<?php echo U('shopnews/create');?>" method="post">
    <div class="mainScAdd">
        <div class="tableBox">
            <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">商家：</td>
                    <td class="rgTdBt">
                        <div class="lt">
                            <input type="hidden" id="shop_id" name="data[shop_id]" value="<?php echo (($detail["shop_id"])?($detail["shop_id"]):''); ?>"/>
                            <input type="text" id="shop_name" name="shop_name" value="" class="scAddTextName w150 sj" />
                        </div>
                        <a mini="select"  w="1000" h="600" href="<?php echo U('shop/select');?>" class="seleSj">选择商家</a>
                    </td>
                </tr><tr>
                    <td class="lfTdBt">标题：</td>
                    <td class="rgTdBt"><input type="text" name="data[title]" value="<?php echo (($detail["title"])?($detail["title"]):''); ?>" class="scAddTextName w260" />

                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">
                <script type="text/javascript" src="__PUBLIC__/js/uploadify/jquery.uploadify.min.js"></script>
                <link rel="stylesheet" href="__PUBLIC__/js/uploadify/uploadify.css">
                缩略图：
                </td>
                <td class="rgTdBt">
                    <div style="width: 300px;height: 100px; float: left;">
                        <input type="hidden" name="data[photo]" value="<?php echo ($detail["photo"]); ?>" id="data_photo" />
                        <input id="photo_file" name="photo_file" type="file" multiple="true" value="" />
                    </div>
                    <div style="width: 300px;height: 100px; float: left;">
                        <img id="photo_img" width="80" height="80"  src="__ROOT__/attachs/<?php echo (($detail["photo"])?($detail["photo"]):'default.jpg'); ?>" />
                        <a href="<?php echo U('setting/attachs');?>">缩略图设置</a>
                        建议尺寸<?php echo ($CONFIG["attachs"]["shopnews"]["thumb"]); ?>
                    </div>
                    <script>
                        $("#photo_file").uploadify({
                            'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',
                            'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"shopnews"));?>',
                            'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',
                            'buttonText': '上传缩略图',
                            'fileTypeExts': '*.gif;*.jpg;*.png',
                            'queueSizeLimit': 1,
                            'onUploadSuccess': function (file, data, response) {
                                $("#data_photo").val(data);
                                $("#photo_img").attr('src', '__ROOT__/attachs/' + data).show();
                            }
                        });

                    </script>
                </td>
                </tr>
                <tr>
                    <td class="lfTdBt">详细内容：</td>
                    <td class="rgTdBt">
                        <script type="text/plain" id="data_details" name="data[details]" style="width:800px;height:360px;"><?php echo ($detail["details"]); ?></script>
                    </td>
                </tr>
                <link rel="stylesheet" href="__PUBLIC__/umeditor/themes/default/css/umeditor.min.css" type="text/css">
                <script type="text/javascript" charset="utf-8" src="__PUBLIC__/umeditor/umeditor.config.js"></script>
                <script type="text/javascript" charset="utf-8" src="__PUBLIC__/umeditor/umeditor.min.js"></script>
                <script type="text/javascript" src="__PUBLIC__/umeditor/lang/zh-cn/zh-cn.js"></script>
                <script>
                        um = UM.getEditor('data_details', {
                            imageUrl: "<?php echo U('app/upload/editor');?>",
                            imagePath: '__ROOT__/attachs/editor/',
                            lang: 'zh-cn',
                            langPath: UMEDITOR_CONFIG.UMEDITOR_HOME_URL + "lang/",
                            focus: false
                        });
                </script>
                <tr>
                    <td class="lfTdBt">浏览量：</td>
                    <td class="rgTdBt"><input type="text" name="data[views]" value="<?php echo (($detail["views"])?($detail["views"]):''); ?>" class="scAddTextName w260" />

                    </td>
                </tr><tr>
                    <td class="lfTdBt">排序：</td>
                    <td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):'50'); ?>" class="scAddTextName w260" />
                    </td>
                </tr>
            </table>
        </div>
        <div class="smtQr"><input type="submit" value="确认添加" class="smtQrIpt" /></div>
    </div>
</form>

</div>
</body>
</html>