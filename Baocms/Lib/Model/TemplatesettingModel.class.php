<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class  TemplatesettingModel extends CommonModel{
    protected $pk   = 'theme';
    protected $tableName =  'template_setting';
    protected $token = 'template_setting';
    
    public function detail($theme){
        $data = $this->fetchAll();
        return $data[$theme];
    }

    public function _format($data) {
        $data['setting'] = unserialize($data['setting']);
        return $data;
    }
    
   
    
}
