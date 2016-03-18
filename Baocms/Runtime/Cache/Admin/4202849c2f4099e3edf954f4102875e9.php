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
        <li class="li1">频道</li>
        <li class="li2">分类信息</li>
        <li class="li2 li3">信息列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>不需要启用的字段就不需要填写</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top: none;">
            <div class="left">
                <?php echo BA('life/create','','添加内容');?>  
            </div>
            <div class="right">
                <form method="post" action="<?php echo U('life/index');?>">
                    <div class="seleHidden" id="seleHidden">
                        <div class="seleK">
                            <label>
                                <span>分类</span>
                                <select id="cate_id" name="cate_id" class="select manageSelect">
                                    <?php if(is_array($channelmeans)): foreach($channelmeans as $key=>$item): ?><option value=""><?php echo ($item); ?></option>
                                        <?php if(is_array($cates)): foreach($cates as $k=>$it): if(($it["channel_id"]) == $key): ?><option <?php if(($cate_id) == $it["cate_id"]): ?>selected="selected"<?php endif; ?> value="<?php echo ($it["cate_id"]); ?>">&nbsp;&nbsp;-<?php echo ($it["cate_name"]); ?></option><?php endif; endforeach; endif; endforeach; endif; ?>
                                </select>
                            </label>
                            <label>
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo (($user_id)?($user_id):''); ?>" />
                                <input type="text" name="nickname" id="nickname"  value="<?php echo ($nickname); ?>"   class="text" />
                                <a mini="select"  w="800" h="600" href="<?php echo U('user/select');?>" class="sumit">选择用户</a>
                            </label>
                            <label>
                                <span>  区域：</span>   <select name="area_id" id="area_id" class="select manageSelect">
                                    <option value="0">请选择...</option>
                                    <?php if(is_array($areas)): foreach($areas as $key=>$var): ?><option value="<?php echo ($var["area_id"]); ?>"  <?php if(($var["area_id"]) == $area_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["area_name"]); ?></option><?php endforeach; endif; ?>   
                                </select>
                            </label>
                            <label>
                                <span>  关键字：</span>   <input type="text" name="keyword" value="<?php echo (($keyword)?($keyword):''); ?>" class="inptText" /><input type="submit" class="inptButton" value="   搜索" />
                            </label>
                        </div>
                    </div>
                </form>
                <div class="clear"></div>
            </div>
        </div>
        <form  target="baocms_frm" method="post">
            <div class="tableBox">
                <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                    <tr>
                        <td class="w50"><input type="checkbox" class="checkAll" rel="life_id" /></td>
                        <td class="w50">ID</td>
                        <td>标题</td>
                        <td>分类</td>
                        <td>地区</td>
                        <td>商圈</td>
                        <td>用户</td>
                        <td>联系人</td>
                        <td>电话</td>
                        <td>QQ</td>
                        <td>联系地址</td>
                        <td>创建时间</td>
                        <td>创建IP</td>

                        <td>操作</td>
                    </tr>
                    <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                            <td><input class="child_life_id" type="checkbox" name="life_id[]" value="<?php echo ($var["life_id"]); ?>" /></td>
                            <td><?php echo ($var["life_id"]); ?></td>
                            <td><?php echo ($var["title"]); ?></td>
                            <td><?php echo ($cates[$var['cate_id']]['cate_name']); ?></td>
                            <td><?php echo ($areas[$var['area_id']]['area_name']); ?></td>
                            <td><?php echo ($business[$var['business_id']]['business_name']); ?></td>
                            <td><?php echo ($users[$var['user_id']]['account']); ?></td>
                            <td><?php echo ($var["contact"]); ?></td>
                            <td><?php echo ($var["mobile"]); ?></td>
                            <td><?php echo ($var["qq"]); ?></td>
                            <td><?php echo ($var["addr"]); ?></td>
                            <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                            <td><?php echo ($var["create_ip"]); ?></td>

                            <td>
                                <?php echo BA('life/edit',array("life_id"=>$var["life_id"]),'编辑','','remberBtn');?>
                                <?php echo BA('life/delete',array("life_id"=>$var["life_id"]),'删除','act','remberBtn');?>
                                <?php if(($var["audit"]) == "0"): echo BA('life/audit',array("life_id"=>$var["life_id"]),'审核','act','remberBtn'); endif; ?>

                            </td>
                        </tr><?php endforeach; endif; ?>
                </table>
                <?php echo ($page); ?>
            </div>
            <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
                <div class="left">
                    <?php echo BA('life/delete','','批量删除','list','a2');?>
                    <?php echo BA('life/audit','','批量审核','list','remberBtn');?>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
</body>
</html>