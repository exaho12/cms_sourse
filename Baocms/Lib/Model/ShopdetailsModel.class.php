<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShopdetailsModel extends CommonModel{
    protected $pk   = 'shop_id';
    protected $tableName =  'shop_details';
     public function upDetails($shop_id,$data){
        $shop_id = (int)$shop_id;
        $data['shop_id'] = $shop_id;
        $rows = $this->find($shop_id);
        if($rows){
            $this->save($data);
        }else{
            $this->add($data);
        }
        return true;
    }
    
    

    
}