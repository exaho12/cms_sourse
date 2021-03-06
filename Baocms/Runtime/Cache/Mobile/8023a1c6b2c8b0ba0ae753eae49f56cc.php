<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8">
		<title><?php if(!empty($seo_title)): echo ($seo_title); ?>_<?php endif; echo ($CONFIG["site"]["sitename"]); ?></title>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
		<link rel="stylesheet" href="/static/default/wap/css/base.css">
		<link rel="stylesheet" href="/static/default/wap/css/<?php echo ($ctl); ?>.css"/>
		<script src="/static/default/wap/js/jquery.js"></script>
		<script src="/static/default/wap/js/base.js"></script>
		<script src="/static/default/wap/other/layer.js"></script>
		<script src="/static/default/wap/other/roll.js"></script>
		<script src="/static/default/wap/js/public.js"></script>
	
        
        
		 <script>
            function bd_encrypt(gg_lat, gg_lon)   // 百度地图测距偏差 问题修复
            {
                var x_pi = 3.14159265358979324 * 3000.0 / 180.0;
                var x = gg_lon;
                var y = gg_lat;
                var z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * x_pi);
                var theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * x_pi);
                var bd_lon = z * Math.cos(theta) + 0.0065;
                var bd_lat = z * Math.sin(theta) + 0.006;
                dingwei('<?php echo U("mobile/index/dingwei",array("t"=>$nowtime,"lat"=>"llaatt","lng"=>"llnngg"));?>', bd_lat, bd_lon);
            }
            navigator.geolocation.getCurrentPosition(function (position) {
                bd_encrypt(position.coords.latitude, position.coords.longitude);
            });
        
        </script>
        
        
	</head>
	<body>
	<header class="top-fixed bg-yellow bg-inverse">
		<div class="top-back">
			<a class="top-addr" href="<?php echo U('mall/index');?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			在线商城
		</div>
		<div class="top-search" style="display:none;">
			<form method="post" action="<?php echo U('mall/index');?>">
				<input name="keyword" placeholder="输入商品的关键字"  />
				<button type="submit" class="icon-search"></button> 
			</form>
		</div>
		<div class="top-signed">
			<a id="search-btn" href="javascript:void(0);"><i class="icon-search"></i></a>
		</div>
	</header>
    <script>
		$(function(){
			$("#search-btn").click(function(){
				if($(".top-search").css("display")=='block'){
					$(".top-search").hide();
					$(".top-title").show(200);
				}
				else{
					$(".top-search").show();
					$(".top-title").hide(200);
				}
			});
			$("#search-bar li").each(function(e){
				$(this).click(function(){
					if($(this).hasClass("on")){
						$(this).parent().find("li").removeClass("on");
						$(this).removeClass("on");
						$(".serch-bar-mask").hide();
					}
					else{
						$(this).parent().find("li").removeClass("on");
						$(this).addClass("on");
						$(".serch-bar-mask").show();
					}
					$(".serch-bar-mask .serch-bar-mask-list").each(function(i){
						
						if(e==i){
							$(this).parent().find(".serch-bar-mask-list").hide();
							$(this).show();
						}
						else{
							$(this).hide();
						}
						$(this).find("li").click(function(){
							$(this).parent().find("li").removeClass("on");
							$(this).addClass("on");
						});
					});
				});
			});
		});
	</script>
	<div id="search-bar" class="search-bar">
		<ul class="line">
			<li class="x4"><span>分类</span><i></i></li>
			<li class="x4"><span>区域</span><i></i></li>
			<li class="x4"><span>排序</span><i></i></li>
		</ul>
	</div>
    <div class="serch-bar-mask" style="display:none;">
		<div class="serch-bar-mask-list">
			<ul>
			<li <?php if(empty($cat)): ?>class="on"<?php endif; ?> ><a href="<?php echo U('mall/index',array('cat'=>$item['cate_id'],'area'=>$area,'order'=>$order));?>">全部</a></li>
			<?php if(is_array($goodscates)): foreach($goodscates as $key=>$item): if(($item["parent_id"]) == "0"): ?><li <?php if($item['cate_id'] == $cat): ?>class="on"<?php endif; ?>><a href="<?php echo U('mall/index',array('cat'=>$item['cate_id'],'area'=>$area,'order'=>$order));?>"><?php echo ($item["cate_name"]); ?></a></li><?php endif; endforeach; endif; ?>
			
			</ul>
		</div>
		<div class="serch-bar-mask-list">
			<ul>
				<li <?php if(empty($area)): ?>class="on"<?php endif; ?> ><a href="<?php echo U('mall/index',array('cat'=>$cat,'area'=>0,'order'=>$order));?>">全部</a></li>
				<?php if(is_array($areas)): $i = 0; $__LIST__ = $areas;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li <?php if($item['area_id'] == $area): ?>class="on"<?php endif; ?> ><a href="<?php echo U('mall/index',array('cat'=>$cat,'area'=>$item['area_id'],'order'=>$order));?>"><?php echo ($item["area_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		
		<div class="serch-bar-mask-list">
			<ul>
				<li><a href="<?php echo U('mall/index',array('cat'=>$cat,'area'=>$area,'business'=>$var['business_id'],'order'=>'1'));?>">按照时间</a></li>
				<li><a href="<?php echo U('mall/index',array('cat'=>$cat,'area'=>$area,'business'=>$var['business_id'],'order'=>'2'));?>">按照销量</a></li>
			</ul>
		</div>
	</div>
	
	<div class="blank-40"></div>
	<div class="item-list" id="item-list">
		<ul></ul>
	</div>

	<div class="mall-cart">
		<a href="<?php echo U('mall/cart');?>">
		<div class="round radius-circle"><div class="badge-corner"><i class="icon-shopping-cart"></i><span class="badge bg-red"><?php echo ($cartnum); ?></span></div></div>
		</a>
	</div>
	
    <script>
        $(document).ready(function () {
            loaddata('<?php echo ($nextpage); ?>', $("#item-list ul"), true);
        });
    </script>
	
<div class="footer">
    <a href="<?php echo U('mcenter/member/index');?>">客户端</a> | 
    <a href="<?php echo U('mcenter/apply/index');?>" title="商家入驻">商家入驻</a>  | 
    <a href="<?php echo U('mcenter/member/index');?>" title="帮助">帮助</a>                          
</div>
<div class="blank-20"></div>
<?php if($CONFIG[other][footer] == 1): ?><footer class="foot-fixed">
<a class="foot-item <?php if(($ctl == 'index') AND ($act != 'more')): ?>active<?php endif; ?>" href="<?php echo u('index/index');?>">		
<span class="icon icon-home"></span>
<span class="foot-label">首页</span>
</a>

<a class="foot-item <?php if(($ctl) == "tuan"): ?>active<?php endif; ?>" href="<?php echo u('tuan/index');?>">			
<span class="icon icon-cart-plus"></span><span class="foot-label">抢购</span></a>

<a class="foot-item <?php if(($ctl) == "near"): ?>active<?php endif; ?>" href="<?php echo u('community/index');?>">			
<span class="icon icon-star-o"></span><span class="foot-label">小区</span></a>

<a class="foot-item <?php if(($ctl) == "mcenter"): ?>active<?php endif; ?>" href="<?php echo u('mcenter/index/index');?>">			
<span class="icon icon-user"></span><span class="foot-label">我的</span></a>
<a class="foot-item <?php if(($ctl == 'index') AND ($act == 'more')): ?>active<?php endif; ?>" href="<?php echo u('index/more');?>">			
<span class="icon icon-ellipsis-h"></span><span class="foot-label">更多</span></a>
</footer>
<?php else: endif; ?>

<iframe id="x-frame" name="x-frame" style="display:none;">
</iframe>
</body>
</html>