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
			<a class="top-addr" href="<?php echo ($backurl); ?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			用户登录
		</div>
		<div class="top-share">
			<a href="<?php echo U('passport/register');?>">注册</a>
		</div>
	</header>
	
	<div class="line">
		<div class="blank-40"></div>
		<form class="login-form" action="<?php echo U('passport/login');?>" method="post" target="x-frame">
			<input type="hidden" name="backurl" value="<?php echo ($backurl); ?>">
			<div class="form-group">
				<div class="field field-icon">
					<span class="icon icon-user"></span>
					<input id="account" name="account" type="text" class="input" placeholder="请输入账号">
				</div>
			</div>
			
			<div class="form-group">
				<div class="field field-icon">
					<span class="icon icon-key"></span>
					<input id="password" type="password" name="password" class="input" placeholder="请输入密码">
				</div>
			</div>
           
                
			<div class="blank-40"></div>
			<div class="container">
				<div class="form-button"><button class="button button-block button-big bg-dot" type="submit">登录</button></div>
			</div>
		</form>

		<div class="blank-40"></div>
		<div class="container login-open">
			<h5>用其他合作平台帐号登录<br/>
			<a style="color:#333" href="<?php echo U('passport/forget',array('way'=>2));?>">忘记密码？点击这里找回！</a>
            </h5>
			<div class="blank-10"></div>
			<p>
				<a href="<?php echo U('passport/qqlogin');?>">
					<div class="txt radius-circle bg-blue"><i class="icon icon-qq"></i></div>
				</a>      
				<?php if($is_weixin): ?><a href="<?php echo U('passport/wxlogin');?>"><div class="txt radius-circle bg-green"><i class="icon icon-weixin"></i></div></a><?php endif; ?>
				<a href="<?php echo U('passport/wblogin');?>"><div class="txt radius-circle bg-dot"><i class="icon icon-weibo"></i></div></a>
			</p>
		</div>
		<div class="blank-40"></div>
	</div>
	


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