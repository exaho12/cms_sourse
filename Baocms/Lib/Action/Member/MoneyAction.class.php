<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MoneyAction extends CommonAction {

    public function money() {  //余额充值
        $this->assign('payment', D('Payment')->getPayments());
        $this->display();
    }
    
    //冻结资金成为代理商
    public function fzmoney(){
         if(empty($this->_CONFIG['quanming']['is_money'])){
            $this->success('网站还为开启充值代理金',U('index/index'));
         }
         if(!empty($this->ex['frozen_money'])){
             $this->success('您已经冻结过推广保证金了，不需要重复冻结的！',U('index/index'));
         }
     
        $this->assign('payment', D('Payment')->getPayments());
        $this->display();
    }
    public function fzmoneypay() { //后期优化
         if(empty($this->_CONFIG['quanming']['is_money'])){
            $this->success('网站还为开启充值代理金',U('index/index'));
         }
         if(!empty($this->ex['frozen_money'])){
             $this->success('您已经冻结过推广保证金了，不需要重复冻结的！',U('index/index'));
         }
        $money = $this->_CONFIG['quanming']['money']*100;
        $code = $this->_post('code', 'htmlspecialchars');
      
        $payment = D('Payment')->checkPayment($code);
        if (empty($payment)) {
            $this->error('该支付方式不存在');
        }
        $logs = array(
            'user_id' => $this->uid,
            'type' => 'fzmoney',
            'code' => $code,
            'order_id' => 0,
            'need_pay' => $money,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip(),
        );
        $logs['log_id'] = D('Paymentlogs')->add($logs);
        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('money', $money);
        $this->display();
    }
    public function moneypay() { //后期优化
        $money = (int) ($this->_post('money') * 100);
        $code = $this->_post('code', 'htmlspecialchars');
        if ($money <= 0) {
            $this->error('请填写正确的充值金额！');
        }
        $payment = D('Payment')->checkPayment($code);
        if (empty($payment)) {
            $this->error('该支付方式不存在');
        }
        $logs = array(
            'user_id' => $this->uid,
            'type' => 'money',
            'code' => $code,
            'order_id' => 0,
            'need_pay' => $money,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip(),
        );
        $logs['log_id'] = D('Paymentlogs')->add($logs);
        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('money', $money);
        $this->display();
    }

    public function recharge() { //代金券充值
        if ($this->isPost()) {
            $card_key = $this->_post('card_key', htmlspecialchars);
           // if (!D('Lock')->lock($this->uid)) { //上锁
               // $this->baoError('服务器繁忙，1分钟后再试');
            //}
            if(empty($card_key)){
               //  D('Lock')->unlock();
                $this->baoError('充值卡号不能为空');
            }
            if (!$detail = D('Rechargecard')->where(array('card_key' => $card_key))->find()) {
               // D('Lock')->unlock();
                $this->baoError('该充值卡不存在');
            }
            if ($detail['is_used'] == 1) {
               // D('Lock')->unlock();
                $this->baoError('该充值卡已经使用过了');
            }
            $member = D('Users')->find($this->uid);
            $member['money'] += $detail['value'];
            if ( D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'is_used' => 1))) {
                D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']));
                D('Usermoneylogs')->add(array(
                    'user_id' => $this->uid,
                    'money' => $detail['value'],
                    'create_time' => NOW_TIME,
                    'create_ip' => get_client_ip(),
                    'intro' => '代金券充值' . $detail['card_id'],
                ));
     
                D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'user_id' => $this->uid, 'used_time' => NOW_TIME));
       

                //微信通知
                $this->remainMoneyNotify($detail['value'], $member['money'], 1);

                $this->baoSuccess('充值成功！', U('money/recharge'));
            }
           // D('Lock')->unlock();
        } else {
            $this->display();
        }
    }

     //微信余额通知
    private function remainMoneyNotify($pay,$remain,$type=0)//0支出,1收入
    {
        //余额变动,微信通知
        $openid    = D('Connect')->getFieldByUid($this->uid,'open_id'); 
        $order_id  = $order['order_id'];
        $user_name = D('User')->getFieldByUser_id($this->uid,'nickname');
        if($type)
        $words     = "您的账户于".date('Y-m-d H:i:s')."收入".$pay."元,余额".$remain."元";
        else
        $words     = "您的账户于".date('Y-m-d H:i:s')."支出".$pay."元,余额".$remain."元";
        if($openid){
            $template_id = D('Weixintmpl')->getFieldByTmpl_id(4,'template_id');//余额变动模板
            $tmpl_data =  array(
                'touser'      => $openid,//用户微信openid
                'url'         => 'http://'.$_SERVER['HTTP_HOST'].'/mcenter',//相对应的订单详情页地址
                'template_id' => $template_id,
                'topcolor'    => '#2FBDAA',
                'data'        => array(
                    'first'=>array('value'=>'尊敬的用户,您的账户余额有变动！' ,'color'=>'#2FBDAA'),   
                    'keynote1'=>array('value'=> $user_name, 'color'=>'#2FBDAA'),//用户名
                    'keynote2'=>array('value'=> $words, 'color'=>'#2FBDAA'),//详情
                    'remark'  =>array('value'=>'详情请登录您的用户中心了解', 'color'=>'#2FBDAA')
                )
            );
            D('Weixin')->tmplmesg($tmpl_data);
        }
    }

    public function gold() {

        $this->assign('payment', D('Payment')->getPayments());
        $this->display();
    }

    public function goldpay() { //后期优化
        $gold = (int) $this->_post('gold');
        $code = $this->_post('code', 'htmlspecialchars');
        if ($gold <= 0) {
            $this->error('请填写正确的金块数！');
            die;
        }
        $payment = D('Payment')->checkPayment($code);
        if (empty($payment)) {
            $this->error('该支付方式不存在');
            die;
        }
        $logs = array(
            'user_id' => $this->uid,
            'type' => 'gold',
            'code' => $code,
            'order_id' => 0,
            'need_pay' => $gold * 100,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip(),
        );
        $logs['log_id'] = D('Paymentlogs')->add($logs);

        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('gold', $gold);
        $this->display();
    }

}
