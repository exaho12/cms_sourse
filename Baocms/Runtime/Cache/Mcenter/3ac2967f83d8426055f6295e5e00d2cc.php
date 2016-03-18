<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8">
		<title><?php if(!empty($seo_title)): echo ($seo_title); ?>_<?php endif; echo ($CONFIG["site"]["sitename"]); ?>会员中心</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
		<?php if($CONFIG[site][concat] != 1): ?><link rel="stylesheet" href="/static/default/wap/css/base.css">
		<link rel="stylesheet" href="/static/default/wap/css/mcenter.css"/>
		<script src="/static/default/wap/js/jquery.js"></script>
		<script src="/static/default/wap/js/base.js"></script>
		<script src="/static/default/wap/other/layer.js"></script>
		<script src="/static/default/wap/other/roll.js"></script>
		<script src="/static/default/wap/js/public.js"></script>
		<?php else: ?>
		<link rel="stylesheet" href="/static/default/wap/css/??base.css,mcenter.css" />
		<script src="/static/default/wap/js/??jquery.js,base.js,roll.js,layer.js,public.js"></script><?php endif; ?>
	</head>
	<body>



<header class="top-fixed bg-yellow bg-inverse">

	<div class="top-back">

		<a class="top-addr" href="<?php echo U('mobile/index/index');?>"><i class="icon-angle-left"></i></a>

	</div>

		<div class="top-title">

			会员中心

		</div>

	<div class="top-signed">

		<a href="<?php echo U('mobile/message/index');?>"><i class="icon-envelope"></i></a>

	</div>

</header>



<div class="index-top">

	<div class="top-a">

		<div class="user">	

			<span class="avatar"><img src="__ROOT__/attachs/<?php echo (($MEMBER["face"])?($MEMBER["face"]):'default.jpg'); ?>" /></span>

			<div class="info">	

				<p>

                

                <?php if($MEMBER["nickname"] != null): echo ($shop["tel"]); ?>

                <?php echo ($MEMBER["nickname"]); ?> uid:(<?php echo ($MEMBER["user_id"]); ?>)

                <?php else: ?>

                <a href="<?php echo U('mcenter/info/nickname');?>">设置昵称</a><?php endif; ?>

           

                 （VIP <?php echo ($MEMBER['rank_id']); ?>）</p>	

				<p>

					<span>积分：<?php echo ($MEMBER["integral"]); ?></span>

					<span>余额：<?php echo round($MEMBER['money']/100,2);?></span>

					<span>金块：<?php echo round($MEMBER['gold']/100,2);?></span>

				</p>	

			</div>	

		</div>	



	</div>

	

    <div class="top-g">

		<div class="line">

			<a class="x4 btn btn-1"  href="<?php echo U('mobile/favorites/index');?>">

				<i class="icon-star-o"></i>

				<p>我的收藏</p>

			</a>

			<a class="x4 btn btn-2"  href="<?php echo U('tuan/index');?>">

				<i class="icon-bookmark-o"></i>

				<p>团购订单</p>

			</a>

			<a class="x4 btn btn-3"  href="<?php echo U('goods/index');?>">

				<i class="icon-sticky-note-o"></i>

				<p>购物订单</p>

			</a>

			<a class="x4 btn btn-4"  href="<?php echo U('eleorder/index');?>">

				<i class="icon-cutlery"></i>

				<p>我的订餐</p>

			</a>

			<a class="x4 btn btn-5"  href="<?php echo U('ding/index');?>">

				<i class="icon-tty"></i>

				<p>我的订座</p>

			</a>

			<a class="x4 btn btn-6"  href="<?php echo U('yuyue/index');?>">

				<i class="icon-history"></i>

				<p>我的预约</p>

			</a>

		</div>

	</div>

    

	

	<a class="top-s"  href="<?php echo U('information/index');?>">

		 <i class="icon-cog"></i>

	</a>

</div>



<div class="blank-10 bg"></div>



<div class="panel-list">

	<ul>

        

<li><a href="<?php echo U('information/index');?>"><span class="icon-sign-out"></span>管理我的账户<i class="icon-angle-right"></i></a></li>

<?php if($is_shop != null): ?><li><a href="<?php echo U('store/index/index');?>"><span class="icon-home"></span>管理我的商家<font>（<?php echo ($is_shop_name); ?>）</font><i class="icon-angle-right"></i></a></li>

<?php else: ?>

<li><a href="<?php echo U('/mcenter/apply/index');?>"><span class="icon-user"></span>商家申请入驻<i class="icon-angle-right"></i></a></li><?php endif; ?>

<li><a href="<?php echo U('mcenter/member/xiaoqu');?>"><span class="icon-tasks"></span>我的小区 

				<?php if($xiaoqu > 0): ?><font>(<?php echo ($xiaoqu); ?>)</font> 

                <?php else: endif; ?> <i class="icon-angle-right"></i></a>

</li>

<li><a href="<?php echo U('mcenter/member/zijinguanli');?>"><span class="icon-database"></span>资金管理中心<i class="icon-angle-right"></i></a></li>

<li><a href="<?php echo U('mcenter/member/xiaoxizhongxin');?>"><span class="icon-volume-up"></span>消息中心<i class="icon-angle-right"></i></a></li>

	</ul>

</div>

<div class="blank-10 bg"></div>



<div class="panel-list">

	<ul>

		<li>

			<a href="<?php echo U('tuancode/index');?>">

				<span class="icon-film"></span>

				我的团购券&nbsp; 

                <?php if($code > 0): ?><font>待消费：(<b><?php echo ($code); ?></b>)</font>

                <?php else: endif; ?> 

                

				<i class="icon-angle-right"></i>

			</a>

		</li>

		<li>

			<a href="<?php echo U('coupon/index');?>">

				<span class="icon-ticket"></span>

				我的优惠券&nbsp; 

                <?php if($coupon > 0): ?><font>未使用：(<b><?php echo ($coupon); ?></b>)</font>

                <?php else: endif; ?> 

                

                

				<i class="icon-angle-right"></i>

			</a>

		</li>

         <li>

			<a href="<?php echo U('yuyue/index');?>">

				<span class="icon-history"></span>

				我的预约

                <?php if($shop_yuyue > 0): ?><font>未使用：(<b><?php echo ($shop_yuyue); ?></b>)</font>

                <?php else: endif; ?> 

                

				<i class="icon-angle-right"></i>

			</a>

		</li> 

       

        
 <li>

			<a href="<?php echo U('tieba/index');?>">

				<span class="icon-comments"></span>

				我的贴吧&nbsp; 

                <?php if($tieba > 0): ?><!--如果贴吧不等于0-->

                <font>(<?php echo ($tieba); ?>)</font><!--显示数据-->

                <?php else: endif; ?>  

                

                

                <?php if($counts['tieba'] != null): ?><font>今日：(<b><?php echo ($counts["tieba"]); ?></b>)</font>  

                <?php else: endif; ?>  

				<i class="icon-angle-right"></i>

			</a>

		</li>

        

        <div class="blank-10 bg"></div>

        
          <li>

			<a href="<?php echo U('life/index');?>">

				<span class="icon-truck"></span>

				我的同城信息&nbsp; 

                <?php if($life > 0): ?><font>(<?php echo ($life); ?>)</font>  

                <?php else: endif; ?>  

				<i class="icon-angle-right"></i>

			</a>

		</li>  

            

   

       

        

        

		<li>

			<a href="<?php echo U('exchange/index');?>">

				<span class="icon-gift"></span>

				我的礼品&nbsp; 

                <?php if($lipin > 0): ?><font>(<?php echo ($lipin); ?>)</font>

                <?php else: endif; ?> 

                

				<i class="icon-angle-right"></i>

			</a>

		</li>

        

        <li>

			<a href="<?php echo U('Lifeservice/index');?>">

				<span class="icon-umbrella"></span>

				我的家政

				<i class="icon-angle-right"></i>

			</a>

		</li>
 <div class="blank-10 bg"></div>
 
 	   <?php if($distribution == 1): ?><!--如果开启分销-->
       <li><a href="<?php echo U('distribution/index');?>"><span class="icon-ticket"></span>我的分销<i class="icon-angle-right"></i></a></li>
       <?php else: endif; ?> 
        
        
        
        
         <li>

			<a href="<?php echo U('express/index');?>">

				<span class="icon-truck"></span>

				我的快递&nbsp; 

                

				<i class="icon-angle-right"></i>

			</a>

		</li>  

        <li>

			<a href="<?php echo U('mobile/favorites/index');?>">

				<span class="icon-star-o"></span>

				我的收藏

				<i class="icon-angle-right"></i>

			</a>

		</li>

	

		<li>

			<a href="<?php echo U('mobile/passport/logout');?>">

				<span class="icon-sign-out"></span>

				注销登录

				<i class="icon-angle-right"></i>

			</a>

		</li>

	</ul>

</div>

<div class="blank-10 bg"></div>


<div class="blank-20"></div>
<?php if($CONFIG[other][footer] == 1): ?><footer class="foot-fixed">
<a class="foot-item <?php if(($ctl == 'index') AND ($act != 'more')): ?>active<?php endif; ?>" href="<?php echo u('mobile/index/index');?>">		
<span class="icon icon-home"></span>
<span class="foot-label">首页</span>
</a>

<a class="foot-item <?php if(($ctl) == "tuan"): ?>active<?php endif; ?>" href="<?php echo u('mcenter/tuan/index');?>">			
<span class="icon icon-star-o"></span><span class="foot-label">订单</span></a>

<a class="foot-item <?php if(($ctl) == "member"): ?>active<?php endif; ?>" href="<?php echo u('mcenter/member/zijinguanli');?>">			
<span class="icon icon-database"></span><span class="foot-label">资金</span></a>

<a class="foot-item <?php if(($ctl) == "mcenter"): ?>active<?php endif; ?>" href="<?php echo u('mcenter/member/xiaoxizhongxin');?>">			
<span class="icon icon-volume-up"></span><span class="foot-label">消息</span></a>
<a class="foot-item <?php if(($ctl == 'tuancode') AND ($act == 'tuancode')): ?>active<?php endif; ?>" href="<?php echo u('mcenter/tuancode/index');?>">			
<span class="icon icon-ticket"></span><span class="foot-label">卡劵</span></a>
</footer>
<?php else: endif; ?>

<iframe id="x-frame" name="x-frame" style="display:none;">
</iframe>
</body>
</html>