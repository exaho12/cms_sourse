<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class money{//余额支付
    
    public function  getCode($logs,$setting=array()){
        
        return '<input type="button" class="payment" onclick="window.open(\''.U('member/pay/pay',array('logs_id'=>$logs['logs_id'])).'\')" value=" 立刻支付 " />';
    }

    public function respond(){
        
    }
    
}