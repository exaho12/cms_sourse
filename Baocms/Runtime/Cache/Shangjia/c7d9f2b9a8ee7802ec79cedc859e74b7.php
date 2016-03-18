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

        <li><a href="#">商家管理</a> > <a href="">分店管理</a> > <a>分店列表</a></li>

    </ul>

</div>

<div class="tuan_content">

    <form method="post" action="<?php echo U('branch/index');?>">

    <div class="radius5 tuan_top">

        <div class="tuan_top_t">

            <div class="left tuan_topser_l">分店名称或者地址：<input type="text" class="radius3 tuan_topser"  name="keyword" value="<?php echo ($keyword); ?>"/><input type="submit" style="margin-left:10px;" class="radius3 sjgl_an tuan_topbt" value="搜 索"/></div>

            <div class="right tuan_topfb_r"><a class="radius3 sjgl_an tuan_topbt" target="main_frm" href="<?php echo U('branch/create');?>">添加分店+</a></div>

        </div>

    </div>

    </form>

    <div class="tuanfabu_tab">

        <ul>

            <li class="tuanfabu_tabli on"><a href="<?php echo U('branch/index');?>">分店</a></li>

        </ul>

    </div> 

    <table class="tuan_table" width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr style="background-color:#eee;">

            <td>分店名称</td>

            <td>城市</td>

            <td>地区</td>

            <td>商圈</td>

            <td>地址</td>

            <td>分数</td>

            <td>创建时间</td>

            <td>操作</td>

        </tr>

        <?php if(is_array($list)): foreach($list as $key=>$var): ?><tr>

                <td><?php echo ($var["name"]); ?></td>

                <td><?php echo ($city[$var['city_id']]['name']); ?></td>

                <td><?php echo ($area[$var['area_id']]['area_name']); ?></td>

                <td><?php echo ($business[$var['business_id']]['business_name']); ?></td>

                <td><?php echo ($var["addr"]); ?></td>

                <td><?php echo ($var["score"]); ?></td>

                <td><?php echo (date('Y-m-d H:i',$var["create_time"])); ?></td>

                <td>

                <a href="<?php echo U('branch/edit',array('branch_id'=>$var['branch_id']));?>">编辑</a>

                <a mini='confirm' href="<?php echo U('branch/delete',array('branch_id'=>$var['branch_id']));?>">删除</a>

                <a href="<?php echo U('branch/manage',array('branch_id'=>$var['branch_id']));?>">设置口令</a>

                <a target="_blank" href="<?php echo U('pchome/check/index',array('shop_id'=>$var['shop_id'],'branch_id'=>$var['branch_id']));?>">管理地址</a>

            </td>

            </tr><?php endforeach; endif; ?>

    </table>

    <?php echo ($page); ?>

</div>

</body>
</html>