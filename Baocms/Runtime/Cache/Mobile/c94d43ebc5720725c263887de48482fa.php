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
			<a class="top-addr" href="<?php echo U('index/index');?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			商家分类
		</div>
		<div class="top-search" style="display:none;">
			<form method="post" action="<?php echo U('shop/index');?>">
				<input name="keyword" placeholder="输入商家的关键字"  />
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
	});
	</script>
	
	<div class="line">
		<div id="roll" class="roller">
			<div class="bd">
				<ul>
				<?php  $cache = cache(array('type'=>'File','expire'=> 43200)); $token = md5("Ad,closed=0 AND site_id=53 and bg_date <= '{$today}' AND end_date >= '{$today}' ,0,5,43200,orderby asc,,"); if(!$items= $cache->get($token)){ $items = D("Ad")->where("closed=0 AND site_id=53 and bg_date <= '{$today}' AND end_date >= '{$today}' ")->order("orderby asc")->limit("0,5")->select(); $cache->set($token,$items); } ; $index=0; foreach($items as $item): $index++; ?><li><a href="<?php echo ($item["link_url"]); ?>" target="_blank" ><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" /></a></li> <?php endforeach; ?>
				</ul>
			</div>
			<div class="hd">
				<ul></ul>
			</div>
		</div>
	</div>
	<script type="text/javascript">	
		TouchSlide({ 
			slideCell:"#roll",
			titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			mainCell:".bd ul", 
			effect:"leftLoop", 
			autoPage:true //自动分页
		});
	</script>
	
	<div class="cate-list">
		<?php $i=0; foreach($shopcates as $val){if($val['parent_id'] == 0){$i++; ?>	
		<div class="classify" data-id="cate-<?php echo ($i); ?>"  data-click="0">
			<a href="<?php echo U('shop/index',array('cat'=>$val['cate_id']));?>" class="fl">
			<img src="/static/default/wap/image/shop/cate-<?php echo ($val['cate_id']); ?>.png"  class="sublogo">	
			</a>
			<div class="info">	
				<div class="title"><a href="<?php echo U('shop/index',array('cat'=>$val['cate_id']));?>"><?php echo ($val['cate_name']); ?></a></div>	
				<div class="subtitle">
					<?php $a = 0 ;foreach($shopcates as $v){if($v['parent_id'] == $val['cate_id']){ $a++;if($a<3){ ?>
					<?php echo ($v['cate_name']); ?>、
					<?php }}} ?>
					<?php echo ($val['title']); ?>
				</div>	
			</div>	
			<img src="/static/default/wap/image/shop/ic_arrow_down_black.png"  class="cate-arrow" id="cate-<?php echo ($i); ?>-arrow">	
		</div>	
		<div class="subclass" id="cate-<?php echo ($i); ?>">	
			<?php $k =0; $items = array(); foreach($shopcates as $v){ if($v['parent_id'] == $val['cate_id']){ $items[] = $val; $k++; $y = $k%3; ?>
			<?php if($y == 1): ?><div class="row">	
				<div class="col fw flipcard brb"><a href="<?php echo U('shop/index',array('cat'=>$v['cate_id']));?>" ><?php echo ($v["cate_name"]); ?></a></div>
			<?php elseif($y == 2): ?>
				<div class="col fw flipcard brb"><a href="<?php echo U('shop/index',array('cat'=>$v['cate_id']));?>" ><?php echo ($v["cate_name"]); ?></a></div>
			<?php elseif($y == 0): ?>
				<div class="col fw flipcard br"><a href="<?php echo U('shop/index',array('cat'=>$v['cate_id']));?>" ><?php echo ($v["cate_name"]); ?></a></div>	
			</div><?php endif; ?>
			<?php }} $x = $k%3; ?>
			<?php if($x == 1): ?><div class="col fw flipcard brb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
				<div class="col fw flipcard br">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
			</div><?php endif; ?>
			<?php if($x == 2): ?><div class="col fw flipcard br">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
			</div><?php endif; ?>
		</div>
		<?php }} ?>

<script>	
$(document).ready(function(){
	$(".classify").click(function(){
		var click = $(this).attr("data-click");
		var cate = $(this).attr("data-id");
		if (click == "0") {	
			// 点开	
			$(this).attr("data-click","1");
			var bid = cate + "-arrow";
			$('#'+bid).attr("src","/static/default/wap/image/shop/ic_arrow_up_black.png");	
			$('#'+cate).css("display","block");
		}else{	
			// 关闭	
			$(this).attr("data-click","0");
			var bid = cate + "-arrow";
			$('#'+bid).attr("src","/static/default/wap/image/shop/ic_arrow_down_black.png");	
			$('#'+cate).css("display","none");
		}	
	});

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