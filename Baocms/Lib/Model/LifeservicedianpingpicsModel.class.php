<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifeservicedianpingpicsModel extends CommonModel{
    protected $pk   = 'pic_id';
    protected $tableName =  'lifeservice_dianping_pics';
    
    public function upload($id,$photos){
        $id = (int)$id;
        $this->delete(array("where"=>array('id'=>$id)));
        foreach($photos as $val){
            $this->add(array('pic'=>$val,'id'=>$id));
        }
        return true;
    }
    
   
    
    public function getPics($id){
        $id = (int)$id;
        return $this->where(array('id'=>$id))->select();
    }
    
}