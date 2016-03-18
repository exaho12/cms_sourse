<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class  CommonAction extends  Action{
    protected  $_CONFIG = array();
    protected  $_token  = '07e9d1f8962c13a5f3366c2ca23126e0'; //默认的TOKEN
    protected  $shop_id = 0;
    protected  $shopdetails = array();
    protected  $weixin = null;
    protected  function _initialize(){ //SHOP_ID 为空的时候        
        $this->_CONFIG = D('Setting')->fetchAll();               
        define('__HOST__', 'http://'.$_SERVER['HTTP_HOST']);
        $this->shop_id = empty($_GET['shop_id']) ? 0 : (int)$_GET['shop_id'];
        if(!empty($this->shop_id)){
            $this->shopdetails = D('Shopdetails')->find($this->shop_id);
        }
        $this->_token = $this->_get_token();
       // file_put_contents('/www/web/bao_baocms_cn/public_html/Baocms/Lib/Action/Weixin/cc.txt',  $this->_token);
       // file_put_contents('/www/web/bao_baocms_cn/public_html/Baocms/Lib/Action/Weixin/aaa.txt', var_export($_GET,true));
        $this->weixin = D('Weixin');
        $this->weixin->init($this->_token); // 修改了 ThinkWechat  让他支持  主动发送微信消息
       
    }           
    
 


    protected function _get_token(){     
        if(!empty($this->shop_id)){
            return $this->shopdetails['token'];
        }
        return $this->_CONFIG['weixin']['token']; 
    }
   
    
}