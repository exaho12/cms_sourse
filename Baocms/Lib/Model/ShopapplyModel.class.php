<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShopapplyModel extends CommonModel{
    protected $pk   = 'apply_id';
    protected $tableName =  'shop_apply';
    
    public  function _format($data){
        static $cates  = null;
        if($cates == null){
            $cates = D('Shopcate')->fetchAll();
        }
        $data['cate_name'] = $cates[$data['cate_id']]['cate_name'];
        return $data;
    }
}