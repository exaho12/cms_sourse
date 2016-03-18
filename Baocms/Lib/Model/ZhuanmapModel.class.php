<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ZhuanmapModel extends CommonModel{
    protected $pk        = 'map_id';
    protected $tableName = 'zhuan_map';
    protected $token     = 'zhuan_map';
  
    protected $_validate = array(
    	array('title', '2,10','专题名称2至10个字符'  ,Model::MUST_VALIDATE,'length',Model::MODEL_BOTH),
    	array('status','/^\d{1,}$/'  ,'状态值不合法'         ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    );
    protected $_auto = array(
    	array('status', 1, Model::MODEL_BOTH, 'string'),
    );
}