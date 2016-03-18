<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PaymentAction extends CommonAction {

    protected function ele_success($message, $detail) {
        $order_id = $detail['order_id'];
        $eleorder = D('Eleorder')->find($order_id);
        $detail['single_time'] = $eleorder['create_time'];
        $detail['settlement_price'] = $eleorder['settlement_price'];
        $detail['new_money'] = $eleorder['new_money'];
        $detail['fan_money'] = $eleorder['fan_money'];
        $addr_id = $eleorder['addr_id'];
        $product_ids = array();
        $ele_goods = D('Eleorderproduct')->where(array('order_id' => $order_id))->select();
        foreach ($ele_goods as $k => $val) {
            if (!empty($val['product_id'])) {
                $product_ids[$val['product_id']] = $val['product_id'];
            }
        }
        $addr = D('Useraddr')->find($addr_id);
        $this->assign('addr', $addr);
        $this->assign('ele_goods', $ele_goods);
        $this->assign('products', D('Eleproduct')->itemsByIds($product_ids));
        $this->assign('message', $message);
        $this->assign('detail', $detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('ele');
    }

    protected function goods_success($message, $detail) {
        $order_ids = array();
        if (!empty($detail['order_id'])) {
            $order_ids[] = $detail['order_id'];
        } else {
            $order_ids = explode(',', $detail['order_ids']);
        }
        $goods = $good_ids = $addrs = array();
        foreach ($order_ids as $k => $val) {
            if (!empty($val)) {
                $order = D('Order')->find($val);
                $addr = D('Useraddr')->find($order['addr_id']);
                $ordergoods = D('Ordergoods')->where(array('order_id' => $val))->select();
                foreach ($ordergoods as $a => $v) {
                    $good_ids[$v['goods_id']] = $v['goods_id'];
                }
            }
            $goods[$k] = $ordergoods;
            $addrs[$k] = $addr;
        }
        $this->assign('addr', $addrs[0]);
        $this->assign('goods', $goods);
        $this->assign('good', D('Goods')->itemsByIds($good_ids));
        $this->assign('detail', $detail);
        $this->assign('message', $message);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('goods');
    }

    public function detail($order_id) {
        $dingorder = D('Shopdingorder');
        $dingyuyue = D('Shopdingyuyue');
        $dingmenu = D('Shopdingmenu');
        if (!$order = $dingorder->where('order_id = ' . $order_id)->find()) {
            $this->baoError('该订单不存在');
        } else if (!$yuyue = $dingyuyue->where('ding_id = ' . $order['ding_id'])->find()) {
            $this->baoError('该订单不存在');
        } else if ($yuyue['user_id'] != $this->uid) {
            $this->error('非法操作');
        } else {
            $arr = $dingorder->get_detail($this->shop_id, $order, $yuyue);
            $menu = $dingmenu->shop_menu($this->shop_id);
            $this->assign('yuyue', $yuyue);
            $this->assign('order', $order);
            $this->assign('order_id', $order_id);
            $this->assign('arr', $arr);
            $this->assign('menu', $menu);
            $this->display();
        }
    }

    protected function ding_success($message, $detail) {
        $dingorder = D('Shopdingorder');
        $dingyuyue = D('Shopdingyuyue');
        $dingmenu = D('Shopdingmenu');

        if (!$order = $dingorder->where('order_id = ' . $detail['order_id'])->find()) {
            $this->error('该订单不存在');
        } else if (!$yuyue = $dingyuyue->where('ding_id = ' . $order['ding_id'])->find()) {
            $this->error('该订单不存在');
        } else if ($yuyue['user_id'] != $this->shop_id) {
            $this->error('非法操作');
        } else {
            $arr = $dingorder->get_detail($yuyue['shop_id'], $order, $yuyue);
            $menu = $dingmenu->shop_menu($yuyue['shop_id']);
            $this->assign('yuyue', $yuyue);
            $this->assign('order', $order);
            $this->assign('order_id', $detail['order_id']);
            $this->assign('arr', $arr);
            $this->assign('menu', $menu);
            $this->assign('message', $message);
            $this->assign('paytype', D('Payment')->getPayments());
            $this->display('ding');
        }
    }

    protected function other_success($message, $detail) {
        //dump($detail);
        $tuanorder = D('Tuanorder')->find($detail['order_id']);
        if (!empty($tuanorder['branch_id'])) {
            $branch = D('Shopbranch')->find($tuanorder['branch_id']);
            $addr = $branch['addr'];
        } else {
            $shop = D('Shop')->find($tuanorder['shop_id']);
            $addr = $shop['addr'];
        }

        $this->assign('addr', $addr);
        $tuans = D('Tuan')->find($tuanorder['tuan_id']);
        $this->assign('tuans', $tuans);
        $this->assign('tuanorder', $tuanorder);
        $this->assign('message', $message);
        $this->assign('detail', $detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('other');
    }

    public function respond() {
        $code = $this->_get('code');
        //$code = 'alipay';
        if (empty($code)) {
            $this->error('没有该支付方式！');
            die;
        }
        $ret = D('Payment')->respond($code);
        if ($ret == false) {
            $this->error('支付验证失败！');
            die;
        }
        if ($this->isPost()) {
            echo 'SUCESS';
            die;
        }
        
        $type = D('Payment')->getType();
        $log_id = D('Payment')->getLogId();
        $detail = D('Paymentlogs')->find($log_id);
        if(!empty($detail)){
            if ($type == 'ele') {
                $this->ele_success('恭喜您支付成功啦！', $detail);
            } elseif ($type == 'ding') {
                $this->ding_success('恭喜您支付成功啦！', $detail);
            } elseif ($type == 'goods') {
                
                if(empty($detail['order_id'])){
                    $this->success('合并付款成功', U('member/order/index'));
                }else{
                    $this->goods_success('恭喜您支付成功啦！', $detail);
                }
               
             
            } elseif ($type == 'gold' || $detail['type'] == 'money') {
                $this->success('恭喜您充值成功', U('member/index/index'));

            } else {
                $this->other_success('恭喜您支付成功啦！', $detail);
            }
        }else{
             $this->success('支付成功', U('member/index/index'));
        }
        //if(empty($type) || empty($log_id)){
        //$this->success('支付成功！', U('index/index'));
        //  }
    }

    public function payment($log_id) {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
        $log_id = (int) $log_id;

        $logs = D('Paymentlogs')->find($log_id);
        if (empty($logs) || $logs['user_id'] != $this->uid || $logs['is_paid'] == 1) {
            $this->error('没有有效的支付记录1！');
            die;
        }
        $url = "";
        if ($logs['type'] == "tuan") {
            $url = U('tuan/pay', array('order_id' => $logs['order_id']));
        } elseif ($logs['type'] == "ele") {
            $url = U('ele/pay', array('order_id' => $logs['order_id']));
        } elseif ($logs['type'] == "goods") {
            $url = U('mall/pay', array('order_id' => $logs['order_id']));
        } elseif ($logs['type'] == "ding") {
            $url = U('ding/pay2', array('order_id' => $logs['order_id']));
        }
        $this->assign('url', $url);
        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('types', D('Payment')->getTypes());
        $this->assign('logs', $logs);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display();
    }

}
