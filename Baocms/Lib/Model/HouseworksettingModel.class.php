<?php

/****************** 版权声明 ******************************
 *
 *----------------合肥生活宝网络科技有限公司-----------------
 *----------------      www.baocms.com    -----------------
 *QQ:800026911  
 *电话:0551-63641901  
 *EMAIL：youge@baocms.com
 * 
 ***************  未经许可不得用于商业用途  ****************/

class  HouseworksettingModel  extends  CommonModel{
    protected $pk   = 'id';
    protected $tableName =  'housework_setting';
    
    
    public function detail($id){
        $id = (int)$id;
        $data = $this->find($id);
        if(empty($data)){
            $data = array('id'=>$id);
            $this->add($data);
        }
        return $data;
    }
    
}