
<?php
class  HotelAction extends  CommonAction{
    
    public function main() {
        $cates = D('Shopcate')->where('parent_id=13')->select();
		$indexHotels = array();
		$hotels = array();
		$catesStr = '(';
		foreach ($cates as $k => $val) {
			$catesStr .= $val['cate_id'].",";
            $hotels[$val['cate_id']] = D('Shop')->where(array('cate_id' => $val['cate_id'], 'closed' => 0))->order(array('orderby' => 'asc', 'shop_id' => 'desc'))->limit(0,8)->select();
        }
		$index = strrpos($catesStr, ",");
		$catesStr = substr($catesStr,0,$index).') ';
		//$indexHotels = D('Shop')->where("cate_id in ".$catesStr."")->order(array('orderby' => 'asc', 'shop_id' => 'desc'))->limit(0,8)->select();
		//echo $catesStr;
		//print_r($indexHotels);
		$this->assign('catesStr', $catesStr);
		$this->assign('hotels', $hotels);
		$this->assign('cates', $cates);
		$this->assign('cate_id', '13');
		$this->display(); // 输出模板 
    }
	public function yuding(){
		$tuan_id = (int) $this->_get('tuan_id');
		if (empty($tuan_id)) {
			$this->error('该抢购信息不存在！');
			die;
		}
		if (!$detail = D('Tuan')->find($tuan_id)) {
			$this->error('该抢购信息不存在！');
			die;
		}
		if ($detail['closed']) {
			$this->error('该抢购信息不存在！');
			die;
		}
		if ($this->isPost()) {
			$data = $this->checkFields($this->_post('data', false), array('name', 'mobile', 'content', 'yuyue_date','yuyue_time', 'number'));
			$data['user_id'] = (int) $this->uid;
			$shop_id = $detail['shop_id'];
			$shop = D('Shop')->find($shop_id);
			$data['shop_id'] = $shop_id;
			$data['name'] = htmlspecialchars($data['name']);
			if (empty($data['name'])) {
                $this->baoError('称呼不能为空');
            }
            $data['content'] = htmlspecialchars($data['content']);
            if (empty($data['content'])) {
                $this->baoError('留言不能为空');
            }
            $data['mobile'] = htmlspecialchars($data['mobile']);
            if (empty($data['mobile'])) {
                $this->baoError('手机不能为空');
            }
            if (!isMobile($data['mobile'])) {
                $this->baoError('手机格式不正确');
            }
            $data['yuyue_date'] = htmlspecialchars($data['yuyue_date']);
            $data['yuyue_time'] = htmlspecialchars($data['yuyue_time']);
            if (empty($data['yuyue_date']) || empty($data['yuyue_time'])) {
                $this->baoError('预定日期不能为空');
            }
            if (!isDate($data['yuyue_date'])) {
                $this->baoError('预定日期格式错误！');
            }
			$data['create_time'] = NOW_TIME;
			$data['create_ip'] = get_client_ip();
			$data['yuyue_type'] = 1;
			$obj = D('Shopyuyue');
            $data['code'] = $obj->getCode();
			if($obj->add($data)){
				D('Sms')->sendSms('sms_shop_yuyue', $data['mobile'], array(
                    'shop_name' => $shop['shop_name'],
                    'shop_tel' => $shop['tel'],
                    'shop_addr' => $shop['addr'],
                    'code' => $data['code']
                ));
                $this->baoSuccess('预定成功，请尽快入住！', U('index/index'));
            }else{
                $this->baoError('提交预定信息失败！');
            }
			die;
		}else{
			$detail = D('Tuan')->_format($detail);
			$this->assign('detail', $detail);
		}
		
		$this->display(); // 输出模板 
	}
    public function index() {
        $Shop = D('Shop');
        import('ORG.Util.Page'); // 导入分页类
        //初始数据
        $cates = D('Shopcate')->fetchAll();
        $linkArr = array();
        $map = array('closed' => 0, 'audit' => 1,'city_id'=>$this->city_id);
        $cat = (int) $this->_param('cat');
        $cate_id = (int) $this->_param('cate_id');
        if ($cat) {
            if (!empty($cate_id)) {
                $map['cate_id'] = $cate_id;
                $this->seodatas['cate_name'] = $cates[$cate_id]['cate_name'];
                $linkArr['cat'] = $cat;
                $linkArr['cate_id'] = $cate_id;
            } else {
                $catids = D('Shopcate')->getChildren($cat);
                if (!empty($catids)) {
                    $map['cate_id'] = array('IN', $catids);
                }
                $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
                $linkArr['cat'] = $cat;
            }
        }
        $this->assign('cat', $cat);
        $this->assign('cate_id', $cate_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name|tags'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $this->assign('searchindex', 0);
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
            $this->seodatas['area_name'] = $this->areas[$area]['area_name'];
            $linkArr['area'] = $area;
        }
        $this->assign('area_id', $area);
        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
            $this->seodatas['business_name'] = $this->bizs[$business]['business_name'];
            $linkArr['business'] = $business;
        }
        $this->assign('business_id', $business);
        $areas = D('Area')->fetchAll();
        $this->assign('areas', $areas);
        $order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case 't':
                $orderby = array('shop_id' => 'desc');
                break;
            case 'x':
                $orderby = array('score' => 'desc');
                break;
            case 'h':
                $orderby = array('view' => 'desc');
                break;
            default:
                $orderby = array('orderby' => 'asc');
                break;
        }
        if (empty($order)) {
            $order = 'd';
        }
        $this->assign('order', $order);
        $count = $Shop->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shop->order($orderby)->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $tuan = D('Tuan');
        $coupon = D('Coupon');
        $dianping = D('Shopdianping');
        $huodong = D('Activity');
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $list[$k]['tuan'] = $tuan->order('tuan_id desc ')->find(array('where' => array('shop_id' => $val['shop_id'],'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY))));
            $list[$k]['coupon'] = $coupon->order('coupon_id desc ')->find(array('where' => array('shop_id' => $val['shop_id'],'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'expire_date' => array('EGT', TODAY))));
            $list[$k]['huodong'] = $huodong->order('activity_id desc ')->find(array('where' => array('shop_id' => $val['shop_id'],'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'bg_date' => array('ELT', TODAY), 'end_date' => array('EGT', TODAY))));
            $list[$k]['dianping'] = $dianping->order('show_date desc')->find(array('where' => array('shop_id' => $val['shop_id'], 'closed' => 0, 'show_date' => array('ELT', TODAY))));
            if (!$fav = D('Shopfavorites')->where(array('shop_id' => $val['shop_id'], 'user_id' => $this->uid))->find()) {
                $list[$k]['favorites'] = 0;
            } else {
                $list[$k]['favorites'] = 1;
            }
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('details', D('Shopdetails')->itemsByIds($shop_ids));
        $this->assign('total_num', $count);
        $this->assign('areas', $areas);
        $this->assign('cates', $cates);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('linkArr', $linkArr);
        $this->display(); // 输出模板   
    }

    public function photo() {
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
        $this->display(); // 输出模板   
    }

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
		
        $this->display();
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
        if ($favo = D('Shopfavorites')->where(array('shop_id' => $shop_id, 'user_id' => $this->uid))->find()) {
            $detail['favorites'] = 1;
        } else {
            $detail['favorites'] = 0;
        }
        $Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));
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
        $maps = array('closed' => 0, 'shop_id' => $shop_id, 'audit' => 1);
        $lists = D('Shopbranch')->where($maps)->order(array('orderby' => 'asc'))->select();
        $shop_arr = array(
            'name' => '总店',
            'score' => $detail['score'],
            'score_num' => $detail['score_num'],
            'lng' => $detail['lng'],
            'lat' => $detail['lat'],
            'telephone' => $detail['tel'],
            'addr' => $detail['addr'],
        );
        if(!empty($lists)){
            array_unshift($lists,$shop_arr);
        }else{
            $lists[] = $shop_arr;
        }
        $counts = count($lists);
        if($counts%5 ==0 ){
            $num = $counts/5;
        }else{
            $num = (int)($counts/5) + 1;
        }
        $this->assign('count',$counts);
        $this->assign('totalnum', $num);
        $this->assign('lists',$lists);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('detail', $detail);
        $ex = D('Shopdetails')->find($shop_id);
        $this->assign('ex', $ex);


        $tuan = D('Tuan')->where(array('shop_id' => $shop_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order(' tuan_id desc ')->limit(0, 6)->select();
        $this->assign('tuan', $tuan);
        $goods = D('Goods')->where(array('shop_id' => $shop_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'end_date' => array('EGT', TODAY)))->order('goods_id desc')->limit(0, 6)->select();
        $this->assign('goods', $goods);
        $coupon = D('Coupon')->order('coupon_id desc ')->where(array('shop_id' => $shop_id, 'audit' => 1,'city_id'=>$this->city_id, 'closed' => 0, 'expire_date' => array('EGT', TODAY)))->limit(0, 6)->select();
        $this->assign('coupon', $coupon);
        $huodong = D('Activity')->order('activity_id desc ')->where(array('shop_id' => $shop_id,'city_id'=>$this->city_id, 'audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY), 'bg_date' => array('ELT', TODAY)))->limit(0, 6)->select();
        $this->assign('huodong', $huodong);
        D('Shop')->updateCount($shop_id, 'view');
        $this->seodatas['shop_name'] = $detail['shop_name'];
        $this->seodatas['shop_tel'] = $detail['shop_tel'];
        if ($this->uid) {
            D('Userslook')->look($this->uid, $shop_id);
        }
        $this->assign('shoppic', D('Shoppic')->order('orderby asc')->limit(0, 8)->where(array('shop_id' => $shop_id))->select());
        $this->assign('cate', $this->shopcates[$detail['cate_id']]);
        $this->assign('host', __HOST__);
        $this->assign('height_num',700);
        $this->display();
    }

    public function favorites() {
        if (empty($this->uid)) {
            $this->ajaxLogin(); //提示异步登录
        }
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->baoError('没有该商家');
        }
        if ($detail['closed']) {
            $this->baoError('该商家已经被删除');
        }
        if (D('Shopfavorites')->check($shop_id, $this->uid)) {
            $this->baoError('您已经关注过该商家了！');
        }
        $data = array(
            'shop_id' => $shop_id,
            'user_id' => $this->uid,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip()
        );
        if (D('Shopfavorites')->add($data)) {
            D('Shop')->updateCount($shop_id,'fans_num');
            $this->baoSuccess('恭喜您关注成功！');
        }
        $this->baoError('关注失败！');
    }

    public function cancel() {
        if (empty($this->uid)) {
            $this->ajaxLogin(); //提示异步登录
        }
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->baoError('没有该商家');
        }
        if ($detail['closed']) {
            $this->baoError('该商家已经被删除');
        }
        if (!$favo = D('Shopfavorites')->where(array('shop_id' => $shop_id, 'user_id' => $this->uid))->find()) {
            $this->baoError('您还未关注该商家！');
        }
        if (false !== D('Shopfavorites')->save(array('favorites_id' => $favo['favorites_id'], 'closed' => 1))) {
            $this->baoSuccess('恭喜您成功取消关注！');
        }
        $this->baoError('取消关注失败！');
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
            $yzm = $this->_post('yzm');
            if (strtolower($yzm) != strtolower(session('verify'))) {
                session('verify', null);
                $this->baoError('验证码不正确!', 2000, true);
            }

            $data = $this->createCheck();

            $obj = D('Shop');
            $details = $this->_post('details', 'htmlspecialchars');
            if ($words = D('Sensitive')->checkWords($details)) {
                $this->baoError('商家介绍含有敏感词：' . $words, 2000, true);
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
                $this->baoSuccess('恭喜您申请成功！', U('shop/index'));
            }
            $this->baoError('申请失败！');
        } else {
            $areas = D('Area')->fetchAll();
            $this->assign('cates', D('Shopcate')->fetchAll());
            $this->assign('areas', $areas);
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), array('cate_id', 'tel', 'logo', 'photo', 'shop_name', 'contact', 'details', 'business_time','city_id', 'area_id','business_id', 'addr', 'lng', 'lat'));
        $data['shop_name'] = htmlspecialchars($data['shop_name']);
        if (empty($data['shop_name'])) {
            $this->baoError('店铺名称不能为空', 2000, true);
        }
        $data['lng'] = htmlspecialchars($data['lng']);
        $data['lat'] = htmlspecialchars($data['lat']);
        if (empty($data['lng']) || empty($data['lat'])) {
            $this->baoError('店铺坐标需要设置', 2000, true);
        }
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('分类不能为空', 2000, true);
        }
        $data['city_id'] = (int) $data['city_id'];
        if (empty($data['city_id'])) {
            $this->baoError('城市不能为空', 2000, true);
        }
        $data['area_id'] = (int) $data['area_id'];
        if (empty($data['area_id'])) {
            $this->baoError('地区不能为空', 2000, true);
        }$data['business_id'] = (int) $data['business_id'];
        if (empty($data['business_id'])) {
            $this->baoError('商圈不能为空', 2000, true);
        }$data['contact'] = htmlspecialchars($data['contact']);
        if (empty($data['contact'])) {
            $this->baoError('联系人不能为空', 2000, true);
        }$data['business_time'] = htmlspecialchars($data['business_time']);
        if (empty($data['business_time'])) {
            $this->baoError('营业时间不能为空', 2000, true);
        }
        if (!isImage($data['logo'])) {
            $this->baoError('请上传正确的LOGO', 2000, true);
        }
        if (!isImage($data['photo'])) {
            $this->baoError('请上传正确的店铺图片', 2000, true);
        }
        $data['addr'] = htmlspecialchars($data['addr']);
        if (empty($data['addr'])) {
            $this->baoError('地址不能为空', 2000, true);
        }
        $data['tel'] = htmlspecialchars($data['tel']);
        if (empty($data['tel'])) {
            $this->baoError('联系方式不能为空', 2000, true);
        }
        if (!isPhone($data['tel']) && !isMobile($data['tel'])) {
            $this->baoError('联系方式格式不正确', 2000, true);
        }
        $detail = D('Shop')->where(array('user_id' => $this->uid))->find();
        if (!empty($detail)) {
            $this->baoError('您已经是商家了', 2000, true);
        }
        $data['user_id'] = $this->uid;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

    public function tui() {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
        }
        if ($this->isPost()) {
            $yzm = $this->_post('yzm');
            if (strtolower($yzm) != strtolower(session('verify'))) {
                session('verify', null);
                $this->baoError('验证码不正确!', 2000, true);
            }

            $account['account'] = htmlspecialchars($this->_post('account'));
            if (!isMobile($account['account']) && !isEmail($account['account'])) {
                session('verify', null);
                $this->baoError('用户名只允许手机号码或者邮件!', 2000, true);
            }
            $account['password'] = trim(htmlspecialchars($this->_post('password'))); //整合UC的时候需要
            if (empty($account['password']) || strlen($account['password']) < 6) {
                session('verify', null);
                $this->baoError('请输入正确的密码!密码长度必须要在6个字符以上', 2000, true);
            }


            $data = $this->tuiCheck();
            $account['nickname'] = $data['shop_name'];
            if (isEmail($account['account'])) { //如果邮件的@前面超过15就不好了
                $local = explode('@', $account['account']);
                $account['ext0'] = $local[0];
            } else {
                $account['ext0'] = $account['account']; //兼容UCENTER
            }

            $account['reg_ip'] = get_client_ip();
            $account['reg_time'] = NOW_TIME;



            $obj = D('Shop');
            $details = $this->_post('details', 'SecurityEditorHtml');
            if ($words = D('Sensitive')->checkWords($details)) {
                $this->baoError('商家介绍含有敏感词：' . $words, 2000, true);
            }

            $ex = array(
                'details' => $details,
                'near' => $data['near'],
                'price' => $data['price'],
                'business_time' => $data['business_time'],
            );
            unset($data['near'], $data['price'], $data['business_time']);
            if (!D('Passport')->register($account)) {
                $this->baoError('创建帐号失败！');
            }
            $token = D('Passport')->getToken();
            $data['user_id'] = $token['uid'];
            if ($shop_id = $obj->add($data)) {
                D('Shopdetails')->upDetails($shop_id, $ex);
                $this->baoSuccess('恭喜您申请成功！', U('shop/index'));
            }
            $this->baoError('申请失败！');
        } else {
            $areas = D('Area')->fetchAll();
            $this->assign('cates', D('Shopcate')->fetchAll());
            $this->assign('areas', $areas);
            $this->display();
        }
    }

    private function tuiCheck() {
        $data = $this->checkFields($this->_post('data', false), array('cate_id', 'tel', 'logo', 'photo', 'shop_name', 'contact', 'details', 'business_time', 'area_id', 'addr', 'lng', 'lat'));
        $data['shop_name'] = htmlspecialchars($data['shop_name']);
        if (empty($data['shop_name'])) {
            $this->baoError('店铺名称不能为空', 2000, true);
        }
        $data['lng'] = htmlspecialchars($data['lng']);
        $data['lat'] = htmlspecialchars($data['lat']);
        if (empty($data['lng']) || empty($data['lat'])) {
            $this->baoError('店铺坐标需要设置', 2000, true);
        }
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('分类不能为空', 2000, true);
        }$data['area_id'] = (int) $data['area_id'];
        if (empty($data['area_id'])) {
            $this->baoError('地区不能为空', 2000, true);
        }$data['contact'] = htmlspecialchars($data['contact']);
        if (empty($data['contact'])) {
            $this->baoError('联系人不能为空', 2000, true);
        }$data['business_time'] = htmlspecialchars($data['business_time']);
        if (empty($data['business_time'])) {
            $this->baoError('营业时间不能为空', 2000, true);
        }
        if (!isImage($data['logo'])) {
            $this->baoError('请上传正确的LOGO', 2000, true);
        }
        if (!isImage($data['photo'])) {
            $this->baoError('请上传正确的店铺图片', 2000, true);
        }
        $data['addr'] = htmlspecialchars($data['addr']);
        if (empty($data['addr'])) {
            $this->baoError('地址不能为空', 2000, true);
        }
        $data['tel'] = htmlspecialchars($data['tel']);
        if (empty($data['tel'])) {
            $this->baoError('联系方式不能为空', 2000, true);
        }
        if (!isPhone($data['tel']) && !isMobile($data['tel'])) {
            $this->baoError('联系方式格式不正确', 2000, true);
        }
        $data['tui_uid'] = $this->uid;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

    public function dianping() {
        if (empty($this->uid)) {
            $this->ajaxLogin(); //提示异步登录
        }
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->baoError('没有该商家');
        }
        if ($detail['closed']) {
            $this->baoError('该商家已经被删除');
        }
        if (D('Shopdianping')->check($shop_id, $this->uid)) {
            $this->baoError('不可重复评价一个商户');
        }


        $data = $this->checkFields($this->_post('data', false), array('score', 'd1', 'd2', 'd3', 'cost', 'contents'));
        $data['user_id'] = $this->uid;
        $data['shop_id'] = $shop_id;
        $data['score'] = (int) $data['score'];

        if (empty($data['score'])) {
            $this->baoError('评分不能为空');
        }
        if ($data['score'] > 5 || $data['score'] < 1) {
            $this->baoError('评分不能为空');
        }

        $cate = $this->shopcates[$detail['cate_id']];
        $data['d1'] = (int) $data['d1'];
        if (empty($data['d1'])) {
            $this->baoError($cate['d1'] . '评分不能为空');
        }
        if ($data['d1'] > 5 || $data['d1'] < 1) {
            $this->baoError($cate['d1'] . '评分不能为空');
        }
        $data['d2'] = (int) $data['d2'];
        if (empty($data['d2'])) {
            $this->baoError($cate['d2'] . '评分不能为空');
        }
        if ($data['d2'] > 5 || $data['d2'] < 1) {
            $this->baoError($cate['d2'] . '评分不能为空');
        }
        $data['d3'] = (int) $data['d3'];
        if (empty($data['d3'])) {
            $this->baoError($cate['d3'] . '评分不能为空');
        }
        if ($data['d3'] > 5 || $data['d3'] < 1) {
            $this->baoError($cate['d3'] . '评分不能为空');
        }


        $data['cost'] = (int) $data['cost'];
        $data['contents'] = htmlspecialchars($data['contents']);
        if (empty($data['contents'])) {
            $this->baoError('评价内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['contents'])) {
            $this->baoError('评价内容含有敏感词：' . $words);
        }
        $data['show_date'] = date('Y-m-d', NOW_TIME); //马上生效 -> ->
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        if ($dianping_id = D('Shopdianping')->add($data)) {
            $photos = $this->_post('photos', false);
            $local = array();
            foreach ($photos as $val) {
                if (isImage($val))
                    $local[] = $val;
            }
            if (!empty($local))
                D('Shopdianpingpics')->upload($dianping_id, $data['shop_id'], $local);
            D('Users')->prestige($this->uid, 'dianping');
            D('Shop')->updateCount($shop_id, 'score_num');
            D('Users')->updateCount($this->uid, 'ping_num');
            D('Shopdianping')->updateScore($shop_id);
            $this->baoSuccess('恭喜您点评成功!', U('shop/detail', array('shop_id' => $shop_id)));
        }
        $this->baoError('点评失败！');
    }

    public function yuyue() {
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $this->ajaxReturn(array('status'=>'error','msg'=>'没有该商家'));
        }
        if ($detail['closed']) {
            $this->ajaxReturn(array('status'=>'error','msg'=>'该商家已经被删除'));
        }
        if (IS_AJAX) {
            $data = $this->checkFields($this->_post('data', false), array('name', 'mobile', 'content', 'yuyue_date', 'yuyue_time', 'number'));
            $data['user_id'] = (int) $this->uid;
            $data['shop_id'] = (int) $shop_id;
            $data['name'] = htmlspecialchars($data['name']);
            if (empty($data['name'])) {
                $this->ajaxReturn(array('status'=>'error','msg'=>'称呼不能为空'));
            }
            $data['content'] = htmlspecialchars($data['content']);
            if (empty($data['content'])) {
                $this->ajaxReturn(array('status'=>'error','msg'=>'留言不能为空'));
            }
            $data['mobile'] = htmlspecialchars($data['mobile']);
            if (empty($data['mobile'])) {
                $this->ajaxReturn(array('status'=>'error','msg'=>'手机不能为空'));
            }
            if (!isMobile($data['mobile'])) {
                $this->ajaxReturn(array('status'=>'error','msg'=>'手机格式不正确'));
            }
            $data['yuyue_date'] = htmlspecialchars($data['yuyue_date']);
            $data['yuyue_time'] = htmlspecialchars($data['yuyue_time']);
            if (empty($data['yuyue_date']) || empty($data['yuyue_time'])) {
                $this->ajaxReturn(array('status'=>'error','msg'=>'预定日期不能为空'));
            }
            if (!isDate($data['yuyue_date'])) {
                $this->ajaxReturn(array('status'=>'error','msg'=>'预定日期格式错误'));
            }
            $data['number'] = (int) $data['number'];
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $obj = D('Shopyuyue');
            $data['code'] = $obj->getCode();
            if ($obj->add($data)) {
                D('Sms')->sendSms('sms_shop_yuyue', $data['mobile'], array(
                    'shop_name' => $detail['shop_name'],
                    'shop_tel' => $detail['tel'],
                    'shop_addr' => $detail['addr'],
                    'code' => $data['code']
                ));
                $this->ajaxReturn(array('status'=>'success','msg'=>'预约成功','url'=>U('shop/detail',array('shop_id'=>$shop_id))));
            }
            $this->ajaxReturn(array('status'=>'error','msg'=>'预约失败'));
        } 
    }
   
}