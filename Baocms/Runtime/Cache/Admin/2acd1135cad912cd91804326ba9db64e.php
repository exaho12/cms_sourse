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
        <li class="li2">商家新闻</li>
        <li class="li2 li3">新闻列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>商家新闻发布</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('shopnews/create','','添加内容');?>
            </div>
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('shopnews/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <span>关键字</span>
                        <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="   搜索"  class="inptButton" />
                    </div> 
                </form>
                <a href="javascript:void(0);" class="searchG">高级搜索</a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <form method="post" action="<?php echo U('shopnews/index');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>分类：</span>
                            <select id="cate_id" name="cate_id" class="select w100">
                                <option value="0">请选择...</option>
                                <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                    <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> > &nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
                            </select>
                        </label>
                       
                        <label>
                            <input type="hidden" id="shop_id" name="shop_id" value="<?php echo (($shop_id)?($shop_id):''); ?>"/>
                            <input class="text" type="text"   id="shop_name" name="shop_name" value="<?php echo ($shop_name); ?>"/>
                            <a mini="select"  w="1000" h="800" href="<?php echo U('shop/select');?>" class="sumit">选择商家</a>
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
                    <td class="w50"><input type="checkbox" class="checkAll" rel="news_id" /></td>
                    <td class="w50">ID</td>
                    <td>商家</td>
                    <td>分类</td>
                    <td>标题</td>
                    <td>创建时间</td>
                    <td>创建IP</td>
                    <td>浏览量</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input class="child_news_id" type="checkbox" name="news_id[]" value="<?php echo ($var["news_id"]); ?>" /></td>
                        <td><?php echo ($var["news_id"]); ?></td>
                        <td><?php echo ($shops[$var['shop_id']]['shop_name']); ?></td>
                        <td><?php echo ($cates[$var['cate_id']]['cate_name']); ?></td>
                        <td><?php echo ($var["title"]); ?></td>
                        <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                        <td><?php echo ($var["create_ip_area"]); echo ($var["create_ip"]); ?></td>
                        <td><?php echo ($var["views"]); ?></td>
                        <td><?php if(($var["audit"]) == "0"): ?>等待审核<?php else: ?>正常<?php endif; ?></td>
                    <td>
                        <?php echo BA('shopnews/edit',array("news_id"=>$var["news_id"]),'编辑','','remberBtn');?>
                        <?php echo BA('shopnews/delete',array("news_id"=>$var["news_id"]),'删除','act','remberBtn');?>
                        <?php if(($var["audit"]) == "0"): echo BA('shopnews/audit',array("news_id"=>$var["news_id"]),'审核','act','remberBtn'); endif; ?>

                    </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
                <?php echo BA('shopnews/audit','','批量审核','list','remberBtn');?>
                <?php echo BA('shopnews/delete','','批量删除','list',' a2');?>
            </div>
        </div>
    </form>
</div>
</div>

</div>
</body>
</html>