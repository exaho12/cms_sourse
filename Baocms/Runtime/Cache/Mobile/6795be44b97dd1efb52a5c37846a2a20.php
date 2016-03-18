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
			<a href="<?php echo U('shop/detail',array('shop_id'=>$shop['shop_id']));?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			商家地图
		</div>
	</header>

	<style type="text/css">
		body,html,#allmap{width:100%;height:100%;overflow:hidden;}
		body{padding:50px 0 0 0;}
		#golist {display: none;}
		@media (max-device-width: 800px){#golist{display: block!important;}}
	</style>
	
	<script src="http://api.map.baidu.com/components?ak=C9613fa45f450daa331d85184c920119&v=1.0"></script>
	
	<lbs-map width="100%" style="height:100%" center="<?php echo ($shop["lng"]); ?>,<?php echo ($shop["lat"]); ?>">
		<lbs-poi name="<?php echo ($shop["shop_name"]); ?>" location="<?php echo ($shop["lng"]); ?>,<?php echo ($shop["lat"]); ?>" addr="<?php echo ($shop["addr"]); ?>" ></lbs-poi>
	</lbs-map>
</body>
</html>