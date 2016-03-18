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
        <li class="li1">商城</li>
        <li class="li2">抢购管理</li>
        <li class="li2 li3">编辑</li>
    </ul>
</div>
<form target="baocms_frm" action="<?php echo U('tuan/edit',array('tuan_id'=>$detail['tuan_id']));?>" method="post">
    <div class="mainScAdd ">
        <div class="tableBox">
            <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;" >
                <tr>
                    <td class="lfTdBt">商家：</td>
                    <td class="rgTdBt"> <div class="lt">
                            <input type="hidden" id="shop_id" name="data[shop_id]" value="<?php echo (($detail["shop_id"])?($detail["shop_id"]):''); ?>"/>
                            <input type="text" id="shop_name" name="shop_name" value="<?php echo ($shop["shop_name"]); ?>" class="scAddTextName sj" />
                        </div>
                        <a mini="select"  w="1000" h="600" href="<?php echo U('shop/select');?>" class="seleSj">选择商家</a>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">分类：</td>
                    <td class="rgTdBt"> 
                        <select id="data[cate_id]" name="data[cate_id]" class="seleFl w200">
                            <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $detail["cate_id"]): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $detail["cate_id"]): ?>selected="selected"<?php endif; ?> > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
                        </select>
                    </td> 
                </tr>
                <tr>
                    <td class="lfTdBt">商品名称：</td>
                    <td class="rgTdBt"><input type="text" name="data[title]" value="<?php echo (($detail["title"])?($detail["title"]):''); ?>" class="manageInput" />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">商品副标题：</td>
                    <td class="rgTdBt"><input type="text" name="data[intro]" value="<?php echo (($detail["intro"])?($detail["intro"]):''); ?>" class="manageInput" />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">参加活动：</td>
                    <td class="rgTdBt">
                        <select id="activity_id" name="data[activity_id]" class="seleFl w300">
                            <option value="0">请选择</option>
                            <?php if(is_array($hd)): foreach($hd as $key=>$var): ?><option <?php if(($var["activity_id"]) == $detail['activity_id']): ?>selected="selected"<?php endif; ?>value="<?php echo ($var["activity_id"]); ?>"><?php echo ($var["title"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">选择分店：</td>
                    <td class="rgTdBt">
                <?php if(is_array($branch)): foreach($branch as $key=>$item): ?><label style="margin-right: 10px;"><span><?php echo ($item["name"]); ?>：</span><input style="width: 20px; height: 20px;" type="checkbox" name="branch_id[]" value="<?php echo ($item["branch_id"]); ?>" <?php if(in_array($item['branch_id'],$branch_id)){?> checked="checked" <?php }?> /></label><?php endforeach; endif; ?>
                </td>
                </tr>
                <tr>
                    <td class="lfTdBt">
                <script type="text/javascript" src="__PUBLIC__/js/uploadify/jquery.uploadify.min.js"></script>
                <link rel="stylesheet" href="__PUBLIC__/js/uploadify/uploadify.css">
                图片：
                </td>
                <td class="rgTdBt">
                    <div style="width: 300px;height: 100px; float: left;">
                        <input type="hidden" name="data[photo]" value="<?php echo ($detail["photo"]); ?>" id="data_photo" />
                        <input id="photo_file" name="photo_file" type="file" multiple="true" value="" />
                    </div>
                    <div style="width: 300px;height: 100px; float: left;">
                        <img id="photo_img" width="80" height="80"  src="__ROOT__/attachs/<?php echo (($detail["photo"])?($detail["photo"]):'default.jpg'); ?>" />
                        <a href="<?php echo U('setting/attachs');?>">缩略图设置</a>
                        建议尺寸<?php echo ($CONFIG["attachs"]["tuan"]["thumb"]); ?>
                    </div>
                    <script>
                        $("#photo_file").uploadify({
                            'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',
                            'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"tuan"));?>',
                            'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',
                            'buttonText': '上传图片',
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
                    <td  class="lfTdBt">抢购组图：</td>
                    <td class="rgTdBt">
                        <div>
                            <input id="thumb_file" name="logo_file" type="file" multiple="true" value="" />
                        </div>
                        <div class="jq_uploads_img">
                            <?php if(is_array($thumb)): foreach($thumb as $key=>$item): ?><span style="width: 200px; height: 120px; float: left; margin-left: 5px; margin-top: 10px;"> 
                                    <img width="200" height="100" src="__ROOT__/attachs/<?php echo ($item); ?>">  
                                    <input type="hidden" name="thumb[]" value="<?php echo ($item); ?>" />  
                                    <a href="javascript:void(0);">取消</a>  
                                </span><?php endforeach; endif; ?>
                        </div>
                        <script>
                            $("#thumb_file").uploadify({
                                'swf': '__PUBLIC__/js/uploadify/uploadify.swf?t=<?php echo ($nowtime); ?>',
                                'uploader': '<?php echo U("app/upload/uploadify",array("model"=>"tuan"));?>',
                                'cancelImg': '__PUBLIC__/js/uploadify/uploadify-cancel.png',
                                'buttonText': '上传图片',
                                'fileTypeExts': '*.gif;*.jpg;*.png',
                                'queueSizeLimit': 5,
                                'onUploadSuccess': function (file, data, response) {
                                    var str = '<span style="width: 200px; height: 120px; float: left; margin-left: 5px; margin-top: 10px;">  <img width="200" height="100" src="__ROOT__/attachs/' + data + '">  <input type="hidden" name="thumb[]" value="' + data + '" />    <a href="javascript:void(0);">取消</a>  </span>';
                                    $(".jq_uploads_img").append(str);
                                }
                            });

                            $(document).on("click", ".jq_uploads_img a", function () {
                                $(this).parent().remove();
                            });

                        </script>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">属性：</td>
                    <td class="rgTdBt">
                        <label><span>是否热门：</span><input type="checkbox" name="data[is_hot]" <?php if($detail['is_hot'] == 1): ?>checked="checked"<?php endif; ?>  value="1" /></label>
                        <label><span style="margin-left: 20px;">是否精选：</span><input type="checkbox" name="data[is_chose]" <?php if($detail['is_chose'] == 1): ?>checked="checked"<?php endif; ?> value="1" /></label>
                        <label><span style="margin-left: 20px;">是否新单：</span><input type="checkbox" name="data[is_new]" <?php if($detail['is_new'] == 1): ?>checked="checked"<?php endif; ?> value="1" /></label>
                        <label><span style="margin-left: 20px;">是否免预约：</span><input type="checkbox" name="data[freebook]" <?php if($detail['freebook'] == 1): ?>checked="checked"<?php endif; ?> value="1" /></label>
                        <label><span style="margin-left: 20px;">是否返现1%：</span><input type="checkbox" name="data[is_return_cash]" <?php if($detail['is_return_cash'] == 1): ?>checked="checked"<?php endif; ?> value="1" /></label>
						<label><span style="margin-left: 20px;">是否仅能下一次单</span><input type="checkbox" name="data[xiadan]" <?php if($detail['xiadan'] == 1): ?>checked="checked"<?php endif; ?> value="1" /></label>
                    </td>
                </tr>
				<tr>
                    <td  class="lfTdBt">每天抢购份数：</td>
                    <td class="rgTdBt"><input type="text" name="data[xiangou]" value="<?php echo (($detail["xiangou"])?($detail["xiangou"]):'0'); ?>" class="manageInput" />
 					<code>0表示不限购</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">市场价格：</td>
                    <td class="rgTdBt"><input type="text" name="data[price]" value="<?php echo (($detail["price"])?($detail["price"]):''); ?>" class="manageInput" />

                    </td>
                </tr><tr>
                    <td class="lfTdBt">抢购价格：</td>
                    <td class="rgTdBt"><input type="text" name="data[tuan_price]" value="<?php echo (($detail["tuan_price"])?($detail["tuan_price"]):''); ?>" class="manageInput" />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">手机下单优惠价格：</td>
                    <td class="rgTdBt"><input type="text" name="data[mobile_fan]" value="<?php echo (($detail["mobile_fan"])?($detail["mobile_fan"]):''); ?>" class="manageInput" />

                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">可使用积分：</td>
                    <td class="rgTdBt"><input type="text" name="data[use_integral]" value="<?php echo (($detail["use_integral"])?($detail["use_integral"]):''); ?>" class="manageInput" />
                        <code>100积分抵用1块钱RMB</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">结算价格：</td>
                    <td class="rgTdBt"><input type="text" name="data[settlement_price]" value="<?php echo (($detail["settlement_price"])?($detail["settlement_price"]):''); ?>" class="manageInput" />
                        <code>网站和商家结算的价格</code>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">库存：</td>
                    <td class="rgTdBt"><input type="text" name="data[num]" value="<?php echo (($detail["num"])?($detail["num"]):''); ?>" class="manageInput" />
                    </td>
                </tr><tr>
                    <td class="lfTdBt">售出数：</td>
                    <td class="rgTdBt"><input type="text" name="data[sold_num]" value="<?php echo (($detail["sold_num"])?($detail["sold_num"]):''); ?>" class="manageInput" />
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">排序：</td>
                    <td class="rgTdBt"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="manageInput" />

                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">开始时间：</td>
                    <td class="rgTdBt"><input type="text" name="data[bg_date]" value="<?php echo (($detail["bg_date"])?($detail["bg_date"]):''); ?>" onfocus="WdatePicker();"  class="inputData" />
                    </td>
                </tr><tr>
                    <td class="lfTdBt">结束时间：</td>
                    <td class="rgTdBt"><input type="text" name="data[end_date]" value="<?php echo (($detail["end_date"])?($detail["end_date"]):''); ?>" onfocus="WdatePicker();"  class="inputData" />

                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">虚拟物品过期时间：</td>
                    <td class="rgTdBt"><input type="text" name="data[fail_date]" value="<?php echo (($detail["fail_date"])?($detail["fail_date"]):''); ?>" onfocus="WdatePicker();"  class="inputData" />
                    </td>
                </tr><tr>
                    <td class="lfTdBt">抢购详情：</td>
                    <td class="rgTdBt">
                        <script type="text/plain" id="data_details" name="details" style="width:800px;height:360px;"><?php echo ($tuan_details["details"]); ?></script>
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
                    <td class="lfTdBt">购买须知：</td>
                    <td class="rgTdBt">
                        <script type="text/plain" id="instructions" name="instructions" style="width:800px;height:360px;"><?php echo ($tuan_details["instructions"]); ?></script>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">是否分成给上级分销商：</td>
                    <td class="rgTdBt"><input type="checkbox" name="data[profit_enable]" value='1' <?php if($detail['profit_enable'] == 1): ?>checked="checked"<?php endif; ?> /></td>
                </tr>
                <tr>
                    <td class="lfTdBt">购买付款后等级升为：</td>
                    <td class="rgTdBt">
                        <select name="data[profit_rank_id]" class="seleFl w200">
                            <option value="0">不设置</option>
                            <?php if(is_array($ranks)): foreach($ranks as $key=>$item): ?><option <?php if(($item["rank_id"]) == $detail["profit_rank_id"]): ?>selected="selected"<?php endif; ?> value="<?php echo ($item["rank_id"]); ?>"><?php echo ($item["rank_name"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="lfTdBt">一级会员分成比例：</td>
                    <td class="rgTdBt"><input type="number" min="0" max="100" name="data[profit_rate1]" value='<?php echo ($detail["profit_rate1"]); ?>' class="manageInput " />%</td>
                </tr>
                <tr>
                    <td class="lfTdBt">二级会员分成比例：</td>
                    <td class="rgTdBt"><input type="number" min="0" max="100" name="data[profit_rate2]" value='<?php echo ($detail["profit_rate2"]); ?>' class="manageInput " />%</td>
                </tr>
                <tr>
                    <td class="lfTdBt">三级会员分成比例：</td>
                    <td class="rgTdBt"><input type="number" min="0" max="100" name="data[profit_rate3]" value='<?php echo ($detail["profit_rate3"]); ?>' class="manageInput " />%</td>
                </tr>
                <script>
                    um2 = UM.getEditor('instructions', {
                        imageUrl: "<?php echo U('app/upload/editor');?>",
                        imagePath: '__ROOT__/attachs/editor/',
                        lang: 'zh-cn',
                        langPath: UMEDITOR_CONFIG.UMEDITOR_HOME_URL + "lang/",
                        focus: false
                    });
                </script>
            </table>
        </div>
        <div class="smtQr"><input type="submit" value="确认保存" class="smtQrIpt" /></div>      
    </div>
</form>

</div>
</body>
</html>