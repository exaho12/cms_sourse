<?php 
/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

	class  McenterAction extends  CommonAction{

		//我的优惠卷
		public function coupon()
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$Coupondownloads = D('Coupondownload');
				$map = array('user_id' => $uid);
				
				$aready = (int) $this->_get('aready');

				if ($aready == 2) {
					$map['is_used'] = array('egt',1);
				}elseif ($aready == 1) {
					$map['is_used'] = 0;
				}else{
					$aready == null;
				}

				$count = $Coupondownloads->where($map)->count();
				
				$limit = 4;
				$pages = ceil($count/$limit);
				
				$page = $this->_param('page')?$this->_param('page'):1;
				
				if($page>$pages){
					die(0);
				}

				$start = ($page-1)*$limit;
				$list = $Coupondownloads->where($map)->order('is_used asc')->limit($start.','.$limit)->select();
				$coupon_ids = array();
				foreach ($list as $k => $val) {
					$coupon_ids[$val['coupon_id']] = $val['coupon_id'];
				}
				$coupon = D('Coupon')->itemsByIds($coupon_ids);
				
				$data = array('status'=>self::BAO_REQUEST_SUCCESS,'coupon'=>$coupon,'list'=> $list,'page'=>$page+1);
			}
			$this->stringify($data);

		}
		
		//删除我的优惠卷
		public function coupondel()
		{
			$download_id = (int) $this->_param('download_id');
			if(!$uid = $this->_param('uid')){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else if (empty($download_id)) {
				$data = array('status'=>self::BAO_COUPON_NO_EXSITS,'msg'=>'该优惠券不存在!');
			}else if (!$detail = D('Coupondownload')->find($download_id)) {
				$data = array('status'=>self::BAO_COUPON_NO_EXSITS,'msg'=>'该优惠券不存在!');
			}else if ($detail['user_id'] != $uid) {
				$data = array('status'=>self::BAO_COUPON_OWNSHIP,'msg'=>'请不要操作别人的优惠卷!');
				$this->stringify($data);
			}else{
				D('Coupondownload')->delete($download_id);
				$data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>'删除成功!');
			}
			$this->stringify($data);
		}


		//团购相关

		public function tuan()
		{
			//uid,aready,page
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{

				$Tuanorder = D('Tuanorder');
				$map = array('user_id' => $uid); //这里只显示 实物
				$aready = (int) $this->_param('aready');
				if ($aready == 1) {
					$map['status'] = 1;
				}elseif ($aready == 0) {
					$map['status'] = 0;
				}else{
					$map['status'] = 0;
				}
				$count = $Tuanorder->where($map)->count();
				$limit = 10;
				$pages = ceil($count/$limit);
				$page  = $this->_param('page');
				$page  = $_page = ($page<=$pages)?($page>=1?$page:1):$pages;
				$limit = ($page-1)*$limit+$limit;
				$page  = ($page>=$pages)?'null':($page+1);
				$list  = $Tuanorder->where($map)->order(array('order_id' => 'desc'))->limit($limit)->select();
				$tuan_ids = array();
				foreach ($list as $k => $val) {
					$tuan_ids[$val['tuan_id']] = $val['tuan_id'];
				}
		        $tuans = D('Tuan')->itemsByIds($tuan_ids);
				$data  = array('status'=>self::BAO_REQUEST_SUCCESS,'list'=>$list, 'tuans'=>$tuans,'page'=>$page);
			}
			$this->stringify($data);
		}

		public function  tuan_delete()
		{
			//order_id
			$order_id = $this->_param('order_id');

            if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else if(!$detail = D('Tuanorder')->find($order_id)){
				$data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"该团购不存在或者已经被删除",'url'=>U('mcenter/tuan',array('uid'=>$uid)));
            }else if ($detail['user_id'] != $uid){
				$data = array('status' => self::BAO_PERMISSION_NO_OPERATION, 'msg'=>"请不要操作他人的订单",'url'=>U('mcenter/tuan',array('uid'=>$uid)));
            }else if($detail['status'] != 0){
			   $data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"该团购不存在或者已经被删除",'url'=>U('mcenter/tuan',array('uid'=>$uid)));
            }else{
				if(D('Tuanorder')->delete($order_id)){
					$data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>"取消订单成功！",'url'=>U('mcenter/tuan',array('uid'=>$uid)));
				}else{
					$data = array('status' => self::BAO_DELETE_FALSE, 'msg'=>"取消订单失败！",'url'=>U('mcenter/tuan',array('uid'=>$uid)));
				}
			}
            $this->stringify($data);
            
        }


		public function tuan_detail()
		{
			$order_id = $this->_param('order_id');
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else if(empty($order_id) || !$detail = D('Tuanorder')->find($order_id)){
				$data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"该订单不存在");
            }else if($detail['user_id'] != $uid){
				$data = array('status' => self::BAO_PERMISSION_NO_OPERATION, 'msg'=>"请不要操作他人的订单");
			}else{
				if(!$dianping = D('Tuandianping')->where(array('order_id'=>$order_id,'user_id'=>$uid))->find()){
					$detail['dianping'] = 0;
				}else{
					$detail['dianping'] = 1;
				}
				$default_addr = D('UserAddr')->where(array('user_id'=>$uid,'is_default'=>1))->getField('addr');
				$tuans = D('Tuan')->find($detail['tuan_id']);
				$data = array('status'=>self::BAO_REQUEST_SUCCESS,'detail'=>$detail,'tuans'=>$tuans,'addr'=>$default_addr);
			}
			$this->stringify($data);
        }
		
		// 收货地址

		public function addr_index() 
		{

			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$u = D('Users');
				$ud = D('UserAddr');
				$addr = $ud -> where('user_id='.$uid) -> select();
				$data = array('status'=>self::BAO_REQUEST_SUCCESS,'addr'=>$addr);
			}

			$this->stringify($data);
		}

		public function update_addr()
		{

			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$addr_id = trim(intval($this->_param('addr')));
				if(!$addr_id){
					$data = array('status' => self::BAO_EDIT_FALSE, 'msg'=>"错误！修改失败");
				}else{
					$ud = D('UserAddr');
					$up1 = $ud -> where('user_id ='.$this->uid)->setField('is_default',0);
					$up2 = $ud -> where('addr_id ='.$addr_id)->setField('is_default',1);
					$data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>"修改成功");
				}
			}
			$this->stringify($data);
		}

		public function add_addr()
		{

            if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
                $name = trim(htmlspecialchars($this->_param('name')));
				$area_id = trim($this->_param('area_id'));
				$city_id = trim($this->_param('city_id'));
				$business_id = trim($this->_param('business_id'));
				$mobile = trim($this->_param('mobile'));
				$addr = trim(htmlspecialchars($this->_param('addr')));

				if(!$name){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"联系人没有填写！");
				}else if(!$city_id || !$area_id || !$business_id){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"城市、地区、商圈必须都选择！");
				}else if(!isMobile($mobile)){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"手机号码不正确！");
				}else if(!$addr){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"收货地址没有填写！");
				}else{
					$datas = array();
					$datas['name'] = $name;
					$datas['city_id'] = $city_id;
					$datas['area_id'] = $area_id;
					$datas['business_id'] = $business_id;
					$datas['mobile'] = $mobile;
					$datas['addr'] = $addr;
					$datas['user_id'] = $uid;
					$datas['is_default'] = 0;
					$datas['closed'] = 0;
					
					$ud = D('UserAddr');
					$add = $ud -> add($datas);
					if($add){ 
						$data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>"添加成功！");
					}else{
						$data = array('status' => self::BAO_ADD_FALSE, 'msg'=>"添加失败！");
					}
				}
			}
			$this->stringify($data);
	
		}

		public function addr_detail()
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else if(!$id = $this->_param('id')){
				$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"未指定地址id");
			}else{
				$ud = D('UserAddr');
				$f = $ud -> where('addr_id ='.$id)-> find();
				$city  = D('City')->where('city_id ='.$f['city_id'])-> find();
				$f['city_name'] = $city['name'];
				$area      = D('Area')->where('city_id ='.$f['area_id'])-> find();
				$f['area_name'] = $area['area_name'];
				$business   = D('Business')->where('business_id ='.$f['business_id'])-> find();
				$f['business_name'] = $business['business_name'];
				$data = array('status'=>self::BAO_REQUEST_SUCCESS,'addr'=>$f);
			}
			$this->stringify($data);
		}



		public function edit_addr()
		{

			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{

				$addr_id = trim(intval($this->_param('addr_id')));
				$name = trim(htmlspecialchars($this->_param('name')));
				$area_id = trim($this->_param('area_id'));
				$city_id = trim($this->_param('city_id'));
				$business_id = trim($this->_param('business_id'));
				$mobile = trim($this->_param('mobile'));
				$addr = trim(htmlspecialchars($this->_param('addr')));

				$ud = D('UserAddr');
				if(!$addr_id){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"错误！");
				}else{
				   $f = $ud -> where('addr_id ='.$addr_id)-> find(); 
				   if(!$f){
					   $data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"错误！");
				   }else{
					   if($f['user_id'] != $uid){
						   $data = array('status' => self::BAO_PERMISSION_NO_OPERATION, 'msg'=>"无权限操作");
					   }
				   }
				}

				if(!$name){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"联系人没有填写！");
				}else if(!$city_id || !$area_id || !$business_id){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"城市、地区、商圈必须都选择！");
				}else if(!isMobile($mobile)){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"手机号码不正确！");
				}else if(!$addr){
					$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"收货地址没有填写！");
				}else{
				
					$datas = array();
					$datas['name'] = $name;
					$datas['city_id'] = $city_id;
					$datas['area_id'] = $area_id;
					$datas['business_id'] = $business_id;
					$datas['mobile'] = $mobile;
					$datas['addr'] = $addr;
					$datas['user_id'] = $uid;
					$datas['is_default'] = $f['is_default'];
					$datas['closed'] = 0;
					
					
					$add = $ud -> where('addr_id ='.$addr_id) -> setField($datas);
					if($add){ 
						$data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>"修改成功！");
					}else{
						$data = array('status' => self::BAO_EDIT_FALSE, 'msg'=>"修改失败！");
					}
				}
			}

			$this->stringify($data);
		}

		//余额充值

		public function money()
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$payment =  D('Payment')->getPayments();
				$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'payment'=>$payment);
			}

			$this->stringify($data);
		}

		public function moneypay() 
		{ 
			$money = trim(intval($this->_param('money')* 100));
			$code = trim(htmlspecialchars($this->_param('code')));
			$payment = D('Payment')->checkPayment($code);
			if ($money <= 0) {
				$data = array('status' => self::BAO_FROM_FALSE, 'msg'=>"请填写正确的充值金额！");
			}else if (empty($payment)) {
				$data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"该支付方式不存在");
			}else{
				$logs = array(
					'user_id' => $this->_param('uid'),
					'type' => 'money',
					'code' => $code,
					'order_id' => 0,
					'need_pay' => $money,
					'create_time' => NOW_TIME,
					'create_ip' => get_client_ip(),
				);
				$logs['log_id'] = D('Paymentlogs')->add($logs);
				$button = D('Payment')->getCode($logs);
				$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'button'=>$button,'money'=>$money);
			}
			$this->stringify($data);
		}

		//消息中心

		public function msg()
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$msgs = D('Msg')->where(array('user_id' => array('IN', array(0, $uid))))->limit(0, 20)->select();
				$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msgs'=>$msgs);
			}
			$this->stringify($data);
		}

		public function msgshow() 
		{

			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else if(!$msg_id = trim(intval($this->_param('msg_id')))){
				$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数错误！");
			}else if(!$detail = D('Msg')->find($msg_id)){
				$data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"消息不存在");
			}else if($detail['user_id'] != $uid && $detail['user_id'] != 0){
				$data = array('status' => self::BAO_PERMISSION_NO_OPERATION, 'msg'=>"您没有权限查看该消息");
			}else{
				if(!D('Msgread')->find(array('user_id' => $uid, 'msg_id' => $msg_id))){
					D('Msgread')->add(array(
						'user_id' => $uid,
						'msg_id' => $msg_id,
						'create_time' => NOW_TIME,
						'create_ip' => get_client_ip()
					));
				}
				if ($detail['link_url']) {
					header("Location:" . $detail['link_url']);
					die;
				}else{
					$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'detail'=>$detail);
				}

			}
			$this->stringify($data);
		}


		//我的关注

		public function favorites()
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$Shopfavorites = D('Shopfavorites');
				$map = array('user_id' => $uid);
				$count = $Shopfavorites->where($map)->count();
				$limit = 10;
				$pages = ceil($count/$limit);
				$page = $this->_param('page')?$this->_param('page'):1;
				if($page>$pages){
					die('0');
				}
				$start = ($page-1)*$limit;
				$list = $Shopfavorites->where($map)->order('favorites_id desc')->limit($start.','.$limit)->select();
				foreach ($list as $k => $val) {
					$shop_ids[$val['shop_id']] = $val['shop_id'];
				}
				$shops = D('Shop')->itemsByIds($shop_ids);
				$prices = D('Shopdetails')->itemsByIds($shop_ids);
				$data = array('status'=>self::BAO_REQUEST_SUCCESS,'shops'=>$shops,'prices'=> $prices,'list'=> $list,'page'=>$page+1);
			}

			$this->stringify($data);
		}

		public function deletefavo() 
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else if(!$detial = D('Shopfavorites')->find($this->_param('favorites_id'))){
				$data = array('status'=>self::BAO_COUPON_NO_EXSITS,'msg'=>'该关注不存在!');
			}else if($detial['user_id'] != $uid){
				$data = array('status' => self::BAO_PERMISSION_NO_OPERATION, 'msg'=>"您没有权限查看该消息");
			}else{
				D('Shopfavorites')->delete($favorites_id);
				$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msg'=>'取消关注成功!','url'=>U('mcenter/favorites',array('uid'=>$uid)));
			}

			$this->stringify($data);
		}


		//账号管理

		public function nickname() 
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
				$this->stringify($data);
			}else{
				if($nickname = $this->_param('nickname')){
					$datas = array('user_id' => $uid, 'nickname' => $nickname);
					if (false !== D('Users')->save($datas)) {
						$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msg'=>'昵称设置成功！!','url'=>U('mcenter/nickname',array('uid'=>$uid)));
					}else{
						$data = array('status' => self::BAO_EDIT_FALSE, 'msg'=>"设置昵称失败！");
					}
				}else{
					$member = D('Users')->find($uid);
					$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msg'=>"设置昵称成功！");
				}
				$this->stringify($data);
			}
		}

		public function member()
		{
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$member = D('Users')->find($uid);
				$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'member'=>$member);
			}
			$this->stringify($data);
		}

		public function memberorder(){
			if(!$this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$data = array('status','order' => D('Tuanorder')->where(array('user_id' => $this->uid, 'status' => 0))->count()); 
			}
			$this->stringify($data);
		}

		public function faces() {
			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				if($face = htmlspecialchars($this->_param('face'))){
					$datas = array('user_id' => $uid, 'face' => $face);
					if (false !== D('Users')->save($datas)) {
						$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msg'=>'设置头像成功！!','url'=>U('mcenter/face',array('uid'=>$uid)));
					}else{
						$data = array('status' => self::BAO_EDIT_FALSE, 'msg'=>"设置头像失败！");
					}
				}else{
					$member = D('Users')->find($uid);
					$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'member'=>$member);
				}
				$this->stringify($data);
			}
		}

		public function upload() 
		{
			
			import('ORG.Net.UploadFile');
			$upload = new UploadFile(); // 
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->allowExts = array('jpg', 'gif', 'png', 'jpeg','ico'); // 设置附件上传类型
			$name = date('Y/m/d', NOW_TIME);
			$dir = BASE_PATH . '/attachs/' . $name . '/';
			if (!is_dir($dir)) {
				mkdir($dir, 0755, true);
			}
			$upload->savePath = $dir; // 设置附件上传目录
			
			if (!$upload->upload()) {// 上传错误提示错误信息
				$data = array('status' => self::BAO_EDIT_FALSE, 'msg'=>$upload->getErrorMsg());
			} else {// 上传成功 获取上传文件信息
				$info = $upload->getUploadFileInfo();
				echo $name . '/' . $info[0]['savename'];
			}
		}

		public function password() 
		{

			if(!$uid = $this->uid){
				$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
			}else{
				$member = D('Users')->find($uid);
				if($oldpwd = $this->_param('oldpwd')){
					if(($newpwd = $this->_param('newpwd')) && ($pwd2 = $this->_param('pwd2'))){
						
						if ($newpwd != $pwd2) {
							$data = array('status' => self::BAO_PWD_NO_AGREE, 'msg'=>"两次密码输入不一致！");
						}else if ($member['password'] != md5($oldpwd)) {
							$data = array('status' => self::BAO_PWD_NO_FALSE, 'msg'=>"原密码不正确");
						}else{
							if (D('Passport')->uppwd($member['account'], $oldpwd, $newpwd)) {
								session('uid', null);
								$data = array('status'=>self::BAO_REQUEST_SUCCESS , 'msg'=>"更改密码成功！");
							}else{
								$data = array('status' => self::BAO_EDIT_FALSE, 'msg'=>"修改密码失败！");
							}
						}
					}else{
						$data = array('status' => self::BAO_DB_ERROR, 'msg'=>"密码修改失败!");
					}
				}
				$this->stringify($data);
			} 
		}
	}
?>