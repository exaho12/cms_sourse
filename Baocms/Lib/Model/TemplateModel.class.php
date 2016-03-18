<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TemplateModel extends CommonModel{
    protected $pk   = 'template_id';
    protected $tableName =  'template';
    protected $token = 'template';
    
     public function fetchAll(){
        $cache = cache(array('type'=>'File','expire'=>  $this->cacheTime));
        if(!$data = $cache->get($this->token)){
            $result = $this->order($this->orderby)->select();
            $data = array();
            foreach($result  as $row){
                $data[$row['theme']] = $row;
            }
            $cache->set($this->token,$data);
        }   
        return $data;
     }
    
     public function getDefaultTheme(){
         $data = $this->fetchAll();
         foreach ($data as $k=>$v){
             if($v['is_default']) return $v['theme'];
         }
         return C('DEFAULT_THEME');
     }
     
}