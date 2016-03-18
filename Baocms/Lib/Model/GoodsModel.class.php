<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class GoodsModel extends CommonModel{
    protected $pk   = 'goods_id';
    protected $tableName =  'goods';
	 protected $_validate = array(
        array( ),
        array( ),
        array( )
    );

    
    
    public function _format($data){
        $data['save'] =  round(($data['price'] - $data['mall_price'])/100,2);
        $data['price'] = round($data['price']/100,2);
		//多属性开始
		$data['mobile_fan'] = round($data['mobile_fan']/100,2);
		//多属性结束
		 
        $data['mall_price'] = round($data['mall_price']/100,2); 
        $data['settlement_price'] = round($data['settlement_price']/100,2); 
        $data['commission'] = round($data['commission']/100,2); 
        $data['discount'] = round($data['mall_price'] * 10 / $data['price'],1);
        return $data;
    }
	
}