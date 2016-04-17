<?php

import('WxPay', APP_PATH . 'Lib/Payment/weixin', '.Api.php');

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
            if ($detail['type'] == 'ele') {
                $this->ele_success('恭喜您支付成功啦！', $detail);
            } elseif ($detail['type'] == 'ding') {
                $this->ding_success('恭喜您支付成功啦！', $detail);
            } elseif ($detail['type'] == 'goods') {
                
                if(empty($detail['order_id'])){
                    $this->success('合并付款成功', U('member/order/index'));
                }else{
                    $this->goods_success('恭喜您支付成功啦！', $detail);
                }
               
             
            } elseif ($detail['type'] == 'gold' || $detail['type'] == 'money'|| $detail['type'] == 'fzmoney') {
                $this->success('恭喜您充值成功', U('member/index/index'));

            } else {
                $this->other_success('恭喜您支付成功啦！', $detail);
            }
        }else{
             $this->success('支付成功', U('member/index/index'));
        }
        
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
	
	 public function b2CQuery(){
            $map['log_id'] = I('log_id');
            $logs = D('Paymentlogs')->where($map)->find();
//            if (!empty($logs) && $logs['is_paid'] == 1) {
            $button= D('Payment')->b2CQuery($logs);
            if(is_array($button)){
                $this->assign('list', $button);
            }else{
                $this->assign('button', $button);
            }
//            }
            $this->assign('types', D('Payment')->getTypes());
            $this->assign('logs', $logs);
            $this->assign('paytype', D('Payment')->getPayments());

            $this->display();
        }

        public function b2cRefund(){
            $map['log_id'] = I('log_id');
            $logs = D('Paymentlogs')->where($map)->find();
//            if (!empty($logs) && $logs['is_paid'] == 1) {
            $button= D('Payment')->b2cRefund($logs);
            if(is_array($button)){
                $this->assign('list', $button);
            }else{
                $this->assign('button', $button);
            }

//            }
//            dump($logs);
            $this->assign('types', D('Payment')->getTypes());
            $this->assign('logs', $logs);
            $this->assign('paytype', D('Payment')->getPayments());

            $this->display();
        }

        public function orderQuery()
        {
            $input = new WxPayOrderQuery();
            if (isset($_POST["transaction_id"]) && $_POST["transaction_id"] != "") {
                $transaction_id = $_POST["transaction_id"];
                $input->SetTransaction_id($transaction_id);
                $output = WxPayApi::orderQuery($input);
            } elseif (isset($_REQUEST["out_trade_no"]) && $_POST["out_trade_no"] != "") {
                $out_trade_no = $_POST["out_trade_no"];
                $input->SetOut_trade_no($out_trade_no);
                $output = WxPayApi::orderQuery($input);
            }
            $this->assign('list', $output);
            $this->display();
        }

        public function refundQuery()
        {
            $input = new WxPayRefundQuery();
            if (isset($_POST["transaction_id"]) && $_POST["transaction_id"] != "") {
                $transaction_id = $_POST["transaction_id"];
                $input->SetTransaction_id($transaction_id);
                $output = WxPayApi::refundQuery($input);
            } elseif (isset($_POST["out_trade_no"]) && $_POST["out_trade_no"] != "") {
                $out_trade_no = $_POST["out_trade_no"];
                $input->SetOut_trade_no($out_trade_no);
                $output = WxPayApi::refundQuery($input);
            } elseif (isset($_POST["out_refund_no"]) && $_POST["out_refund_no"] != "") {
                $out_refund_no = $_REQUEST["out_refund_no"];
                $input->SetOut_refund_no($out_refund_no);
                $output = WxPayApi::refundQuery($input);
            } elseif (isset($_POST["refund_id"]) && $_POST["refund_id"] != "") {
                $refund_id = $_POST["refund_id"];
                $input->SetRefund_id($refund_id);
                $output = WxPayApi::refundQuery($input);

            }
            $this->assign('list', $output);
            $this->display();
        }

        //
        public function refund()
        {
            $input = new WxPayRefund();
            if (isset($_POST["transaction_id"]) && $_POST["transaction_id"] != "") {
                $transaction_id = $_POST["transaction_id"];
                $total_fee = $_POST["total_fee"];
                $refund_fee = $_POST["refund_fee"];
                $input->SetTransaction_id($transaction_id);
                $input->SetTotal_fee($total_fee);
                $input->SetRefund_fee($refund_fee);
                $input->SetOut_refund_no(WxPayConfig::MCHID . date("YmdHis"));
                $input->SetOp_user_id(WxPayConfig::MCHID);
                $output = WxPayApi::refund($input);

            } elseif (isset($_POST["out_trade_no"]) && $_POST["out_trade_no"] != "") {
                $out_trade_no = $_REQUEST["out_trade_no"];
                $total_fee = $_REQUEST["total_fee"];
                $refund_fee = $_REQUEST["refund_fee"];
                $input = new WxPayRefund();
                $input->SetOut_trade_no($out_trade_no);
                $input->SetTotal_fee($total_fee);
                $input->SetRefund_fee($refund_fee);
                $input->SetOut_refund_no(WxPayConfig::MCHID . date("YmdHis"));
                $input->SetOp_user_id(WxPayConfig::MCHID);
                $output = WxPayApi::refund($input);
            }
            $this->assign('list', $output);
            $this->display();
        }

        public function micropay()
        {
            if (isset($_POST["auth_code"]) && $_POST["auth_code"] != "") {
                $auth_code = $_POST["auth_code"];
                $input = new WxPayMicroPay();
                $input->SetAuth_code($auth_code);
                $input->SetBody($_POST['body']);
                $input->SetTotal_fee($_POST['total_fee']);
                $input->SetOut_trade_no($_POST['out_trade_no']);

                $microPay = new MicroPay();
                $output = $microPay->pay($input);
            }
            $this->assign('list', $output);
            $this->display();
        }

        public function download()
        {
//            $datetime1 = date_create($_POST["bill_date1"]);
//            $datetime2 = date_create($_POST["bill_date2"]);
//            $interval = date_diff($datetime1, $datetime2);
//            echo $interval;
//            echo $interval->format('%R%a days');
//            
            if (isset($_POST["bill_date"]) && $_POST["bill_date"] != "") {
                $bill_date = $_POST["bill_date"];
                $bill_type = $_POST["bill_type"];
                $input = new WxPayDownloadBill();
                $input->SetBill_date($bill_date);
                $input->SetBill_type($bill_type);
                $strings = WxPayApi::downloadBill($input);
                $strings = str_replace('`', '', $strings);
                $lines = explode(PHP_EOL, $strings);
                unset($lines[count($lines) - 1]);
                $title = explode(',', $lines[0]);

                $tail = explode(',', $lines[count($lines) - 2]);
                $tails = explode(',', $lines[count($lines) - 1]);

                $num = count($lines);
                unset($lines[$num - 1]);
                unset($lines[$num - 2]);
                unset($lines[0]);
                foreach ($lines as $k => $val) {
                    $arr[] = explode(',', $val);
                }

                $this->assign('title', $title);
                $this->assign('tail', $tail);
                $this->assign('tails', $tails);
                $this->assign('arr', $arr);
                if (isset($_POST['expType']) && $_POST['expType'] != '') {
                    exportExcel('下载对账单', $title, $arr, $_POST['expType']);
                }
            }

            $this->display();

        }

        public function tool()
        {
            $this->display();
        }


}
