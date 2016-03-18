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
        <li><a href="#">商家管理</a> > <a href="">抢购</a> > <a>正在抢购</a></li>
    </ul>
</div>
<div class="tuan_content">
	<form method="post" action="<?php echo U('tuan/index');?>">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t">
            <div class="left tuan_topser_l">抢购名称：<input type="text" class="radius3 tuan_topser"  name="keyword" value="<?php echo ($keyword); ?>"/><input type="submit" style="margin-left:10px;" class="radius3 sjgl_an tuan_topbt" value="搜 索"/></div>
            <div class="right tuan_topfb_r"><a class="radius3 sjgl_an tuan_topbt" target="main_frm" href="<?php echo U('tuan/create');?>">发布抢购+</a></div>
        </div>
    </div>
    </form>
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli on"><a href="<?php echo U('tuan/index');?>">正在抢购</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('tuan/history');?>">往期抢购</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('tuan/order');?>">订单管理</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('tuan/used');?>">抢购券管理</a></li>
        </ul>
    </div> 
    <table class="tuan_table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="background-color:#eee;">
            <td width="50">抢购ID</td>
            <td width="120">抢购图片</td>
            <td width="130">抢购名称</td>
            <td width="150">抢购时间</td>
            <td width="70">原价</td>
            <td width="80">抢购价格</td>
            <td width="70">销量</td>
            <td width="70">浏览次数</td>
            <td width="70">状态</td>
            <td width="140">操作</td>
        </tr>
        <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
            <tr>
                <td><?php echo ($var["tuan_id"]); ?></td>
                <td><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>"  style="width: 160px; margin: 0px auto;" /></td>
                <td><?php echo ($var["title"]); ?></td>
                <td><?php echo ($var["bg_date"]); ?>--<?php echo ($var["end_date"]); ?></td>
                <td><?php echo ($var['price']); ?></td>
                <td style="height: 100px;"><?php echo ($var['tuan_price']); ?></td>
                <td><?php echo ($var["sold_num"]); ?></td>
                <td><?php echo ($var["views"]); ?></td>
                <td><?php if(($var["audit"]) == "0"): ?>等待审核<?php else: ?>正常<?php endif; ?></td>
                <td>
                    <a href="<?php echo U('tuan/edit',array('tuan_id'=>$var['tuan_id']));?>">编辑</a>
                    <a href="<?php echo U('tuan/setting',array('tuan_id'=>$var['tuan_id']));?>">设置套餐</a>
                </td>
            </tr>
        </tr><?php endforeach; endif; ?>
    </table>
    <div class="paging">
        <?php echo ($page); ?>
    </div>
</div>

</body>
</html>