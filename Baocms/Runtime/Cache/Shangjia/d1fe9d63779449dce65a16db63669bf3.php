<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($CONFIG["site"]["title"]); ?>商户中心</title>
<meta name="description" content="<?php echo ($CONFIG["site"]["title"]); ?>商户中心" />
<meta name="keywords" content="<?php echo ($CONFIG["site"]["title"]); ?>商户中心" />
<link href="__TMPL__statics/css/newstyle.css" rel="stylesheet" type="text/css" />
 <link href="__PUBLIC__/js/jquery-ui.css" rel="stylesheet" type="text/css" />
<script>
var BAO_PUBLIC = '__PUBLIC__'; var BAO_ROOT = '__ROOT__';
</script>
<script src="__PUBLIC__/js/jquery.js"></script>
<script src="__PUBLIC__/js/jquery-ui.min.js"></script>
<script src="__PUBLIC__/js/web.js"></script>
<script src="__PUBLIC__/js/layer/layer.js"></script>

</head>

<body>
<iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
<div class="sjgl_lead">
    <ul>
        <li><a href="#">系统设置</a> > <a href="">评价管理</a> > <a>点评管理</a></li>
    </ul>
</div>
<div class="tuan_content">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t tuanfabu_top">
            <div class="left tuan_topser_l">如果收到恶意评价，可以联系网站客服：<?php echo ($CONFIG["site"]["tel"]); ?></div>
        </div>
    </div>
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli on"><a href="<?php echo U('dianping/index');?>">商家点评</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('dianping/tuan');?>">抢购点评</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('dianping/mall');?>">商城点评</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('dianping/waimai');?>">外卖点评</a></li>
            <li class="tuanfabu_tabli"><a href="<?php echo U('dianping/ding');?>">订座点评</a></li>
        </ul>
    </div> 
    <table class="tuan_table" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr style="background-color:#eee;">
            <td>编号</td>
            <td>用户</td>
            <td>评分</td>
            <td>平均花费</td>
            <td>评价时间</td>
            <td>评价IP</td>
            <td>生效日期</td>
            <td style=" border-right: solid 1px #e1e6eb;" >操作</td>
        </tr>
        <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr  style="background-color: #F9F9F9;">
                <td style="width: 100px;"><?php echo ($var["dianping_id"]); ?></td>
                <td><?php echo ($users[$var['user_id']]['nickname']); ?>(ID:<?php echo ($var["user_id"]); ?>)</td>
                <td><?php echo ($var["score"]); ?></td>
                <td><?php echo ($var["cost"]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s',$var["create_time"])); ?></td>
                <td><?php echo ($var["create_ip"]); ?>(来自<?php echo ($var["create_ip_area"]); ?>)</td>
                <td><?php echo ($var["show_date"]); ?></td>
                <td style=" border-right: solid 1px #e1e6eb;" >
            <?php if(empty($var['reply'])): ?><a mini="load" h="400" w="500" href="<?php echo U('dianping/reply',array('dianping_id'=>$var['dianping_id']));?>">回复</a><?php endif; ?>
            </td>   
            </tr>
            <tr>
                <td>
                    <h1>评价</h1>
                </td>
                <td colspan="8" class="html_contents" style="text-align: left;">
                    <?php echo ($var["contents"]); ?>
                </td>
            </tr>
            <?php if(!empty($var['reply'])): ?><tr>
                    <td>
                        <h1  style=" color:#F00; font-weight:bold;">商家回复：</h1>
                    </td>
                    <td colspan="8"  style="text-align: left;color:#F00; font-weight:bold;" class="html_contents" >
                        <?php echo ($var["reply"]); ?>
                    </td>
                </tr><?php endif; ?>
            
            <?php if(!empty($var['pichave'])): ?><tr>
                    <td>
                        <h1>图片</h1>
                    </td>
                    <td colspan="8"  style="text-align: left;" class="html_contents">
                        <?php if(is_array($pics)): foreach($pics as $key=>$item): if($var['dianping_id'] == $item['dianping_id']): ?><img src="__ROOT__/attachs/<?php echo ($item['pic']); ?>" width="120" height="80"/><?php endif; endforeach; endif; ?>
                    </td>
                </tr><?php endif; endforeach; endif; ?>
    </table>
    <?php echo ($page); ?>
</div>
</body>
</html>