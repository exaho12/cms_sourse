<?php if (!defined('THINK_PATH')) exit();?>
<?php if(is_array($list)): foreach($list as $key=>$item): ?><a href="<?php echo U('coupon/detail',array('coupon_id'=>$item['coupon_id']));?>" class="item">	
	<div class="line">
		<div class="x4"><img class="pic" src="__ROOT__/attachs/<?php echo (($item["photo"])?($item["photo"]):'default.jpg'); ?>"  style="width:90%"></div>
		<div class="x8">
			<h3><?php echo ($item['title']); ?></h3>
			<p class="intro"><?php echo ($item['intro']); ?></p>
			<p class="info">
				<span class="float-left">下载：<em class="text-yellow"><?php echo ($item["downloads"]); ?></em>人</span>	
				<span class="float-right">有效期至：<em class="text-yellow"><?php echo ($item['expire_date']); ?></em></span>
			</p>
		</div>
	</div>
</a><?php endforeach; endif; ?>