<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class EleorderModel extends CommonModel {

    protected $pk = 'order_id';
    protected $tableName = 'ele_order';
    protected $cfg = array(
        0 => '等待付款',
        1 => '已付款,等待配送',
        2 => '正在配送',
		3 => '等待退款',
		4 => '退款完成',		
        8 => '已完成',
    );

    public function checkIsNew($uid, $shop_id) {
        $uid = (int) $uid;
        $shop_id = (int) $shop_id;
        return $this->where(array('user_id' => $uid, 'shop_id' => $shop_id, 'closed' => 0))->count();
    }

    public function getCfg() {
        return $this->cfg;
    }

    public function overOrder($order_id) {
        $detail = D('Eleorder')->find($order_id);
        if (empty($detail))
            return false;
        if ($detail['status'] != 2)
            return false;
        $ele = D('Ele')->find($detail['shop_id']);
        $shop = D('Shop')->find($detail['shop_id']);
        if (D('Eleorder')->save(array('order_id' => $order_id, 'status' => 8))) { //防止并发请求
            if ($detail['is_pay'] == 1) {
                $settlement_price = $detail['settlement_price'];
                if ($ele['is_fan']) { //如果商家开通了返现金额
                    $fan_money = $ele['fan_money'] > $settlement_price ? $settlement_price : $ele['fan_money'];
                    $fan = rand(0, $fan_money);
                    if ($fan > 0) {//返现金额大于0 那么更新订单 
                        $settlement_price = $settlement_price - $fan;
                        D('Eleorder')->save(array(
                            'order_id' => $order_id,
                            'settlement_price' => $settlement_price,
                            'fan_money' => $fan,
                        ));
                        D('Users')->addMoney($detail['user_id'], $fan, $ele['shop_name'] . '订餐返现');
                    }
                }

                if ($settlement_price > 0) {
                    D('Shopmoney')->add(array(
                        'shop_id' => $detail['shop_id'],
						'city_id' => $shop['city_id'],
						'area_id' => $shop['area_id'],
                        'type' => 'ele',
                        'money' => $settlement_price,
                        'create_ip' => get_client_ip(),
                        'create_time' => NOW_TIME,
                        'order_id' => $order_id,
                        'intro' => '餐饮订单:' . $order_id
                    ));
					
 					D('Users')->Money($shop['user_id'], $settlement_price, '商户餐饮订单资金结算:' . $order_id);//写入金块
                    //D('Users')->addMoney($shop['user_id'], $settlement_price, '餐饮订单:' . $order_id);
                }
                D('Users')->gouwu($detail['user_id'],$detail['total_price'],'外卖积分奖励');

            }
            //更新卖出数
            D('Eleorderproduct')->updateByOrderId($order_id);
            D('Ele')->updateCount($detail['shop_id'], 'sold_num'); //这里是订单数
            D('Ele')->updateMonth($detail['shop_id']);
        }
        return true;
    }

}
