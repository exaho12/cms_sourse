<?php if (!defined('THINK_PATH')) exit(); if(is_array($list)): $index = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?><li class="line">		
    <a href="<?php echo U('mall/detail',array('goods_id'=>$item['goods_id']));?>">		
    <div class="x3">			<img src="__ROOT__/attachs/<?php echo ($item["photo"]); ?>" />		
    </div>		
    <div class="x9">			<h5><?php echo ($item["title"]); ?></h5>		
    	<p class="desc"><?php echo bao_Msubstr($item[instructions],0,60);?></p>			
        <p class="info"><span>&yen;<?php echo round($item['mall_price']/100,2);?></span><del>&yen;<?php echo round($item['price']/100,2);?></del>				<em>已售<?php echo ($item["sold_num"]); ?></em>		
        	</p>	
        	</div>		
            </a>	
            </li><?php endforeach; endif; else: echo "" ;endif; ?>