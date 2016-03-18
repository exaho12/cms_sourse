<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CityModel extends CommonModel{
    protected $pk   = 'city_id';
    protected $tableName =  'city';
    protected $token = 'city';
    protected $orderby = array('orderby'=>'asc');
   
    public function setToken($token)
    {
        $this->token = $token;
    }
 
}