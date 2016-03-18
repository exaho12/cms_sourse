<?php

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