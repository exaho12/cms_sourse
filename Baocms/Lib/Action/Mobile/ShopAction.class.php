<?php

/*
 小灰灰
 */

class ShopAction extends CommonAction {

    public function index() {
        $cat = (int) $this->_param('cat');
        $this->assign('cat', $cat);
        $order = (int) $this->_param('order');
        $this->assign('order', $order);
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $areas = D('Area')->fetchAll();
        $area = (int) $this->_param('area');
        $this->assign('area_id', $area);
        $biz = D('Business')->fetchAll();
        $business = (int) $this->_param('business');
        $this->assign('business_id', $business);
        $this->assign('areas', $areas);
        $this->assign('biz', $biz);
        $this->assign('nextpage', LinkTo('shop/loaddata', array('cat' => $cat, 'area' => $area, 'business' => $business, 'order' => $order, 't' => NOW_TIME, 'keyword' => $keyword, 'p' => '0000')));
        $this->display(); // 输出模板   
    }
    
    
	
	//二维码名片开始
	 public function qrcode($shop_id){
        $shop_id = (int)$shop_id;
        if(empty($shop_id)){
            $this->error('该商家不存在');
        }
        $shop = D('Shop')->find($shop_id);
        
		//商家二维码
        $file = D('Weixin')->getCode($shop_id,1);
        $this->assign('file', $file);
		
        $this->assign('shop',$shop);
        $this->display();
    }
//二维码名片结束



    
    public function branch(){
        
        $shop_id = I('shop_id',0,'intval,trim');
		$branch_id = (int) $this->_get('branch_id');
		$shopbranch = D('Shopbranch');
		import('ORG.Util.Page'); // 导入分页类
		
		$map = array('shop_id'=>$shop_id,'closed'=>0,'audit'=>1);
			
		$count = $shopbranch->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出


		$branch = $shopbranch->where($map)->select();

		if (empty($shop_id) && empty($branch_id)) {
            $this->error('该分店已删除或者未通过审核');
        }
        
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        
        foreach ($branch as $k => $val) {
            $branch[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        
        $this->assign('branch_id',$branch_id);
		$this->assign('count',$count);
		$this->assign('page', $show); // 赋值分页输出
        $this->assign('branch',$branch);
		$this->display();
        
    }
    
    public function gps($shop_id){
        $shop_id = (int)$shop_id;
        if(empty($shop_id)){
            $this->error('该商家不存在');
        }
        $shop = D('Shop')->find($shop_id);
        
        $this->assign('shop',$shop);
        $this->display();
    }
    
    public function main() {

        $this->display();
    }

    public function loaddata() {

        $Shop = D('Shop');
        //var_dump($Shop);die;
        import('ORG.Util.Page'); // 导入分页类
        //初始数据
        $map = array('closed' => 0, 'audit' => 1,'city_id'=>$this->city_id);
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Shopcate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }

        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name|addr'] = array('LIKE', '%' . $keyword . '%');
        }
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        }
        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
        }

        $order=(int) $this->_param('order');
		
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        switch ($order) {
            case 2:
                $orderby = array('orderby' => 'asc', 'ranking' => 'desc');
                break;
            default:
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";

                break;
        }
        $count = $Shop->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Shop->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
	
        foreach ($list as $k => $val) {
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $shop_ids = array();
        foreach ($list as $key => $v) {
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }
        $shopdetails = D('Shopdetails')->itemsByIds($shop_ids);
		
        foreach ($list as $k => $val) {
            $list[$k]['price'] = $shopdetails[$val['shop_id']]['price'];
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function detail() {

        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->error('没有该商家');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
            die;
        }       
		$Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));
        $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 4); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit(0,4)->select();
        $all_ping = $Shopdianping->where('shop_id ='.$shop_id)->count();
        $this->assign('all_ping',$all_ping);
        $user_ids = $dianping_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($dianping_ids)) {
            $this->assign('pics', D('Shopdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出		
		$this->assign('favnum', D('Shopfavorites')->where(array('shop_id'=>$shop_id))->count());
        $this->assign('detail', $detail);
		$this->seodatas['title'] = $detail['shop_name'];
        $this->assign('ex', D('Shopdetails')->find($shop_id));
        $this->assign('cates', D('Shopcate')->fetchAll());
        $tuans = D('Tuan')->order(' tuan_id desc ')->where(array('shop_id' => $shop_id, 'audit' => 1, 'closed' => 0, 'bg_date' => array('ELT', TODAY), 'end_date' => array('EGT', TODAY)))->limit(0, 4)->select();
        $this->assign('tuans', $tuans);
		
		
		
		/***********20150915新增********/
		//招聘
        $work = D('work')->order('work_id desc ')->where(array('shop_id' => $shop_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'expire_date' => array('EGT', TODAY)))->select();
        $this->assign('work', $work);
		
		//微店
		 $weidian = D('WeidianDetails')->where(array('audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, ))->order('id desc')->limit(0, 1)->select();
		$this->assign('weidian', $weidian); 
		 
		//商品
        $goods = D('Goods')->where(array('shop_id' => $shop_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order('goods_id desc')->select();

        $this->assign('goods', $goods);
		//优惠
        $coupon = D('Coupon')->order('coupon_id desc ')->where(array('shop_id' => $shop_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'expire_date' => array('EGT', TODAY)))->select();

        $this->assign('coupon', $coupon);
		//活动
        $huodong = D('Activity')->order('activity_id desc ')->where(array('shop_id' => $shop_id,'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY), 'bg_date' => array('ELT', TODAY)))->select();

        $this->assign('huodong', $huodong);
		//外卖
		 $ele_menu = D('ele_product')->order('product_id desc ')->where(array('shop_id' => $shop_id,'city_id'=>$this->city_id))->select();

        $this->assign('ele_menu', $ele_menu);
		
		//菜单
		 $ding_menu = D('shop_ding_menu')->order('menu_id desc ')->where(array('shop_id' => $shop_id,'city_id'=>$this->city_id))->select();

        $this->assign('ding_menu', $ding_menu);
		
		/******************/
		
		
        D('Shop')->updateCount($shop_id, 'view');
        if ($this->uid) {
            D('Userslook')->look($this->uid, $shop_id);
        }
		/*******查询商家微店***********/
		$Weidian = D('Weidian_details');       
        $weidianid=$Weidian->where('shop_id='.$shop_id.' ')->find();
		$this->assign('weidian_id', $weidianid['id']);
		
		/********查询商家微店结束**********/
		$this->assign('pic',$pic = D('Shoppic')->where(array('shop_id' => $shop_id))->order(array('pic_id' => 'desc'))->count());
        $this->display();
    }

    public function favorites() {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->error('没有该商家');
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
        }
        if (D('Shopfavorites')->check($shop_id, $this->uid)) {
            $this->error('您已经收藏过了！');
        }
        $data = array(
            'shop_id' => $shop_id,
            'user_id' => $this->uid,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip()
        );
        if (D('Shopfavorites')->add($data)) {
            $this->success('恭喜您收藏成功！', U('shop/detail', array('shop_id' => $shop_id)));
        }
        $this->error('收藏失败！');
    }

    //点评
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
        $Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));
        $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }

        $show = $Page->show(); // 分页显示输出
        $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $dianping_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($dianping_ids)) {
            $this->assign('pics', D('Shopdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('detail', $detail);
        $this->display();
    }
	
	
	//新添加预约商家开始
	
	 public function book($shop_id) {
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }
        $shop_id = (int) $shop_id;
        $detail = D('Shop')->find($shop_id);
        if (empty($detail)) {
            $this->error('商家不存在');
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
            die;
        }
		
	
		
		//去除商家手机号开始
		$sj_user=$detail['user_id'];
		$shangjia_mobile=D('Users')->find($sj_user);
		$sj_mobile=$shangjia_mobile['mobile'];	
		$sj_email = $shangjia_mobile['email'];//获得商家的邮件
		//去除商家手机号结束	
		
        if ($this->isPost()) {
            $data = $this->checkBook($shop_id);
            $obj = D('Shopyuyue');
            $data['shop_id'] = (int) $shop_id;
            $data['code'] = $obj->getCode();
            if ($obj->add($data)) {
                D('Sms')->sendSms('sms_shop_yuyue', $data['mobile'], array(
                    'shop_name' => $detail['shop_name'],
                    'shop_tel' => $detail['tel'],
                    'shop_addr' => $detail['addr'],
                    'code' => $data['code']
                ));
				
							
				//预约通知商家功能开始
				if(!empty($sj_mobile)){
                D('Sms')->sendSms('sms_shangjia_yuyue',$sj_mobile,array('name'=>$data['name'],'content'=>$data['content'],'yuyue_time'=>$data['yuyue_time'],'mobile'=>$data['mobile'],'number'=>$data['number'],'yuyue_date'=>$data['yuyue_date']));
				  }
                //预约通知商家功能结束
              //邮件功能
              if(!empty($sj_email)){
                 D('Email')->sendMail('email_yuyue', $sj_email, '邮件标题', array('name'=>$data['name'],'content'=>$data['content'],'yuyue_time'=>$data['yuyue_time'],'mobile'=>$data['mobile'],'number'=>$data['number'],'yuyue_date'=>$data['yuyue_date']));
                } //邮件功能
				
				
				//更新了商家的预约人数
                D('Shop')->updateCount($shop_id, 'yuyue_total');
				
				
                $this->niuMsg('预约成功！', U('mcenter/yuyue/index'));
            }
            $this->niuMsg('操作失败！');
        } else {
            $this->assign('shop_id', $shop_id);
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function checkBook() {
        $data = $this->checkFields($this->_post('data', false), array('name', 'mobile', 'content', 'yuyue_date', 'yuyue_time', 'number'));
        $data['user_id'] = (int) $this->uid;
        $data['name'] = htmlspecialchars($data['name']);
        if (empty($data['name'])) {
            $this->niuMsg('称呼不能为空');
        }
        $data['content'] = htmlspecialchars($data['content']);
        if (empty($data['content'])) {
            $this->niuMsg('留言不能为空');
        }
        $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
            $this->niuMsg('手机不能为空');
        }
        if (!isMobile($data['mobile'])) {
            $this->niuMsg('手机格式不正确');
        }
        $data['yuyue_date'] = htmlspecialchars($data['yuyue_date']);
        $data['yuyue_time'] = htmlspecialchars($data['yuyue_time']);
        if (empty($data['yuyue_date']) || empty($data['yuyue_time'])) {
            $this->niuMsg('预定日期不能为空');
        }
        if (!isDate($data['yuyue_date'])) {
            $this->niuMsg('预定日期格式错误！');
        }
        $data['number'] = (int) $data['number'];
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }
	//预约商家结束
	
	
	//增加分店开始
	public function shop() {
		
		
		
		
		
		
		
		
        $shop_id = (int) $this->_get('shop_id');
        $branch_id = (int) $this->_get('branch_id');
        $branch = D('Shopbranch')->where(array('shop_id'=>$shop_id,'closed'=>0,'audit'=>1))->select();
        if (empty($shop_id) && empty($branch_id)) {
            $this->error('该商家不存在');
        }
		
		
		
		
		
        $Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        if (empty($branch_id)) {
            if (!$detail = D('Shop')->find($shop_id)) {
                $this->error('该商家不存在');
                die;
            }
            if ($detail['closed'] != 0 || $detail['audit'] != 1) {
                $this->error('该商家不存在');
                die;
            }
            if(!$rs = D('Shopfavorites')->where(array('shop_id'=>$shop_id,'user_id'=>$this->uid))->find()){
               $detail['fav'] = 0; 
            }else{
                $detail['fav'] = 1; 
            }
            
            $goods = D('Goods')->where(array('shop_id' => $shop_id, 'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order('goods_id desc')->limit(0,12)->select();
            $this->assign('goods', $goods);
            $tuan = D('Tuan')->where(array('shop_id' => $shop_id,'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order(' tuan_id desc ')->limit(0,10)->select();
            $this->assign('tuan', $tuan);
            
            $map = array('closed' => 0, 'shop_id' => $shop_id,'branch_id'=>0, 'show_date' => array('ELT', TODAY));
            $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
            $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show(); // 分页显示输出
            $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $user_ids = $dianping_ids = array();
            foreach ($list as $k => $val) {
                $list[$k] = $val;
                $user_ids[$val['user_id']] = $val['user_id'];
                $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
            }
            if (!empty($user_ids)) {
                $this->assign('users', D('Users')->itemsByIds($user_ids));
            }
            if (!empty($dianping_ids)) {
                $this->assign('pics', D('Shopdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
            }

            $ex = D('Shopdetails')->find($shop_id);
            $detail['business_time'] = $ex['business_time'];
            $detail['details'] = $ex['details'];
            $this->assign('detail', $detail);
        } else {
            $detail = D('Shopbranch')->find($branch_id);
            if(empty($detail)||$detail['shop_id'] != $shop_id){
                $this->error('该分店不存在');
            }
            if ($detail['closed'] != 0 || $detail['audit'] != 1) {
                $this->error('该分店不存在');
                die;
            }
            $goods = D('Goods')->where(array('shop_id' => $shop_id, 'branch_id'=>$branch_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order('goods_id desc')->select();
            $this->assign('goods', $goods);
            $tuan = D('Tuan')->where(array('shop_id' => $shop_id, 'branch_id'=>$branch_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order(' tuan_id desc ')->select();
            $this->assign('tuan', $tuan);
            $map = array('closed' => 0, 'shop_id' => $shop_id,'branch_id'=>$branch_id, 'show_date' => array('ELT', TODAY));
            $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
            $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show(); // 分页显示输出
            $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $user_ids = $dianping_ids = array();
            foreach ($list as $k => $val) {
                $list[$k] = $val;
                $user_ids[$val['user_id']] = $val['user_id'];
                $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
            }
            if (!empty($user_ids)) {
                $this->assign('users', D('Users')->itemsByIds($user_ids));
            }
            if (!empty($dianping_ids)) {
                $this->assign('pics', D('Shopdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
            }
            $shopdetail = D('Shop')->find($shop_id);
            $ex = D('Shopdetails')->find($shop_id);
            array_unshift($branch,$shopdetail);
            foreach($branch as $k=>$val){
                if($val['branch_id'] == $branch_id){
                    unset($branch[$k]);
                }
            }
            $detail['logo'] = $shopdetail['logo'];
            $detail['shop_name'] = $shopdetail['shop_name'];
            $detail['details'] = $ex['details'];
            $detail['shop_id'] = $shop_id;
            $this->assign('detail', $detail);
        }
		
		
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->assign('branch_id',$branch_id);
        $this->assign('branch',$branch);
		
		$this->assign('height_num',350);
		
		
		D('Shopbranch')->updateCount($branch_id, 'view');//分店详情页浏览量小灰灰增加的哦
		//$this->assign('view', D('Shopbranch')->where(array('branch_id'=>$branch_id))->count());//收藏商家
		$this->assign('favnum', D('Shopfavorites')->where(array('shop_id'=>$shop_id))->count());//收藏商家有严重问题的,暂时调用下应急，懒得新建字段了！、
		//后期有空调整
		
        $this->display();
    }
	
	
	//增加团购
	public function tuan() {
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
        $this->assign('nextpage', LinkTo('shop/tuanload', array('shop_id' => $shop_id, 't' => NOW_TIME, 'keyword' => $keyword, 'p' => '0000')));
        $this->display(); // 输出模板   
    }
	
	public function tuanload() {
        $shop_id = (int) $this->_get('shop_id');
		
		
		$tuanload = D('Tuan');//定义数据库
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));//查询当前shop下面的团购
        $count = $tuanload->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $tuanload->where($map)->order(array('tuan_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list',$list);
		
		
        $this->display(); // 输出模板   
    }
	
	//增加优惠劵
	public function coupon() {
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
        $this->assign('nextpage', LinkTo('shop/couponload', array('shop_id' => $shop_id, 't' => NOW_TIME, 'keyword' => $keyword, 'p' => '0000')));
        $this->display(); // 输出模板   
    }
	
	public function couponload() {
        $shop_id = (int) $this->_get('shop_id');
        $couponload = D('Coupon');//定义数据库
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));//查询当前shop下面的团购
        $count = $couponload->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $couponload->where($map)->order(array('coupon_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list',$list);
		
        $this->display(); // 输出模板   
    }

	//团购图文详情

    public function pic() {

        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->error('没有该商家');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
            die;
        }
		$map = array('shop_id' => $shop_id);//查询当前shop下面的团购
		$list = D('Shoppic')->where($map)->order(array('pic_id' => 'desc'))->select();
		$this->assign('list',$list);
		$thumb = unserialize($detail['thumb']);
        $this->assign('thumb', $thumb);
        $this->assign('detail', $detail);
        $this->display();

    }
}
