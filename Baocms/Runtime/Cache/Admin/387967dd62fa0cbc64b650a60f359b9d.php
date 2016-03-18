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
   <div class="main"><br />
<style>
.w20{ width:20px;}
.main-cate .jsglNr .selectNr .left a.on{ background-color:#F00}
.main-cate .jsglNr .tableBox table tr td a.remberBtn2{font-size: 12px;line-height: 20px;color: #FFF;background-color: #F00; display: inline-block;height: 20px;padding-right: 10px;padding-left: 10px;border-radius: 0px;margin: 0 3px;}
.main-cate .jsglNr .tableBox table tr td a.remberBtn3{font-size: 12px;line-height: 20px;color: #FFF;background-color:#ccc; display: inline-block;height: 20px;padding-right: 10px;padding-left: 10px;border-radius: 0px;margin: 0 3px;}
.main-cate .jsglNr .tableBox table tr td a.remberBtn4{font-size: 12px;line-height: 20px;color: #FFF;background-color:#AAADB8; display: inline-block;height: 20px;padding-right: 10px;padding-left: 10px;border-radius: 0px;margin: 0 3px;}
</style>
<div class="mainBt">
    <ul>
        <li class="li1">导航</li>
        <li class="li2">手机导航</li>
        <li class="li2 li3">导航列表</li>
    </ul>
</div>
<div class="main-cate">
    <p class="attention"><span>注意：</span>这里管理你的导航，添加菜单成功后无法编辑类型，模板调用方式：<a style="color:#F00; font-weight:bold" target="_blank" href="http://www.fengmiyuanma.com/10326.html">点击查看</a> 或者访问：http://www.fengmiyuanma.com/10326.html </p>
    <div class="jsglNr">
        <form id="cate_action" action="" target="baocms_frm" method="post">
            <div class="selectNr" style="border-top: 1px solid #dbdbdb;">
                <div class="left">
                    <?php echo BA('navigation/create','','添加导航','load','',600,600);?>
                             <a <?php if(($aready == '4') OR ($aready == '')): ?>class="on"<?php endif; ?> href="<?php echo U('admin/navigation/index', array('aready' => 4));?>">电脑主导航列表</a>
                    <a <?php if(($aready) == "2"): ?>class="on"<?php endif; ?> href="<?php echo U('admin/navigation/index', array('aready' => 2));?>">手机首页导航列表</a>
           
     
                </div>
                <div class="right">
                    <?php echo BA('navigation/update','','更新','list','remberBtn');?>
                </div>
            </div>
            <div class="tableBox">
                <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF; text-align:center;">
                    <tr bgcolor="#F5F6FA" height="48px;" style="color:#333; font-size:16px; line-height:48px;">
                      <td>UID</td>
                        <td>导航名称</td>
                        <td>排序</td>
                        <td>ioc图标/颜色</td>
                        <td>url连接</td>
                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): if(($var["parent_id"] == 0)): ?><tr bgcolor="#fff" height="48px" style="font-size:14px; color:#333; text-align:left; line-height:48px;">
                              <td style="padding-left:20px;"><?php echo ($var["nav_id"]); ?></td>

                                <td style="padding-left:10px;"><?php echo ($var["nav_name"]); ?>
                                <?php if(($var["closed"]) == "0"): ?><a class="remberBtn2">显</a><?php endif; ?>
                                <?php if(($var["closed"]) == "1"): ?><a class="remberBtn3">隐</a><?php endif; ?>
                                <?php if(($var["target"]) == "1"): ?><a class="remberBtn4">新窗</a><?php endif; ?>
                                <?php if(($var["status"]) == "2"): ?><a class="remberBtn4">手机首页导航</a><?php endif; ?>
                                <?php if(($var["status"]) == "4"): ?><a class="remberBtn4">电脑主导航</a><?php endif; ?>
                                </td>
                                <td style="padding-left:10px;"><input name="orderby[<?php echo ($var["nav_id"]); ?>]" value="<?php echo ($var["orderby"]); ?>" type="text" class="remberinput w20" /></td>					
                                <td style="padding-left:10px;"><?php echo ($var["ioc"]); ?> <?php echo ($var["colour"]); ?></td>
                                <td style="padding-left:10px;"><?php echo ($var["url"]); ?></td>
          
                                <td style="text-align: center;">
                                    <?php echo BA('navigation/edit',array("nav_id"=>$var["nav_id"]),'编辑','load','remberBtn',600,600);?>
                                    <?php echo BA('navigation/delete',array("nav_id"=>$var["nav_id"]),'删除','act','remberBtn');?>
                                </td>

                            </tr><?php endif; endforeach; endif; ?>        
                </table>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>