<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ZhuanModel extends CommonModel{
    protected $pk   = 'zhuan_id';
    protected $tableName =  'zhuan';
    protected $token = 'zhuan';
    protected $orderby = array('sort'=>'asc');
  
    protected $_validate = array(
    	array('sort','/^\d{1,}$/','排序值不合法'  ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    	array('floor_id','/^\d{1,}$/','楼层不合法'  ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    	array('deadline','/^\d{10,11}$/','到期时间不合法'  ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    );


}