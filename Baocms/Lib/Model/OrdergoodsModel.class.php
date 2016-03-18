<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class OrdergoodsModel extends CommonModel {

    protected $pk = 'id';
    protected $tableName = 'order_goods';
    protected $types = array(
        0 => '等待发货',
        1 => '已经捡货',
        8 => '已完成配送',
    );

    public function getType() {
        return $this->types;
    }

}