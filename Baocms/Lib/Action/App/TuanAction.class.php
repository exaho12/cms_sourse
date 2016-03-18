<?php
/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanAction extends CommonAction 
{
	
	//列表首页
	public function items()
	{
		$lat = $this->lat;//CommonAction获取
        $lng = $this->lng;//CommonAction获取
		//接收BAO筛选条件
        $aready  = (int) $this->_param('aready');
        //接收BAO城市ID
        $city_id = (int) $this->_param('city_id');

        if ($aready == 1) {
            $order = 'create_time desc';
        } elseif ($aready == 2) {
            $order = 'sold_num desc';
        } elseif ($aready == 3) {
            $order = 'views desc';
        }

        $map = array('audit' => 1, 'closed' => 0, 'city_id' => $city_id, 'end_date' => array('EGT', TODAY));

        $tuan= D('Tuan');
        //分页功能
		$count = $tuan->where($map)->count();
		$limit = 10;
		$pages = ceil($count/$limit);
		$page  = $this->_param('page');
		$page  = $_page = ($page<=$pages)?($page>=1?$page:1):$pages;
		$limit = ($page-1)*$limit+$limit;
		$page  = ($page==$pages)?'null':($page+1);
        $tuan_list = $tuan->where($map)->order($order)->limit($limit)->select();
        foreach ($tuan_list as $k => $val) {
            $tuan_list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $this->stringify(array('status'=>self::BAO_REQUEST_SUCCESS,'tuan_list'=>$tuan_list,'page'=>$page));
	}
	

	public function detail()
	{
		if(!$tuan_id=(int)$this->_param('tuan_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if(!$detail=D('Tuan')->find($tuan_id)){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"抢购信息不存在！");
		}else if($detail['closed']){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"抢购信息不存在！");
		}else{
			$detail = D('Tuan')->_format($detail);
			$detail['end_time'] = strtotime($detail['end_date']) - NOW_TIME + 86400;


			$shop_id = $detail['shop_id'];
			$shop = D('Shop')->find($shop_id);
		   
			$tuans = D('Tuan')->where(array('audit' => 1, 'closed' => 0, 'shop_id' => $shop_id, 'bg_date' => array('ELT', TODAY), 'end_date' => array('EGT', TODAY), 'tuan_id' => array('NEQ', $tuan_id)))->limit(0, 5)->select();
			$tuandetails = D('Tuandetails')->find($tuan_id);
			$data = array('status'=>self::BAO_REQUEST_SUCCESS,'detail'=>$detail,'tuandetails'=>$tuandetails,'shop'=>$shop,'tuans'=>$tuans);
		}
		$this->stringify($data);
	}
	
	//购物车
	public function buy()
	{
		//uid,tuan_id,

		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else if(!$tuan_id = (int) $this->_param('tuan_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if (!$detail = D('Tuan')->find($tuan_id)) {
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该商品不存在！");
        }else if ($detail['closed'] == 1 || $detail['end_date'] < TODAY) {
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该商品已结束或被删除！");
        }else{
			$detail = D('Tuan')->_format($detail);
			$data = array('status' => self::BAO_REQUEST_SUCCESS,'detail'=>$detail);
		}
		$this->stringify($data);
	}

	//生成订单
	public function order() {
		//uid,tuan_id,num
        if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else if(!$tuan_id = (int) $this->_param('tuan_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if (!$detail = D('Tuan')->find($tuan_id)) {
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该商品不存在！");
        }else if ($detail['closed'] == 1 || $detail['end_date'] < TODAY) {
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该商品已结束或被删除！");
        }else{
			$num = (int) $this->_param('num');
			if ($num <= 0 || $num > 99) {
				$data = array('status' => self::BAO_REG_NO_FIND, 'msg'=>"请输入正确的购买数量");
			}else{
				$datas = array(
					'tuan_id' => $tuan_id,
					'num' => $num,
					'user_id' => $uid,
					'shop_id' => $detail['shop_id'],
					'create_time' => NOW_TIME,
					'create_ip' => get_client_ip(),
					'total_price' => $detail['tuan_price'] * $num,
					'mobile_fan' => $detail['mobile_fan']*$num,
					'need_pay' => $detail['tuan_price'] * $num - $detail['mobile_fan']*$num,
					'status' => 0,
					'is_mobile' => 1,
				);
				if ($order_id = D('Tuanorder')->add($datas)) {
					$map  = array('user_id'=>1,'is_default'=>1);
					$addr = D('Useraddr')->where($map)->getField('addr');
					$data = array('status' => self::BAO_REQUEST_SUCCESS, 'addr'=>$addr,'order'=>$order_id);
				}else{
					$data = array('status' => self::BAO_ADD_FALSE, 'msg'=>"创建订单失败！");
				}
			}

			$this->stringify($data);
		}
    }

	//支付
	public function pay()
	{
		//uid,order_id,
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, "未登录或登录状态不正确！");
		}else if(!$order_id = (int) $this->_param('order_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "参数不正确！");
		}else if (!$order = D('Tuanorder')->find($order_id)) {
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "该订单不存在！");
        }else if($order['status'] != 0 || $order['user_id'] != $uid){
			$data = array('status' => self::BAO_PERMISSION_NO_OPERATION, "您没有权限操作该订单");
		}else if(!$tuan = D('Tuan')->find($order['tuan_id'])){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "该抢购不存在！");
		}else if($tuan['closed'] == 1 || $tuan['end_date'] < TODAY){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "该抢购被删除或已过期！");
		}else{
			$use_integral = $tuan['use_integral'] * $order['num'];
			$payment      =  D('Payment')->getPayments(true);
			$data = array('status' => self::BAO_REQUEST_SUCCESS,'use_integral'=>$use_integral,'payment'=>$payment,'tuan'=>$tuan,'order'=>$order);
		}
		$this->stringify($data);

	}

public function inlinepay()
{
		//order_id,
		//$pay_mode = "货到支付";
		$codes = array();
		$obj = D('Tuancode');
		if(!$order_id = (int) $this->_param('order_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}elseif(!$order = D('Tuanorder')->find($order_id)){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该订单不存在！");
		}elseif (D('Tuanorder')->save(array('order_id' => $order_id, 'status' => '-1'))) { //更新成到店付的状态
			$tuan = D('Tuan')->find($order['tuan_id']);
			for ($i = 0; $i < $order['num']; $i++) {
				$local = $obj->getCode();
				$insert = array(
					'user_id' => $uid,
					'shop_id' => $tuan['shop_id'],
					'order_id' => $order['order_id'],
					'tuan_id' => $order['tuan_id'],
					'code' => $local,
					'price' => 0, //价值用0来代替了这样就说明他是到店付
					'real_money' => 0, //退款的时候用
					'real_integral' => 0, //退款的时候用
					'fail_date' => $tuan['fail_date'],
					'settlement_price' => 0, //结算时候
					'create_time' => NOW_TIME,
					'create_ip' => $ip,
				);
			$codes[] = $local;
			$obj->add($insert);
			}
			
			D('Tuan')->updateCount($tuan['tuan_id'], 'sold_num'); //更新卖出产品
			$codestr = join(',', $codes);
			//发送短信
			D('Sms')->sendSms('sms_tuan', $this->member['mobile'], array(
				'code' => $codestr,
				'nickname' => $this->member['nickname'],
				'tuan' => $tuan['title'],
			));
			//更新贡献度
			D('Users')->prestige($uid, 'tuan');
			D('Sms')->tuanTZshop($tuan['shop_id']);
			$data = array('status' => self::BAO_REQUEST_SUCCESS);
		}else{
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"您已经设置过该抢购为到店付了！");
		}	
		$this->stringify($data);
	}

    public function pay2() 
    {
    	//uid,
        if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, "未登录或登录状态不正确！");
		}else if(!$order_id = (int) $this->_param('order_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "参数不正确！");
		}else if (!$order = D('Tuanorder')->find($order_id)) {
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "该订单不存在！");
        }else if($order['status'] != 0 || $order['user_id'] != $uid){
			$data = array('status' => self::BAO_PERMISSION_NO_OPERATION, "您没有权限操作该订单");
		}else if(!$code = $this->_param('code')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, "参数不正确！");
		}else{
			$pay_mode = "在线支付";

			if ($code == 'wait') { // 到店付 将不会有支付记录，并且不能使用在线的积分
				$pay_mode = "货到支付";
				$codes = array();
				$obj = D('Tuancode');
				if (D('Tuanorder')->save(array('order_id' => $order_id, 'status' => '-1'))) { //更新成到店付的状态
					$tuan = D('Tuan')->find($order['tuan_id']);
					for ($i = 0; $i < $order['num']; $i++) {
						$local = $obj->getCode();
						$insert = array(
							'user_id' => $uid,
							'shop_id' => $tuan['shop_id'],
							'order_id' => $order['order_id'],
							'tuan_id' => $order['tuan_id'],
							'code' => $local,
							'price' => 0, //价值用0来代替了这样就说明他是到店付
							'real_money' => 0, //退款的时候用
							'real_integral' => 0, //退款的时候用
							'fail_date' => $tuan['fail_date'],
							'settlement_price' => 0, //结算时候
							'create_time' => NOW_TIME,
							'create_ip' => $ip,
						);
						$codes[] = $local;
						$obj->add($insert);
					}
					D('Tuan')->updateCount($tuan['tuan_id'], 'sold_num'); //更新卖出产品
					$codestr = join(',', $codes);
					//发送短信
					D('Sms')->sendSms('sms_tuan', $this->member['mobile'], array(
						'code' => $codestr,
						'nickname' => $this->member['nickname'],
						'tuan' => $tuan['title'],
					));
					//更新贡献度
					D('Users')->prestige($uid, 'tuan');
					D('Sms')->tuanTZshop($tuan['shop_id']);

					$data = array('status' => self::BAO_REQUEST_SUCCESS,'url'=>U('tuan/items'));
				} else {
					$data = array('status' => self::BAO_PAGE_NO_EXSITS, "您已经设置过该抢购为到店付了！");
				}
			}else {
				$payment = D('Payment')->checkPayment($code);
				if (empty($payment)) {
					$data = array('status' => self::BAO_FROM_FALSE, "该支付方式不存在");
				}else if(empty($order['use_integral'])) {
					if(!$tuan = D('Tuan')->find($order['tuan_id'])){
						$data = array('status' => self::BAO_PAGE_NO_EXSITS, "该抢购不存在！");
					}else if($tuan['closed'] == 1 || $tuan['end_date'] < TODAY){
						$data = array('status' => self::BAO_PAGE_NO_EXSITS, "该抢购被删除或已过期！");
					}else{
						$canuse = $tuan['use_integral'] * $order['num'];
						if (!empty($this->member['integral'])) {
							$member = D('Users')->find($uid); //重新查是因为绕过了之前的判断是很可能的
							$used = 0;
							if ($member['integral'] < $canuse) {
								$used = $member['integral'];
								$member['integral'] = 0;
							} else {
								$used = $canuse;
								$member['integral'] -= $canuse;
							}
							D('Users')->save(array('user_id' => $uid, 'integral' => $member['integral']));
							D('Userintegrallogs')->add(array(
								'user_id' => $uid,
								'integral' => -$used,
								'intro' => "订单" . $order_id . "积分抵用",
								'create_time' => NOW_TIME,
								'create_ip' => get_client_ip()
							));
							$order['use_integral'] = $used;
							$order['need_pay'] = $order['total_price'] - $order['mobile_fan'] - $used;
							D('Tuanorder')->save($order);
						}

						$logs = D('Paymentlogs')->getLogsByOrderId('tuan', $order_id);
						if (empty($logs)) {
							$logs = array(
								'type' => 'tuan',
								'user_id' => $uid,
								'order_id' => $order_id,
								'code' => $code,
								'need_pay' => $order['need_pay'],
								'create_time' => NOW_TIME,
								'create_ip' => get_client_ip(),
								'is_paid' => 0
							);
							$logs['log_id'] = D('Paymentlogs')->add($logs);
						} else {
							$logs['need_pay'] = $order['need_pay'];
							$logs['code'] = $code;
							D('Paymentlogs')->save($logs);
						}
						header("Location:" . U('payment/payment', array('uid'=>$uid,'log_id' => $logs['log_id'])));
						die;
					}
				}
			}
		}

		$this->stringify($data);
    }

    public function alipay()
    {
    	$map        = array('code'=>'alipay');
    	if($setting = D('Payment')->where($map)->getField('setting')){
    		$setting= unserialize($setting);
    		$data   = array('status'=>self::BAO_REQUEST_SUCCESS,'config'=> $setting);
    	}else{
    		$data   = array('status'=>self::BAO_DETAIL_NO_EXSITS,'msg'=>'数据不存在！');
    	}
    	$this->stringify($data);
    }


    public function index() 
    {
    	//cat,area,order,city_id
        $Tuan    = D('Tuan');
        $city_id = $this->_param('city_id');
        $map = array('audit' => 1, 'closed' => 0, 'city_id' =>$city_id, 'end_date' => array('EGT', TODAY));

        if($keyword = (int) $this->_param('keyword')){
        	$map['keyword'] = $keyword;
        }

        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Tuancate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        }
        $order = $this->_param('order', 'htmlspecialchars');

        $lat = $this->lat;
        $lng = $this->lng;

        $orderby = '';
        switch ($order) {
            case 2:
                $orderby = array('orderby' => 'asc', 'tuan_id' => 'desc');
                break;
            default:
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
                break;
        }
        //分页功能
		$count = $Tuan->where($map)->count();
		$limit = 10;
		$pages = ceil($count/$limit);
		$page  = $this->_param('page');
		$page  = $_page = ($page<=$pages)?($page>=1?$page:1):$pages;
		$limit = ($page-1)*$limit+$limit;
		$page  = ($page==$pages)?'null':($page+1);

        $list = $Tuan->where($map)->order($orderby)->limit($limit)->select();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val['end_time'] = strtotime($val['end_date']) - NOW_TIME + 86400;
            $list[$k] = $val;
        }
        
        $data = array('list'=>$list,'page'=>$page);
        if ($shop_ids) {
            $shops = D('Shop')->itemsByIds($shop_ids);
            $ids = array();
            foreach ($shops as $k => $val) {
                $shops[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
                $d = getDistanceNone($lat, $lng, $val['lat'], $val['lng']);
                $ids[$d][] = $k; //防止同样的距离出现 
            }
            ksort($ids);
            $showshops = array();

            foreach ($ids as $arr1) {
                foreach ($arr1 as $val) {
                    $showshops[$val] = $shops[$val];
                }
            }
            $data['shops'] = $showshops;
        }
        
        $tuan_cate  = D('TuanCate')->select();
        $cate       = array();
        foreach($tuan_cate as $item){
            $cate[$item['cate_id']] = $item['cate_name'];
        }

        
        $this->stringify(array('status'=>self::BAO_REQUEST_SUCCESS,'data'=>$data,'cate'=>$cate,'page'=>$page));
    }
}