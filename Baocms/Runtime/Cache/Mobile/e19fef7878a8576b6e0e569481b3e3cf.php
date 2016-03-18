<?php if (!defined('THINK_PATH')) exit(); if(is_array($list)): foreach($list as $key=>$var): ?><a class="item media media-x" href="<?php echo U('news/detail',array('article_id'=>$var['article_id']));?>">
			<span class="float-left">
				<img class="radius" src="__ROOT__/attachs/<?php echo (($var['photo'])?($var['photo']):'default.jpg'); ?>" />	
			</span>
			<div class="media-body">
				<strong><?php echo ($var["title"]); ?></strong>
				<p class="desc"><?php echo bao_Msubstr($var['details'],0,100);?></p>
				<p class="info">
					<i class="icon-commenting-o"></i> <em><?php echo ($var["views"]); ?></em>
					<i class="icon-clock-o"></i> <em><?php echo (date('Y-m-d',$var["create_time"])); ?></em>
				</p>
			</div>
		</a><?php endforeach; endif; ?>