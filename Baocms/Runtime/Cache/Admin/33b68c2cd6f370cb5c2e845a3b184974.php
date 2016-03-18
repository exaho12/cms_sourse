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
<div class="main-jsgl main-sc">
    <div class="jsglNr">
        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="right">    
                <form class="search_form" method="post" action="<?php echo U('shop/select');?>">
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
        <form method="post" action="<?php echo U('shop/select');?>">
            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                        <label>
                            <span>分类：</span>
                            <select id="cate_id" name="cate_id" class="select w100" style="margin-right: 10px;">
                                <option value="0">请选择...</option>
                                <?php if(is_array($cates)): foreach($cates as $key=>$var): if(($var["parent_id"]) == "0"): ?><option value="<?php echo ($var["cate_id"]); ?>"  <?php if(($var["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> ><?php echo ($var["cate_name"]); ?></option>                
                                    <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $var["cate_id"]): ?><option value="<?php echo ($var2["cate_id"]); ?>"  <?php if(($var2["cate_id"]) == $cate_id): ?>selected="selected"<?php endif; ?> > &nbsp;&nbsp;<?php echo ($var2["cate_name"]); ?></option><?php endif; endforeach; endif; endif; endforeach; endif; ?>
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
                            <input type="text" name="keyword" value="<?php echo ($keyword); ?>" class="inptText w120" />
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
                    <td class="w50">ID</td>
                    <td>分类</td>
                    <td>所在区域商圈</td>
                    <td>商铺名称</td>
                    <td>店铺电话</td>
                </tr>
                <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>
                        <td><input  rel="<?php echo ($var["shop_name"]); ?>"  type="radio" name="shop_id" value="<?php echo ($var["shop_id"]); ?>" /> <?php echo ($var["shop_id"]); ?></td>
                        <td><?php echo ($cates[$var['cate_id']]['cate_name']); ?></td>
                        <td><?php echo ($citys[$var['city_id']]['name']); ?>-<?php echo ($areas[$var['area_id']]['area_name']); ?>-<?php echo ($business[$var['business_id']]['business_name']); ?></td>
                        <td><?php echo ($var["shop_name"]); ?></td>
                        <td><?php echo ($var["tel"]); ?></td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
                <input style="border:1px solid #dbdbdb; width: 100px; height: 38px; line-height: 38px; text-align: center;" type="button" id="select_btn" class="remberBtn" value="确定选择" />
            </div>
        </div>
    </form>
</div>
</div>

<script>
    $(document).ready(function (e) {
        $("#select_btn").click(function () {
            $("input[name='shop_id']").each(function (a) {
                if ($(this).prop("checked") == true) {
                    parent.selectCallBack('shop_id', 'shop_name', $(this).val(), $(this).attr('rel'));
                }
            });
        });

    });
</script>

</div>
</body>
</html>