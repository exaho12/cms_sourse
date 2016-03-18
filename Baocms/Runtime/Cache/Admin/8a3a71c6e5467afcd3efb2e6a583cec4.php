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
        <li class="li2">商家管理</li>
        <li class="li2 li3">商家图片</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span> 由于上传组件是为了以后静态文件分离使用的，所以在删除记录后不会删除对应图片！</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('shoppic/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <span>关键字(电话或商户名称)</span>
                        <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="   搜索"  class="inptButton" />
                    </div> 
                </form>
                <a href="javascript:void(0);" class="searchG">高级搜索</a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <form method="post" action="<?php echo U('shoppic/index');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>状态：</span>
                            <select class="select w100" name="audit">
                                <option value="0"  >全部</option>
                                <option value="-1" <?php if(($audit) == "-1"): ?>selected="selected"<?php endif; ?> >等待审核</option>
                                <option value="1" <?php if(($audit) == "1"): ?>selected="selected"<?php endif; ?>>正常</option>
                            </select>
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
            <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                <tr>
                    <td class="w50"><input type="checkbox" class="checkAll" rel="pic_id" /></td>
                    <td class="w50">ID</td>
                    <td>商家</td>
                    <td>图片</td>
                    <td>标题</td>
                    <td>排序</td>
                    <td>创建时间</td>
                    <td>创建IP</td>
                    <td>操作</td>
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input class="child_pic_id" type="checkbox" name="pic_id[]" value="<?php echo ($var["pic_id"]); ?>" /></td>
                        <td><?php echo ($var["pic_id"]); ?></td>
                        <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                        <td><img src="__ROOT__/attachs/<?php echo ($var["photo"]); ?>" width="60" height="60"/></td>
                        <td><?php echo ($var["title"]); ?></td>
                        <td><?php echo ($var["orderby"]); ?></td>
                        <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                        <td><?php echo ($var["create_ip_area"]); echo ($var["create_ip"]); ?></td>
                        <td>
                            <?php echo BA('shoppic/delete',array("pic_id"=>$var["pic_id"]),'删除','act','remberBtn');?>
                            <?php if(($var["audit"]) == "0"): echo BA('shoppic/audit',array("pic_id"=>$var["pic_id"]),'审核','act','remberBtn'); endif; ?>
                        </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
                <?php echo BA('shoppic/audit','','批量审核','list',' remberBtn');?>
                <?php echo BA('shoppic/delete','','批量删除','list',' a2');?>
            </div>
        </div>
    </form>
</div>
</div>

</div>
</body>
</html>