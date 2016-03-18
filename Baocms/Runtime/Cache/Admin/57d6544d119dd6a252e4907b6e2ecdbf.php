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
        <li class="li2">商家分类</li>
        <li class="li2 li3">分类列表</li>
    </ul>
</div>
<div class="main-cate">
    <p class="attention"><span>注意：</span>暂时2级分类</p>
    <div class="jsglNr">
        <div class="selectNr" style="border-top: 1px solid #dbdbdb;">
            <div class="left">
                <?php echo BA('shopcate/create','','添加一级分类','load','',600,550);?>
            </div>
        </div>

        <div class="tableBox">
            <form  target="baocms_frm" method="post">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF; text-align:center;">
                    <tr bgcolor="#FAE2E2" height="48px;" style="color:#333; font-size:16px; line-height:48px;">
                        <td>分类</td>
                        <td>排序</td>
                        <td>操作</td>
                    </tr>

                    <?php if(is_array($list)): foreach($list as $key=>$var): if(($var["parent_id"]) == "0"): ?><tr bgcolor="#f1f1f1" height="48px" style="font-size:14px; color:#545454; text-align:left; line-height:48px;">
                            <td style="padding-left:20px;"><?php echo ($var["cate_name"]); ?>(<?php echo ($var["cate_id"]); ?>)</td>
                            <td style="padding-left:70px;"><input name="orderby[<?php echo ($var["cate_id"]); ?>]" value="<?php echo ($var["orderby"]); ?>" type="text" class="remberinput w80" /></td>
                            <td style="text-align:center;">
                                <?php echo BA('shopcate/create',array("parent_id"=>$var['cate_id']),'添加子分类','load','remberBtn',600,550);?>
                                <?php if(($var["is_hot"]) == "0"): echo BA('shopcate/hots',array("cate_id"=>$var["cate_id"]),'设为热门','act','remberBtn');?>
                        <?php else: ?>
                        <?php echo BA('shopcate/hots',array("cate_id"=>$var["cate_id"]),'取消热门','act','remberBtn'); endif; ?>
                        <?php echo BA('shopcate/edit',array("cate_id"=>$var["cate_id"]),'编辑','load','remberBtn',600,550);?>
                        <?php echo BA('shopcate/delete',array("cate_id"=>$var["cate_id"]),'删除','act','remberBtn');?>
                        </td>
                        </tr>
                        <?php if(is_array($list)): foreach($list as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><tr height="48px" style="font-size:14px; color:#545454; text-align:center; line-height:48px;">
                                <td><?php echo ($var2["cate_name"]); ?>(<?php echo ($var2["cate_id"]); ?>)</td>
                                <td><input name="orderby[<?php echo ($var2["cate_id"]); ?>]" value="<?php echo ($var2["orderby"]); ?>" type="text" class="remberinput w80" /></td>
                                <td>
                                    <?php if(($var2["is_hot"]) == "0"): echo BA('shopcate/hots',array("cate_id"=>$var2["cate_id"]),'设为热门','act','remberBtn');?>
                            <?php else: ?>
                            <?php echo BA('shopcate/hots',array("cate_id"=>$var2["cate_id"]),'取消热门','act','remberBtn'); endif; ?>
                            <?php echo BA('shopcate/edit',array("cate_id"=>$var2["cate_id"]),'编辑','load','remberBtn',600,550);?>
                            <?php echo BA('shopcate/delete',array("cate_id"=>$var2["cate_id"]),'删除','act','remberBtn');?>
                            </td>
                            </tr><?php endif; endforeach; endif; endif; endforeach; endif; ?>     
                </table>
                <div class="selectNr">
                    <div class="left">
                        <?php echo BA('shopcate/update','','更新','list','remberBtn');?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
</body>
</html>