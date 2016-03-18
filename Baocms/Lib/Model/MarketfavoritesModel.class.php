<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MarketfavoritesModel extends CommonModel{
    protected $pk   = 'favorites_id';
    protected $tableName =  'market_favorites';
    
    
    public function check($market_id,$user_id){
        $data = $this->find(array('where'=>array('market_id'=>(int)$market_id,'user_id'=>(int)$user_id)));
        return $this->_format($data);
    }
}