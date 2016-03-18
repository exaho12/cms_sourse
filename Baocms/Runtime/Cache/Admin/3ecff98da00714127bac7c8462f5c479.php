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
        <li class="li1">设置</li>
        <li class="li2">区域设置</li>
        <li class="li2 li3">商圈管理</li>
    </ul>
</div>
<div class="main-jsgl">
    <div class="jsglNr">
        <div class="selectNr">
            <div class="left">
                <?php echo BA('business/create',array('area_id'=>$area_id),'添加商圈','load','',600,360);?>
            </div>
        </div>
        <form target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td><input type="checkbox" class="checkAll" rel="business_id" /></td>
                        <td>ID</td>
                        <td>商圈名称</td>
                        <td>排序</td>
                        <td>操作</td>   
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_business_id" type="checkbox" name="business_id[]" value="<?php echo ($var["business_id"]); ?>" /></td>
                            <td> <?php echo ($var["business_id"]); ?></td>
                            <td><?php echo ($var["business_name"]); ?></td>
                            <td><?php echo ($var["orderby"]); ?></td>
                            <td>
                                <?php if(($var["is_hot"]) == "0"): echo BA('business/hots',array("business_id"=>$var["business_id"],'area_id'=>$area_id),'设为热门','act','remberBtn');?>
                        <?php else: ?>
                        <?php echo BA('business/hots',array("business_id"=>$var["business_id"],'area_id'=>$area_id),'取消热门','act','remberBtn'); endif; ?>
                        <?php echo BA('business/edit',array("business_id"=>$var["business_id"],'area_id'=>$area_id),'编辑','load','remberBtn',600,360);?>
                        <?php echo BA('business/delete',array("business_id"=>$var["business_id"],'area_id'=>$area_id),'删除','act','remberBtn');?>
                        </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr">
                <div class="left">
                    <?php echo BA('business/delete',array('area_id'=>$area_id),'批量删除','list',' a2');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>