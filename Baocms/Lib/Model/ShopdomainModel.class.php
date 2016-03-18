<?php

class  ShopdomainModel extends CommonModel{
    
    protected $pk   = 'shop_id';
    protected $tableName =  'shop_domain';
    
    public function getDomain($domains){
        $data =  $this->where(array('second_domain'=>$domains))->find();
        
        return $data['shop_id']; 
    }
    
    public function domain($shop_id){
        $data = $this->find($shop_id);
        $return  = $data['domain'];
        if(empty($data['domain'])){
           if(empty($data['second_domain'])) return false;
           $return = $data['second_domain'] .'.'.  getDomain($_SERVER['HTTP_HOST']);
        }
        return $return;
    }
    
    
}
