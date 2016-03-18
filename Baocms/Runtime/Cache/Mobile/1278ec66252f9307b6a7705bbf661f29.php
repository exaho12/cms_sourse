<?php if (!defined('THINK_PATH')) exit(); $seo_title = $detail['title']; ?>
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
<script src="/static/default/wap/other/roll.js"></script>

	<header class="top-fixed bg-yellow bg-inverse">
		<div class="top-back">
			<a class="top-addr" href="<?php echo U('mall/index',array('cat'=>$detail['cate_id']));?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			商品详情
		</div>
		<div class="top-share">
			<a href="javascript:void(0);" class="share-btn" id="share-btn"><i class="icon-share-alt"></i></a>
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
			$(".share-btn").click(function () {
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
			<div id="focus" class="focus">
		<div class="hd">
			<ul></ul>
		</div>
		<div class="bd">
			<ul>
          		<li><a href="javascript:void(0);"><img src="__ROOT__/attachs/<?php echo ($detail["photo"]); ?>" /></a></li>
                <?php if(is_array($pics)): foreach($pics as $key=>$item): ?><li><a href="javascript:void(0);"><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" /></a></li><?php endforeach; endif; ?>
                
			</ul>
		</div>
	</div>
			<div class="title">
				<h1><?php echo msubstr($detail['title'],0,20);?></h1>
				<p><?php echo msubstr($detail['intro'],0,40);?></p>
			</div>	
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
    


<div class="item-detail">

               
           
			<div class="detail-row bb">
				<div class="item-price">
                  
					<p class="price">特卖价：&yen; <?php echo round($detail['mall_price']/100,2);?></p>                  
                    <div class="x6">
					<p class="discout">已经在原价基础上打<?php echo round($detail['mall_price']/$detail['price']*10,1);?>折</p>
                    </div>
                    <div class="x6">
					<p class="text-gray">销量 <?php echo ($detail['sold_num']); ?></p>
                    </div>
                    
                    
					<div class="x6">
                    <span class="text-gray">原价：<del>&yen; <?php echo round($detail['price']/100,2);?></del></span>
                	</div>
                    
                    <div class="x6">
                    <span class="text-gray">库存：<?php echo ($detail['num']); ?></span></span>
                	</div>
                        
                    <p class="sprice">
						
					</p>
				</div>
			</div>
			<div class="detail-row bb">
				<div class="item-tips">
					      <?php if(($detail["is_vs1"]) == "1"): ?><em><span class="text-green"><i class="icon-check-circle"></i></span>认证商家</em><?php endif; ?>
                    <?php if(($detail["is_vs2"]) == "1"): ?><em><span class="text-green"><i class="icon-check-circle"></i></span>正品保证</em><?php endif; ?>
                    <?php if(($detail["is_vs3"]) == "1"): ?><em><span class="text-green"><i class="icon-check-circle"></i></span>假一赔十</em><?php endif; ?>
                    <?php if(($detail["is_vs4"]) == "1"): ?><em><span class="text-green"><i class="icon-check-circle"></i></span>当日送达</em><?php endif; ?>
                    <?php if(($detail["is_vs6"]) == "1"): ?><em><span class="text-green"><i class="icon-check-circle"></i></span>货到付款</em><?php endif; ?>
                    <?php if(($detail["is_vs5"]) == "1"): ?><em><span class="text-green"><i class="icon-check-circle"></i></span>免运费</em><?php endif; ?>
				</div>
			</div>
		</div>
       </div>
		<div class="blank-10 bg"></div>
        
       <div class="tuan-detail">
       <div class="line status">
			<div class="x6">
				<span class="ui-starbar"><span style="width:<?php echo round($score*10,2);?>%"></span></span>
			</div>
			<div class="x6">
				<span class="float-right"><a href="<?php echo U('mall/dianping',array('goods_id'=>$detail['goods_id']));?>"><?php echo ($pingnum); ?>人评价了该商品 </a><i class="icon-angle-right"></i></span>
			</div>
		</div> 
       </div> 
        
        
		<div class="blank-10 bg"></div>
		
		<div class="item-shop">
			<div class="shop-row">
				<div class="shop-pic">
                
					 <a href="<?php echo U('shop/detail',array('shop_id'=>$detail['shop_id']));?>"><img src="__ROOT__/attachs/<?php echo ($shop["photo"]); ?>" /></a>
				</div>
				<div class="shop-name">
					<h2><a href="<?php echo U('shop/detail',array('shop_id'=>$detail['shop_id']));?>"><?php echo ($shop["shop_name"]); ?></a></h2>
					<span class="ui-starbar"><span style="width:<?php echo round($shop['score']*2,2);?>%"></span></span>
				</div>
			</div>
			<div class="shop-desc">
				<p>地址: <?php echo ($shop["addr"]); ?> 【<a class="text-blue" href="<?php echo U('shop/gps',array('shop_id'=>$detail['shop_id']));?>"><i class="icon-location-arrow"></i> 到这去</a>】</p>
				<p>电话: <a class="text-blue" href="tel:<?php echo ($shop["tel"]); ?>"><?php echo ($shop["tel"]); ?></a>
			</div>
		</div>
		
		<div class="blank-10 bg"></div>
		
		<div class="item-intro">
			<h2>商品介绍</h2>
			<div class="intro-bd"><?php echo ($detail["details"]); ?></div>
		</div>
		
		<div class="blank-10 bg"></div>
		
		<div class="item-intro">
			<h2>购买须知</h2>
			<div class="intro-bd"><?php echo ($detail["instructions"]); ?></div>
		</div>
		
		<div class="blank-10 bg"></div>
		
		
		<div class="item-list item-intro" id="item-list">
			<h2>看了又看</h2>
			<ul>
			<?php if(is_array($recom)): $index = 0; $__LIST__ = $recom;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><li class="line">
				<a href="<?php echo U('mall/detail',array('goods_id'=>$item['goods_id']));?>">
				<div class="x3">
					<img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" />
				</div>
				<div class="x9">
					<h5><?php echo ($item["title"]); ?></h5>
					<p class="desc"><?php echo bao_Msubstr($item[instructions],0,60);?></p>
					<p class="info">
						<span>&yen;<?php echo round($item['price']/100,2);?></span><del>&yen;<?php echo round($item['mall_price']/100,2);?></del>
						<em>已售<?php echo ($item["sold_num"]); ?></em>

                
				</div>
				</a>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
<div class="blank-10 bg"></div>
   <script>
        $(document).ready(function () {
            $(".cartadd2").click(function(){
               var url = "<?php echo U('mall/cartadd2');?>";
               var goods_id = "<?php echo ($detail["goods_id"]); ?>" ;
               var shop_id = "<?php echo ($detail["shop_id"]); ?>";
               $.post(url,{goods_id:goods_id,shop_id:shop_id},function(data){
                   if(data.status == 'success'){
                       layer.msg(data.msg, function () {
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 1000)
                        });
                   }else{
                       layer.msg(data.msg);
                   }
               },'json')
           })
        });
      
    </script>
	

	
	<nav class="cart-bar">
		<a class="cart" href="<?php echo U('mall/cart');?>">
			<i class="icon-shopping-cart"></i>
			<div id="num" class="num"><?php echo ($cartnum); ?></div>件商品
		</a>
        <style>
		.right10{ margin-right:5px;}
		.button { padding: 6px 12px !important;}
		</style>
		<div class="result">
			<a id="add-cart" href="javascript:void(0);">
				<button class="cartadd2 button bg-dot right10">
				  加入购物车
				</button>
			</a>
			
            
            
<?php if($detail['num'] <= 0): ?><a href="javascript:void(0);"><button class="button bg-gray">没库存啦</button></a>        
<?php else: ?>
<a href="<?php echo U('mall/buy',array('goods_id'=>$detail['goods_id']));?>"><button class="button bg-dot">立即购买</button></a><?php endif; ?>
		</div>
	</nav>	
	
</body>
</html>