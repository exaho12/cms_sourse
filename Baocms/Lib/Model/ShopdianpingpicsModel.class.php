<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShopdianpingpicsModel extends CommonModel{
    protected $pk   = 'pic_id';
    protected $tableName =  'shop_dianping_pics';
    
    public function upload($dianping_id,$shop_id,$photos){
        $shop_id = (int)$shop_id;
        $dianping_id = (int)$dianping_id;
        $this->delete(array("where"=>array('dianping_id'=>$dianping_id)));
        foreach($photos as $val){
            $this->add(array('dianping_id'=>$dianping_id,'pic'=>$val,'shop_id'=>$shop_id));
        }
        return true;
    }
    
   
    
    public function getPics($dianping_id){
        $dianping_id = (int)$dianping_id;
        return $this->where(array('dianping_id'=>$dianping_id))->select();
    }
    
}