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
        <li class="li2 li3">城市站点</li>
    </ul>
</div>
<div class="main-jsgl">
    <p class="attention"><span>注意：</span>城市站点设置后，启用后前台才能看到该站点</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin: 10px 20px;">
            <div class="left">
                <?php echo BA('city/create','','添加站点');?>
            </div>
            <form method="post" action="<?php echo U('city/index');?>">
                <div class="right">
                    <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="  搜索"  class="inptButton" />
                </div>
            </form>
        </div>
        <form target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="city_id" /></td>
                        <td class="w50">ID</td>
                        <td>站点名称</td>
                        <td>站点拼音</td>
                        <td>是否开启(0关闭1开启)</td>
                 
                        <td class="w120">操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_city_id" type="checkbox" name="city_id[]" value="<?php echo ($var["city_id"]); ?>"/></td>
                            <td><?php echo ($var["city_id"]); ?></td>
                            <td><?php echo ($var["name"]); ?></td>
                            <td><?php echo ($var["pinyin"]); ?></td>
                            <td><?php echo ($var["is_open"]); ?></td>
              

                            <td>
                                <?php echo BA('city/edit',array("city_id"=>$var["city_id"]),'编辑','','remberBtn');?>
                                <?php echo BA('city/delete',array("city_id"=>$var["city_id"]),'删除','act','remberBtn');?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr">
                <div class="left">
                    <?php echo BA('city/delete','','批量删除','list','a2');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>