<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ZhuanfloorModel extends CommonModel {

    protected $pk         = 'floor_id';
    protected $tableName  = 'zhuan_floor';
    protected $token      = 'zhuan_floor';

    protected $_validate = array(
    	array('title','2,15','楼层名称2至15个字符'  ,Model::MUST_VALIDATE,'length',Model::MODEL_BOTH),
    	array('sort' ,'/^\d{1,}$/','排序值不合法',Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH)
    );


}
