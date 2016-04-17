<?php
class CouponAction extends CommonAction {

	
	public function index() {
        $aready = (int) $this->_param('aready');
		$this->assign('aready', $aready);
		$this->display();
	}

	public function couponloading() {
		$Coupondownloads = D('Coupondownload');
		import('ORG.Util.Page');
		$map = array('user_id' => $this->uid);
                
                $aready = (int) $this->_param('aready');

		if ($aready == 2) {
			$map['is_used'] = array('egt',1);
		}elseif ($aready == 1) {
			$map['is_used'] = 0;
                }else{
                    $aready == null;
                }
                
		$count = $Coupondownloads->where($map)->count();
		$Page = new Page($count, 25);
		$show = $Page->show();
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		
		
		
		$list = $Coupondownloads->where($map)->order('is_used asc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$coupon_ids = array();
		foreach ($list as $k => $val) {
			$coupon_ids[$val['coupon_id']] = $val['coupon_id'];
		}
		$shops = D('Shop')->itemsByIds($shop_ids);
		$coupon = D('Coupon')->itemsByIds($coupon_ids);
		$this->assign('coupon', $coupon);
		$this->assign('shops', $shops);
		$this->assign('list', $list);
		$this->assign('page', $show);
		
		
		
		
		$this->display();
	}

	public function coupondel($download_id) {
		$download_id = (int) $download_id;
		if (empty($download_id)) {
			$this->error('该优惠券不存在');
		}
		if (!$detail = D('Coupondownload')->find($download_id)) {
			$this->error('该优惠券不存在');
		}
		if ($detail['user_id'] != $this->uid) {
			$this->error('请不要操作别人的优惠券');
		}
		D('Coupondownload')->delete($download_id);
		$this->success('删除成功！', U('coupon/index'));
	}
	
	
    public function weixin() {
        $download_id = $this->_get('download_id');
        if (!$detail = D('Coupondownload')->find($download_id)) {
            $this->error('没有该优惠券');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->error("优惠券不存在！");
        }
        if ( $detail['is_used'] != 0) {
            $this->error('该优惠券属于不可消费的状态');
        }
        $url = U('weixin/coupon', array('download_id' => $download_id, 't' => NOW_TIME, 'sign' => md5($download_id . C('AUTH_KEY') . NOW_TIME)));
        $token = 'couponcode_' . $download_id;
        $file = baoQrCode($token, $url);
        $this->assign('file', $file);
        $this->assign('detail', $detail);
        $this->display();
    }
	
	public function sms($download_id) {
        $download_id = (int) $download_id;
		$obj = D('Coupondownload');
        if ($detail = D('Coupondownload')->find($download_id)) {
			
            if ($detail['user_id'] != $this->uid) {
                $this->error('非法操作');
            }
			if ($detail['is_sms'] != 0 ) {
            $this->error('您已请求过短信啦~');
            }
			
			$users = D('Users')->find($this->uid);
			$mobile = $users['mobile'];//用户手机
			$user = $users['nickname'];//用户姓名
			
			$list = $obj->find($download_id);
			$code = $list['code'];//团购劵密码
			
			$shop_ids = $list['shop_id'];
			$shop = D('Shop')->find($shop_ids);
			$shop_name = $shop['shop_name'];//取出商家名字
		
			
			if(!empty($mobile)){
				 D('Sms')->sendSms('is_coupondownload',$mobile,array(
					 'user'=>$user,//用户姓名
					 'code'=>$code,
					 'shop_name'=>$shop_name
				 ));
				}else{
					$this->error('您还没绑定手机！', U('coupon/index'));
			}
				
			$obj->save(array('download_id' => $download_id, 'is_sms' => 1));
			$this->success('短信已成功发送到您手机！', U('coupon/index'));
			
			}else{
				$this->error('操作失败');
			}
        
    }
	
	
	 public function give() {
		   $download_id = $this->_get('download_id');
		 
		   if (!$detail = D('Coupondownload')->find($download_id)) {
            $this->error('没有该优惠券');
			}
			if ($detail['user_id'] != $this->uid) {
				$this->error("优惠券不存在！");
			}
			if ( $detail['is_used'] != 0) {
				$this->error('该优惠券属于不可消费的状态');
			}
		 
		 if($this->isPost()){
			$nickname = $this->_post('nickname');
			$user = D('Users')->where(array('nickname'=>$nickname))->find();
			
			
			
			if(empty($user)){
				$this->niuMsg('用户不存在');
			}
			
			$my_user = $this->uid;
			
			
			if($my_user == $user['user_id'] ){
				$this->niuMsg('不能赠送给自己');
			}
			//
			$obj = D('Coupondownload');
			$obj->save(array('download_id' => $download_id, 'user_id' => $user['user_id']));
			
			
			$this->niuMsg('恭喜您赠送成功', U('/mcenter/coupon/index/'));
		}
		
		 $this->assign('detail', $detail);
		 $this->display();
	}
	
	    public function nickcheck() {
		$nickname = $this->_get('nickname');
		$user = D('Users')->where(array('nickname'=>$nickname))->find();
		if(empty($user)){
			echo '0';
		}else{
			echo '1';
		}
	}
	
}