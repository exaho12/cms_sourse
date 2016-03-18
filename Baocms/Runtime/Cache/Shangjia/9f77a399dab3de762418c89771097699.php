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
<script src="__PUBLIC__/js/highcharts/highcharts.js"></script>
<script src="__PUBLIC__/js/highcharts/modules/exporting.js"></script>
<style>
.sy_c1Li {width: 1000px;}
</style>
<div class="sjgl_lead">
    <ul>
        <li><a href="#">商家中心首页</a>
		</li>
    </ul>
</div>
<div class="sy_content">

<div class="radius5 tuan_top">
        <div class="tuan_top_t">
        <div class="left tuan_topser_l">欢迎使用<strong class="green"><?php echo ($CONFIG["site"]["title"]); ?>商户中心</strong> , 为您的营销插上翅膀!	
        <?php if(empty($MEMBER['email'])): ?><i class="icon-remove red"></i> 您还没有通过邮箱认证，<a  target="_blank" href="<?php echo U('member/set/email');?>">立刻认证</a><?php endif; ?>
		<?php if(empty($MEMBER['mobile'])): ?><i class="icon-remove red"></i> 您还没有通过手机认证，<a  target="_blank" href="<?php echo U('member/set/mobile');?>">立刻认证</a><?php endif; ?>
            
            
            </div>
        </div>
    </div> 
    
    
    <div class="sy_c1">
        <ul>
            <li class="left sy_c1Li">
                <table class="tuan_table" width="100%" border="1" cellspacing="0" cellpadding="0">
        <tbody><tr style="background-color:#eee;">
            <td>项目</td>
            <td>总数据</td>
            <td>今日数据</td>
            <td width="70">操作</td>
        </tr>
        <tr>
            </tr>
            
             <tr>
                <td>抢购</td>
                <td><?php echo ($counts['tuan']); ?>条抢购，<?php echo ($counts['tuan_audit']); ?>条待审核,总<?php echo ($counts['tuan_order']); ?>订单</td>
                <td>今日<?php echo ($counts['totay_tuan_order']); ?>单 ，<?php echo ($counts['tuan_order_code_is_used']); ?>单未验证 <?php echo ($counts['tuan_order_code_status']); ?>单退款中</td>
                <td><a target="main_frm" href="<?php echo U('tuan/index');?>">详情</a></td>
            </tr>
            <tr>
                <td>优惠劵</td>
                <td><?php echo ($counts['coupon']); ?>条优惠劵，<?php echo ($counts['coupon_audit']); ?>条待审核</td>
                <td>今日<?php echo ($counts['totay_coupon_download']); ?>人下载，总下载：<?php echo ($counts['coupon_download']); ?>次，<?php echo ($counts['coupon_is_used']); ?>人未验证。</td>
                <td><a target="main_frm" href="<?php echo U('coupon/index');?>">详情</a></td>
            </tr>
            <?php if(!empty($wd)): ?><!--如果商户开通商城-->
            <tr>
                <td>商城</td>
                <td><?php echo ($counts['goods']); ?>个商品，<?php echo ($counts['goods_audit']); ?>个待审核，总<?php echo ($counts['goods_order']); ?>个商城订单</td>
                <td>今日<?php echo ($counts['totay_goods_order']); ?>人下单，正在配送<?php echo ($counts['goods_order_two']); ?>单，已完成<?php echo ($counts['goods_order_three']); ?>单</td>
                 <td><a target="main_frm" href="<?php echo U('order/index');?>">详情</a></td>
            </tr>
            <?php else: endif; ?>
            
             <?php if(!empty($waimai)): ?><!--如果商户开通外卖-->
             <tr>
                <td>外卖</td>
                <td><?php echo ($counts['ele']); ?>条外卖，<?php echo ($counts['ele_audit']); ?>菜品未审核</td>
                <td>今日<?php echo ($counts['totay_ele_order']); ?>人点餐，<?php echo ($counts['ele_order']); ?>总外卖订单<br/>
                	<?php echo ($counts['totay_ele_order']); ?>单等待处理，<?php echo ($counts['totay_ele_order']); ?>单配送中，<?php echo ($counts['totay_ele_order']); ?>单配送完成。</td>
                <td><a target="main_frm" href="<?php echo U('order/index');?>">详情</a></td>
            </tr>
            <?php else: endif; ?>
            
            <?php if($ding['is_ding'] == 1): ?><!--如果后台开通订座-->
             <tr>
                <td>订座</td>
                <td>总<?php echo ($counts['ding_room']); ?>个包厢，总<?php echo ($counts['totay_ding_order']); ?>个人预约订座。</td>
                <td>今日<?php echo ($counts['ele_order_one']); ?>人订座，<?php echo ($counts['ele_order_two']); ?>已付款，<?php echo ($counts['ele_order_eight']); ?>人未付款。</td>
                <td><a target="main_frm" href="<?php echo U('eleorder/index');?>">详情</a></td>
            </tr>
            <?php else: endif; ?>
            
            <?php if($ding['is_biz'] != 0): ?><!--如果后台开通黄页-->
             <tr>
                <td>黄页</td>
                <td>共<?php echo ($counts['biz']); ?>条黄页信息，<?php echo ($counts['biz_audit']); ?>条未审核</td>
                <td></td>
                <td><a target="main_frm" href="<?php echo U('pois/index');?>">详情</a></td>
            </tr>
            <?php else: endif; ?>
            
             <tr>
                <td>粉丝</td>
                <td>总<?php echo ($counts['favorites']); ?>粉丝</td>
                <td>今日新增粉丝<?php echo ($counts['totay_favorites']); ?>条</td>
                <td><a target="main_frm" href="<?php echo U('order/index');?>">详情</a></td>
            </tr>
             <tr>
                <td>评价</td>
                <td>商城<?php echo ($counts['goods_dianping']); ?>条评价，外卖<?php echo ($counts['ele_dianping']); ?>条评价<br/>
                    抢购<?php echo ($counts['tuan_dianping']); ?>条评价，订座<?php echo ($counts['ding_dianping']); ?>条评价</td>
                <td>今日商城<?php echo ($counts['totay_goods_dianping']); ?>条评价，今日外卖<?php echo ($counts['totay_ele_dianping']); ?>条评价<br/>
                	今日抢购<?php echo ($counts['totay_tuan_dianping']); ?>条评价，今日订座<?php echo ($counts['totay_ding_dianping']); ?>条评价</td>
                <td><a target="main_frm" href="<?php echo U('dianping/index');?>">详情</a></td>
            </tr>
             <tr>
                <td>分类信息</td>
                <td>总<?php echo ($counts['life']); ?>条分类信息，<?php echo ($counts['life_audit']); ?>条待审核</td>
                <td>今日<?php echo ($counts['goods']); ?>条</td>
                <td><a target="main_frm" href="<?php echo U('life/index');?>">详情</a></td>
            </tr>
            <tr>
                <td>招聘</td>
                <td>总<?php echo ($counts['work']); ?>条，待审核招聘<?php echo ($counts['work_audit']); ?>条。</td>
                <td></td>
                <td><a target="main_frm" href="<?php echo U('work/index');?>">详情</a></td>
            </tr>
            
            <tr>
                <td>商家预约</td>
                <td>共<?php echo ($counts['shopyuyue']); ?>人预约</td>
                <td>已确认<?php echo ($counts['shopyuyue_one']); ?>人预约，还有<?php echo ($counts['shopyuyue_eight']); ?>人未确认。</td>
                <td><a target="main_frm" href="<?php echo U('yuyue/index');?>">详情</a></td>
            </tr>
            
            </tbody>
          </table>
            </li>
            <li class="left sy_c1Li">
                <div class="sy_c1Linr">
                    <div class="sy_c1Linr_t">商家资金时间段（<?php echo ($bg_date); ?> - <?php echo ($end_date); ?>）内走势 ---- <?php echo ($SHOP['shop_name']); ?></div>
                    <div class="sy_c1List">
                        <script>
                            $(function () {
                            $('#container3').highcharts({
                                chart: {
                            type: 'line',
                            margin: [50, 0, 60, 80] //距离上下左右的距离值
                            },
                            title: {
                            text: '',
                                    x: - 20 //center
                            },
                                    subtitle: {
                                    text: "",
                                            x: - 20
                                    },
                                    xAxis: {
                                    categories: [<?php echo ($money["d"]); ?>]
                                    },
                                    yAxis: {
                                    min: 0,  
                                    title: {
                                    text: '单位元'
                                    },
                                            plotLines: [{
                                            value: 0,
                                                    width: 1,
                                                    color: '#808080'
                                            }]
                                    },
                                    series: [{
                                    name: '收入金额',
                                            data: [<?php echo ($money["price"]); ?>]
                                    }]
                            });
                            });</script>
                                                <div id="container3" style="height: 215px;">

                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="sy_c1">
        <ul>
            <li class="left sy_c1Li">
                <div class="sy_c1Linr">
                    <div class="sy_c1Linr_t">统计分析</div>
                    <div class="sy_c2tjnr">
                        <script>
                            $(function () {
                            $('#container').highcharts({
                            title: {
                            text: '抢购金额时间段（<?php echo ($bg_date); ?> - <?php echo ($end_date); ?>）内趋势',
                                    x: - 20 //center
                            },
                                    subtitle: {
                                    text: "<?php echo ($SHOP['shop_name']); ?>",
                                            x: - 20
                                    },
                                    xAxis: {
                                    categories: [<?php echo ($money["d"]); ?>]
                                    },
                                    yAxis: {
                                    title: {
                                    text: '单位元'
                                    },
                                            plotLines: [{
                                            value: 0,
                                                    width: 1,
                                                    color: '#808080'
                                            }]
                                    },
                                    series: [{
                                    name: '购买金额',
                                            data: [<?php echo ($money["price"]); ?>]
                                    }]
                            });
                            });</script>
                        <div id="container">

                        </div>
                    </div>
                </div>
            </li>
            <li class="left sy_c1Li">
                <div class="sy_c1Linr">
                    <div class="sy_c1Linr_t">销售额分析</div>
                    <div class="sy_c2tjnr">
                        <script>
                            $(function () {
                            $('#container2').highcharts({
                            title: {
                            text: '销售金额时间段（<?php echo ($bg_date); ?> - <?php echo ($end_date); ?>）内趋势',
                                    x: - 20 //center
                            },
                                    subtitle: {
                                    text: "<?php echo ($SHOP['shop_name']); ?>",
                                            x: - 20
                                    },
                                    xAxis: {
                                    categories: [<?php echo ($ordermoney["d"]); ?>]
                                    },
                                    yAxis: {
                                    title: {
                                    text: '单位元'
                                    },
                                            plotLines: [{
                                            value: 0,
                                                    width: 1,
                                                    color: '#808080'
                                            }]
                                    },
                                    series: [{
                                    name: '购买金额',
                                            data: [<?php echo ($ordermoney["price"]); ?>]
                                    }]
                            });
                            });</script>
                        <div id="container2">

                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
</body>
</html>