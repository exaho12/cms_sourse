<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifereportModel extends CommonModel{
    protected $pk   = 'id';
    protected $tableName =  'life_report';
    
    public function check($life_id,$user_id){
        $life_id = (int)$life_id;
        $user_id = (int)$user_id;
        
        return $this->find(array('where'=>array(
            'user_id'   =>$user_id,
            'life_id'   => $life_id
        )));
    }
}