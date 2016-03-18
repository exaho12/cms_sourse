<?php

/****************** 版权声明 ******************************
 *
 *----------------合肥生活宝网络科技有限公司-----------------
 *----------------      www.taobao.com    -----------------
 *QQ:800026911  
 *电话:0551-63641901  
 *EMAIL：youge@baocms.com
 * 
 ***************  未经许可不得用于商业用途  ****************/

class HouseworklookModel  extends  CommonModel{
    protected $pk   = 'look_id';
    protected $tableName =  'housework_look';
    
    public function checkIsLook($shop_id,$housework_id){
        
        return $this->find(array('where'=>array('shop_id'=>(int)$shop_id,'housework_id'=>(int)$housework_id)));
    }
    
    public function checkLook($shop_id,$housework_ids){
        $datas = $this->where(array(
            'shop_id'=>(int)$shop_id,
            'housework_id' => array('IN',$housework_ids),
        ))->select();
        $return = array();
        foreach($datas as $val){
            $return[$val['housework_id']] = $val['housework_id'];
        }
        return $return;
    }
    
}