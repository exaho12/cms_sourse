<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MarketpicModel extends CommonModel{
    protected $pk   = 'pic_id';
    protected $tableName =  'market_pic';
    
    public function upload($market_id,$photos){
        $market_id = (int)$market_id;
        $this->delete(array("where"=>array('market_id'=>$market_id)));
        foreach($photos as $val){
            $this->add(array('pic'=>$val,'market_id'=>$market_id));
        }
        return true;
    }
    
   
    
    public function getPics($market_id){
        $market_id = (int)$market_id;
        return $this->where(array('market_id'=>$market_id))->select();
    }
}