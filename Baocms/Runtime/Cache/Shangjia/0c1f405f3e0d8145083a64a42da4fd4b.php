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
        <li><a href="#">一卡通</a> > <a href="">金块换积分</a> > <a>金块换积分</a></li>
    </ul>
</div>
<div class="tuan_content">
    <div class="radius5 tuan_top">
        <div class="tuan_top_t">
            <div class="left tuan_topser_l">目前积分只能使用金块兑换，如果您没有金块的话可以在金块管理充值金块！ </div>
        </div>
    </div> 
    <div class="tuanfabu_tab">
        <ul>
            <li class="tuanfabu_tabli tabli_change on"><a href="<?php echo U('integral/index');?>">金块换积分</a></li>
            <li class="tuanfabu_tabli tabli_change"><a href="<?php echo U('integrallogs/index');?>">积分日志</a></li>
        </ul>
    </div>
    <div class="tabnr_change  show">
    	<form method="post" class="password"  action="<?php echo U('integral/index');?>"  target="baocms_frm">
    	<table class="tuanfabu_table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2"><div class="tuanfabu_nr">
                您当前共有：<?php echo ($MEMBER["gold"]); ?>金块,<?php echo ($MEMBER["integral"]); ?>积分, 充值请点击 <a href="<?php echo U('gold/index');?>">这里</a>
                </div></td>
            </tr>
            <tr>
                <td><p class="tuanfabu_t"><em>·</em>兑换：</p></td>
                <td><div class="tuanfabu_nr">
                <input type="text" name="num" class="tuanfabu_int tuanfabu_intw2" /> 一金块兑换100积分;
                </div></td>
            </tr>
        </table>
        <div class="tuanfabu_an">
        <input type="submit" class="radius3 sjgl_an tuan_topbt" value="立刻兑换" />
        </div>
        </form>
    </div> 
</div>
</body>
</html>