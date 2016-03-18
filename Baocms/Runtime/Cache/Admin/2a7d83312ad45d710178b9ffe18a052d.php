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
        <li class="li1">手机</li>
        <li class="li2">手机功能</li>
        <li class="li2 li3">消息管理</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：如果不选择用户，那么就是全局的消息,如果外链地址不为空的话，那么点击消息的时候将会直接打开外链</span> </p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <?php echo BA('msg/create','','添加内容');?>
            </div>
            <div class="right">
                <form class="" method="post"  action="<?php echo U('msg/index');?>"> 
                    <div class="seleHidden" id="seleHidden">
                        <span>搜索</span>
                        <input type="text"  class="inptText" name="keyword" value="<?php echo ($keyword); ?>"  />
                        <input type="submit" value=" 搜索"  class="inptButton" />
                    </div> 
                </form>
            </div>
            <div class="clear"></div>
        </div>
        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="msg_id" /></td>
                        <td class="w50">ID</td>
                        <td>用户</td>
                        <td>类型</td>
                        <td>标题</td>
                        <td>描述</td>
                        <td>外链地址</td>
                        <td>创建时间</td>
                        <td>创建IP</td>
                        <td>操作</td>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_msg_id" type="checkbox" name="msg_id[]" value="<?php echo ($var["msg_id"]); ?>" /></td>
                            <td><?php echo ($var["msg_id"]); ?></td>
                            <td><?php echo ($var["user_id"]); ?></td>
                            <td><?php echo ($types[$var['type']]); ?></td>
                            <td><?php echo ($var["title"]); ?></td>
                            <td><?php echo ($var["intro"]); ?></td>
                            <td><?php echo ($var["link_url"]); ?></td>
                            <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                            <td><?php echo ($var["create_ip"]); ?></td>
                            <td>
                                <?php echo BA('msg/edit',array("msg_id"=>$var["msg_id"]),'编辑','','remberBtn');?>
                                <?php echo BA('msg/delete',array("msg_id"=>$var["msg_id"]),'删除','act','remberBtn');?>
                            </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('msg/delete','','批量删除','list','a2');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>