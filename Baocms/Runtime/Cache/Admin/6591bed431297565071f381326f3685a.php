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
        <li class="li2">结算管理</li>
        <li class="li2 li3">资金记录</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>如果是需要抢购线下发货的物品，那么资金会在后台设置抢购为已完成才会有资金记录！如果订单是非实物订单，那么需要在商家确认抢购券消费的时候资金才会转入到商家账户</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('shopmoney/create','','添加资金记录');?>
            </div>
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('shopmoney/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <span>订单编号</span>
                        <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="   搜索"  class="inptButton" />
                    </div> 
                </form>
                <a href="javascript:void(0);" class="searchG">高级搜索</a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <form method="post" action="<?php echo U('shopmoney/index');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>开始时间</span>
                            <input type="text" name="bg_date" value="<?php echo (($bg_date)?($bg_date):''); ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd'});"  class="text" />
                        </label>
                        <label>
                            <span>结束时间</span>
                            <input type="text" name="end_date" value="<?php echo (($end_date)?($end_date):''); ?>" onfocus="WdatePicker({dateFmt: 'yyyy-MM-dd'});"  class="text" />
                        </label>
                        <label>
                            <input type="hidden" id="shop_id" name="shop_id" value="<?php echo (($shop_id)?($shop_id):''); ?>"/>
                            <input type="text"   id="shop_name" name="shop_name" value="<?php echo ($shop_name); ?>" class="text w150 sj" />
                            <a mini="select"  w="1000" h="600" href="<?php echo U('shop/select');?>" class="seleSj">选择商家</a>
                        </label>
                        <label>
                            <span>关键字:</span>
                            <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" />
                        </label>
                    </div>
                </div>
                <div class="right">
                    <input type="submit" value="   搜索"  class="inptButton" />
                </div>
        </form>
        <div class="clear"></div>
    </div>
    <form  target="baocms_frm" method="post">
        <div class="tableBox">
            <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                <tr>
                    <td class="w50"><input type="checkbox" class="checkAll" rel="money_id" /></td>
                    <td class="w50">ID</td>
                    <td>商家</td>
                    <td>资金</td>
                    <td>时间</td>
                    <td>IP</td>
                    <td>类型</td>
                    <td>原始订单</td>
                    <td>说明</td>    
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input class="child_money_id" type="checkbox" name="money_id[]" value="<?php echo ($var["money_id"]); ?>" /></td>
                        <td><?php echo ($var["money_id"]); ?></td>
                        <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                        <td><?php echo round($var['money']/100,2);?></td>
                        <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                        <td><?php echo ($var["create_ip"]); ?></td>
                        <td><?php if(($var["type"]) == "tuan"): ?>抢购订单<?php else: if(($var["type"]) == "ele"): ?>餐饮订餐<?php else: ?>商城订单<?php endif; endif; ?></td>
                    <td><?php echo ($var["order_id"]); ?></td>
                    <td><?php echo ($var["intro"]); ?></td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
    </form>
</div>
</div>

</div>
</body>
</html>