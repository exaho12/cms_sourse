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
		<a class="top-addr" href="<?php echo U('index/index');?>"><i class="icon-angle-left"></i></a>
	</div>
		<div class="top-title">
			我的团购券
		</div>
	<div class="top-signed">
		<a href="<?php echo U('mobile/message/index');?>"><i class="icon-envelope"></i></a>
	</div>
</header>

<!-- 筛选TAB -->
<ul id="shangjia_tab">
        <li style="width: 33.333333336%;"><a href="<?php echo LinkTo('tuancode/index');?>" <?php if(($aready) == ""): ?>class="on"<?php endif; ?>>全部</a></li>
        <li style="width: 33.333333336%;"><a href="<?php echo LinkTo('tuancode/index',array('aready'=>1));?>" <?php if(($aready) == "1"): ?>class="on"<?php endif; ?>>未使用</a></li>
        <li style="width: 33.333333336%;"><a href="<?php echo LinkTo('tuancode/index',array('aready'=>2));?>" <?php if(($aready) == "2"): ?>class="on"<?php endif; ?>>已使用</a></li>
       
</ul>


<div class="list-media-x" id="list-media">
	<ul></ul>
</div>	

<script>
	$(document).ready(function(){
		loaddata('<?php echo U("/mcenter/tuancode/codeloading",array("p"=>"0000","aready"=>$aready));?>',$("#list-media ul"),true);
	});
</script>


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