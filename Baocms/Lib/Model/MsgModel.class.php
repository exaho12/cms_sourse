<?php
class MsgModel extends CommonModel{
    protected $pk   = 'msg_id';
    protected $tableName =  'msg';
    
    protected $types = array(
        'gift'      => '红包礼物',
        'movie'     => '官方动态',
        'message'   => '个人消息',
        'coupon'    => '抢购优惠',
    );
    
    public function getType(){
        return $this->types;
    }
    
}