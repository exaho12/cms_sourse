<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PostzanModel extends CommonModel{
    protected $pk   = 'zan_id';
    protected $tableName =  'post_zan';
    
    public function checkIsZan($post_id,$ip){
        return $this->find(array('where'=>array('post_id'=>$post_id,'create_ip'=>$ip)));        
    }
    
}