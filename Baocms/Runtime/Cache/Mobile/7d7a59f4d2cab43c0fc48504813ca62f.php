<?php if (!defined('THINK_PATH')) exit(); $seo_title = $detail['title'].'团购'; ?>
<!DOCTYPE html>
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
			<a class="top-addr" href="<?php echo U('tuan/index');?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			团购详情
		</div>
		<div class="top-share">
			<a href="javascript:void(0);" id="share-btn"><i class="icon-share"></i></a>
		</div>
	</header>  
	<div id="share-box" class="share-box">
		<div class="dialog-mask"></div>
			<ul class="line">
				<li class="-mob-share-weibo x3"><img src="/static/default/wap/image/share/share-weibo.png" /><p>新浪微博</p></li>
				<li class="-mob-share-tencentweibo x3"><img src="/static/default/wap/image/share/share-twb.png" /><p>腾讯微博</p></li>
				<li class="-mob-share-qzone x3"><img src="/static/default/wap/image/share/share-qzone.png" /><p>QQ空间</p></li>
				<li class="-mob-share-qq x3"><img src="/static/default/wap/image/share/share-py.png" /><p>QQ好友</p></li>
				<li class="-mob-share-weixin x3"><img src="/static/default/wap/image/share/share-weixin.png" /><p>微信</p></li>
				<li class="-mob-share-renren x3"><img src="/static/default/wap/image/share/share-renren.png" /><p>人人网</p></li>
				<li class="-mob-share-kaixin x3"><img src="/static/default/wap/image/share/share-kaixin.png" /><p>开心网</p></li>
				<li id="mui-card-close" class="mui-card-close x3"><img src="/static/default/wap/image/share/share-close.png" /><p>关闭</p></li>
			</ul>
		<script id="-mob-share" src="http://f1.webshare.mob.com/code/mob-share.js?appkey=890ab8bbdb3c"></script>
	</div>
	<script>
		$(document).ready(function () {
			$("#share-box").hide();
			$("#share-btn").click(function () {
				$("#share-box").toggle();
				$('html,body').animate({scrollTop:0}, 'slow');
			});
			$("#mui-card-close").click(function () {
				$("#share-box").hide();
			});
		});
	</script>
	
	<div class="tuan-detail">
		<div class="line banner">	
			<img src="__ROOT__/attachs/<?php echo ($detail["photo"]); ?>">	
			<div class="title">
				<h1><a href="<?php echo U('tuan/pic',array('tuan_id'=>$detail['tuan_id']));?>">点击查看图片相册</a></h1>
			</div>	
		</div>
        
        
        <div class="line price">
			<div class="x12">
				<h3><?php echo bao_Msubstr($detail['title'],0,48,false);?></h3>
                <h4 class="se"><?php echo bao_Msubstr($detail['intro'],0,88,false);?></h4>
			</div> 
		</div>
        
        
		<div class="line info">
			<div class="x6">
				<span class="txt-border txt-little radius-circle border-green"><div class="txt radius-circle bg-green">退</div></span>
				<span class="text-green">支持随时退款</span>
			</div>
			<div class="x6">
				<span class="txt-border txt-little radius-circle border-gray"><div class="txt radius-circle bg-gray"><i class="icon-user"></i></div></span>
				<span class="text-gray">已售 <?php echo ($detail["sold_num"]); ?> 份</span>
			</div>
            
            <!--小灰灰增加开始-->
            <div class="x6">
            <?php if($detail['freebook'] == 1): ?><span class="txt-border txt-little radius-circle border-green"><div class="txt radius-circle bg-green">预</div></span>
			<span class="text-green">免预约</span>
            <?php else: ?>
			<span class="txt-border txt-little radius-circle border-green"><div class="txt radius-circle bg-green">否</div></span>
			<span class="text-green">需要预约哦~</span><?php endif; ?>
			</div>
            <div class="x6">
				<span class="txt-border txt-little radius-circle border-gray"><div class="txt radius-circle bg-gray">距</div></span>
				<span class="text-gray"><?php echo ($detail["d"]); ?></span>
			</div>
           <!--小灰灰增加结束-->

			<div class="x12 margin-top">
				<span class="txt-border txt-little radius-circle border-dot"><div class="txt radius-circle bg-dot"><i class="icon-database"></i></div></span>
				<span class="text-dot">该抢购可以使用<?php echo ($detail["use_integral"]); ?>积分抵现！</span>
			</div>
		</div>
		<hr />
		<div class="blank-10 bg"></div>
		<hr />
		<div class="line status">
			<div class="x6">
				<span class="ui-starbar"><span style="width:<?php echo round($score*10,2);?>%"></span></span>
			</div>
			<div class="x6">
				<span class="float-right margin-small-top"><a href="<?php echo U('tuan/dianping',array('tuan_id'=>$detail['tuan_id']));?>"><?php echo ($pingnum); ?>人评价了该抢购 </a><i class="icon-angle-right"></i></span>
			</div>
		</div>
		<hr />
		<div class="blank-10 bg"></div>
		<hr />
		<div class="line shop">
			<div class="x9 border-right">
				<h2> <a href="<?php echo U('shop/detail',array('shop_id'=>$detail['shop_id']));?>"><?php echo ($shop["shop_name"]); ?></a> <a  style="color:#F00; "href="<?php echo U('shop/gps',array('shop_id'=>$detail['shop_id']));?>"> （点击导航）</a></h2>
				<p><?php echo ($shop["addr"]); ?></p>
			</div>
			<div class="x3">
				<a class="tel" href="tel:<?php echo ($shop["tel"]); ?>"><i class="icon-phone text-main"></i></a>
			</div>
		</div>
		<hr />
		<div class="blank-10 bg"></div>
		<hr />
		<div class="line status">
			<div class="x5">
				<span><a >抢购须知 </a></span>
			</div>
			<div class="x7">
				<span class="float-right"> <a href="<?php echo U('tuan/tuwen',array('tuan_id'=>$detail['tuan_id']));?>">更多图文详情 </a><i class="icon-angle-right"></i></span>
			</div>
		</div>
        <hr />
        <div class="container">
        <?php echo ($tuandetails["instructions"]); ?>
        </div>
        <div class="line status2">
			<div class="x12">
				<span class="margin-small-top"> <a href="<?php echo U('tuan/tuwen',array('tuan_id'=>$detail['tuan_id']));?>">
                点击查看<?php echo bao_Msubstr($detail['title'],0,10,false);?>图文详情 </a><i class="icon-angle-right"></i></span>
			</div>
		</div>
        <hr />
		


  
  <div class="blank-10 bg"></div>   
  <hr />
         <div class="line-title">
		<h5>本店其他抢购</h5>
	    </div>
    
        <div class="main-tuan">
        <?php if(is_array($tuans)): $index = 0; $__LIST__ = $tuans;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><ul id="tuan-list">   
        <li class="x12">
		<a class="line" href="<?php echo U('tuan/detail',array('tuan_id'=>$item['tuan_id']));?>">
			<div class="container">
				<img class="x4" src="__ROOT__/attachs/<?php echo (($item["photo"])?($item["photo"]):'default.jpg'); ?>">	
				<div class="des x8">
					<h5><?php echo bao_msubstr($item['title'],0,18);?></h5>
					<p class="intro">
						<?php echo bao_msubstr($item['intro'],0,28);?></p>
					<p class="info">
						<span>￥ <em><?php echo round($item['tuan_price']/100,2);?></em></span> <del>￥<?php echo round($item['price']/100,2);?></del>
						<span class="text-little float-right badge bg-gray margin-small-top">已售：<?php echo ($item["sold_num"]); ?></span>
					</p>
				</div>
			</div>
		</a>
	</li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
	</div>
   </div>
</div>

<div class="blank-10 bg"></div>


<section class="buy-btn-wrap" id="j-buy-segment">
    <div class="buy-segment">
            <span class="old-current-price">
            	<em class="price-value"><?php echo ($detail["tuan_price"]); ?></em>
            </span>
            <span class="original-price">
            	<del><?php echo ($detail["price"]); ?></del>
            </span>
        <!--判断开始-->
        
                        
                        
                        
        <?php if($detail['bg_date'] > $today): ?><div class="buy-wrapper">
            <div class="privilege-btn">
                <a href="javascript：void(0);" id="wap-buybtn-for-hmt" data-stat-param="href" class="buy-btn" data-role="buy-btn" mon="element=buyBtn" data-platform="0">
                    <span class="tip2">活动价</span>
                    <span class="privilege"><em class="price"><?php echo ($detail["tuan_price"]); ?></em></span>
                    <span class="text">即将开始</span>
                </a>
            </div>
        </div> 
        <?php else: ?>
        <?php if($detail["num"] < 1 ): ?><div class="buy-wrapper">
            <div class="privilege-btn">
                <a href="#" id="wap-buybtn-for-hmt"  class="buy-btn" >
                    <span class="tip2">活动价</span>
                    <span class="privilege"><em class="price"><?php echo ($detail["tuan_price"]); ?></em></span>
                    <span class="text">卖光了</span>
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="buy-wrapper">
            <div class="privilege-btn">
                <a href="<?php echo U('tuan/buy',array('tuan_id'=>$detail['tuan_id']));?>" id="wap-buybtn-for-hmt" data-stat-param="href" class="buy-btn" data-role="buy-btn" mon="element=buyBtn" data-platform="0">
                    <span class="tip2">活动价</span>
                    <span class="privilege"><em class="price"><?php echo ($detail["tuan_price"]); ?></em></span>
                    <span class="text">立即抢购</span>
                </a>
            </div>
        </div><?php endif; endif; ?>           
    </div>
</section>



	</body>
</html>