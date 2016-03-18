<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class WeidiancateModel extends CommonModel{
    protected $pk   = 'cate_id';
    protected $tableName =  'weidian_cate';
    protected $token = 'weidian_cate';
    protected $orderby = array('orderby'=>'asc');
  
  public function getParentsId($id) {
        $data = $this->fetchAll();
        $parent_id = $data[$id]['parent_id']; 
        return $parent_id;
    }

    public function getChildren($id ,$ty= true) {
        $local = array();
        //暂时 只支持 2级分类
        $data = $this->fetchAll();
        if($ty) $local[] = $id;
        foreach ($data as $val) {
            if ($val['parent_id'] == $id) {
                $local[] = $val['cate_id'];
            }
        }
        return $local;
    }
   
}
