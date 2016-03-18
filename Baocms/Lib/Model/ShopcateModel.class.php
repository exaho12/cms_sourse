<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShopcateModel extends CommonModel {

    protected $pk = 'cate_id';
    protected $tableName = 'shop_cate';
    protected $token = 'shop_cate';
    protected $orderby = array('orderby' => 'asc');

    public function getParentsId($id) {
        $data = $this->fetchAll();
        $parent_id = $data[$id]['parent_id']; 
        return $parent_id;
    }

    public function getChildren($id) {
        $local = array();
        //暂时 只支持 2级分类
        $data = $this->fetchAll();
        $local[] = $id;
        foreach ($data as $val) {
            if ($val['parent_id'] == $id) {
                $local[] = $val['cate_id'];
            }
        }
        return $local;
    }

}