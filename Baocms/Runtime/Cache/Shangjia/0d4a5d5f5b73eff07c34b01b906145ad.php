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
<div class="jump">
    <span class="succe failu">&nbsp;</span><?php echo $error?$error:$message ; ?>

</div>
<b id="wait" style="display:none;"><?php echo($waitSecond); ?></b>
<script type="text/javascript">
(function(){
	var wait = document.getElementById('wait');
	var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		window.location.href = '<?php echo($jumpUrl);?>';
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>