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
	<link href="/static/default/wap/other/jquery-ui.css" rel="stylesheet" />
	<script src="/static/default/wap/other/jquery-ui.js"></script> 
	<header class="top-fixed bg-yellow bg-inverse">
		<div class="top-back">
			<a class="top-addr" href="<?php echo U('shop/detail',array('shop_id'=>$detail['shop_id']));?>"><i class="icon-angle-left"></i></a>
		</div>
		<div class="top-title">
			预约<?php echo ($detail["shop_name"]); ?>
		</div>
	</header>


	<div class="send-form">
		<form method="post" action="<?php echo U('shop/book',array('shop_id'=>$detail['shop_id']));?>"  target="x-frame">
        
        <div class="row">
				<span class="icon icon-user"></span>
				<input type="text" class="line-input" id="name" name="data[name]" placeholder="填写联系人姓名"  value="<?php if($MEMBER["nickname"] != null): echo ($MEMBER["nickname"]); ?> <?php else: endif; ?>"/>
			</div>
			<div class="row">
				<span class="icon icon-mobile"></span>
				<input type="text"   onkeyup="this.value=this.value.replace(/[^0-9-]+/,'')"   class="line-input" id="tel" name="data[mobile]" placeholder="填写手机号码" value="<?php if($MEMBER["mobile"] != null): echo ($MEMBER["mobile"]); ?> <?php else: endif; ?>" />
			</div>
            
            
			<div class="row">
				<span class="icon icon-calendar"></span>
				<input type="text" class="line-input datepicker" id="date" name="data[yuyue_date]" size="30"  readonly="readonly"  placeholder="预约日期" />
			</div>
            
            
            
             <div class="row">
             <span class="icon icon-calendar-check-o"></span>
                    <select id="yuyue_time"  name="data[yuyue_time]" class="text-select"  placeholder="选择时间段"  >
                        <?php $__FOR_START_24284__=0;$__FOR_END_24284__=24;for($i=$__FOR_START_24284__;$i < $__FOR_END_24284__;$i+=1){ ?><option value="<?php echo ($i); ?>:00"><?php echo ($i); ?>:00</option>
						<option value="<?php echo ($i); ?>:30"><?php echo ($i); ?>:30</option><?php } ?>
                    </select>
                    <script>
					$("#yuyue_time").val('<?php echo ($yuyue_time); ?>');
				    </script>
           </div>
           
            
            
			<div class="row">
             <span class="icon icon-users"></span>
                   <select id="number" name="data[number]" class="text-select">
					<?php $__FOR_START_7141__=1;$__FOR_END_7141__=10;for($i=$__FOR_START_7141__;$i < $__FOR_END_7141__;$i+=1){ ?><option <?php if(($number) == $i): ?>selected="selected"<?php endif; ?> value="<?php echo ($i); ?>"><?php echo ($i); ?>人</option><?php } ?>
					<option value="10"  <?php if(($number) == "10"): ?>selected="selected"<?php endif; ?>>10人及以上</option>
				</select>
           </div>
           
          
           
           
        
			
			<div class="row">
				<span class="icon icon-pencil-square-o"></span>
				<input type="text" class="line-input" id="contents" name="data[content]" placeholder="留言信息" />
			</div>
			
			<div class="blank-40"></div>
			
            <?php if(empty($MEMBER)){ ?>
            <div class="container text-center"><a href="<?php echo U('mobile/passport/login');?>" class="button button-block button-big bg-black" type="submit">立即登录</a></div>
            <?php }else{ ?>
            <div class="container text-center"><button class="button button-block button-big bg-dot" type="submit">提交订单</button></div>                          
            <?php } ?>
			
		</form>
	</div>
	<script>
		jQuery(function($){
			$.datepicker.regional['zh-CN'] = {
				closeText: '关闭',
				prevText: '&#x3c;上月',
				nextText: '下月&#x3e;',
				currentText: '今天',
				monthNames: ['一月','二月','三月','四月','五月','六月',
				'七月','八月','九月','十月','十一月','十二月'],
				monthNamesShort: ['一','二','三','四','五','六',
				'七','八','九','十','十一','十二'],
				dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
				dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
				dayNamesMin: ['日','一','二','三','四','五','六'],
				weekHeader: '周',
				dateFormat: 'yy-mm-dd',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: true,
				yearSuffix: '年'};
			$.datepicker.setDefaults($.datepicker.regional['zh-CN']);
		});
		$(function() {
			$( ".datepicker" ).datepicker();
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