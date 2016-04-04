<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo ($CONFIG["site"]["title"]); ?>管理后台</title>
        <meta name="description" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <meta name="keywords" content="<?php echo ($CONFIG["site"]["title"]); ?>管理后台" />
        <!-- <link href="__TMPL__statics/css/index.css" rel="stylesheet" type="text/css" /> -->
        <link href="__TMPL__statics/css/style.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/land.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/pub.css" rel="stylesheet" type="text/css" />
        <link href="__TMPL__statics/css/main.css" rel="stylesheet" type="text/css" />
        <link href="__PUBLIC__/js/jquery-ui.css" rel="stylesheet" type="text/css" />
        <script> var BAO_PUBLIC = '__PUBLIC__'; var BAO_ROOT = '__ROOT__'; </script>
        <script src="__PUBLIC__/js/jquery.js"></script>
        <script src="__PUBLIC__/js/jquery-ui.min.js"></script>
        <script src="__PUBLIC__/js/my97/WdatePicker.js"></script>
        <script src="/Public/js/layer/layer.js"></script>
        <script src="__PUBLIC__/js/admin.js?v=20150409"></script>
    </head>
    <body>
         <iframe id="baocms_frm" name="baocms_frm" style="display:none;"></iframe>
   <div class="main">
<div class="mainBt">
    <ul>
        <li class="li1">首页</li>
        <li class="li2">网站运行状态</li>
    </ul>
</div>
<div class="main-jsgl main-sc">
    <div class="jsglNr">

        <div class="selectNr" style="margin-top: 0px; border-top:none;">
            <div class="left">
                <a>欢迎您：<?php echo ($admin["username"]); ?>（<?php echo ($admin["role_name"]); ?>）</a>
                <a>上次登录地址：<?php echo ($ad["last_ip"]); ?></a>
                <a>更新到<?php echo ($v); ?></a>
                <a>php版本：<?php echo phpversion();?></a>

            </div>
            <div class="right">
               
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>

            <div class="selectNr selectNr2">
                <div class="left">
                    <div class="seleK">
                       
                    </div>

                </div>
                <div class="right">
                  
                </div>

        <div class="clear"></div>
    </div>

    <style>
	.gray{background-color: #F8F8F8; color:#F00 }
	.main-jsgl .jsglNr table tr td .gray{ color:#F00 }
	.main-jsgl .jsglNr table tr td .dot{ color:#F00 }
	.main-jsgl .jsglNr table tr td .dot1{ background: #F00;}
	</style>
        <div class="tableBox">
            <table bordercolor="#e1e6eb" cellspacing="0" width="100%" border="1px"  style=" border-collapse: collapse; margin:0px; vertical-align:middle; background-color:#FFF;"  >
                <tr>
                    <td class="w50">ID</td>
                    <td>项目</td>
                    <td>总数量</td>
                    <td class="gray">今日数量</td>
                    <td>操作</td>

                </tr>
            
            
            		 <tr>
                        <td>1</td>
                        <td>会员数量</td>
                        <td><?php echo ($counts["users"]); ?></td>
                        <td class="gray">今日新增<a class="dot">(<?php echo ($counts["totay_user"]); ?>)</a>个会员</td>
                        <td><a href="<?php echo U('user/index');?>" class="remberBtn ">详情</a></td>
                    </tr>
                     <tr>
                        <td>2</td>
                        <td>商家数量</td>
                        <td><?php echo ($counts["shops"]); ?></td>
                        <td class="gray">
                        今日入驻商家<a class="dot">(<?php echo ($counts["totay_shop"]); ?>)</a>家
                        待审核<a href="<?php echo U('shop/apply');?>"   class="dot">(<?php echo ($counts["totay_shop_audit"]); ?>)</a>条
                        待认领<a href="<?php echo U('shop/recognition');?>" class="dot">(<?php echo ($counts["shoprecognition"]); ?>)</a>家
                        </td>

                       

                        <td>
                        <?php if($counts['totay_shop_audit'] > 0): ?><a href="<?php echo U('shop/apply');?>"  class="remberBtn dot1">审核</a>
                        <?php else: endif; ?>
                        <a href="<?php echo U('shop/index');?>" class="remberBtn ">详情</a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>3</td>
                        <td>网站订单数量</td>
                        <td>商：<?php echo ($counts["order"]); ?>，外：<?php echo ($counts["order_waimai"]); ?>，团：<?php echo ($counts["order_tuan"]); ?></td>
                        <td class="gray">
                        今日商城<a href="<?php echo U('order/index');?>"  class="dot">(<?php echo ($counts["totay_order"]); ?>)</a>单
                        今日外卖<a href="<?php echo U('eleorder/index');?>"  class="dot">(<?php echo ($counts["totay_order_waimai"]); ?>)</a>单
                        今日团购<a href="<?php echo U('tuanorder/index');?>"  class="dot">(<?php echo ($counts["totay_order_tuan"]); ?>)</a>单
                        <br/><!--下面是退款的-->
                        商城退款<a href="<?php echo U('order/index');?>"  class="dot">(<?php echo ($counts["order_tui"]); ?>)</a>笔
                        外卖退款<a href="<?php echo U('eleorder/index');?>"  class="dot">(<?php echo ($counts["order_waimai_tui"]); ?>)</a>笔
                       
                        </td>
                        <td>
                        <?php if($counts['totay_order'] > 0): ?><a href="<?php echo U('order/index');?>" class="remberBtn ">商城</a>
                        <?php else: endif; ?>
                        <?php if($counts['totay_order_waimai'] > 0): ?><a href="<?php echo U('eleorder/index');?>" class="remberBtn ">外卖</a>
                        <?php else: endif; ?>
                        <?php if($counts['totay_order_tuan'] > 0): ?><a href="<?php echo U('tuanorder/index');?>" class="remberBtn ">团购</a>
                        <?php else: endif; ?>
                        
                        
                        
                        </td>
                    </tr>
                    
                     <tr>
                        <td>4</td>
                        <td>会员充值数</td>
                        <td><?php echo ($counts["totay_gold"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["totay_gold"]); ?>)</a>人充值</td>
                        <td><a href="<?php echo U('usermoneylogs/index');?>" class="remberBtn ">详情</a></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>家政服务预约</td>
                        <td><?php echo ($counts["jiazheng"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["totay_jiazheng"]); ?>)</a>人预约
                        其中<a class="dot">(<?php echo ($counts["totay_jiazheng_queren"]); ?>)</a>人需要确认</td>
                        <td>
                        <?php if($counts['totay_jiazheng_queren'] > 0): ?><a href="<?php echo U('housework/index');?>"  class="remberBtn dot1">家政确认</a>
                        <?php else: endif; ?>
                        
                        </td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>优惠劵下载</td>
                        <td><?php echo ($counts["coupon"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["today_coupon"]); ?>)</a>次下载</td>
                        <td><a href="<?php echo U('coupondownload/index');?>" class="remberBtn ">详情</a></td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>点评数量</td>
                        <td>
                        商家：<?php echo ($counts["dianping"]); ?>
                        商城：<?php echo ($counts["dianping_goods"]); ?>
                        抢购：<?php echo ($counts["dianping_tuan"]); ?>
                        外卖：<?php echo ($counts["dianping_waimai"]); ?>
                        </td>
                        <td class="gray">
                        今日商家<a href="<?php echo U('shopdianping/index');?>"  class="dot">(<?php echo ($counts["totay_dianping"]); ?>)</a>
                        今日商城<a href="#"  class="dot">(<?php echo ($counts["totay_dianping_goods"]); ?>)</a>
                        今日团购<a href="<?php echo U('tuandianping/index');?>"  class="dot">(<?php echo ($counts["totay_dianping_tuan"]); ?>)</a>
                        今日外卖<a href="#"  class="dot">(<?php echo ($counts["totay_dianping_waimai"]); ?>)</a>
                      
                        <td>
                        <?php if($counts['totay_dianping'] > 0): ?><a href="<?php echo U('shopdianping/index');?>" class="remberBtn ">商家点评</a>
                        <?php else: endif; ?>
                        <?php if($counts['totay_dianping_tuan'] > 0): ?><a href="<?php echo U('tuandianping/index');?>" class="remberBtn ">团购点评</a>
                        <?php else: endif; ?>
                       
                        </td>
                    </tr>
                    
                     <tr>
                        <td>8</td>
                        <td>帖子数量</td>
                        <td><?php echo ($counts["post"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["totay_post"]); ?>)</a>篇新帖
                        待审核<a href="<?php echo U('post/index');?>"   class="dot">(<?php echo ($counts["totay_post_audit"]); ?>)</a>条
                        </td>
                        <td>
                        <?php if($counts['totay_post_audit'] > 0): ?><a href="<?php echo U('post/index');?>"  class="remberBtn dot1">审核</a>
                        <?php else: endif; ?>
                        <a href="<?php echo U('post/index');?>" class="remberBtn ">详情</a>
                        </td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>分类信息数量</td>
                        <td><?php echo ($counts["life"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["totay_life"]); ?>)</a>条分类信息
                        
                        待审核<a href="<?php echo U('life/index');?>"   class="dot">(<?php echo ($counts["totay_life_audit"]); ?>)</a>条
                        </td>
                        <td>
                        <?php if($counts['totay_life_audit'] > 0): ?><a href="<?php echo U('life/index');?>"  class="remberBtn dot1">审核</a>
                        <?php else: endif; ?>
                        <a href="<?php echo U('life/index');?>" class="remberBtn ">详情</a>
                        </td>
                    </tr>
                    <!--家政预约-->
                    <tr>
                        <td>10</td>
                        <td>网站预约</td>
                        <td><?php echo ($counts["yuyue"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["today_yuyue"]); ?>)</a>人预约</td>
                        <td><a href="<?php echo U('shopyuyue/index');?>" class="remberBtn ">预约详情</a></td>
                    </tr>
                    
                     <tr>
                        <td>11</td>
                        <td>网站缴费</td>
                        <td><?php echo ($counts["bill"]); ?></td>
                        <td class="gray">今日<a class="dot">(<?php echo ($counts["totay_bill"]); ?>)</a>条分类信息
                        
                        待审核<a href="<?php echo U('bill/billorder');?>"   class="dot">(<?php echo ($counts["totay_bill_audit"]); ?>)</a>条
                        </td>
                        <td>
                        <?php if($counts['totay_bill_audit'] > 0): ?><a href="<?php echo U('bill/billorder');?>"  class="remberBtn dot1">确认</a>
                        <?php else: endif; ?>
                        <a href="<?php echo U('bill/billorder');?>" class="remberBtn ">退款</a>
                        </td>
                    </tr>
                    
           
            </table>
            <?php echo ($page); ?>
        </div>
        <div class="selectNr" style="margin-bottom: 0px; border-bottom: none;">
            <div class="left">
               <a href="http://www.fengmiyuanma.com/list-83.html"  class="remberBtn ">点击获取更新</a>
               <a href="http://www.fengmiyuanma.com/list-83.html"  class="remberBtn ">使用教程</a>
               <a href="http://www.fengmiyuanma.com/list-87.html"  class="remberBtn ">免费下载精品源码</a>
               <a href="http://www.baocms.com" target="_blank"  class="remberBtn ">正式运营购买正版授权</a>
            </div>
        </div>

</div>
</div>

</div>
</body>
</html>