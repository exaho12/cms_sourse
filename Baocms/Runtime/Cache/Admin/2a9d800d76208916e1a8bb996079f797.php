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
        <li class="li2">商家产品</li>
        <li class="li2 li3">商品列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>商家必须入住了商城才能发布产品</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('goods/create','','添加内容');?>  
            </div>
            <div class="right">
                <form method="post" action="<?php echo U('goods/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <div class="seleK">
                            <label>
                                <input type="hidden" id="shop_id" name="shop_id" value="<?php echo (($shop_id)?($shop_id):''); ?>"/>
                                <input type="text"   id="shop_name" name="shop_name" value="<?php echo ($shop_name); ?>" class="inptText w200" />
                                <a mini="select"  w="1000" h="600" href="<?php echo U('shop/select');?>" class="sumit">选择商家</a>
                            </label>
                        <span>分类</span>
                        <select id="cate_id" name="cate_id" class="selecttop w120">
                            <option value="0">请选择...</option>
                            <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> > &nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
                        </select>
                        <span>  状态：</span>   

                        <select class="selecttop" name="audit">
                            <option value="0"  >全部</option>
                            <option value="-1" <?php if(($audit) == "-1"): ?>selected="selected"<?php endif; ?> >等待审核</option>
                            <option value="1" <?php if(($audit) == "1"): ?>selected="selected"<?php endif; ?>>正常</option>
                        </select>
                        <span>  关键字：</span>  
                        <input type="text" name="keyword" value="<?php echo (($keyword)?($keyword):''); ?>" class="inptText" /><input type="submit" class="inptButton" value="  搜索" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="goods_id" /></td>
                        <td class="w50">ID</td>
                        <td>产品名称</td>
                        <td>商家</td>
                        <td>缩略图</td>
                        <td>市场价格</td>
                        <td>商城价格</td>
                        <td>结算价格</td>
                        <td>卖出数量</td>
                        <td>浏览量</td>
                        <td>创建时间</td>
                        <td>创建IP</td>
                        <td>是否审核</td>

                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_goods_id" type="checkbox" name="goods_id[]" value="<?php echo ($var["goods_id"]); ?>" /> </td>
                            <td><?php echo ($var["goods_id"]); ?></td>
                            <td><?php echo ($var["title"]); ?></td>
                            <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                            <td><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>" class="w80" /></td>
                            <td><?php echo ($var["price"]); ?></td>
                            <td><?php echo ($var["mall_price"]); ?></td>
                            <td><?php echo ($var["settlement_price"]); ?></td>
                            <td><?php echo ($var["sold_num"]); ?></td>
                            <td><?php echo ($var["views"]); ?></td>
                            <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                            <td><?php echo ($var["create_ip_area"]); echo ($var["create_ip"]); ?></td>
                            <td><?php if(($var["audit"]) == "0"): ?>等待审核<?php else: ?>正常<?php endif; ?></td>

                        <td>
                            <?php echo BA('goods/edit',array("goods_id"=>$var["goods_id"]),'编辑','','remberBtn');?>
                            <?php echo BA('goods/delete',array("goods_id"=>$var["goods_id"]),'删除','act','remberBtn');?>
                            <?php if(($var["audit"]) == "0"): echo BA('goods/audit',array("goods_id"=>$var["goods_id"]),'审核','act','remberBtn'); endif; ?>
                        </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('goods/delete','','批量删除','list','a2');?>
                    <?php echo BA('goods/audit','','批量审核','list','remberBtn');?>
                </div>
            </div>
        </form>
    </div>
    
</div>
</body>
</html>