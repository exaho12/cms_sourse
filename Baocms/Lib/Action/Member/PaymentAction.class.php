<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class IndexAction extends CommonAction {

    public function pay() {
        $logs_id = (int)$this->_get('logs_id');
        if (empty($logs_id)) {
            $this->error('没有有效的支付');
        }
       // if (!D('Lock')->lock($this->uid)) { //上锁
           // $this->error('服务器繁忙，1分钟后再试');
      //  }
        if (!$detail = D('Paymentlogs')->find($logs_id)) {
          //  D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        if ($detail['code'] != 'money') {
          //  D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        $member = D('Users')->find($this->uid);

        if ($detail['is_paid']) {
           // D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        if ($member['money'] < $detail['need_pay']) {
           // D('Lock')->unlock();
            $this->error('很抱歉您的账户余额不足', U('member/index'));
        }
        $member['money'] = $member['money'] - $detail['need_pay'];



        if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
            D('Usermoneylogs')->add(array(
                'user_id' => $this->uid,
                'money' => -$detail['need_pay'],
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
                'intro' => '余额支付' . $logs_id,
            ));
            D('Payment')->logsPaid($logs_id);
           // D('Lock')->unlock();

            $this->remainMoneyNotify($detail['need_pay'], $member['money']);

            $this->success('支付成功！', U('member/index'));
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
                'url'         => 'http://www.baocms.cn/mcenter',//相对应的订单详情页地址
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

    public function recharge() { //代金券充值
        if ($this->isPost()) {
            $card_key = $this->_post('card_key', htmlspecialchars);
            //if (!D('Lock')->lock($this->uid)) { //上锁
               // $this->baoError('服务器繁忙，1分钟后再试');
            //}
            if (!$detail = D('Rechargecard')->where(array('card_key' => $card_key))->find()) {
             //   D('Lock')->unlock();
                $this->baoError('该充值卡不存在');
            }
            if ($detail['is_used'] == 1) {
               // D('Lock')->unlock();
                $this->baoErrorJump('该充值卡已经使用过了', U('member/recharge'));
            }
            $member = D('Users')->find($this->uid);
            $member['money'] += $detail['value'];
            if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
                D('Usermoneylogs')->add(array(
                    'user_id' => $this->uid,
                    'money' => +$detail['value'],
                    'create_time' => NOW_TIME,
                    'create_ip' => get_client_ip(),
                    'intro' => '代金券充值' . $detail['card_id'],
                ));
                $res = D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'is_used' => 1));
                if (!empty($res)) {
                    D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'user_id' => $this->uid, 'used_time' => NOW_TIME));
                }
                //微信通知
                $this->remainMoneyNotify($detail['value'], $member['money'], 1);

                $this->baoSuccess('充值成功！', U('member/rechargecard'));
            }
           // D('Lock')->unlock();
        } else {
            $this->display();
        }
    }
}
