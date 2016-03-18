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
        <li><a href="#">商家管理</a> > <a href="">商城</a> > <a>商城商品</a></li>
    </ul>
</div>
<div class="tuan_content">
    <form method="post" action="<?php echo U('goods/index');?>">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t">
            <div class="left tuan_topser_l">
            商品名称：<input type="text" class="radius3 tuan_topser"  name="keyword" value="<?php echo ($keyword); ?>" />
            <input type="submit" style="margin-left:10px;" class="radius3 sjgl_an tuan_topbt" value="搜 索"/>
            </div>
            <div class="right tuan_topfb_r"><a class="radius3 sjgl_an tuan_topbt" target="main_frm" href="<?php echo U('goods/create');?>">发布商品+</a></div>
        </div>
    </div>
    </form>
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli on"><a href="<?php echo U('goods/index');?>">商城商品</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('order/index');?>">卖出商品</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('order/wait');?>">付款订单</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('order/wait2');?>">货到付款</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('order/picks');?>">捡货单</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('order/delivery');?>">发货管理</a></li>
        </ul>
    </div> 
    <table class="tuan_table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="background-color:#eee;">
            <td>商品名称</td>
            <td>商品图片</td>
            <td>发布商家</td>
            <td>市场价格</td>
            <td>商城价格</td>
            <td>可使用积分数</td>
            <td>售出数量</td>
            <td>浏览量</td>
            <td>是否审核</td>
            <td>创建时间</td>
            <td>操作</td>
        </tr>
        <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                <td><?php echo ($var["title"]); ?></td>
                <td style="height: 80px;"><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>"  style="width: 80px;  margin: 0px auto;" /></td>
                <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                <td><?php echo ($var["price"]); ?></td>
                <td><?php echo ($var["mall_price"]); ?></td>
                <td><?php echo ($var["use_integral"]); ?></td>
                <td><?php echo ($var["sold_num"]); ?></td>
                <td><?php echo ($var["views"]); ?></td>
                <td><?php if(($var["audit"]) == "0"): ?>等待审核<?php else: ?>正常<?php endif; ?></td>
                <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                <td>
                <a class="radius3 sy_c1an" href="<?php echo U('goods/edit',array('goods_id'=>$var['goods_id']));?>">编辑</a>
            </td>
            </tr><?php endforeach; endif; ?>
    </table>
    <?php echo ($page); ?>
</div>

</body>
</html>