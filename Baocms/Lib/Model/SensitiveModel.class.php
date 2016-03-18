<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class SensitiveModel extends CommonModel{
    protected $pk   = 'words_id';
    protected $tableName =  'sensitive_words';
    protected $token = 'sensitive_words';
    protected $cacheTime = 8640000;//100天


    //return false  表示正常，否则会返回对应的敏感词
    public function checkWords($content){
        $words = $this->fetchAll();
        foreach($words as $val){
            if(strstr($content,$val['words'])) return $val['words']; 
        }    
        return false;     
    }
    
}