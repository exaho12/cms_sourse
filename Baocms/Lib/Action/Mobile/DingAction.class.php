<?php

class DingAction extends CommonAction {

	protected $cart = array();

   public function _initialize() {
        parent::_initialize();
		if($shop_id = $_REQUEST['shop_id']){
			$S = $shop_id.'dingproduct';
			$this->cart = cookie($S);
			$this->assign('cartnum', (int) array_sum($this->cart));
		}
		$obj = D('Shopdingsetting');
		$this->assign('cfg',$obj->getCfg());
		$room = D('Shopdingroom');
		$this->assign('room',$room->getType());	
    }

   public function index() {
        $linkArr = array();
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $linkArr['keyword'] = $keyword;

        $cate = $this->_param('cate','htmlspecialchars');
        $this->assign('cate', $cate);
        $linkArr['cate'] = $cate;
        
        $order = $this->_param('order','htmlspecialchars');
        $this->assign('order', $order);
        $linkArr['order'] = $order;

        $area = (int) $this->_param('area');
        $this->assign('area', $area);
        $linkArr['area'] = $area;

        $business = (int) $this->_param('business');
        $this->assign('business', $business);
        $linkArr['business'] = $business;
		$this->assign('keywords',D('Keyword')->where(array('type'=>'6'))->select());
        $this->assign('nextpage', LinkTo('ding/loaddata',$linkArr,array('t' => NOW_TIME,'p' => '0000')));
        $this->assign('linkArr',$linkArr);
        $this->display(); // 输出模板 
    }

	public function loaddata()
	{
		$keyword = D('Keyword');
		$shop = D('Shop');
		import('ORG.Util.Page');
		$price = D('Shopdingsetting')->getprice();
		$this->assign('shop',$arr['shop']);
		$this->assign('cate',$arr['cat']);
		$this->assign('price_list',$price);
		$kw = D('Keyword')->where(array('type'=>'6'))->select();
		$this->assign('keyword',$kw);
		$linkArr = array();

		$lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }

        $keyw = $this->_param('cate');
		$sql = "audit = 1 and closed=0 and is_ding =1  and city_id=".$this->city_id;
        if ($keyw) {
			foreach($kw as $k => $v){
				if($v['key_id'] == $keyw){
					$value = $v['keyword'];
				}
			}
			$sql.= " AND ( (s.tags LIKE '%".$value."%') OR (s.shop_name LIKE '%".$value."%'))";
        }
        $areas = D('Area')->fetchAll();
        if ($area = (int) $this->_param('area')) {
			$sql.= " AND (s.`area_id`=".$area.")";
        }

		$business = (int) $this->_param('business');
        if ($business) {
            $sql.= " AND (s.`business_id`=".$business.")";
        }
		$order = $this->_param('order');
		if ($order) {
			if($order == 2){
				$orderby = "(ABS(s.lng - '{$lng}') +  ABS(s.lat - '{$lat}')) asc";
			}elseif($order == 3){
				$orderby = 's.ding_num desc';
			}elseif($order == 4){
				$orderby = 'd.price asc';
			}
        }else{
			$orderby = 's.orderby desc';
		}
		
		$count = $shop->countDingShop($sql); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出  
        
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		$shop_list = $shop->get_ding_shop($sql,$orderby,$Page->firstRow,$Page->listRows);
		foreach($shop_list['shop'] as $k => $v){
             $shop_list['shop'][$k]['d'] = getDistance($lat, $lng, $v['lat'], $v['lng']);
		}
		$this->assign('shop_list',$shop_list['shop']);
		$this->assign('cate',$shop_list['cat']);
		$this->assign('set',$shop_list['set']);
		$this->display();
	}

	public function detail($shop_id)
	{
		$shop = D('Shop');
		if(!$detail = $shop->getbuyshopID($shop_id)){
			$this->error('该餐厅不存在');
		}else{
			D('Shop')->updateCount($shop_id, 'view');
			$room = D('Shopdingroom');
			$cate = D('Shopdingcate');
			$menu = D('Shopdingmenu');
			$pic = $shop->getphoto($shop_id,$detail['shop']['photo']);
			$menu_list = $menu->get_menu($shop_id);
			$get_cate = $menu->get_cate($shop_id);
			$get_count = $menu->get_count($shop_id);
			$this->assign('tuan',$shop->gettuan($shop_id));
			$this->assign('coupon',$shop->getcoupon($shop_id));
			$this->assign('pic',$pic);
			$this->assign('time',date('Y-m-d'));
			$this->assign('cate',$get_cate);
			$this->assign('menu',$menu_list);
			$this->assign('count',$get_count);
			$this->assign('room',$room->shoptype($shop_id));
			$this->assign('room_detail',$room->getroom($shop_id));
			$this->assign('shop_time',D('Shopdingsetting')->get_time($shop_id));
			$this->assign('detail',$detail);



			$Shopdianping = D('Shopdingdianping');
			import('ORG.Util.Page'); // 导入分页类
			$map = array('closed' => 0, 'shop_id' => $shop_id);
			$count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
			$Page = new Page($count, 4); // 实例化分页类 传入总记录数和每页显示的记录数
			$show = $Page->show(); // 分页显示输出
			$list = $Shopdianping->where($map)->order(array('order_id' => 'desc'))->limit(0,4)->select();
			$Shopdianping->getLastSql();
			$all_ping = $Shopdianping->where('shop_id ='.$shop_id)->count();
			$this->assign('all_ping',$all_ping);
			$user_ids = $order_ids = array();
			foreach ($list as $k => $val) {
				$list[$k] = $val;
				$user_ids[$val['user_id']] = $val['user_id'];
				$order_ids[$val['order_id']] = $val['order_id'];
			}
			if (!empty($user_ids)) {
				$this->assign('users', D('Users')->itemsByIds($user_ids));
			}
			if (!empty($order_ids)) {
				$this->assign('pics', D('Shopdingdianpingpic')->where(array('order_id' => array('IN', $order_ids)))->select());
			}
			$this->assign('totalnum', $count);
			$this->assign('list', $list); // 赋值数据集
			$this->assign('page', $show); // 赋值分页输出
		}
		$this->display();
	}

	public function dianping() {
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->error('没有该商家');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
            die;
        }
        $this->assign('detail', $detail);
        $this->display();
    }

	public function dianpingloading() {
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            die('0');
        }
        if ($detail['closed']) {
            die('0');
        }
        $Shopdianping = D('Shopdingdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id);
        $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }

        $show = $Page->show(); // 分页显示输出
        $list = $Shopdianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$user_ids = $order_ids = array();
		foreach ($list as $k => $val) {
			$list[$k] = $val;
			$user_ids[$val['user_id']] = $val['user_id'];
			$order_ids[$val['order_id']] = $val['order_id'];
		}
		if (!empty($user_ids)) {
			$this->assign('users', D('Users')->itemsByIds($user_ids));
		}
		if (!empty($order_ids)) {
			$this->assign('pics', D('Shopdingdianpingpic')->where(array('order_id' => array('IN', $order_ids)))->select());
		}
        $this->assign('totalnum', $count);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('detail', $detail);
        $this->display();
    }

	public function yuyue($shop_id)
	{
		if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
		if (empty($shop_id)) {
           $this->error('该商家不存在');
        }

		$shop = D('shop')->find($shop_id);
        if (empty($shop)) {
           $this->error('该商家不存在');
        }
		$bao = $this->_post('bao', false);
		
		$data = array();

		$date = $this->_post('date', false);
		if (empty($date) || strpos($date,'-') === false) {
			//$this->error('选择日期不正确');
		}
		
		$time = $this->_post('time', false);
		$obj = D('Shopdingsetting');
		$room = D('Shopdingroom');
		$is_open = $obj->get_time($shop_id);
		$datetime = $date.' '.$time;
		$t = strtotime($datetime);
		if (empty($time)) { 
            $this->error('请选择时间');
        }else if($t<time()){
			$this->error('预约时间已经过期，请选择正确的时间');
		}else if(!(in_array($time,$is_open))){
			$this->error('商家已经打烊，实在对不住客官');
		}
		$reson = $this->_post('reson', false);
		$reson_last = $room->shoptype($shop_id);
		$is_reson_true = false;
		foreach($reson_last as $k => $v){
			if($v == $reson){
				$number = $k;
				$is_reson_true = true;
			}
		}

		if($is_reson_true == false){
			$this->error('人数不正确');
		}
		$last_time = $obj->get_time_k($time);

		if ($yuyue_id = D('Shopdingyuyue')->add(array(
            'user_id' => $this->uid,
            'shop_id' => $shop_id,
            'last_date' => $date,
			'last_t' => $last_time,
			'number'=> $number,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip(),
            'is_pay' => 0,
         ))) {

			if(!empty($bao)){
				$this->success('订座提交成功，请选择包厢。', U('ding/rooms', array('shop_id' => $shop_id,'yuyue_id' => $yuyue_id)));
			}else{
				$this->success('订座提交成功，请选择菜品。', U('ding/menu', array('shop_id' => $shop_id,'yuyue_id' => $yuyue_id)));
			}
		}

	}

	public function rooms()
	{
		$shop_id = (int) $this->_param('shop_id');
        if (!$detail = D('shop')->where('audit=1,closed=0,is_ding=1,city_id='.$this->city_id)->find($shop_id)) {
            $this->error('该餐厅不存在');
        }
		$yuyue_id = (int) $this->_param('yuyue_id');
		$dingyuyue = D('Shopdingyuyue');
		if (!$yuyue = $dingyuyue->find($yuyue_id)) {
            $this->error('请按正确的流程操作');
        }

		$room = D('Shopdingroom');

		$room_detail = $room->getrooms($shop_id,$yuyue['last_date'],$yuyue['last_t'],$yuyue['number']);
		
		$getType = $room->getType();
		foreach($getType as $k => $v){
			if($v == $reson){
				$tt = $k;
			}
		}
		$tem = '';
		if($tt){
			$this->assign('tt',$tt);
		}else{
			foreach($room_detail as $k => $v){
				if(!$tem){
					$tem = $k;
					$this->assign('tt',$k);
				}
			}
		}
		$this->assign('room',$room->shoptype($shop_id));
		$this->assign('room_detail',$room_detail);
		$this->assign('shop_id',$shop_id);
		$this->assign('yuyue_id',$yuyue_id);
		$this->display();
	}

	public function menu()
	{
		$menu = D('Shopdingmenu');
		$shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('shop')->where('audit=1,closed=0,is_ding=1,city_id='.$this->city_id)->find($shop_id)) {
            $this->error('该餐厅不存在');
        }
		$linkArr = array();

		$yuyue_id = (int) $this->_get('yuyue_id');
		$dingyuyue = D('Shopdingyuyue');
		if (!$yuyue = $dingyuyue->find($yuyue_id)) {
            $this->error('请按正确的流程操作');
        }

		if($is_room = $this->_post('is_room')){
			if(!$roomid = $this->_post('roomid')){
				 $this->error('请选择包厢');
			}else{
				D('Shopdingyuyue')->where('user_id = '.$this->uid.' and ding_id = '.$yuyue_id)->save(array(
					'room_id' => $roomid
				 ));
			}
		}

		$this->assign('yuyue_id', $yuyue_id);
		$linkArr['yuyue_id'] = $yuyue_id;
       

        $cat = (int) $this->_param('cat');
        $this->assign('cat', $cat);
        $linkArr['cat'] = $cat;

        $this->assign('next', LinkTo('ding/load', $linkArr, array('shop_id' => $shop_id, 't' => NOW_TIME, 'p' => '0000')));
		$this->assign('shop_id', $shop_id);
        $this->assign('detail', $detail);
        $this->assign('cates', $menu->get_cate($shop_id));
        $this->display(); // 输出模板 
	}

	public function load()
	{
		$menu = D('Shopdingmenu');
		$shop_id = (int) $this->_get('shop_id');
		$yuyue_id = (int) $this->_get('yuyue_id');
		$cat = (int) $this->_get('cat');

        $detail = D('shop')->where('audit=1,closed=0,is_ding=1,city_id='.$this->city_id)->find($shop_id);
        $dingmenu = D('shopdingmenu');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id);
       
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['cate_id'] = $cat;
        }
        $count = $dingmenu->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $dingmenu->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('detail', $detail);
		$this->assign('yuyue_id', $yuyue_id);
        $this->assign('cates', $menu->get_cate($shop_id));
        $this->assign('shop', $shop);
        if (!empty($this->cart)) {
            $ids = array_keys($this->cart);
            $total = array(
                'num' => 0, 'money' => 0
            );
            $menus = $dingmenu->itemsByIds($ids);
            foreach ($menus as $k => $val) {
                $menus[$k]['cart_num'] = $this->cart[$val['menu_id']];
                $total['num'] += $this->cart[$val['menu_id']];
                $total['money'] +=( $this->cart[$val['menu_id']] * $val['ding_price']);
            }
            $this->assign('total', $total);
            $this->assign('cartgoods', $menus);
        }
		
        $this->display();
	}

	public function add($menu_id) {

        $menu_id = (int) $menu_id;
        if (empty($menu_id)) {
            die('参数错误');
        }
        if (!$detail = D('Shopdingmenu')->find($menu_id)) {
            die('该产品不存在');
        }
        if (!empty($this->cart)) {
            foreach ($this->cart as $k => $v) {
                $data = D('Shopdingmenu')->find($k);
                if ($data['shop_id'] != $detail['shop_id']) {
                    die('一次只能订购一家的外卖，您可以清空购物车重新定！');
                }
                break;
            }
        }
        if (isset($this->cart[$menu_id])) {
            $this->cart[$menu_id]+=1;
        } else {
            $this->cart[$menu_id] = 1;
        }
		$S = $detail['shop_id'].'dingproduct';
        cookie($S, $this->cart);
        die('0');
    }

	 public function cart() {
         //没有东西可判断啊		
        if (!empty($this->cart)) {
            $ids = array_keys($this->cart);
            $total = array(
                'num' => 0, 'money' => 0
            );
						
			$yuyue_id = (int) $this->_param('yuyue_id');
            $menus = D('Shopdingmenu')->itemsByIds($ids);
            foreach ($menus as $k => $val) {
                $menus[$k]['cart_num'] = $this->cart[$val['menu_id']];
                $total['num'] += $this->cart[$val['menu_id']];
                $total['money'] +=( $this->cart[$val['menu_id']] * $val['ding_price']);
				$shop_id = $val['shop_id'];
            }
			

			if (!$total['num']) {
				$this->error('客官，您还没点菜哦~');
			}
			
			
			$yuyue_id = (int) $this->_param('yuyue_id');
			if (!$yuyue_id) {
				$this->error('请按正确的流程操作');
			}
			$this->assign('yuyue_id', $yuyue_id);
            $this->assign('detail', D('shop')->find($shop_id));
            $this->assign('total', $total);
            $this->assign('cartgoods', $menus);
			$this->assign('shop_id', $shop_id);
			$obj = D('Shopdingsetting');
			$this->assign('setting',$obj->find($shop_id));
        }
		//没点餐就回到以前！
		$shop_id = (int) $this->_get('shop_id');
		$yuyue_id = (int) $this->_get('yuyue_id');
		$this->assign('shop_id', $shop_id);
		$this->assign('yuyue_id', $yuyue_id);
        $this->display();
    }

	 public function delete2($menu_id) {
        $menu_id = (int) $menu_id;
		$yuyue_id = (int) $this->_param('yuyue_id');
		$shop_id = (int) $this->_param('shop_id');
        if (!$detail = D('Shopdingmenu')->find($menu_id)) {
            $this->error('该产品不存在');
        }else{
			if (isset($this->cart[$menu_id])) {
				unset($this->cart[$menu_id]);
				cookie($shop_id.'dingproduct', $this->cart);
			}
			
			$arr = json_decode(urldecode(cookie($shop_id.'dingproduct')));
			if(!empty($arr)){
				$this->success('删除成功', U('ding/cart', array('shop_id' => $shop_id,'yuyue_id' => $yuyue_id)));
			}else{
				$this->success('删除成功，不过购物车空空如也！',U('ding/menu', array('shop_id' => $shop_id,'yuyue_id' => $yuyue_id)));
			}
			
		}
    }
	

	public function order() {

		$yuyue_id = (int) $this->_post('yuyue_id');
		$shop_id = (int) $this->_post('shop_id');
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
		if (empty($shop_id)) {
           $this->error('该商家不存在');
        }
		$shop = D('shop')->find($shop_id);
        if (empty($shop)) {
            $this->error('该商家不存在');
        }
        $obj = D('Shopdingsetting');
		$data = array();
        $num = $this->_post('num', false);

        $total = array(
            'money' => 0,
            'num' => 0,
        );
		if(!empty($num)){
			foreach ($num as $key => $val) {
				$key = (int) $key;
				$val = (int) $val;
				if ($val < 1 || $val > 99) {
					$this->error('请选择正确的购买数量');
				}
				$menu = D('Shopdingmenu')->where('shop_id='.$shop_id)->find($key);
				if (empty($menu)) {
					$this->error('产品不正确');
				}
				$menu_str .= $menu['menu_id'].':'.$val.'|';
				$menu['buy_num'] = $val;
				$products[$key] = $menu;
				$total['money'] += ($menu['ding_price'] * $val);

				$total['num'] += $val;
			}
		}

		$order = D('Shopdingorder');
		$order_no = $order->create_order_no();
        $ding_money = $obj->field('money')->where('shop_id ='.$shop_id)->find();

		

        if (D('Shopdingyuyue')->where('user_id = '.$this->uid.' and ding_id = '.$yuyue_id)->save(array(
			'menu' => $menu_str,
			'order_no' => $order_no,
            'is_pay' => 0,
         ))) {
			cookie($shop_id."dingproduct", null);
			$order_id = $order->add(array(
				'order_no' => $order_no,
				'is_dianping' => 0,
				'status' => 0,
				'total_price' => $total['money'],
				'need_price' => $ding_money['money'],
				'user_id' => $this->uid,
				'ding_id' => $yuyue_id,
				'create_time' => NOW_TIME,
				'create_ip' => get_client_ip()
			 ));
			D('Shop')->updateCount($shop_id, 'ding_num');
			$this->success('下单成功！去支付', U('ding/pay', array('order_id' => $order_id)));
        }
        $this->error('创建订单失败！');
    }



  public function pay()
	{
		if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }

		$this->check_mobile();
        
        $order_id = (int) $this->_get('order_id');

        $order = D('Shopdingorder')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->error('该订单不存在');
            die;
        }
		$order_yuyue = D('Shopdingyuyue')->where(array('order_no' => $order['order_no']))->find();
		$room = D('Shopdingroom');
		$setting = D('Shopdingsetting');
        $this->assign('shop', D('Shop')->find($order_yuyue['shop_id']));
		$this->assign('room_d',$room->get_room_d($order_yuyue['shop_id']));
        $this->assign('yuyue', $order_yuyue);
		$this->assign('order_id', $order_id);
        $this->assign('order', $order);
		$this->assign('setting', $setting->find($order_yuyue['shop_id']));
       
        $this->display();
	}

	public function pay2($order_id)
	{
		if (empty($this->uid)) {
			header("Location:" . U('passport/login'));
			die;
		}
		$order = D('Shopdingorder')->find($order_id);
		if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
			$this->error('该订单不存在');
			die;
		}
		$data['name'] = $this->_post('name', false);
		$data['mobile'] = $this->_post('mobile', false);
		$data['sex'] = $this->_post('sex', false);
		$data['tip'] = $this->_post('tip', false);
		if(!$order['name'] && !$order['mobile']){
			if (empty($data['name'])) {
				$this->error('请填写姓名');
				die;
			}
			if (empty($data['mobile']) || !preg_match("/1[1-9]{1}\d{9}$/",$data['mobile'])) {
				$this->error('请填写正确的手机号码');
				die;
			}
			D('Shopdingorder')->where('order_id='.$order_id)->save($data);
		}
		
		$this->assign('order_id', $order_id);
		$this->assign('shop', D('Ele')->find($order_yuyue['shop_id']));
		$this->assign('order', $order);
		$this->assign('payment', D('Payment')->getPayments(true));
		$this->display();
	}

	public function pay3($order_id) {

        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
			die;
        }
        $order = D('Shopdingorder')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->error('该订单不存在');
            die;
        }
       
        if (!$code = $this->_post('code')) {
            $this->error('请选择支付方2式！');
        }
        
		$payment = D('Payment')->checkPayment($code);
		if (empty($payment)) {
			$this->error('该支付方式不存在1');
		}
		$logs = D('Paymentlogs')->getLogsByOrderId('ding', $order_id);
		
		if (empty($logs)) {
			$logs = array(
				'type' => 'ding',
				'user_id' => $this->uid,
				'order_id' => $order_id,
				'code' => $code,
				'need_pay' => $order['need_price'],
				'create_time' => NOW_TIME,
				'create_ip' => get_client_ip(),
				'is_paid' => 0
			);
			$logs['log_id'] = D('Paymentlogs')->add($logs);
		} else {
			$logs['need_pay'] = $order['need_price'];
			$logs['code'] = $code;
			D('Paymentlogs')->save($logs);
		}
		$data['use_integral'] = $code;
		D('Shopdingorder')->where('order_id='.$order_id)->save($data);
		//====================微信支付通知===========================
             
            include_once "Baocms/Lib/Net/Wxmesg.class.php";
           $_data_order = array(//整体变更
                'url'       =>  "http://".$_SERVER['HTTP_HOST']."/mobile/ding/pay2/order_id/".$order_id.".html",
                'topcolor'  =>  '#F55555',
                'first'     =>  '恭喜订单创建成功！' ,
                'remark'    =>  '请在继续下一步完成付款！',
                'money'     =>  round($order['need_price']/100,2).'元',
                'goodsName' =>  '餐厅预订' ,
                'payType'   =>  $payment['name'],
                'orderNum'  =>  '1-'.$order_id,
				'buyNum' 	=>  1,
            );
            $order_data = Wxmesg::order($_data_order);
            $return   = Wxmesg::net($this->uid, 'OPENTM202297555', $order_data);//结束

            //====================微信支付通知==============================

		$this->success('选择支付方式成功！下面请进行支付！', U('payment/payment', array('log_id' => $logs['log_id'])));
        
    }

    private function remainMoneyNotify($order_id, $need_price ,$shop_name)
    {
        //微信通知
        $openid = D('Connect')->getFieldByUid($this->uid,'open_id'); 

        if($openid){
            $template_id = D('Weixintmpl')->getFieldByTmpl_id(2,'template_id');//支付成功模板
            $tmpl_data =  array(
                'touser'      => $openid,//用户微信openid
                 'url'         => 'http://bao.com/member/ding/index.html',//相对应的订单详情页地址
                'template_id' => $template_id,
                'topcolor'    => '#2FBDAA',
                'data'        => array(
                    'first'=>array('value'=>'恭喜订单创建成功！' ,'color'=>'#2FBDAA'),   
                    'keynote1'=>array('value'=> $order_id, 'color'=>'#2FBDAA'),//订单号
                    'keynote2'=>array('value'=> "在{$shop_name}预定了座位", 'color'=>'#2FBDAA'),//商品名称
                    'keynote3'=>array('value'=> 1, 'color'=>'#2FBDAA'),//订单数量
                    'keynote4'=>array('value'=> $need_price, 'color'=>'#2FBDAA'),//订单总额
                    'keynote5'=>array('value'=>'货到付款', 'color'=>'#2FBDAA'),//付款方式
                    'remark'  =>array('value'=>'请在继续下一步配送地址环节！', 'color'=>'#2FBDAA')
                )
            );
            D('Weixin')->tmplmesg($tmpl_data);
        }
    }

	
}
