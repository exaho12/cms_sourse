<?php if (!defined('THINK_PATH')) exit(); if(is_array($list)): foreach($list as $key=>$var): ?><li class="line">
	<a href="<?php echo U('community/detail',array('community_id'=>$var['community_id']));?>" class="item fix hightitem">	
		<div class="x3">
			<img src="__ROOT__/attachs/<?php echo ($var["pic"]); ?>">
		</div>
		<div class="x9">	
			<h5><?php echo ($var["name"]); ?></h5>	
			<p>	
				<span>物业：<?php echo ($var["property"]); ?></span>	
				<span class="float-right"><i class="icon-map-marker"></i> <?php echo ($var["d"]); ?></span>	
			</p>	
			<p class="addr">	
				<?php echo ($var["addr"]); ?>
			</p>
		</div>
	</a>
</li><?php endforeach; endif; ?>