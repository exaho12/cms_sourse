<?php

class WeixinAction extends CommonAction {

    public function tuan() {
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('mobile/passport/login'));
        }
		$user_id = D('Users')->where(array('user_id'=>$this->uid))->getField('user_id');
        //$worker = D('Shopworker')->find($this->uid);
		$worker = D('Shopworker')->where(array('user_id'=>$user_id))->find();
		if(empty($worker)){
			$this->error('您不属于任何一个店铺的授权员工，无权进行管理！', U('index/index'));
		}
		if(empty($worker['status']) || $worker['status'] !=1 ){
			$this->error('您的员工信息还处于待通过状态，无权进行操作！', U('information/worker',array('worker_id'=>$worker['worker_id'])));
		}
		$code_id = (int) $this->_param('code_id');
		
		$obj = D('Tuancode');
		$shopmoney = D('Shopmoney');
		$data = $obj->find($code_id);
		if(empty($data)){
			$this->error('没有找到对应的团购券信息！', U('index/index'));
		}
		if($data['shop_id'] != $worker['shop_id'] || $worker['tuan']!=1){
			$this->error('您不属于该公司的授权员工，无法进行管理！', U('index/index'));
		}
		$shop = D('Shop')->find(array('where' => array('shop_id' => $data['shop_id'])));
		if ((int) $data['is_used'] == 0 && (int) $data['status'] == 0) {
			if ($obj->save(array('code_id' => $data['code_id'], 'is_used' => 1,'used_time' => NOW_TIME,'worker_id' => $this->uid, 'used_ip' => $ip))) { //这次更新保证了更新的结果集              
				//增加MONEY 的过程 稍后补充
				if (!empty($data['price'])) {
					$data['intro'] = '抢购消费'.$data['order_id'];
					$shopmoney->add(array(
						'shop_id' => $data['shop_id'],
						'city_id' => $shop['city_id'],
						'area_id' => $shop['area_id'],
						'branch_id' => $data['branch_id'],
						'money' => $data['settlement_price'],
						'create_ip' => $ip,
						'create_time' => time(),
						'order_id' => $data['order_id'],
						'intro'    => $data['intro'], 
					));
					
					D('Users')->addMoney($shop['user_id'], $data['settlement_price'], '商户抢购资金结算：'.$data['order_id']);//商户资金增加
					$this->success('团购券'.$code_id.'消费成功！',U('index/index'));
				} else {
					$this->success('到店付团购券'.$code_id.'消费成功！',U('index/index'));
				}
				//给用户返还积分
				$order = D('Tuanorder')->find($data['order_id']);
				$tuan = D('Tuan')->find($data['tuan_id']);
				$integral = (int) ($order['total_price'] / 100 / $order['num']);
				D('Users')->addIntegral($data['user_id'], $integral, '抢购' . $tuan['title'] . ';订单' . $order['order_id']);

			}
		} else {
			$this->error('该团购券无效或已经使用！',U('index/index'));
		}
    }
	
	
    public function coupon() {
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('mobile/passport/login'));
        }

		$user_id = D('Users')->where(array('user_id'=>$this->uid))->getField('user_id');
		$worker = D('Shopworker')->where(array('user_id'=>$user_id))->find();
		
		
		
		if(empty($worker)){
			$this->error('您不属于任何一个店铺的授权员工，无权进行管理！', U('index/index'));
		}
		if(empty($worker['status']) || $worker['status'] !=1 ){
			$this->error('您的员工信息还处于待通过状态，无权进行操作2！', U('information/worker',array('worker_id'=>$worker['worker_id'])));
		}
		
		$download_id = (int) $this->_param('download_id');
		$obj = D('Coupondownload');
		$data = $obj->find($download_id);

		if(empty($data)){
			$this->error('没有找到对应的优惠券信息！', U('index/index'));
		}

		if($data['shop_id'] != $worker['shop_id'] || $worker['coupon']!=1){
			$this->error('您不属于该公司的授权员工，无法进行管理！', U('index/index'));
		}

		if ((int) $data['is_used'] == 0 ) {
			$ip = get_client_ip();
			$result = $obj->save(array('download_id' => $data['download_id'], 'is_used' => 1, 'used_time' => time(), 'used_ip' => $ip));
			if($result){
				$this->success('优惠劵'.$code_id.'验证成功！',U('mcenter/coupon/index'));
			}else{
				$this->error('该优惠券验证失败！3',U('mcenter/coupon/index'));
			}
			p($result);die;
		}else{
			$this->error('该优惠券已经使用过了，验证失败！',U('mcenter/coupon/index'));
		}


	}
	

	
	

}
