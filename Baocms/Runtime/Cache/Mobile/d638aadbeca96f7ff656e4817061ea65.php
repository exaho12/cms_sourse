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
<script src="__TMPL__statics/js/jquery.flexslider-min.js" type="text/javascript" charset="utf-8"></script>
	<header class="top-fixed bg-yellow bg-inverse">
		<div class="top-local">
			<a href="<?php echo U('city/index');?>" class="top-addr"><?php echo bao_msubstr($city_name,0,2,false);?> <i class="icon-angle-down"></i></a>
		</div>
			<div class="top-search">
			<form method="post" action="<?php echo U('shop/index');?>">
				<input name="keyword" placeholder="输入商家的关键字"  />
				<button type="submit" class="icon-search"></button> 
			</form>
		</div>
		<div class="top-signed">
<a href="<?php echo U('mobile/sign/signed');?>" class="top-addr icon-calendar-plus-o"> 签到</a>
		</div>
	</header>   
   <script>
		$(document).ready(function () {
			$("#share-box").hide();
			$(".share-btn").click(function () {
				$("#share-box").toggle();
				$('html,body').animate({scrollTop:0}, 'slow');
			});
			$("#mui-card-close").click(function () {
				$("#share-box").hide();
			});
		});
	</script>
  
<script src="/static/default/wap/other/roll.js"></script>
	<div id="focus" class="focus">
		<div class="hd">
			<ul></ul>
		</div>
		<div class="bd">
			<ul>
                 <?php  $cache = cache(array('type'=>'File','expire'=> 7200)); $token = md5("Ad, closed=0 AND site_id=57 AND city_id IN ({$city_ids}) and bg_date <= '{$today}' AND end_date >= '{$today}' ,0,3,7200,orderby asc,,"); if(!$items= $cache->get($token)){ $items = D("Ad")->where(" closed=0 AND site_id=57 AND city_id IN ({$city_ids}) and bg_date <= '{$today}' AND end_date >= '{$today}' ")->order("orderby asc")->limit("0,3")->select(); $cache->set($token,$items); } ; $index=0; foreach($items as $item): $index++; ?><li><a href="<?php echo ($item["link_url"]); ?>"><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" /></a></li> <?php endforeach; ?>
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		TouchSlide({ 
			slideCell:"#focus",
			titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			mainCell:".bd ul", 
			effect:"left", 
			autoPlay:true,//自动播放
			autoPage:true, //自动分页
			switchLoad:"_src" //切换加载，真实图片路径为"_src" 
		});
	</script> 

 <!--分类开始-->
<div id="index" class="page-center-box"> 
            <script>
                    $(document).ready(function () {
                        $('.flexslider_cate').flexslider({
                            directionNav: true,
                            pauseOnAction: false,
                            /*slideshow: false,*/
                            /*touch:true,*/
                        });
                        /*首页菜单分类结束*/
                        $('.flexslider_yiyuan').flexslider({
                            controlNav: false,
                            pauseOnAction: false,
                            /*slideshow: false,*/
                            /*touch:true,*/
                        });
                        /*首页菜单分类结束*/
                    });
            </script>
   

   
<div class="banner mb10">
                <div class="flexslider_cate"> 
                    <ul class="slides">

                        <?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; if($i%8 == 1): ?><li class="list">
                                    <ul class="cate">
                                       <li><a href="<?php echo ($item["url"]); ?>"><div class="icon <?php echo ($item["ioc"]); ?> <?php echo ($item["colour"]); ?>"></div><p><?php echo ($item["nav_name"]); ?></p></a></li>
                                        <?php elseif($i%8 == 0): ?>        

                                       <li><a href="<?php echo ($item["url"]); ?>"><div class="icon <?php echo ($item["ioc"]); ?> <?php echo ($item["colour"]); ?>"></div><p><?php echo ($item["nav_name"]); ?></p></a></li>
                                    </ul>
                                </li>
                                <?php else: ?>
                               <li><a href="<?php echo ($item["url"]); ?>"><div class="icon <?php echo ($item["ioc"]); ?> <?php echo ($item["colour"]); ?>"></div><p><?php echo ($item["nav_name"]); ?></p></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>


                    </ul>  
                </div>
            </div>            
            <!--首页分类结束-->
    
		  <div class="panel-list">
<ul><li style="border-top: thin solid #dedede; font-size:16px"><a href="<?php echo U('community/index');?>" class="icon-home "> 进入小区服务<i class="icon-angle-right"> </i><i class="icon-angle-right"> </i><i class="icon-angle-right"></i></a></li></ul>
</div>  


	<div class="blank-10  bg"></div>
<div class="panel-ad">
<ul><?php  $cache = cache(array('type'=>'File','expire'=> 7200)); $token = md5("Ad, closed=0 AND site_id=61 AND city_id IN ({$city_ids}) and bg_date <= '{$today}' AND end_date >= '{$today}' ,0,1,7200,orderby asc,,"); if(!$items= $cache->get($token)){ $items = D("Ad")->where(" closed=0 AND site_id=61 AND city_id IN ({$city_ids}) and bg_date <= '{$today}' AND end_date >= '{$today}' ")->order("orderby asc")->limit("0,1")->select(); $cache->set($token,$items); } ; $index=0; foreach($items as $item): $index++; ?><li><a href="<?php echo ($item["link_url"]); ?>"><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" /></a></li> <?php endforeach; ?></ul>
</div>  
	<!--首页广告-->

	<div class="index-ads">
		<div class="line border-bottom border-top">
			<div class="x5 ad-1">
				<?php  $cache = cache(array('type'=>'File','expire'=> 7200)); $token = md5("Ad, closed=0 AND site_id=62 AND city_id IN ({$city_ids}) and bg_date <= '{$today}' AND end_date >= '{$today}' ,0,1,7200,orderby asc,,"); if(!$items= $cache->get($token)){ $items = D("Ad")->where(" closed=0 AND site_id=62 AND city_id IN ({$city_ids}) and bg_date <= '{$today}' AND end_date >= '{$today}' ")->order("orderby asc")->limit("0,1")->select(); $cache->set($token,$items); } ; $index=0; foreach($items as $item): $index++; ?><a href="<?php echo ($item["link_url"]); ?>"><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" /></a> <?php endforeach; ?>
			</div>
			<div class="x7 border-left">
				<div class="line">
					<div class="x12 border-bottom ad-2">
						<a href="#"><img src="/static/default/wap/image/index/ads-2.png"></a>
					</div>
					<div class="x6 border-right ad-3">
						<a href="#"><img src="/static/default/wap/image/index/ads-3.png"></a>
					</div>
					<div class="x6 ad-3">
						<a href="#"><img src="/static/default/wap/image/index/ads-4.png"></a>
					</div>
				</div>
			</div>
		</div>
	</div>
    
    <div class="blank-10  bg" style="border-bottom: thin solid #eee;"></div>
    
    <div class="tab index-tab" data-toggle="click">
		<div class="tab-head">
			<ul class="tab-nav line">
				<li class="x4 active"><a href="#tab-shop">附近商家</a></li>
				<li class="x4"><a href="#tab-coupon">附近小区</a></li>
				<li class="x4"><a href="#tab-active">热门资讯</a></li>
			</ul>
		</div>
		<div class="tab-body">
			<div class="tab-panel active" id="tab-shop">
				<ul class="line index-tuan">
				<?php if(is_array($shoplist)): $index = 0; $__LIST__ = $shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><div class="container1" onclick="location='<?php echo U('shop/tuan',array('shop_id'=>$item['shop_id']));?>'">
                        <img class="x2" src="__ROOT__/attachs/<?php echo (($item["photo"])?($item["photo"]):'default.jpg'); ?>">	
                        <div class="des x8">
                        
            <?php $business = D('Business') -> where('business_id ='.$item['business_id']) -> find(); $business_name = $business['business_name']; ?>
            
            
                            <h5><?php echo bao_msubstr($item['shop_name'],0,10,false);?> <a style="color:#999; margin-left:10px;">(<?php echo ($business_name); ?>)</a></h5>
                            <p class="intro"><span class="ui-starbar" style="margin-top:0.2rem;"><span style="width:<?php echo round($item['score']*2,2);?>%"></span></span></p>
                            <p class="intro">地址：<?php echo bao_msubstr($item['addr'],0,12,false);?></p>
                        </div>
                        
                        <div class="des x2">
                            <div class="intro2"><?php echo ($item["view"]); ?></div>
                        </div>
                        
                        
                     </div><?php endforeach; endif; else: echo "" ;endif; ?>
           	</ul>
            <div class="more"><a href="<?php echo U('shop/index');?>">查看更多商家</a></div>	
		</div>
			<div class="tab-panel" id="tab-coupon">
				<ul class="index-tuan">
					<?php if(is_array($community)): $index = 0; $__LIST__ = $community;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><div class="container1" onclick="location='<?php echo U('community/detail',array('community_id'=>$item['community_id']));?>'">
                        <img class="x2" src="__ROOT__/attachs/<?php echo (($item["pic"])?($item["pic"]):'default.jpg'); ?>">	
                        <div class="des x8">
                            <h5><?php echo bao_msubstr($item['name'],0,10,false);?></h5>
                            <p class="intro">地址：<?php echo bao_msubstr($item['addr'],0,12,false);?></p>
                            <p class="info"><span>物业公司：<?php echo bao_msubstr($item['property'],0,10,false);?> </span> </p>
                        </div>
                        
                        <div class="des x2">
                            <div class="intro2" style="width: auto; padding:0 3px;"><?php echo ($item["d"]); ?></div>
                        </div>
                        
                        
                     </div><?php endforeach; endif; else: echo "" ;endif; ?>	
				</ul>
                <div class="more"><a href="<?php echo U('community/index');?>">查看更多小区</a></div>	
			</div>
			<div class="tab-panel" id="tab-active">
                <ul  class="index-tuan">
                    <?php if(is_array($news)): $index = 0; $__LIST__ = $news;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><div class="container1" onclick="location='<?php echo U('news/detail',array('article_id'=>$item['article_id']));?>'">
                        <img class="x2" src="__ROOT__/attachs/<?php echo (($item["photo"])?($item["photo"]):'default.jpg'); ?>">	
                        <div class="des x8">
                            <h5><?php echo bao_msubstr($item['title'],0,10,false);?></h5>
                            <p class="intro">简介：<?php echo bao_msubstr($item['profiles'],0,12,false);?></p>
                            <p class="info"><span>作者：<?php echo ($item["source"]); ?></span> </p>
                        </div>
                        <div class="des x2">
                            <div class="intro2"><?php echo ($item["views"]); ?></div>
                             </div>
                     </div><?php endforeach; endif; else: echo "" ;endif; ?>	
                </ul>
                <div class="more"><a href="<?php echo U('news/index');?>">查看更多资讯</a></div>	
			</div>
		</div>
	</div>
    
    

	<!--/首页广告-->


<div class="blank-10 bg margin-top"></div>
	<div class="index-title">
		<h4>猜您喜欢</h4>
        <em><a href="<?php echo U('tuan/index');?>">更多抢购 <i class="icon-angle-right"></i></a></em>
	</div>
	<div class="line index-tuan">
		<ul id="index-tuan">
			<script>
				$(document).ready(function () {
					loaddata('<?php echo U("tuan/push",array("t"=>$nowtime,"p"=>"0000"));?>', $("#index-tuan"),true);
				});
			</script>
		</ul>
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