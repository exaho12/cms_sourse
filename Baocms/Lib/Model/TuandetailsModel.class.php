<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuandetailsModel extends CommonModel{
    protected $pk   = 'tuan_id';
    protected $tableName =  'tuan_details';
    
    
    public function  getDetail($tuan_id){
        $data= $this->find($tuan_id);
        if(empty($data)){
            $data = array(
                'tuan_id' => $tuan_id,
            );
            $this->add($data);
        }
        return $data;
    }
}