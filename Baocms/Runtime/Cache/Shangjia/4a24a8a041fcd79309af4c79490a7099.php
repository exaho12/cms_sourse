<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($CONFIG["site"]["title"]); ?>商户中心</title>
<meta name="description" content="<?php echo ($CONFIG["site"]["title"]); ?>商户中心" />
<meta name="keywords" content="<?php echo ($CONFIG["site"]["title"]); ?>商户中心" />
<link href="__TMPL__statics/css/newstyle.css" rel="stylesheet" type="text/css" />
 <link href="__PUBLIC__/js/jquery-ui.css" rel="stylesheet" type="text/css" />
<script>
var BAO_PUBLIC = '__PUBLIC__'; var BAO_ROOT = '__ROOT__';
</script>
<script src="__PUBLIC__/js/jquery.js"></script>
<script src="__PUBLIC__/js/jquery-ui.min.js"></script>
<script src="__PUBLIC__/js/web.js"></script>
<script src="__PUBLIC__/js/layer/layer.js"></script>

</head>

<body>
<iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
<div class="sjgl_lead">
    <ul>
        <li><a href="#">系统设置</a> > <a href="">店铺管理</a> > <a>店铺文字资料</a></li>
    </ul>
</div>
<div class="tuan_content">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t">
            <div class="left tuan_topser_l">店铺文字资料介绍店铺的基本信息 </div>
        </div>
    </div> 
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli tabli_change on"><a href="<?php echo U('shop/about');?>">店铺文字资料</a></li>
            <li class="tuanfabu_tabli tabli_change"><a href="<?php echo U('shop/image');?>">店铺形象图</a></li>
            <li class="tuanfabu_tabli tabli_change"><a href="<?php echo U('shop/logo');?>">店铺LOGO</a></li>
            <li class="tuanfabu_tabli tabli_change"><a href="<?php echo U('photo/index');?>">店铺环境图</a></li>
        </ul>
    </div>
    <div class="tabnr_change  show">
    	<form method="post"  action="<?php echo U('shop/about');?>" target="baocms_frm">
    	<table class="tuanfabu_table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="120"><p class="tuanfabu_t">店铺地址：</p></td>
                <td><div class="tuanfabu_nr">
                <input type="text" name="data[addr]" value="<?php echo (($SHOP["addr"])?($SHOP["addr"]):''); ?>" class="tuanfabu_int tuanfabu_intw2" />
                </div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t">联系人：</p></td>
                <td><div class="tuanfabu_nr">
                <input type="text" name="data[contact]" value="<?php echo (($SHOP["contact"])?($SHOP["contact"]):''); ?>" class="tuanfabu_int tuanfabu_intw2" />
                </div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t">店铺附近坐标：</p></td>
                <td><div class="tuanfabu_nr">
                <input type="text" name="data[near]" value="<?php echo (($ex["near"])?($ex["near"]):''); ?>" class="tuanfabu_int tuanfabu_intw2" />
                </div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t">营业时间：</p></td>
                <td><div class="tuanfabu_nr">
                <input type="text" name="data[business_time]" value="<?php echo (($ex["business_time"])?($ex["business_time"]):''); ?>" class="tuanfabu_int tuanfabu_intw2" />
                </div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t">配送等候时间：</p></td>
                <td><div class="tuanfabu_nr">
                <input type="text" name="data[delivery_time]" value="<?php echo (($ex["delivery_time"])?($ex["delivery_time"]):'30'); ?>" class="tuanfabu_int tuanfabu_intw2" />分钟
                </div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t">详情：</p></td>
                <td><div class="tuanfabu_nr">
                <script type="text/plain" id="data_details" name="details" style="width:800px;height:360px;"><?php echo ($ex["details"]); ?></script>
                </div></td>
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
        </table>
        <div class="tuanfabu_an">
        <input type="submit" class="radius3 sjgl_an tuan_topbt" value="确认保存" />
        </div>
        </form>
    </div> 
</div>
</body>
</html>