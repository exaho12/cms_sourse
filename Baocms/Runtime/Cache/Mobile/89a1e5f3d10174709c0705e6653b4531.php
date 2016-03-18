<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
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
			<a class="top-addr" href="<?php echo U('index/index');?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			<?php echo ($detail["name"]); ?>
		</div>
		<div class="top-signed">
          <a href="<?php echo U('community/index',array('change'=>1));?>" class="top-addr icon-navicon"> 切换</a>
		</div>
	</header>

 
 
 
 <div class="tuan-detail">
<div class="line banner">	


<?php if(!empty($ads)): ?><div id="focus" class="focus">
		<div class="hd"><ul></ul></div>
		<div class="bd">
			<ul>
                <?php if(is_array($ads)): foreach($ads as $key=>$item): ?><li><a href="<?php echo ($item["link_url"]); ?>"><img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" /></a></li><?php endforeach; endif; ?>
			</ul>
		</div>
	</div>
<?php else: ?>    
    <div id="focus" class="focus">
		<div class="hd"><ul></ul></div>
		<div class="bd">
			<ul><li><a href="javascript:void(0);"><img src="__ROOT__/attachs/<?php echo ($detail[pic]); ?>" /></a></li>
			</ul>
		</div>
	</div><?php endif; ?>     
			<div class="title">
				<h1><?php echo bao_msubstr($detail['name'],0,10);?>
                <?php $village_ids = D('Village') -> where('village_id ='.$detail['village_id']) -> find(); $village_name = $village_ids['name']; ?>
              <?php if(!empty($detail['village_id'])): ?><a style="color:#FFF; font-size:12px;" href="<?php echo U('village/detail',array('village_id'=>$detail['village_id']));?>">(<?php echo ($village_name); ?>)</a>
              <?php else: ?>
             <a style="color:#FFF; font-size:12px;">（未加入社区）</a><?php endif; ?>   
                </h1>
				<p><?php echo bao_msubstr($detail['name'],0,10);?>有业主<?php echo ($user_owner); ?>人，邻居<?php echo ($counts); ?>人。
                <?php if(!empty($products_pay)): ?><a style="color:#FFF;" href="<?php echo U('mcenter/community/order');?>">您有待缴费<?php echo ($products_pay); ?>单</a>。
                <?php else: endif; ?>
                
                </p>
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

	<!--小区广告结束-->
    




	<div class="cite-title padding border-bottom">
		<h5><i class="icon-building"></i><?php echo ($detail["name"]); ?> </h5>
         <?php if($isjoin != 0): ?><a href="<?php echo U('community/neighbor',array('community_id'=>$detail['community_id']));?>">找邻居 <i class="icon-angle-right"></i></a>
         <?php else: ?>
         <a href="<?php echo U('community/join',array('community_id'=>$detail['community_id']));?>">加入小区 <i class="icon-angle-right"></i></a><?php endif; ?>
	</div>
	<div class="xiaoqu-nav line">
		<div class="x3 border-right">
           <a href="<?php echo U('community/order',array('community_id'=>$detail['community_id']));?>">
    			<img src="/static/default/wap/image/index/app-community.png" />
                
                <?php if(!empty($products_pay)): ?><span class="badge bg-red jiaofei" onclick="location='<?php echo U('mcenter/community/order');?>'"><?php echo ($products_pay); ?></span>
                <?php else: endif; ?>
                
    			<p>水电气费</p>
    		</a>
		</div>
		<div class="x3 border-right">
    		<a href="<?php echo U('community/tuan');?>">
    			<img src="/static/default/wap/image/index/app-tuan.png" />
    			<p>小区团购</p>
    		</a>
		</div>
		<div class="x3 border-right">
        <a href="<?php echo U('community/feedback',array('community_id'=>$detail['community_id']));?>">
				<img src="/static/default/wap/image/community/admin.png" />
    			<p>报事报修</p>
    		</a> 
            
            
        
		</div>
        
       
        <?php if(!empty($owner)): ?><div class="x3">
        <a href="<?php echo U('mcenter/community/order');?>">
    			<img src="/static/default/wap/image/index/app-sqyz.png" />
    			<p>对账单</p>
    		</a> 
		</div>
        <?php else: ?>
        <div class="x3">
        <a href="<?php echo U('community/owner',array('community_id'=>$detail['community_id']));?>">
    			<img src="/static/default/wap/image/index/app-nearwork.png" />
    			<p>申请业主</p>
    		</a> 
		</div><?php endif; ?>
        
		<div class="x3 border-right">
         <a href="<?php echo U('life/lists',array('cat'=>47));?>">
    			<img src="/static/default/wap/image/index/app-life.png" />
    			<p>便民信息</p>
    		</a>
            
    		
		</div>
		<div class="x3 border-right">
    		<a href="<?php echo U('community/contact',array('community_id'=>$detail['community_id']));?>">
				<img src="/static/default/wap/image/community/feed.png" />
    			<p>办事指南</p>
    		</a> 
		</div>
		<div class="x3 border-right">
        <a href="<?php echo U('mobile/lifeservice/index');?>">
    			<img src="/static/default/wap/image/index/app-billboard.png" />
    			<p>家政</p>
    		</a>
        
       
            
		</div>
		<div class="x3">
    		 <?php if($isjoin != 0): ?><a href="<?php echo U('community/out',array('community_id'=>$detail['community_id']));?>">
				<img src="/static/default/wap/image/community/exit.png" />
    			<p>撤离邻居</p>
    		</a> 
			<?php else: ?>
    		<a href="<?php echo U('community/join',array('community_id'=>$detail['community_id']));?>">
				<img src="/static/default/wap/image/community/plus.png" />
    			<p>加入邻居</p>
    		</a><?php endif; ?> 
		</div>

	</div>
	
	<div class="blank-10 bg"></div>
	<div class="cite-title padding border-bottom">
		<h5><i class="icon-bullhorn"></i> 物业通知</h5>
		<a href="<?php echo U('community/newslist',array('community_id'=>$detail['community_id']));?>">+ 更多</a>
	</div>
	<div class="text-list">
		<ul>
			<?php if(is_array($news)): foreach($news as $key=>$item): ?><li class="padding border-bottom">
				<a href="<?php echo U('community/news',array('news_id'=>$item['news_id']));?>"><?php echo bao_Msubstr($item['title'],0,14,false);?><span><?php echo (date('Y-m-d',$item["create_time"])); ?></span></a>
			</li><?php endforeach; endif; ?>
		</ul>
	</div>
	<div class="blank-10 bg"></div>
		
	<div class="cite-title padding border-bottom">
		<h5><i class="icon-phone"></i>  便民电话</h5>
		<a href="<?php echo U('community/together',array('community_id'=>$detail['community_id']));?>">+ 合作</a>
	</div>
	<div class="text-list">
		<ul>
			<?php if(is_array($phones)): $index = 0; $__LIST__ = $phones;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$var): $mod = ($index % 2 );++$index;?><li class="padding border-bottom">
				<a class="line" title="<?php echo ($var["name"]); ?>" href="tel:<?php echo ($var["phone"]); ?>">
					<em class="x4"><?php echo bao_Msubstr($var['name'],0,4,false);?></em>
					<em class="x8"><?php echo ($var["phone"]); ?> <span><i class="icon-angle-right"></i></span></em>
				</a> 
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
	<div class="blank-10 bg"></div>
	<div class="cite-title padding border-bottom">
		<h5><i class="icon-fire"></i>  身边抢购</h5>
		<a href="javascript:void(0);" onclick="refresh();"><i class="icon-refresh"></i> 换一换</a>
	</div>
	<div class="mart-flush" id="flush">
		<div class="bd">
			<ul>
				<?php if(is_array($keys)): $i = 0; $__LIST__ = $keys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?><li class="padding">
						<a class="line" href="<?php echo U('tuan/detail',array('tuan_id'=>$tuan[$k]['tuan_id']));?>">
							<div class="x5"><img src="/attachs/<?php echo ($tuan[$k]['photo']); ?>"></div>
							<div class="x7">
								<p><?php echo ($tuan[$k]['title']); ?></p>
								<p>
								<span class="text-yellow margin-right">￥<?php echo round($tuan[$k]['tuan_price']/100,2);?></span>
								<del class="text-gray text-small">￥<?php echo round($tuan[$k]['price']/100,2);?></del>
								</p>
							</div>
						</a>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
	</div>
	<div class="blank-10 bg"></div>
	
	
	
	

	<script>	
		$(document).ready(function () {
			TouchSlide({ 
				slideCell:"#roll",
				titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
				mainCell:".bd ul", 
				effect:"left", 
				autoPlay:false,//自动播放
				autoPage:true, //自动分页
			});
			TouchSlide({ 
				slideCell:"#flush",
				mainCell:".bd ul", 
				effect:"left", 
				autoPlay:false,//自动播放
				autoPage:false, //自动分页
			});
			
			$(document).ready(function () {
				loaddata('<?php echo ($nextpage); ?>', $("#tie-list ul"), true);
			});

		});
		
		function refresh(){
			var width = $('#flush .bd li').width() + 20;
			var offset = 0 -( Math.ceil(Math.random()*9) * width );
			var css = "translateX("+offset+"px)";
			$('#flush .bd ul').css("transform",css);
		}

	</script>
		

	<div class="cite-title padding border-bottom">
		<h5><i class="icon-comments"></i> 小区贴吧</h5>
		<a href="<?php echo U('community/tieba',array('community_id'=>$detail['community_id']));?>">+ 进入</a>
	</div>
	<div id="tie-list" class="text-list">
		<ul>
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