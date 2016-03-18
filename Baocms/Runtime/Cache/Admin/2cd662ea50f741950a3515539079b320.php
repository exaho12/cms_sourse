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
        <li class="li2 li3">区域管理</li>
    </ul>
</div>
<div class="main-jsgl  main-sc">
    <div class="jsglNr">
        <div class="selectNr">
            <div class="left">
                <?php echo BA('area/create','','新增区域','load','',600,300);?>
            </div>
               <div class="right">
              <form method="post" action="<?php echo U('area/index');?>">
             
                    <div class="seleHidden" id="seleHidden">
                        <span>城市</span>
                        <select id="city_id" name="city_id" class="select">
                            <option value="0">请选择...</option>
                            <?php if(is_array($citys)): foreach($citys as $key=>$var): ?><option value="<?php echo ($var["city_id"]); ?>"  <?php if(($var["city_id"]) == $city_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["name"]); ?></option><?php endforeach; endif; ?>
                        </select>
                    <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText" /><input type="submit" value="  搜索"  class="inptButton" />
                    </div>
                    
            </form>
            </div>
        </div>
        <form target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td><input type="checkbox" class="checkAll" rel="area_id" /></td>
                        <td>ID</td>
                        <td>区域名称</td>
                        <td>所在城市</td>
                        <td>排序</td>
                        <td>操作</td>   
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_area_id" type="checkbox" name="area_id[]" value="<?php echo ($var["area_id"]); ?>" /></td>
                            <td><?php echo ($var["area_id"]); ?></td>
                            <td><?php echo ($var["area_name"]); ?></td>
                            <td><?php echo ($citys[$var['city_id']]['name']); ?></td>
                            <td><?php echo ($var["orderby"]); ?></td>
                            <td>
                                <?php echo BA('business/index',array("area_id"=>$var["area_id"]),'商圈管理','','remberBtn');?>
                                <?php echo BA('area/edit',array("area_id"=>$var["area_id"]),'编辑','load','remberBtn',600,300);?>
                                <?php echo BA('area/delete',array("area_id"=>$var["area_id"]),'删除','act','remberBtn');?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                  <?php echo ($page); ?>
            </div>
            <div class="selectNr">
                <div class="left">
                    <?php echo BA('area/delete','','批量删除','list',' a2');?>
                </div>
            </div>
        </form>

    </div>
</div>

</div>
</body>
</html>