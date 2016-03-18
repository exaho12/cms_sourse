<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class SettingModel extends CommonModel{
    protected $pk   = 'k';
    protected $tableName =  'setting';   
    protected $token = 'setting';
    protected $settings = null;

    public function fetchAll(){
        $cache = cache(array('type'=>'File','expire'=>  $this->cacheTime));
        if(!$data= $cache->get($this->token)){
            $result = $this->select();

            foreach($result  as $row){
                $row['v'] = unserialize($row['v']);
              
                $data[$row[$this->pk]] = $row['v'];
            }
            $cache->set($this->token,$data);
        }   
        $this->settings = $data;
        return $this->settings;
     }
     
     public function save($arr){
         if($this->find(array("where"=>array('k'=>$arr['k'])))){
             return  parent::save($arr);
         }else{
             return $this->add($arr);
         }         
     }
     

}