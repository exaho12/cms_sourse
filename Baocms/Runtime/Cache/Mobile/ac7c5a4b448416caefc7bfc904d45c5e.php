<?php if (!defined('THINK_PATH')) exit(); if(is_array($list)): $index = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><a class="item" href="<?php echo U('shop/detail',array('shop_id'=>$item['shop_id']));?>">	
		<img class="pic" src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" />	
		<div class="des">	
			<h5><?php echo msubstr($item['shop_name'],0,6,'utf-8',false);?>
				<span class="distance"><?php echo ($item["d"]); ?></span>	
			</h5>	
			<div class="info">	
				<span class="ui-starbar"><span style="width:<?php echo round($item['score']*2,2);?>%"></span></span>
                
                <?php if(!empty($item['price'])): ?><!--如果价格等于0-->
                <span>￥<?php echo ($item['price']); ?>/位</span>	
                <?php else: ?>
                <span></span>	<!--0元消费--><?php endif; ?>
			</div>	
			<span class="addr">	
				<?php echo msubstr($item['addr'],0,26,'utf-8',false);?>
			</span>
		</div>	
	</a><?php endforeach; endif; else: echo "" ;endif; ?>