<?php if (!defined('THINK_PATH')) exit();?>
<?php if(is_array($list)): foreach($list as $key=>$item): ?><li>
			<a class="line" href="<?php echo U('tuan/detail',array('tuan_id'=>$item['tuan_id']));?>" >
				<div class="container1">
					<img class="x4" src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" />	
					<div class="des x8">
						<h5><?php echo ($item["title"]); ?></h5>
						<p class="intro">
							<?php echo msubstr($item['intro'],0,20);?>
						</p>
						<p class="info">
							<span>￥ <em><?php echo round($item['tuan_price']/100,2);?></em></span> <del>¥ <?php echo round($item['price']/100,2);?></del>
							<span class="text-little float-right badge bg-yellow margin-small-top padding-right">立省<?php echo round($item['price']/100 - $item['tuan_price']/100,2);?>元</span>
						</p>
					</div>
				</div>
			</a>
		</li><?php endforeach; endif; ?>