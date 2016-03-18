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
        <li><a href="#">商家管理</a> > <a href="">分类</a> > <a>添加分类</a></li>
    </ul>
</div>
<div class="tuan_content">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t">
            <div class="left tuan_topser_l">商家设置自定义分类</div>
        </div>
    </div> 
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli"><a href="<?php echo U('goodsshopcate/index');?>">分类列表</a></li>
            <li class="tuanfabu_tabli on"><a href="<?php echo U('goodsshopcate/create');?>">添加分类</a></li>
        </ul>
    </div>
    <div class="tabnr_change  show">
    	<form method="post"  action="<?php echo U('goodsshopcate/create');?>"  target="baocms_frm">
    	<table class="tuanfabu_table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="120"><p class="tuanfabu_t">分类名称：</p></td>
                <td><div class="tuanfabu_nr"><input type="text" name="data[cate_name]" value="<?php echo (($detail["cate_name"])?($detail["cate_name"]):''); ?>" class="tuanfabu_int tuanfabu_intw2" /></div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t">排序：</p></td>
                <td><div class="tuanfabu_nr"><input type="text" name="data[orderby]" value="<?php echo (($detail["orderby"])?($detail["orderby"]):''); ?>" class="tuanfabu_int tuanfabu_intw2" /></div></td>
            </tr>
        </table>
        <div class="tuanfabu_an">
        <input type="submit" class="radius3 sjgl_an tuan_topbt" value="确认添加" />
        </div>
        </form>
    </div> 
</div>
</body>
</html>