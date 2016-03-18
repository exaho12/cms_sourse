<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class  LifedetailsModel extends  CommonModel{
    protected $pk   = 'life_id';
    protected $tableName =  'life_details';
    
    public function updateDetails($life_id,$details){
        $data = $this->find($life_id);
        if($data){
            $this->save(array('life_id'=>$life_id,'details'=>$details));
        }else{
            $this->add(array('life_id'=>$life_id,'details'=>$details)); 
        }
        return true;
    }
    
}