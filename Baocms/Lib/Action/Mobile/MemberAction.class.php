<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MemberAction extends CommonAction {

	public function _initialize() {
		parent::_initialize();
		if (empty($this->uid)) {
			header("Location: " . U('passport/login'));
			die;
		}
	}
        
        public function cash()
        {
            $Users = D('Users');
            $data = $Users->find($this->uid);
            if (IS_POST)
            {
                $money = (int)$_POST['money'];
                if ($money == 0)
                {
                    $this->error('提现金额不能为0');
                }
                $money *= 100;
                if ($money > $data['money'] || $data['money'] == 0)
                {
                    $this->error('余额不足，无法提现');
                }
                $arr = array();
                $arr['user_id'] = $this->uid;
                $arr['money']   = $money;
                $arr['addtime'] = NOW_TIME;
                $arr['account'] = $data['account'];
                $arr['bank_name'] = $data['bank_name'];
                $arr['bank_num'] = $data['bank_num'];
                $arr['bank_realname'] = $data['bank_realname'];
                $arr['bank_branch'] = $data['bank_branch'];
                D('Userscash')->add($arr);
                //扣除余额
                $Users->addMoney($data['user_id'], -$money, '申请提现，扣款');
                $this->success('申请成功', U('member/cashlog'));
            }
            else
            {
                $this->assign('money', $data['money'] / 100);
                $this->display();
            }
        }
        
        public function cashlog()
        {
            $this->display();
        }
        
        public function cashlogloaddata() 
        {
		$Userscash = D('Userscash');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('user_id'=>$this->uid);
		$count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

	public function goods() {
		$this->display();
	}

	public function goodsloaddata() {
		$Order = D('Order');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('closed' => 0, 'user_id' => $this->uid);
		// var_dump($map);die();
		$count = $Order->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Order->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$user_ids = $order_ids = $addr_ids = array();
		foreach ($list as $key => $val) {
			$user_ids[$val['user_id']] = $val['user_id'];
			$order_ids[$val['order_id']] = $val['order_id'];
			$addr_ids[$val['addr_id']] = $val['addr_id'];
		}
		if (!empty($order_ids)) {
			$goods = D('Ordergoods')->where(array('order_id' => array('IN', $order_ids)))->select();
			$goods_ids = $shop_ids = array();
			foreach ($goods as $val) {
				$goods_ids[$val['goods_id']] = $val['goods_id'];
				$shop_ids[$val['shop_id']] = $val['shop_id'];
			}
			$this->assign('goods', $goods);
			$this->assign('products', D('Goods')->itemsByIds($goods_ids));
			$this->assign('shops', D('Shop')->itemsByIds($shop_ids));
		}
		$this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
		$this->assign('areas', D('Area')->fetchAll());
		$this->assign('business', D('Business')->fetchAll());
		$this->assign('users', D('Users')->itemsByIds($user_ids));
		$this->assign('types', D('Order')->getType());
		$this->assign('goodtypes', D('Ordergoods')->getType());
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

	public function orderdel($order_id = 0) {
		if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
			$obj = D('Order');
			if (!$detial = $obj->find($order_id)) {
				$this->error('该订单不存在');
			}
			if ($detial['user_id'] != $this->uid) {
				$this->error('请不要操作他人的订单');
			}
			if ($detial['status'] != 0) {
				$this->error('该订单暂时不能删除');
			}

			$obj->save(array('order_id' => $order_id, 'closed' => 1));
			$this->success('删除成功！', U('mcenter/goods/index'));
		} else {
			$this->error('请选择要删除的订单');
		}
	}

	public function pay() {
		$logs_id = (int) $this->_get('logs_id');
		if (empty($logs_id)) {
			$this->error('没有有效的支付');
		}
		//if (!D('Lock')->lock($this->uid)) { //上锁
			//$this->error('服务器繁忙，1分钟后再试');
		//}
		if (!$detail = D('Paymentlogs')->find($logs_id)) {
			//D('Lock')->unlock();
			$this->error('没有有效的支付');
		}
		if ($detail['code'] != 'money') {
			//D('Lock')->unlock();
			$this->error('没有有效的支付');
		}
		$member = D('Users')->find($this->uid);
		if ($detail['is_paid']) {
			//D('Lock')->unlock();
			$this->error('没有有效的支付');
		}
		if ($member['money'] < $detail['need_pay']) {
			//D('Lock')->unlock();
			$this->error('很抱歉您的账户余额不足', U('/mcenter/money'));
		}

		$member['money'] -= $detail['need_pay'];

		if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
			D('Usermoneylogs')->add(array(
				'user_id' => $this->uid,
				'money' => -$detail['need_pay'],
				'create_time' => NOW_TIME,
				'create_ip' => get_client_ip(),
				'intro' => '余额支付' . $logs_id,
			));
			D('Payment')->logsPaid($logs_id);
                        //D('Lock')->unlock();
			$this->success('支付成功！', U('/mcenter/'));
		}
		
	}

	public function apply() {
		if (empty($this->uid)) {
			header("Location:" . U('passport/login'));
			die;
		}
		if (D('Shop')->find(array('where' => array('user_id' => $this->uid)))) {

			$this->error('您已经拥有一家店铺了！', U('shangjia/index/index'));
		}
		if ($this->isPost()) {
			$data = $this->createCheck();
			$obj = D('Shop');
			$details = $this->_post('details', 'htmlspecialchars');
			if ($words = D('Sensitive')->checkWords($details)) {
				$this->error('商家介绍含有敏感词：' . $words);
			}
             
			$ex = array(
				'details' => $details,
				'near' => $data['near'],
				'price' => $data['price'],
				'business_time' => $data['business_time'],
			);
			unset($data['near'], $data['price'], $data['business_time']);
			if ($shop_id = $obj->add($data)) {
				$wei_pic = D('Weixin')->getCode($shop_id, 1);
				$ex['wei_pic'] = $wei_pic;
				D('Shopdetails')->upDetails($shop_id, $ex);
				$this->success('恭喜您申请成功！', U('shop/index'));
			}
			$this->error('申请失败！');
		} else {
			$lat = addslashes(cookie('lat'));
			$lng = addslashes(cookie('lng'));
			if (empty($lat) || empty($lng)) {
				$lat = $this->city['lat'];
				$lng = $this->city['lng'];
			}
			if ($business_id = (int) $this->_param('business_id')) {
            $map['business_id'] = $business_id;
            $this->assign('business_id', $business_id);
        }     
		
		     $this->assign('business', D('Business')->fetchAll());
			$this->assign('lat', $lat);
			$this->assign('lng', $lng);
			$areas = D('Area')->fetchAll();
		
			$this->assign('cates', D('Shopcate')->fetchAll());

			$this->assign('areas', $areas);
			
			$this->display();
		}
	}

	private function createCheck() {
		$data = $this->checkFields($this->_post('data', false), array('cate_id', 'tel', 'logo', 'photo', 'shop_name', 'contact', 'details', 'business_time', 'area_id', 'addr', 'lng', 'lat'));
		$data['shop_name'] = htmlspecialchars($data['shop_name']);
	     if (empty($data['shop_name'])) {
			$this->error('店铺名称不能为空');
		}
		$data['lng'] = htmlspecialchars($data['lng']);
		$data['lat'] = htmlspecialchars($data['lat']);
		if (empty($data['lng']) || empty($data['lat'])) {
			$this->error('店铺坐标需要设置');
		}
		$data['cate_id'] = (int) $data['cate_id'];
	    $data['area_id'] = (int) $data['area_id'];
		if (empty($data['area_id'])) {
			$this->error('地区不能为空');
			
		}
		$data['contact'] = htmlspecialchars($data['contact']);
		if (empty($data['contact'])) {
			$this->error('联系人不能为空');
		}$data['business_time'] = htmlspecialchars($data['business_time']);
		if (empty($data['business_time'])) {
			$this->error('营业时间不能为空');
		}
		if (!isImage($data['logo'])) {
			$this->error('请上传正确的LOGO');
		}
		if (!isImage($data['photo'])) {
			$this->error('请上传正确的店铺图片');
		}
		$data['addr'] = htmlspecialchars($data['addr']);
		if (empty($data['addr'])) {
			$this->error('地址不能为空');
		}
		$data['tel'] = htmlspecialchars($data['tel']);
		if (empty($data['tel'])) {
			$this->error('联系方式不能为空');
		}
		
		if(!isPhone($data['tel']) && !isMobile($data['tel'])) {
			$this->error('联系方式格式不正确');
		}
		if(isMobile($data['tel'])){
			$data['phone']=$data['tel'];
		}
		$detail = D('Shop')->where(array('user_id' => $this->uid))->find();
		if (!empty($detail)) {
			$this->error('您已经是商家了');
		}
		$data['user_id'] = $this->uid;
		$data['create_time'] = NOW_TIME;
		$data['create_ip'] = get_client_ip();
		return $data;
	}    

	

	public function index() {
		$this->assign('order', D('Tuanorder')->where(array('user_id' => $this->uid, 'status' => 0))->count());
		$this->assign('code', D('Tuancode')->where(array('user_id' => $this->uid, 'is_used' => 0, 'status' => 0))->count());
		$this->assign('user_id', $this->uid);
		$this->display();
	}

	public function password() {
		if ($this->isPost()) {
			$oldpwd = $this->_post('oldpwd', 'htmlspecialchars');
			if (empty($oldpwd)) {
				$this->error('旧密码不能为空！');
			}
			$newpwd = $this->_post('newpwd', 'htmlspecialchars');
			if (empty($newpwd)) {
				$this->error('请输入新密码');
			}
			$pwd2 = $this->_post('pwd2', 'htmlspecialchars');
			if (empty($pwd2) || $newpwd != $pwd2) {
				$this->error('两次密码输入不一致！');
			}
			if ($this->member['password'] != md5($oldpwd)) {
				$this->error('原密码不正确');
			}
			if (D('Passport')->uppwd($this->member['account'], $oldpwd, $newpwd)) {
				session('uid', null);
				$this->success('更改密码成功！', U('passport/login'));
			}
			$this->error('修改密码失败！');
		} else {
			$this->display();
		}
	}

	public function weixin() {
		$code_id = $this->_get('code_id');
		if (!$detail = D('Tuancode')->find($code_id)) {
			$this->error('没有该抢购券');
		}
		if ($detail['user_id'] != $this->uid) {
			$this->error("抢购券不存在！");
		}
		if ($detail['status'] != 0 || $detail['is_used'] != 0) {
			$this->error('该抢购券属于不可消费的状态');
		}

		$url = U('weixin/index', array('code_id' => $code_id, 't' => NOW_TIME, 'sign' => md5($code_id . C('AUTH_KEY') . NOW_TIME)));

		$token = 'tuancode_' . $code_id;

		$file = baoQrCode($token, $url);
		$this->assign('file', $file);
		$this->assign('detail', $detail);
		$this->display();
	}

	public function refund($code_id) {
		$code_id = (int) $code_id;
		if ($detail = D('Tuancode')->find($code_id)) {
			if ($detail['user_id'] != $this->uid) {
				$this->error('非法操作');
			}
			if ($detail['status'] != 0 || $detail['is_used'] != 0) {
				$this->error('该抢购券不能申请退款');
			}
			if (D('Tuancode')->save(array('code_id' => $code_id, 'status' => 1))) {
				$this->success('申请成功！等待网站客服处理！', U('member/tuancode'));
			}
		}
		$this->error('操作失败');
	}

	public function looks() {
		$this->display();
	}

	public function looksloading() {
		$Userslook = D('Userslook');
		import('ORG.Util.Page');
		$map = array('user_id' => $this->uid);
		$count = $Userslook->where($map)->count();
		$Page = new Page($count, 25);
		$show = $Page->show();
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Userslook->where($map)->order('last_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$shop_ids = array();
		foreach ($list as $k => $val) {
			$shop_ids[$val['shop_id']] = $val['shop_id'];
		}
		$lat = addslashes(cookie('lat'));
		$lng = addslashes(cookie('lng'));
		if (empty($lat) || empty($lng)) {
			$lat = $this->city['lat'];
			$lng = $this->city['lng'];
		}
		$shopdetails = D('Shopdetails')->itemsByIds($shop_ids);
		$shops = D('Shop')->itemsByIds($shop_ids);
		foreach ($shops as $k => $val) {
			$shops[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
		}
		$this->assign('shops', $shops);
		$this->assign('shopdetails', $shopdetails);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function mycoupon() {

		$this->display();
	}

	public function couponloading() {
		$Coupondownloads = D('Coupondownload');
		import('ORG.Util.Page');
		$map = array('user_id' => $this->uid);
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
		$this->success('删除成功！', U('member/mycoupon'));
	}

	public function favorites() {
		$this->display();
	}

	public function favoritesloading() {
		$Shopfavorites = D('Shopfavorites');
		import('ORG.Util.Page');
		$map = array('user_id' => $this->uid);
		$count = $Shopfavorites->where($map)->count();
		$Page = new Page($count, 25);
		$show = $Page->show();
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}

		$list = $Shopfavorites->where($map)->order('favorites_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$shop_ids = array();
		foreach ($list as $k => $val) {
			$shop_ids[$val['shop_id']] = $val['shop_id'];
		}
		$lat = addslashes(cookie('lat'));
		$lng = addslashes(cookie('lng'));
		if (empty($lat) || empty($lng)) {
			$lat = $this->city['lat'];
			$lng = $this->city['lng'];
		}
		$shops = D('Shop')->itemsByIds($shop_ids);
		$shopdetails = D('Shopdetails')->itemsByIds($shop_ids);
		foreach ($shops as $k => $val) {
			$shops[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
		}
		$this->assign('shopdetails', $shopdetails);
		$this->assign('shops', $shops);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function myexchange() {
		$this->display();
	}

	//积分兑换记录
	public function exchangeloading() {
		$Integralexchange = D('Integralexchange');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('user_id' => $this->uid);
		$count = $Integralexchange->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Integralexchange->where($map)->order(array('exchange_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$shop_ids = $good_ids = $addr_ids = array();
		foreach ($list as $val) {
			$shop_ids[$val['shop_id']] = $val['shop_id'];
			$good_ids[$val['goods_id']] = $val['goods_id'];
			$addr_ids[$val['addr_id']] = $val['addr_id'];
		}
		$this->assign('areas', D('Area')->fetchAll());
		$this->assign('business', D('Business')->fetchAll());
		$this->assign('shops', D('Shop')->itemsByIds($shop_ids));
		$this->assign('goods', D('Integralgoods')->itemsByIds($good_ids));
		//var_dump(D('Integralgoods')->itemsByIds($good_ids));
		$this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

	public function order() {
		$aready = (int) $this->_param('aready');
		$this->assign('aready', $aready);
		$this->display(); // 输出模板
	}

	public function orderloading() {
		$Tuanorder = D('Tuanorder');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('user_id' => $this->uid); //这里只显示 实物
		$aready = (int) $this->_param('aready');
		if ($aready == 1) {
			$map['status'] = 1;
		}
		if ($aready == 2) {
			$map['status'] = 0;
		}
		$count = $Tuanorder->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Tuanorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$tuan_ids = array();
		foreach ($list as $k => $val) {
			$tuan_ids[$val['tuan_id']] = $val['tuan_id'];
		}
		$this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

	public function codeloading() {
		$Tuancode = D('Tuancode');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('user_id' => $this->uid); //这里只显示 实物
		if ($order_id = (int) $this->_get('order_id')) {
			$map['order_id'] = $order_id;
		}
		$count = $Tuancode->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Tuancode->where($map)->order(array('code_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$tuan_ids = array();
		foreach ($list as $val) {
			$tuan_ids[$val['tuan_id']] = $val['tuan_id'];
		}
		$this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

	public function Tuancode() {

		$this->display(); // 输出模板
	}

	public function mobile() {
		if (!empty($this->member['mobile'])) {
			$this->success("恭喜您！您的手机已经绑定，可以正常购物！");
		}
		if ($this->isPost()) {
			$mobile = $this->_post('mobile');
			$yzm = $this->_post('yzm');
			if (empty($mobile) || empty($yzm))
				$this->error('请填写正确的手机及手机收到的验证码！');
			$s_mobile = session('mobile');
			$s_code = session('code');
			if ($mobile != $s_mobile)
				$this->error('手机号码和收取验证码的手机号不一致！');
			if ($yzm != $s_code)
				$this->error('验证码不正确');
			$data = array(
				'user_id' => $this->uid,
				'mobile' => $mobile
			);
			if (D('Users')->save($data)) {
				D('Users')->integral($this->uid, 'mobile');
				$this->success('恭喜您通过手机认证', U('member/mobile'));
			}
			$this->error('更新数据失败！');
		} else {

			$this->display();
		}
	}
	public function mobile_name() {
		$mobile = $this->_post('mobile');
		$where['mobile'] = $mobile;
			$user = D('Users')->where($where)->find();
			if ($user) 
				echo 1;
	}
	public function sendsms() {
		$mobile = $this->_post('mobile');
		if (isMobile($mobile)) {
			session('mobile', $mobile);
			$randstring = session('code');
			if (empty($randstring)) {
				$randstring = rand_string(6, 1);
				session('code', $randstring);
			}
			//大鱼通知
			if($this->_CONFIG['sms']['dxapi'] == 'dy'){
                D('Sms')->DySms($this->_CONFIG['site']['sitename'], 'sms_yzm', $mobile, array(
                    'sitename'=>$this->_CONFIG['site']['sitename'], 
                    'code' => $randstring
                ));
            }else{
                D('Sms')->sendSms('sms_code', $mobile, array('code' => $randstring));  
            }
		}
	}

	public function message() {


		$this->display();
	}

	public function msgshow($msg_id) {
		$msg_id = (int) $msg_id;
		if (!$detail = D('Msg')->find($msg_id)) {
			$this->error('消息不存在');
		}
		if ($detail['user_id'] != $this->uid && $detail['user_id'] != 0) {
			$this->error('您没有权限查看该消息');
		}
		if (!D('Msgread')->find(array('user_id' => $this->uid, 'msg_id' => $msg_id))) {
			D('Msgread')->add(array(
				'user_id' => $this->uid,
				'msg_id' => $msg_id,
				'create_time' => NOW_TIME,
				'create_ip' => get_client_ip()
			));
		}
		if ($detail['link_url']) {
			header("Location:" . $detail['link_url']);
			die;
		}
		$this->assign('detail', $detail);
		$this->display();
	}

	public function usercard() {
		$this->display();
	}

	public function cardloading() {
		$Usercard = D('Usercard');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('user_id' => $this->uid); //这里只显示 实物
		$count = $Usercard->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Usercard->where($map)->order(array('card_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$cards_ids = array();
		$shop_ids = $user_ids = array();
		foreach ($list as $k => $val) {
			$list[$k] = $Usercard->_format($val);
			$cards_ids[$val['card_id']] = $val['card_id'];

			if (!empty($val['shop_id'])) {
				$shop_ids[$val['shop_id']] = $val['shop_id'];
				$user_ids[$val['user_id']] = $val['user_id'];
			}
		}
		if ($shop_id = (int) $this->_param('shop_id')) {
			$shop = D('Shop')->find($shop_id);
			$this->assign('shop_name', $shop['shop_name']);
			$this->assign('shop_id', $shop_id);
		}

		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->assign('users', D('Users')->itemsByIds($user_ids));
		$this->assign('shops', D('Shop')->itemsByIds($shop_ids));
		$this->assign('shopdetails', D('Shopdetails')->itemsByIds($shop_ids));
		$this->display(); // 输出模板
	}

	public function eleorder() {
		$this->display();
	}

	public function eleloading() {
		$Eleorder = D('Eleorder');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('user_id' => $this->uid, 'closed' => 0); //这里只显示 实物
		if (($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
			$bg_time = strtotime($bg_date);
			$end_time = strtotime($end_date);
			$map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
			$this->assign('bg_date', $bg_date);
			$this->assign('end_date', $end_date);
		} else {
			if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
				$bg_time = strtotime($bg_date);
				$this->assign('bg_date', $bg_date);
				$map['create_time'] = array('EGT', $bg_time);
			}
			if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
				$end_time = strtotime($end_date);
				$this->assign('end_date', $end_date);
				$map['create_time'] = array('ELT', $end_time);
			}
		}
		if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
			$map['order_id'] = array('LIKE', '%' . $keyword . '%');
			$this->assign('keyword', $keyword);
		}
		if (isset($_GET['st']) || isset($_POST['st'])) {
			$st = (int) $this->_param('st');
			if ($st != 999) {
				$map['status'] = $st;
			}
			$this->assign('st', $st);
		} else {
			$this->assign('st', 999);
		}
		$count = $Eleorder->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $Eleorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$user_ids = $order_ids = $addr_ids = array();
		foreach ($list as $k => $val) {
			$order_ids[$val['order_id']] = $val['order_id'];
			$addr_ids[$val['addr_id']] = $val['addr_id'];
			$user_ids[$val['user_id']] = $val['user_id'];
		}
		if (!empty($order_ids)) {
			$products = D('Eleorderproduct')->where(array('order_id' => array('IN', $order_ids)))->select();
			$product_ids = $shop_ids = array();
			foreach ($products as $val) {
				$product_ids[$val['product_id']] = $val['product_id'];
				$shop_ids[$val['shop_id']] = $val['shop_id'];
			}
			$this->assign('products', $products);
			$this->assign('eleproducts', D('Eleproduct')->itemsByIds($product_ids));
			$this->assign('shops', D('Shop')->itemsByIds($shop_ids));
		}
		$this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
		$this->assign('areas', D('Area')->fetchAll());
		$this->assign('business', D('Business')->fetchAll());
		$this->assign('users', D('Users')->itemsByIds($user_ids));
		$this->assign('cfg', D('Eleorder')->getCfg());
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出    
		$this->display();
	}

	private function addressCheck() {
		$data = $this->checkFields($this->_post('data', false), array('addr_id', 'area_id', 'business_id', 'name', 'mobile', 'addr'));
		$data['name'] = htmlspecialchars($data['name']);
		if (empty($data['name'])) {
			$this->error('收货人不能为空');
		}
		$data['user_id'] = (int) $this->uid;
		$data['area_id'] = (int) $data['area_id'];
		$data['business_id'] = (int) $data['business_id'];
		if (empty($data['area_id'])) {
			$this->error('地区不能为空');
		}
		if (empty($data['business_id'])) {
			$this->error('商圈不能为空');
		}
		$data['mobile'] = htmlspecialchars($data['mobile']);
		if (empty($data['mobile'])) {
			$this->error('手机号码不能为空');
		}
		if (!isMobile($data['mobile'])) {
			$this->error('手机号码格式不正确');
		}
		$data['addr'] = htmlspecialchars($data['addr']);
		if (empty($data['addr'])) {
			$this->error('具体地址不能为空');
		}
		return $data;
	}

	public function addressadd() {

		if ($this->isPost()) {
			$data = $this->addressCheck();
			$obj = D('Useraddr');
			$data['is_default'] = 0;
			if ($obj->add($data)) {
				$backurl = $this->_post('backurl', 'htmlspecialchars');
				$this->success('新增收货地址成功', $backurl);
			}
			$this->error('操作失败！');
		} else {
			if (!empty($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
				$backurl = $_SERVER['HTTP_REFERER'];
			} else {
				$backurl = U('member/index');
			}
			$this->assign('backurl', $backurl);

			$this->assign('areas', D('Area')->fetchAll());
			$this->assign('business', D('Business')->fetchAll());
			$this->display();
		}
	}

	public function child($area_id = 0) {
		$datas = D('Business')->fetchAll();
		$str = '<option value="0">请选择</option>';
		foreach ($datas as $val) {
			if ($val['area_id'] == $area_id) {
				$str.='<option value="' . $val['business_id'] . '">' . $val['business_name'] . '</option>';
			}
		}
		echo $str;
		die;
	}

	public function money() {

		$this->assign('payment', D('Payment')->getPayments());
		$this->display();
	}

	public function moneypay() { //后期优化
		$money = (int) ($this->_post('money') * 100);
		$code = $this->_post('code', 'htmlspecialchars');
		if ($money <= 0) {
			$this->error('请填写正确的充值金额！');
			die;
		}
		$payment = D('Payment')->checkPayment($code);
		if (empty($payment)) {
			$this->error('该支付方式不存在');
			die;
		}
		$logs = array(
			'user_id' => $this->uid,
			'type' => 'money',
			'code' => $code,
			'order_id' => 0,
			'need_pay' => $money,
			'create_time' => NOW_TIME,
			'create_ip' => get_client_ip(),
		);
		$logs['log_id'] = D('Paymentlogs')->add($logs);

		$this->assign('button', D('Payment')->getCode($logs));
		$this->assign('money', $money);
		$this->display();
	}

	public function hdmobile() {
		$this->display(); // 输出模板;
	}

	public function hdloaddata() {
		$huodong = D('Huodong');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('closed' => 0, 'user_id' => $this->uid);
		$count = $huodong->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出

		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $huodong->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

		$huodongsign = D('Huodongsign');
		$maps = array('user_id' => $this->uid);
		$counts = $huodongsign->where($maps)->count(); // 查询满足要求的总记录数 
		$Pages = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$shows = $Pages->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Pages->totalPages < $p) {
			die('0');
		}
		$lists = $huodongsign->where($maps)->limit($Pages->firstRow . ',' . $Pages->listRows)->select();
		$huodong_ids = array();
		foreach ($lists as $k => $val) {
			if ($val['huodong_id']) {
				$huodong_ids[$val['huodong_id']] = $val['huodong_id'];
			}
		}
		$this->assign('huodong', D('Huodong')->itemsByIds($huodong_ids));
		$getHuoCate = D('Huodong')->getHuoCate();

		$this->assign('getHuoCate', $getHuoCate);
		$getPeopleCate = D('Huodong')->getPeopleCate();
		$this->assign('getPeopleCate', $getPeopleCate);

		$this->assign('lists', $lists); // 赋值数据集
		$this->assign('pages', $shows); // 赋值分页输出
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

	public function hdfabu() {
		if (empty($this->uid)) {
			$this->error('登录状态失效!', U('passport/login'));
		}

		if ($this->isPost()) {
			$data = $this->fabuCheck();
			$obj = D('Huodong');
			if ($obj->add($data)) {
				$this->success('添加成功', U('member/index'));
			}
			$this->error('操作失败！');
		} else {
			$getHuoCate = D('Huodong')->getHuoCate();
			$this->assign('getHuoCate', $getHuoCate);
			$getPeopleCate = D('Huodong')->getPeopleCate();
			$this->assign('getPeopleCate', $getPeopleCate);
			$this->display();
		}
	}

	public function fabuCheck() {
		$data = $this->checkFields($this->_post('data', false), array('title', 'addr', 'intro', 'sex', 'photo', 'cate_id', 'time'));
		$data['user_id'] = $this->uid;
		$data['cate_id'] = (int) $data['cate_id'];
		$data['sex'] = (int) $data['sex'];

		$data['title'] = trim(htmlspecialchars($data['title']));
		if (empty($data['title'])) {
			$this->error('活动标题不能为空！');
		}
		$data['intro'] = trim(htmlspecialchars($data['intro']));
		if (empty($data['intro'])) {
			$this->error('详情不能为空！');
		}
		$data['photo'] = htmlspecialchars($data['photo']);
		if (empty($data['photo'])) {
			$this->error('请上传缩略图');
		}
		if (!isImage($data['photo'])) {
			$this->error('缩略图格式不正确');
		}
		$data['audit'] = 1;
		$data['create_time'] = NOW_TIME;
		$data['create_ip'] = get_client_ip();
		return $data;
	}

	public function join_people() {
		$huodong_id = (int) $this->_param('huodong_id');
		$this->assign('nextpage', LinkTo('member/join_loaddata', array('t' => NOW_TIME, 'huodong_id' => $huodong_id, 'p' => '0000')));
		$this->display(); // 输出模板
	}

	public function join_loaddata() {
		$huodong_id = (int) $this->_param('huodong_id');
		if (!$detail = D('Huodong')->find($huodong_id)) {
			$this->error('活动不存在');
		}
		if ($detail['audit'] != 1 || $detail['closed'] != 0) {
			$this->error('活动不存在');
		}
		if ($detail['user_id'] != $this->uid) {
			$this->error('请不要查看别人的活动报名');
		}
		$huodongsign = D('Huodongsign');

		import('ORG.Util.Page'); // 导入分页类
		$count = $huodongsign->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $huodongsign->where(array('huodong_id' => $huodong_id))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板
	}

}
