<?php
/**
 * 会员中心提问列表模型
 */
Class ShophuihuiViewModel extends ViewModel {

	 Protected $viewFields = array(
		'shop_ding_yuyue' => array(
			'ding_id', 'room_id', 'shop_id', 'last_date', 'number','is_pay',
			'_type' => 'LEFT'
			),
		'shop_ding_order' => array(
			'name','mobile','need_price', '_on' => 'shop_ding_yuyue.order_no = shop_ding_order.order_no'
			),
		);
}
?>