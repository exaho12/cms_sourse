<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class WeixinkeywordModel extends CommonModel{
    protected $pk   = 'keyword_id';
    protected $tableName =  'weixin_keyword';
    protected $token = 'weixin_keyword';
  
    public  function checkKeyword($keyword){
        $words = $this->fetchAll();
        foreach($words as $val){
            if($val['keyword'] == $keyword)
                return  $val;
        }
        return false;
    }
}