<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PaymentlogsModel extends CommonModel{
    protected $pk   = 'log_id';
    protected $tableName =  'payment_logs';
    
    public function getLogsByOrderId($type,$order_id){
        $order_id = (int)$order_id;
        $type = addslashes($type);
        return $this->find(array('where'=>array('type'=>$type, 'order_id'=>$order_id)));
    }
    
}