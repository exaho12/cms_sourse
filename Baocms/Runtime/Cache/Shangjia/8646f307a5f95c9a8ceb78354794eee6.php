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
        <li><a href="#">其他</a> > <a href="">人才招聘</a> > <a>人才招聘</a></li>
    </ul>
</div>
<div class="tuan_content">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t tuanfabu_top">
            <div class="left tuan_topser_l">缺人才怎么办，这里可以发布您的招聘信息，为您找到您需要的人才！</div>
        </div>
    </div>
    <form method="post" action="<?php echo U('work/index');?>">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t">
            <div class="left tuan_topser_l">标题：<input type="text" class="radius3 tuan_topser"  name="keyword" value="<?php echo ($keyword); ?>"/><input type="submit" style="margin-left:10px;" class="radius3 sjgl_an tuan_topbt" value="搜 索"/></div>
            <div class="right tuan_topfb_r"><a class="radius3 sjgl_an tuan_topbt" target="main_frm" href="<?php echo U('work/create');?>">发布招聘+</a></div>
        </div>
    </div>
    </form>
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli on"><a href="<?php echo U('work/index');?>">人才招聘</a></li>
        </ul>
    </div> 
    <table class="tuan_table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="background-color:#eee;">
            <td>标题</td>
            <td>创建时间</td>
            <td>浏览量</td>
            <td>状态</td>
            <td>操作</td>
        </tr>
        <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                <td><?php echo ($var["title"]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                <td><?php echo ($var["views"]); ?></td>
                <td><?php if(($var["audit"]) == "0"): ?>等待审核<?php else: ?>正常<?php endif; ?></td>
            	<td><a href="<?php echo U('work/edit',array('work_id'=>$var['work_id']));?>">编辑</a></td>
            </tr><?php endforeach; endif; ?>
    </table>
    <?php echo ($page); ?>
</div>
</body>
</html>