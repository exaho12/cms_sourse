<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class WeixintmplModel extends CommonModel{
	protected $pk   = 'tmpl_id';
    protected $tableName =  'weixin_tmpl';
	protected $_validate = array(
		array('title','2,10','模板标题2至10个字符！',Model::MUST_VALIDATE, 'length', Model::MODEL_BOTH),
		array('serial','/^\w{3,}$/','请输入正确的模板库编号！',Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH),
		//array('template_id','/^\w{10,}$/','请输入正确的模板ID！',Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH),
		array('status','0,1','状态值不合法,必须0或1！',Model::MUST_VALIDATE, 'in', Model::MODEL_BOTH),
		array('sort','/^\d{1,4}$/','排序值不合法！',Model::MUST_VALIDATE, 'regex', Model::MODEL_BOTH),
	);
}