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
        <li class="li2 li3">微店审核列表</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <p class="attention"><span>注意：</span>前台申请 后台审核！</p>
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="right">
                <form class="search_form" method="post" action="<?php echo U('weidian/index');?>">
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
        <form method="post" action="<?php echo U('weidian/index');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>微店类型：</span>
                            <select id="cate_id" name="cate_id" class="select w100">
                                <option value="0">请选择...</option>
                                <?php if(is_array($cates)): foreach($cates as $key=>$item): ?><option value="<?php echo ($item["cate_id"]); ?>"><?php echo ($item["cate_name"]); ?></option><?php endforeach; endif; ?>
                            </select>
                        </label>
                        <label>
                            <span>区域：</span>
                             <select name="city_id" id="city_id"  class="select w100"></select>
                            <select name="area_id" id="area_id"  class="select w100"></select>
                     
                        </label>
                <script src="<?php echo U('app/datas/cityarea');?>"></script>
                <script>
                    var city_id = <?php echo (int)$city_id;?>;
                    var area_id = <?php echo (int)$area_id;?>;
                    function changeCity(cid){
                        var area_str = '<option value="0">请选择.....</option>';
                        for(a in cityareas.area){
                           if(cityareas.area[a].city_id ==cid){
                                if(area_id == cityareas.area[a].area_id){
                                    area_str += '<option selected="selected" value="'+cityareas.area[a].area_id+'">'+cityareas.area[a].area_name+'</option>';
                                }else{
                                     area_str += '<option value="'+cityareas.area[a].area_id+'">'+cityareas.area[a].area_name+'</option>';
                                }  
                            }
                        }
                        $("#area_id").html(area_str);
                    }
                    $(document).ready(function(){
                        var city_str = '<option value="0">请选择.....</option>';
                        for(a in cityareas.city){
                           if(city_id == cityareas.city[a].city_id){
                               city_str += '<option selected="selected" value="'+cityareas.city[a].city_id+'">'+cityareas.city[a].name+'</option>';
                           }else{
                                city_str += '<option value="'+cityareas.city[a].city_id+'">'+cityareas.city[a].name+'</option>';
                           }  
                        }
                        $("#city_id").html(city_str);
                        if(city_id){
                            changeCity(city_id);
                        }
                        $("#city_id").change(function(){
                            city_id = $(this).val();
                            changeCity($(this).val());
                        });
                    });
                </script>
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
            <table bordercolor="#dbdbdb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                <tr>
                    <td class="w50"><input type="checkbox" class="checkAll" rel="shop_id" /></td>
                    <td class="w50">ID</td>
                    <td>商铺名称</td>
                    <td>营业时间</td>
                    <td>商铺LOGO</td>
                    <td>商铺形象照</td>
                    <td>店铺分类</td>
                    <td>申请时间</td>
                    <td>状态审核以及时间</td>
                    <td>操作</td>
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input class="child_shop_id" type="checkbox" name="id[]" value="<?php echo ($var["id"]); ?>" /></td>
                        <td><?php echo ($var["id"]); ?></td>
                        <td><?php echo ($var["weidian_name"]); ?></td>
                        <td><?php echo ($var["business_time"]); ?></td>
                        <td><img src="__ROOT__/attachs/<?php echo ($var["logo"]); ?>" class="w80" /></td>
                        <td><img src="__ROOT__/attachs/<?php echo ($var["pic"]); ?>" class="w80" /></td>
                        <td><?php echo ($cates[$var['cate_id']]['cate_name']); ?></td>
                        <td><?php echo (date('Y-m-d H:i:s',$var["reg_time"])); ?></td>
                        <td>
                        	<?php echo (date('Y-m-d H:i:s',$var["update_time"])); ?>
                            
                            <?php switch($var["audit"]): case "0": ?><span style="color:#ff5700;">待审核</span><?php break;?>
                                <?php case "1": ?><span style="color:#009944;">已通过</span><?php break;?>
                                <?php case "2": ?><span style="color:#ff0000;">已拒绝</span><?php break; endswitch;?>
                        </td>
                        <td>
                            <?php echo BA('weidian/edit',array("shop_id"=>$var["shop_id"]),'编辑','','remberBtn');?>
                            <?php echo BA('weidian/delete',array("wd_id"=>$var["id"]),'删除','act','remberBtn');?>
                            <?php if(($var["audit"]) == "0"): echo BA('weidian/audit',array("wd_id"=>$var["id"]),'审核','act','remberBtn'); endif; ?>
                        </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
                <?php echo BA('weidian/delete','','批量删除','list','a2');?>
                <?php echo BA('weidian/audit','','批量审核','list',' remberBtn');?>
            </div>
        </div>
    </form>
</div>
</div>

</div>
</body>
</html>