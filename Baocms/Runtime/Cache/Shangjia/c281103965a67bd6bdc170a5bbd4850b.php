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
        <li><a href="#">商家管理</a> > <a href="">分类</a> > <a>分类列表</a></li>
    </ul>
</div>
<div class="tuan_content">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t tuanfabu_top">
            <div class="left tuan_topser_l">商家自定义分类列表</div>
        </div>
    </div>
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli on"><a href="<?php echo U('goodsshopcate/index');?>">分类列表</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('goodsshopcate/create');?>">添加分类</a></li>
        </ul>
    </div> 
    <table class="tuan_table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="background-color:#eee;">
            <td>分类名称</td>
            <td>排序</td>
            <td>操作</td>
        </tr>
        <?php if(is_array($autocates)): foreach($autocates as $key=>$var): ?><tr>
                <td><?php echo ($var["cate_name"]); ?></td>
                <td><?php echo ($var["orderby"]); ?></td>
                <td>
                    <a href="<?php echo U('goodsshopcate/edit',array('cate_id'=>$var['cate_id']));?>">编辑</a>
                    <a href="<?php echo U('goodsshopcate/delete',array('cate_id'=>$var['cate_id']));?>">删除</a>
                </td>
            </tr><?php endforeach; endif; ?>
    </table>
    <?php echo ($page); ?>
</div>
</body>
</html>