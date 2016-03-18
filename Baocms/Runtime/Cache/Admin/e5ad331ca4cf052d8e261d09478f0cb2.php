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
        <li class="li1">餐饮管理</li>
        <li class="li2">菜单管理</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <div class="jsglNr">
        <div class="selectNr" style="border-top: none; margin-top: 0px;">
            <div class="left">
            </div>
            <div class="right">
                <form method="post" action="<?php echo U('eleproduct/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <div class="seleK">
                            <label>
                                <input type="hidden" id="shop_id" name="shop_id" value="<?php echo (($shop_id)?($shop_id):''); ?>"/>
                                <input type="text"   id="shop_name" name="shop_name" value="<?php echo ($shop_name); ?>" class="text w200" />
                                <a mini="select"  w="1000" h="600" href="<?php echo U('shop/select');?>" class="sumit">选择商家</a>
                            </label>
                            <label>
                                <span>  关键字：</span>   <input type="text" name="keyword" value="<?php echo (($keyword)?($keyword):''); ?>" class="inptText" /><input type="submit" class="inptButton" value="   搜索" /></label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="product_id" /></td>
                        <td class="w50">ID</td>
                        <td>菜名</td>
                        <td>商家</td>
                        <td>分类</td>
                        <td>缩略图</td>
                        <td>价格</td>
                        <td>结算价格</td>
                        <td>是否新品</td>
                        <td>是否热门</td>
                        <td>是否推荐</td>
                        <td>卖出数</td>
                        <td>月卖出数</td>
                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_product_id" type="checkbox" name="product_id[]" value="<?php echo ($var["product_id"]); ?>" /></td>
                            <td><?php echo ($var["product_id"]); ?></td>
                            <td><?php echo ($var["product_name"]); ?></td>
                            <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                            <td><?php echo ($cates[$var['cate_id']]['cate_name']); ?></td>
                            <td><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>" class="w80" /></td>
                            <td><?php echo round($var['price']/100,2);?></td>
                            <td><?php echo round($var['settlement_price']/100,2);?></td>
                            <td><?php if(($var["is_new"]) == "1"): ?>新品<?php else: ?>无<?php endif; ?></td>
                        <td>
                        <?php if(($var["is_hot"]) == "1"): ?>热门<?php else: ?>无<?php endif; ?>
                        </td>
                        <td>
                        <?php if(($var["is_tuijian"]) == "1"): ?>推荐<?php else: ?>无<?php endif; ?>
                        </td>
                        <td><?php echo ($var["sold_num"]); ?></td>
                        <td><?php echo ($var["month_num"]); ?></td>
                        <td>
                            <?php echo BA('eleproduct/edit',array("product_id"=>$var["product_id"]),'编辑','','remberBtn');?>
                            <?php echo BA('eleproduct/delete',array("product_id"=>$var["product_id"]),'删除','act','remberBtn');?>
                            <?php if(($var["audit"]) == "0"): echo BA('eleproduct/audit',array("product_id"=>$var["product_id"]),'审核','act','remberBtn'); endif; ?>
                        </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('eleproduct/delete','','批量删除','list','a2');?>
                    
                        <?php echo BA('eleproduct/audit','','批量审核','list','a1');?>

                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>