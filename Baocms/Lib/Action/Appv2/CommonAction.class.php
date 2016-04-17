<?php

class CommonAction extends  Action{
      protected $uid        = 0;//用户ID
      protected $city_id    = 0;//城市ID
      protected $lat        = 0;
      protected $lng        = 0;
      protected $_CONFIG    = array();
      protected $citys      = array();
      protected $city       = array();
      protected function _initialize() {
      
          $this->_CONFIG = D('Setting')->fetchAll();
          $this->lat = htmlspecialchars(cookie('lat'));
          $this->lng = htmlspecialchars(cookie('lng'));
          $this->city_id = (int)cookie('city_id');
          $this->uid = getUid();
          //dump($this->uid);die;
          $this->citys   = D('City')->fetchAll();
          if(!empty($this->city_id)){
              $this->city = $this->citys[$this->city_id];
              if(!empty($this->city) && (empty($this->lat)||empty($this->lng))){
                  $this->lat = $this->city['lat'];
                  $this->lng = $this->city['lng'];
              }
          } else{
              
               import('ORG/Net/IpLocation');
                $IpLocation = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
                $result = $IpLocation->getlocation($_SERVER['REMOTE_ADDR']);
                foreach ($this->citys as $val) {
                    if (strstr($result['country'], $val['name'])) {
                        $this->city = $val;
                        $this->city_id = $val['city_id'];
                        break;
                    }
                }
                if (empty($this->city)) {
                    $this->city_id = $this->_CONFIG['site']['city_id'];
                    $this->city = $this->citys[$this->_CONFIG['site']['city_id']];
                }
                $this->lat = $this->city['lat'];
                $this->lng = $this->city['lng'];
          }        
      }
      
      //返回给APP的SHOWMSG 接口
      protected function showmsg($data,$ret=0){
          $datas = array();
          if($ret==0){
              $datas = array(
                  'ret' => 0,
                  'msg' => $data
              );
          }else{
              $datas = array(
                  'ret' => $ret, 
                  'msg' => $data
              );
          }
          die(json_encode($datas));
      }
      
      
}