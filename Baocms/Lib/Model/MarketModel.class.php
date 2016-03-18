<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MarketModel extends CommonModel{
    protected $pk   = 'market_id';
    protected $tableName =  'market'; //数据表结构的修改主要是为了兼容之前的
    
    public function getType(){
        
        return  array(
            1   => '美食',
            2   => '购物',
            3   => '电影',
            4   => '亲子',
            5   => '休闲娱乐',
            6   => '超市',
        );
    }
    
    
}