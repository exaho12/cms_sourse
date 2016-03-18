<?php


class CouponAction extends CommonAction {

    public function index() {
        if ($this->isPost()) {
            $code = $this->_post('code', false);
            foreach ($code as $v) {
                if (empty($v)) {
					$this->error('请输入电子优惠券');
                }
            }
            $obj = D('Coupondownload');
            $ip = get_client_ip();
            $return = array();
            foreach ($code as $key => $var) {
                $var = trim(htmlspecialchars($var));
                if (!empty($var)) {
                    $data = $obj->find(array('where' => array('code' => $var)));
                    if (!empty($data) && (int) $data['shop_id'] == $this->shop_id && (int) $data['is_used'] == 0) {
                        if (false !== $obj->save(array('download_id' => $data['download_id'], 'is_used' => 1, 'used_ip' => $ip, 'used_time' => NOW_TIME))) {
                            $return[$var] = $var;
                        }
                    } else {
                        continue;
                    }
                }
            }
            if (!empty($return)) {
                $msg = join(',',$return);
                $this->error("恭喜您，您成功消费的优惠券如下：".$msg); //放入foreach内循环一次后便会退出
            }else{
				$this->error('无效的电子优惠券');
 
            }
        } else {
            $this->display();
        }
    }

//         if($this->isPost()){
//            $code=$this->_post('code',false); 
//	
//            if(empty($code)){
//				$this->error('请输入电子优惠券');
//                exit('<script>parent.used("请输入电子优惠券！");</script>');
//            }
//            $obj =  D('Coupondownload');
//			
//            $return = array();
//            $ip = get_client_ip();
//            foreach($code  as $var){
//                if(!empty($var)){
//                    $data =$obj->find(array('where'=>array('code'=>$var)));
//                    if(!empty($data) && $data['shop_id'] == $this->shop_id && $data['is_used'] == 0 ){
//                      $obj->save(array('download_id'=>$data['download_id'],'is_used'=>1,'used_time'=>NOW_TIME,'used_ip'=>$ip));
//                      $return[$var] = $var;
//                    }
//                }
//            }   
//            if(empty($return)){
//				$this->error('请输入电子优惠券');
//                exit('<script>parent.used("没有可消费的电子优惠券！");</script>');
//				//$this->error("没有可消费的电子优惠券！");
//            }
//            if(NOW_TIME - $this->shop['ranking'] < 86400){ //更新排名
//                D('Shop')->save(array('shop_id'=>  $this->shop_id,'ranking'=>NOW_TIME));
//            }
//			$this->error('恭喜您，您成功消费的优惠券如下');
//            //echo '<script>parent.used("恭喜您，您成功消费的优惠券如下："+"'.join(',',$return).'");</script>';
//        }else{
//            $this->display();
//        }       
}
