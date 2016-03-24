<?php 
/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

	class  CouponAction extends  CommonAction{

	  	public function main() {
       		$news= (int) $this->_param('news');
	     	if ($news == 1) {
	            $orderby = array('coupon_id' => 'desc');
	        }
	        if ($news == 2) {
	            $orderby = array('downloads' => 'desc');
	        }	
	        $lat = addslashes($this->_get('lat'));
	        $lng = addslashes($this->_get('lng'));
	        if (empty($lat) || empty($lng)) {
	            $lat = $this->city['lat'];
	            $lng = $this->city['lng'];
	        }
	        $Coupon = D('Coupon');
	        $map = array('audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'expire_date' => array('EGT', TODAY));
	        $list = $Coupon->where($map)->order($orderby)->limit(0,10)->select();
	        $shop_ids = array();
	        foreach ($list as $key => $v) {
	            $shop_ids[$v['shop_id']] = $v['shop_id'];
	            $list[$key]['title'] = bao_msubstr($list[$key]['title'],0,5,true);

	        }


	        $shops = D('Shop')->itemsByIds($shop_ids);
	           foreach ($shops as $k => $val) {
	            $shops[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
	        } 
	        //city_id IN city_ids;
	        $map = array('closed' => 0,'site_id' => 32, 'city_id' =>$this->city_id ,'bg_date'=> array('ELT',TODAY),'end_date'=>array('EGT',TODAY));
	        $order = 'orderby asc';
	        $ad = D('ad')->where($map)->limit(0,5)->order($order)->select();
	        $map = array('group_id' => 7,'bg_date' => array('ELT',TODAY),'end_date' => array('EGT',TODAY));
	        $recommend = D('Recommend')->where($map)->limit(0,2)->order($order)->select();

			$data = array('status' => self::BAO_REQUEST_SUCCESS, 'list'=>$list,'shops'=>$shops ,'ad' => $ad);
			$this->stringify($data);
   		 }



		//优惠卷分页加载
		public function data(){
			import('ORG.Util.Page'); // 导入分页类
		  	$news= (int) $this->_get('order');
     	    if ($news == 1) {
            $orderby = array('coupon_id' => 'desc');
     	    }else{
            $orderby = array('downloads' => 'desc');
	        }	

	        $city_id = $this->city_id;
			//DEBUG
	        $city_id = 1;
			$map =  array('city_id' => $city_id,'closed' => 0, 'expire_date' => array('EGT', TODAY));
			$cat = (int) $this->_get('cat');
     	    if ($cat) {
            $catids = D('Shopcate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
           		} else {
                $map['cate_id'] = $cat;
           		}
       	    }

       	    $area_id = (int) $this->_get('area_id');
       	    if ($area_id) {
				$map['area_id'] = $area_id;
        	}
        	
       	    $Coupon = D('Coupon');
       	    $count = $Coupon->where($map)->count();
       	    $Page = new Page($count, 10);

       	    $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
	        $p = $_GET[$var];
	        if ($Page->totalPages < $p) {
	            die('0');
	        }
			$coupons = $Coupon->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();

			$data = array('status' => self::BAO_REQUEST_SUCCESS, 'coupons'=>$coupons,'news'=>$orderby);
			$this->stringify($data);
		}

		//详情
		public function detail(){
			$coupon_id= (int) $this->_get('coupon_id');
			if($coupon_id <= 0){
				$data = array('status' => self::BAO_INPUT_ERROR);
				$this->stringify($data);
				die();
			}
			$Coupon = D('Coupon');	
			$Coupon->updateCount($coupon_id, 'views');
		    $map = array('coupon_id' =>$coupon_id);
			$coupons = $Coupon->where($map)->find();

			if($coupons['shop_id']){
				$Shop = D('Shop');
				$map = array('shop_id' => $coupons['shop_id']);
				$shops = $Shop ->where($map)->find();
				$data = array('status' => self::BAO_REQUEST_SUCCESS,'coupons'=> $coupons,'shop' => $shops );
				$this->stringify($data);
			}else{
				$data = array('status' => self::BAO_DB_ERROR,'msg'=>'未知错误');
				$this->stringify($data);
			}

			
		}

		//下载优惠卷
		public function download(){
			 if (empty($this->uid)) {
			 	$data = array('status' => self::BAO_LOGIN_ERROR,'msg'=>'登录状态失效' );
			 	$this->stringify($data);
	        } 
	        if (empty($this->member['mobile'])) {
	        	$data = array('status' => self::BAO_INPUT_ERROR,'msg'=>'亲还没有验证手机号码！' );
	        	$this->stringify($data);
	        }
	        $coupon_id = (int) $this->_get('coupon_id');
	        if (empty($coupon_id)) {
				$data = array('status' => self::BAO_COUPON_ERROR,'msg'=>'该优惠券不存在！' );
	            $this->stringify($data);
	        }
	        $Coupon = D('Coupon');
	        if (!$detail = $Coupon->find($coupon_id)) {
	             $data = array('status' => self::BAO_COUPON_ERROR,'msg'=>'该优惠券不存在！' );
	             $this->stringify($data);
	        }

	        if ($detail['expire_date'] < TODAY) {
        	  	$data = array('status' => self::BAO_COUPON_EXPIRES,'msg'=>'该优惠券已经过期' );
        	  	$this->stringify($data);
	        }
	        
	         if($detail['num'] <= 0){
	         	$data = array('status' => self::BAO_COUPON_NONUM,'msg'=>'该优惠券已经下载完了' );
	         	$this->stringify($data);
	        }
        
	        if($detail['limit_num']){
	            $count = D('Coupondownload')->where(array( 'coupon_id' => $coupon_id,'user_id'=>  $this->uid))->count();
	            if($count+1 > $detail['limit_num']){
	            	$data = array('status' => self::BAO_COUPON_LIMITED,'msg'=>'您已经超过下载该优惠券的限制了！' );
	            	$this->stringify($data);
	            }
	        }

         	$shop = D('Shop')->find($detail['shop_id']);

	        $code = D('Coupondownload')->getCode();
	        $data = array(
	            'user_id' => $this->uid,
	            'shop_id' => $detail['shop_id'],
	            'coupon_id' => $coupon_id,
	            'create_time' => NOW_TIME,
	            'mobile' => $this->member['mobile'],
	            'create_ip' => get_client_ip(),
	            'code' => $code,
	        );
	        $Coupondownload = D('Coupondownload');
	        if ($Coupondownload->add($data)) {
	            D('Coupon')->updateCount($coupon_id, 'downloads');
	            D('Coupon')->updateCount($coupon_id,'num',-1);
	            D('Sms')->sendSms('sms_coupon_downloads', $this->member['mobile'], array(
	                'coupon_title' => $detail['title'],
	                'shop_name' => $shop['shop_name'],
	                'code' => $code,
	                'expire_date' => $detail['expire_date'],
	            ));
	            $data = array('status' => self::BAO_REQUEST_SUCCESS,'msg'=>'下载成功',$Coupondownload->getLastSql(),'mobile'=>$this->member['mobile']);
	            $this->stringify($data);
	        }
       		 $data = array('status' => self::BAO_DB_ERROR,'msg'=>'未知错误');
       		 $this->stringify($data);
		}


		

	}
?>