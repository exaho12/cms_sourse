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
	<header class="top-fixed bg-yellow bg-inverse">
		<div class="top-back">
			<a class="top-addr" href="<?php echo U('index/index');?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			热门资讯
		</div>
        <div class="top-search" style="display:none;">
			<form method="post" action="<?php echo U('news/index');?>">
				<input name="keyword" placeholder="输入新闻的关键字"  />
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
    

	
    <div id="search-bar" class="search-bar">
		<ul class="line">
			<li class="x6"><span>选择分类</span><i></i></li>
			<li class="x6"><span>推荐排序</span><i></i></li>
		</ul>
	</div>
    
    <div class="serch-bar-mask" style="display:none;">
		<div class="serch-bar-mask-list">
			<ul>

				<li class="<?php if(empty($cat)): ?>on<?php endif; ?> "><a href="<?php echo U('mobile/news/index');?>" >全部</a></li>
			 
				<?php if(is_array($cates)): foreach($cates as $key=>$item): if(($item["parent_id"]) == "0"): ?><li class="hui <?php if($item["cate_id"] == $cat): ?>on<?php endif; ?> "><a title="<?php echo ($item["cate_name"]); ?>" href="<?php echo LinkTo('news/index',$linkArr,array('cat'=>$item['cate_id']));?>"><?php echo ($item["cate_name"]); ?></a></li><?php endif; ?>
                        
                        <?php if(is_array($cates)): foreach($cates as $key=>$var2): if(($var2["parent_id"]) == $item["cate_id"]): ?><li class="<?php if($var2["cate_id"] == $cat): ?>on<?php endif; ?> "><a  style="margin-left:20px;" title="<?php echo ($var2["cate_name"]); ?>" href="<?php echo LinkTo('news/index',$linkArr,array('cat'=>$var2['cate_id']));?>"><?php echo ($var2["cate_name"]); ?></a></li><?php endif; endforeach; endif; endforeach; endif; ?>
				
			</ul>
		</div>
		
		<div class="serch-bar-mask-list">
			<ul>
          
            
<li <?php if($order == 1): ?>class="on"<?php endif; ?>><a href="<?php echo LinkTo('news/index',array('cat'=>$cat,'order'=>1));?>">热度优先</a></li>
<li <?php if($order == 2): ?>class="on"<?php endif; ?>><a href="<?php echo LinkTo('news/index',array('cat'=>$cat,'order'=>2));?>">默认排序</a></li>


			</ul>
		</div>
	</div>
    
    <script>
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
	</script>  
    
    <div class="blank-40"></div>	
	<div id="roll" class="roll">
		<div class="bd">
			<ul>
				<?php $i=0; ?>
				<?php  $cache = cache(array('type'=>'File','expire'=> 43200)); $token = md5("Article,closed = 0 AND isroll = 1 AND photo != '',0,5,43200,article_id desc,,"); if(!$items= $cache->get($token)){ $items = D("Article")->where("closed = 0 AND isroll = 1 AND photo != ''")->order("article_id desc")->limit("0,5")->select(); $cache->set($token,$items); } ; $index=0; foreach($items as $item): $index++; $i++; if($i==1){ $first = $item['title']; } ?>
				<li>
					<a class="pic" href="<?php echo U('news/detail',array('article_id'=>$item['article_id']));?>"><img src="__ROOT__/attachs/<?php echo ($item['photo']); ?>" /></a>
					<a class="tit" href="<?php echo U('news/detail',array('article_id'=>$item['article_id']));?>"><?php echo ($item['title']); ?></a>
				</li> <?php endforeach; ?>
			</ul>
		</div>
		<div class="hd">
			<ul></ul>
		</div>
	</div>
	
	<div class="blank-10"></div>
	
	
	
	
	<div class="blank-10"></div>
	<div class="sec-title">	
		<div class="divider"></div>	
		<span>资讯列表：总<?php echo ($countss); ?>条资讯，今日更新：<?php echo ($counts["article"]); ?>篇</span>
	</div>
	
	<div class="news-list" id="shop-list">
    
    <script>
		$(document).ready(function () {
			loaddata('<?php echo ($nextpage); ?>', $("#shop-list"), true);
		});
	</script>
    
    
		
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