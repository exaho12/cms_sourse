<?php if (!defined('THINK_PATH')) exit();?>
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
        <li class="li1">商城</li>
        <li class="li2">团购</li>
        <li class="li2 li3">团购列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('tuan/create','','添加抢购');?>
            </div>
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('tuan/index');?>"> 
                    <div class="seleHidden" id="seleHidden">
                        <div class="seleK">
                            <label>
                                <input type="hidden" id="shop_id" name="shop_id" value="<?php echo (($shop_id)?($shop_id):''); ?>"/>
                                <input type="text"   id="shop_name" name="shop_name" value="<?php echo ($shop_name); ?>" class="inptText w200" />
                                <a  href="<?php echo U('shop/select');?>" mini="select" w="800" h="600" class="sumit">选择商家</a>  
                            </label>
                            <label>
                                <span>分类</span>
                                <select id="cate_id" name="cate_id" class="selecttop">
                                    <option value="0">请选择...</option>
                                    <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                        <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> > &nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
                                </select>
                            </label>
                            <label>
                                <span>状态</span>
                                <select name="audit" class="selecttop">
                                    <option value="0"  >全部</option>
                                    <option value="-1" <?php if(($audit) == "-1"): ?>selected="selected"<?php endif; ?> >等待审核</option>
                                    <option value="1" <?php if(($audit) == "1"): ?>selected="selected"<?php endif; ?>>正常</option>
                                </select>
                            </label>
                            <label>
                                <span>关键字：</span>
                                <input type="text" name="keyword" id="keyword"  value="<?php echo ($keyword); ?>"   class="inptText" />
                                <input type="submit" value="  搜索"  class="inptButton" />
                            </label>
                        </div>
                    </div> 
                </form>

                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>

                        <td class="w50"><input type="checkbox" class="checkAll" rel="tuan_id" /></td>
                        <td class="w50">ID</td>
                        <td>商家</td>
                        <td>抢购标题</td>
                        <td>图片</td>
                        <td>市场价格</td>
                        <td>抢购价格</td>
                        <td>售出数</td>
                        <td>开始时间</td>
                        <td>结束时间</td>
                        <td>创建时间</td>
                        <td>创建IP</td>
                        <td>是否审核</td>
                        <td>操作</td>

                    </tr>
                    <?php if(is_array($list)): $index = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$var): $mod = ($index % 2 );++$index;?><tr>
                            <td><input class="child_tuan_id" type="checkbox" name="tuan_id[]" value="<?php echo ($var["tuan_id"]); ?>" /> </td>
                            <td><?php echo ($var["tuan_id"]); ?></td>
                            <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                            <td><?php echo ($var["title"]); ?></td>
                            <td><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>" class="w80" /></td>
                            <td><?php echo ($var["price"]); ?></td>
                            <td><?php echo ($var["tuan_price"]); ?></td>
                            <td><?php echo ($var["sold_num"]); ?></td>
                            <td><?php echo ($var["bg_date"]); ?></td>
                            <td><?php echo ($var["end_date"]); ?></td>
                            <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                            <td><?php echo ($var["create_ip_area"]); echo ($var["create_ip"]); ?></td>
                            <!--<td><?php if(($var["is_seckill"]) == "0"): ?>未开启<?php else: ?>正在秒杀<?php endif; ?></td> -->
                            <td><?php if(($var["audit"]) == "0"): ?>等待审核<?php else: ?>正常<?php endif; ?></td>
                        <td>
                            <?php if(($var["audit"]) != "0"): echo BA('zhuanti/addgoods',array("goods_id"=>$var["tuan_id"]),'专题','','remberBtn'); endif; ?>
                            <?php echo BA('tuan/edit',array("tuan_id"=>$var["tuan_id"]),'编辑','','remberBtn');?>
                            <?php echo BA('tuan/delete',array("tuan_id"=>$var["tuan_id"]),'删除','act','remberBtn');?>
                            <?php if(($var["audit"]) == "0"): echo BA('tuan/audit',array("tuan_id"=>$var["tuan_id"]),'审核','act','remberBtn'); endif; ?>
                        </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('tuan/delete','','批量删除','list',' a2');?>
                    <?php echo BA('tuan/audit','','批量审核','list',' remberBtn');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>