<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PostpicsModel extends CommonModel{
    protected $pk   = 'pic_id';
    protected $tableName =  'post_pics';
    
    public function upload($post_id,$photos){
        $post_id = (int)$post_id;
        $this->delete(array("where"=>array('post_id'=>$post_id)));
        foreach($photos as $val){
            $this->add(array('pic'=>$val,'post_id'=>$post_id));
        }
        return true;
    }
    
   
    
    public function getPics($post_id){
        $post_id = (int)$post_id;
        return $this->where(array('post_id'=>$post_id))->select();
    }
    
    
}