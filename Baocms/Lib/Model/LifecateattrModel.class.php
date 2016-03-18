<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class  LifecateattrModel extends CommonModel{
    
    protected $pk = 'attr_id';
    protected $tableName = 'life_cate_attr';
    
    protected $token = 'life_cate_attr';

    protected $orderby = array('orderby'=>'asc','attr_id'=>'asc');
    

    public function getAttrs($cate_id){
        $items  = $this->where(array('cate_id'=>(int)$cate_id))->select();
        $return  = array();
        foreach($items as $val){
            $return[$val['type']][$val['attr_id']] = $val;
        }
        return $return;
    }
    
    
}