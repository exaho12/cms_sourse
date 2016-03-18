<?php


/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifeServiceModel extends RelationModel {

	
	protected $_link = array(

         'LifeServiceCate' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'LifeServiceCate',
            'foreign_key' => 'cate_id',
            'mapping_fields' =>'cate_name',
            'as_fields'=>'cate_name,cate_name', 
        )
	
        
    );
}