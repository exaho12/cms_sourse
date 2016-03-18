<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CityAction extends CommonAction{
    
    public function index(){
        $citylists = array();
        foreach($this->citys as $val){
			 if($val['is_open'] == 1){
            $a = strtoupper($val['first_letter']);
            $citylists[$a][] = $val;
        }
		}	

        ksort($citylists);
        $this->assign('citylists',$citylists);
        $this->display();
    }
    
    public function change($city_id){
        if(empty($city_id)){
            $this->error('没有正确的城市');
        }
        if(isset($this->citys[$city_id])){            
            cookie('city_id',$city_id,86400*30);
            header("Location:".U('index/index'));die;
        }
        $this->error('没有正确的城市');
    }
    
    
}