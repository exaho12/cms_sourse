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
<style>
.container { margin-top: 3rem;}
.container2 {margin: 0 auto; }
.coupon-list .item {  padding: 5px 0px 0px 5px;}
.coupon-list .item .intro { height: initial;}
.panel-head { background-color: #fff;}
p, .p {margin-bottom: 0px; }
</style>
	<header class="top-fixed bg-yellow bg-inverse">
		<div class="top-back">
			<a class="top-addr" href="<?php echo U('shop/index');?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			<?php echo ($detail["shop_name"]); ?>
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
	
    
        
<!-- 筛选TAB -->
<ul id="shangjia_tab">
        <li style="width: 25%;"><a href="<?php echo U('shop/detail',array('shop_id'=>$detail['shop_id']));?>" class="on">首页</a></li>
        <li style="width: 25%;"><a href="<?php echo U('shop/tuan',array('shop_id'=>$detail['shop_id']));?>">团购</a></li>
        <li style="width: 25%;"><a href="<?php echo U('shop/coupon',array('shop_id'=>$detail['shop_id']));?>">优惠</a></li>
        <li style="width: 25%;"><a href="<?php echo U('shop/dianping',array('shop_id'=>$detail['shop_id']));?>">评价</a></li>
    </ul>



	<div class="container">
		<div class="line detail-base">
			<div class="x4">
				<div class="pic">
					<a href="<?php echo U('shop/pic',array('shop_id'=>$detail['shop_id']));?>"><img src="__ROOT__/attachs/<?php echo (($detail["photo"])?($detail["photo"]):'default.jpg'); ?>" /></a> 				<?php if(!empty($pic)): ?><span class="album"><?php echo ($pic); ?></span>
                    <?php else: endif; ?>
				</div>
			</div>
			<div class="x5">
				<h1><?php echo ($detail["shop_name"]); ?></h1>
				<p><span class="ui-starbar"><span style="width:<?php echo round($detail['score']*2,2);?>%"></span></span></p>
				<p>浏览量: <?php echo ($detail["view"]); ?> 次</p>
				<p class="text-small"><span class="text-yellow"><?php echo ($ex["business_time"]); ?> </span></p>
			</div>
			<div class="x3">
				<?php if(($detail["niu_date"]) > $today): ?><p class="text-center"><img src="/static/default/wap/image/shop/icon-cx.png" /></p><?php endif; ?>
				<div class="blank-10"></div>
				<p class="text-center"><a class="text-dot" href="#ping">商铺评价</a></p>
				<p class="text-small text-center">( <em class="text-yellow"><?php echo ($totalnum); ?></em>人评价 )</p>
			</div>
		</div>
       </div>
       
       
		<div class="blank-10 bg"></div>
	<div class="container2">
		<div class="line detail-contact">
			<div class="x9">
				<p class="addr"><i class="icon icon-map-marker"></i> <?php echo ($detail["addr"]); ?> </p>
				<p class="margin-top"><i class="icon icon-phone"></i> 
                <?php if(!empty($detail['tel'])): ?><a class="text-large" href="tel:<?php echo ($detail["tel"]); ?>"><?php echo ($detail["tel"]); ?></a>
                <?php else: ?>
                <a class="text-large">暂无联系方式</a><!--该商家暂无联系方式--><?php endif; ?>
                </p>
			</div>
			<div class="x3">
				<a class="favor" href="<?php echo U('shop/favorites',array('shop_id'=>$detail['shop_id']));?>">
					<div class="txt radius-circle bg-green"><i class="icon-heart"></i></div>
					<p>关注该商家</p>
					<p class="text-small">已关注<em class="text-yellow"><?php echo ($favnum); ?></em>人</p>
				</a>
			</div>
		</div>
		
        
        <?php $sb = D('ShopBranch');$rsb = $sb -> where('shop_id ='.$detail['shop_id']) -> count(); ?>
        <?php if(!empty($rsb)): ?><div class="list-link detail-link radius-none">
		    <a href="<?php echo U('shop/branch/',array('shop_id'=>$detail['shop_id']));?>">
				<span class="txt txt-little radius-little bg-yellow">店</span> 查看另外<?php echo ($rsb); ?>家分店
				<span class="float-right icon-angle-right"></span>
			</a>
           </div>
        <?php else: ?>
       <!--该商家无分店--><?php endif; ?>
       </div> 
        
        <div class="blank-10 bg"></div>
        
        
      <div class="container2"> 
        <div class="list-link detail-link radius-none">
			<div class="line border-bottom">
				<div class="x6 border-right text-center">
					<a href="<?php echo U('shop/gps',array('shop_id'=>$detail['shop_id']));?>"><i class="icon icon-send"></i> 导航到这去</a>
				</div>
				<div class="x6 text-center">
					<a href="<?php echo U('shop/qrcode',array('shop_id'=>$detail['shop_id']));?>"><i class="icon icon-qrcode"></i> 二维码名片</a>
				</div>
			</div>
		    <?php if(!empty($tuans)): ?><a href="<?php echo U('tuan/index',array('shop_id'=>$detail['shop_id']));?>">
				   	<span class="txt txt-little radius-little bg-green">团</span> 去逛逛商家抢购
			    	<span class="float-right icon-angle-right"></span>
			    </a>		   
            <?php else: endif; ?>		           
           
            <?php if(!empty($coupon)): ?><!--显示近期优惠券数据$detail['shop_id-->			
                <a href="<?php echo U('mobile/coupon/main',array('coupon'=>$detail['coupon_id']));?>">
				    <span class="txt txt-little radius-little bg-red">劵</span> 商家优惠券下载
				    <span class="float-right icon-angle-right"></span>
			    </a>
            <?php else: endif; ?>            
            <!--显示近期优惠劵结束-->
            
             <?php if(!empty($work)): ?><div class="panel-head"><b>商家招聘信息</b></div>			
            <?php if(is_array($work)): $index = 0; $__LIST__ = $work;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><!--循环输出的一条数据-->
            <a href="<?php echo U('nearwork/detail',array('work_id'=>$item['work_id']));?>">
				 <span class="txt txt-little radius-little bg-green">聘</span> <?php echo ($item["title"]); ?>
				 <span class="float-right icon-angle-right"></span>
			</a><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: endif; ?><!--显示近期招聘结束-->           
            <?php if(!empty($huodong)): ?><!--显示近期活动数据-->			
                <a href="<?php echo U('mobile/huodong/index',array('activity_id'=>$activity_id['activity_id']));?>">
				    <span class="txt txt-little radius-little bg-red">活</span>商家近期活动
				    <span class="float-right icon-angle-right"></span>
			    </a>
            <?php else: endif; ?><!--显示近期活动结束-->          
            <?php if(!empty($ele_menu)): ?><!--显示近期外卖数据-->			
                <a href="<?php echo U('mobile/ele/shop/',array('shop_id'=>$detail['shop_id']));?>">
			     	<span class="txt txt-little radius-little bg-yellow">外</span> 商家外卖精选
        		    <span class="float-right icon-angle-right"></span>
			    </a>
            <?php else: endif; ?><!--显示近期外卖结束-->                
                       
            
                       
			<a href="<?php echo U('shop/book',array('shop_id'=>$detail['shop_id']));?>">
				<span class="txt txt-little radius-little bg-blue">约</span> 预约去消费
				<span class="float-right icon-angle-right"></span>
			</a>
		</div>
	</div>
        
        
        <div class="blank-10 bg"></div>
     <div class="container2">
		<div class="panel detail-intro radius-none">
			<div class="panel-head">商家介绍</div>
			<div class="panel-body">
				<?php echo ($ex["details"]); ?>
				<?php if(($detail["niu_date"]) > $today): ?><span class="niu"><img src="/static/default/wap/image/shop/icon-niu.png" /></span><?php endif; ?>
			</div>
		</div>		
	</div>
        
        
        <div class="blank-10 bg"></div>
		
        
     
	<div class="container2">
        <div class="panel detail-intro radius-none">
			<div class="panel-head">附近抢购</div>
		    <div class="main-tuan" id="main-tuan" style="padding:0 10px;">
            <?php if(is_array($tuans)): $i = 0; $__LIST__ = $tuans;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li>
			<a class="line" href="<?php echo U('tuan/detail',array('tuan_id'=>$item['tuan_id']));?>" >
				<div class="container1">
					<img class="x4" src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" />	
					<div class="des x8">
						<h5><?php echo ($item["title"]); ?></h5>
						<p class="intro">
							<?php echo msubstr($item['intro'],0,20);?>
						</p>
						<p class="info">
							<span>￥ <em><?php echo round($item['tuan_price']/100,2);?></em></span> <del>¥ <?php echo round($item['price']/100,2);?></del>
							<span class="text-little float-right badge bg-yellow margin-small-top padding-right">立省<?php echo round($item['price']/100 - $item['tuan_price']/100,2);?>元</span>
						</p>
					</div>
				</div>
			</a>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>
                
                
                </div>
		</div>
       </div>
        
        
	
<div class="blank-10"></div>	
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