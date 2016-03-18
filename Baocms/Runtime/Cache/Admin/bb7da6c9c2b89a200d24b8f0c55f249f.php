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
<!DOCTYPE html>
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
        <li class="li1">频道</li>
        <li class="li2">餐饮频道</li>
        <li class="li2 li3">商家列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>如果未添加商家，那么不能发布菜单</p>
    <div class="jsglNr">
        <div class="selectNr" style="border-top: none; margin-top: 0px;">
            <div class="left">
                <?php echo BA('ele/create','','添加内容');?>  
            </div>
            <div class="right">
                <form method="post" action="<?php echo U('ele/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <span>分类</span>
                        <select id="cate_id" name="cate_id" class="select">
                            <option value="0">请选择...</option>
                            <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
                        </select>


                        <span>  区域：</span>   
                        <select name="area_id" id="area_id" class="select manageSelect">
                            <option value="0">请选择...</option>
                            <?php if(is_array($areas)): foreach($areas as $key=>$var): ?><option value="<?php echo ($var["area_id"]); ?>"  <?php if(($var["area_id"]) == $area_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["area_name"]); ?></option><?php endforeach; endif; ?>   
                        </select>
                        <span>  关键字：</span>   
                        <input type="text" name="keyword" value="<?php echo (($keyword)?($keyword):''); ?>" class="inptText" />
                        <input type="submit" class="inptButton" value="  搜索" />

                    </div>
                </form>
            </div>
        </div>
        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="shop_id" /></td>
                        <td class="w50">ID</td>
                        <td>商家名称</td>
                        <td>纬度</td>
                        <td>是否打洋</td>
                        <td>是否支持在线付款</td>
                        <td>是否返利</td>
                        <td>最高返利金额</td>
                        <td>新客户下单立减</td>
                        <td>满多少钱</td>
                        <td>减多少钱</td>
                        <td>配送费</td>
                        <td>起送价</td>
                        <td>卖出数</td>
                        <td>月卖出数</td>
                        <td>排序</td>
                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_shop_id" type="checkbox" name="shop_id[]" value="<?php echo ($var["shop_id"]); ?>" /></td>
                            <td><?php echo ($var["shop_id"]); ?></td>
                            <td><?php echo ($var["shop_name"]); ?></td>
                            <td><?php echo ($var["lat"]); ?></td>
                            <td>
                        <?php if(($var["is_open"]) == "1"): ?>营业中<?php else: ?>打烊了<?php endif; ?>
                        </td>
                        <td>
                        <?php if(($var["is_pay"]) == "1"): ?>支持在线付款<?php else: ?>不支持<?php endif; ?>
                        </td>
                        <td>
                        <?php if(($var["is_fan"]) == "1"): ?>返利<?php else: ?>无<?php endif; ?>
                        </td>
                        <td><?php echo round($var['fan_money']/100,2);?></td>
                        <td><?php if(($var["is_new"]) == "1"): ?>新单优惠<?php else: ?>无<?php endif; ?></td>
                        <td><?php echo round($var['full_money']/100,2);?></td>
                        <td><?php echo round($var['new_money']/100,2);?></td>
                        <td><?php echo round($var['logistics']/100,2);?></td>
                        <td><?php echo round($var['since_money']/100,2);?></td>
                        <td><?php echo ($var["sold_num"]); ?></td>
                        <td><?php echo ($var["month_num"]); ?></td>
                        <td><?php echo ($var["orderby"]); ?></td>
                        <td>
                            <?php echo BA('ele/edit',array("shop_id"=>$var["shop_id"]),'编辑','','remberBtn');?>
                            <?php echo BA('ele/delete',array("shop_id"=>$var["shop_id"]),'删除','act','remberBtn');?>
                            <?php if(($var["is_open"]) == "0"): echo BA('ele/opened',array("shop_id"=>$var["shop_id"],'type'=>'open'),'开始接客','act','remberBtn');?>
                        <?php else: ?>
                        <?php echo BA('ele/opened',array("shop_id"=>$var["shop_id"],'type'=>'closed'),'打烊','act','remberBtn'); endif; ?>
                        <a target="_blank" class="remberBtn" href="<?php echo U('shop/login',array('shop_id'=>$var['shop_id']));?>">管理商家</a>
                        </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('ele/delete','','批量删除','list','a2');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>