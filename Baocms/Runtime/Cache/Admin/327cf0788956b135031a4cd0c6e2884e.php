<?php if (!defined('THINK_PATH')) exit();?><div class="mainScAdd ">
    <div class="tableBox">
        <form target="baocms_frm" action="<?php echo U('weidiancate/edit',array('cate_id'=>$detail['cate_id']));?>" method="post">
            <table  bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">分类：</td>
                    <td class="rgTdBt"><input type="text" name="data[cate_name]" value="<?php echo (($detail["cate_name"])?($detail["cate_name"]):''); ?>" class="manageInput" />

                    </td>
                </tr>
                <tr>
                            <td class="lfTdBt">
                        <script type="text/javascript" src="__PUBLIC__/js/uploadify/jquery.uploadify.min.js"></script>
                        <link rel="stylesheet" href="__PUBLIC__/js/uploadify/uploadify.css">
                        头像：
                        </td>
                        <td class="rgTdBt">
                            <div style="width: 300px;height: 100px; float: left;">
                                <input type="hidden" name="data[photo]" value="<?php echo ($detail["photo"]); ?>" id="data_photo" />
                                <input id="photo_file" name="photo_file" type="file" multiple="true" value="" />
                            </div>
                            <div style="width: 200px;height: 100px; float: left;">
                                <img id="photo_img" width="80" height="80"  src="__ROOT__/attachs/<?php echo (($detail["photo"])?($detail["photo"]):'default.jpg'); ?>" />
                                <a href="<?php echo U('setting/attachs');?>">分类ioc图片上传</a>
                                建议尺寸：60*60像素<?php echo ($CONFIG["attachs"]["user"]["thumb"]["thumb"]); ?>
                            </div>
                            <script>
                                $("#photo_file").uploadify({
                                    'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',
                                    'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"weidiancate"));?>',
                                    'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',
                                    'buttonText': '上传头像',
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
                <tr>
                    <td class="lfTdBt">排序：</td>
                    <td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="manageInput" />
                        <code>数字越小越高</code>
                    </td>
                </tr>
            </table>
            <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>
        </form>
    </div>
</div>