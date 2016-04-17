<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MarketAction extends CommonAction {
	
	protected function _initialize() {
        parent::_initialize();
		$market = (int)$this->_CONFIG['operation']['market'];
		if ($market == 0) {
				$this->error('此功能已关闭');
				die;
		}
      }

    public function marketshop() {
        $market_id = (int) $this->_param('market_id');
        $market = D('Market');
        if (!$detail = $market->find($market_id)) {
            $this->error('没有该商场');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商场已经被删除');
            die;
        }
        $types = D('Market')->getType();
            for ($i = 1; $i <= 6; $i++) {
                if ($detail['type' . $i] == 0) {
                    unset($types[$i]);
                }
            }
            $this->assign('types',$types);
            $floors = D('Marketfloor')->where(array('market_id'=>$market_id))->select();
            $this->assign('floors',$floors);
        $this->assign('market_id', $market_id);
        $cate_id = (int) $this->_param('cate_id');
        $this->assign('cate_id', $cate_id);
        $floor_id = (int) $this->_param('floor_id');
        $this->assign('floor_id', $floor_id);
        $order = $this->_param('order', 'htmlspecialchars');
        $this->assign('order', $order);
        $this->assign('nextpage', LinkTo('market/shopload', array('market_id' => $market_id, 'floor_id' => $floor_id, 'cate_id' => $cate_id, 'order' => $order, 't' => NOW_TIME, 'p' => '0000')));
        $this->display(); // 输出模板   
    }

    public function shopload() {
        $market_id = (int) $this->_param('market_id');
        $market = D('Market');
        import('ORG.Util.Page'); // 导入分页类
        $maps = array('market_id' => $market_id);
        $cate_id = (int) $this->_param('cate_id');
        if(!empty($cate_id)) $maps['cate_id'] = $cate_id;
        $floor_id = (int) $this->_param('floor_id');
        if(!empty($floor_id)) $maps['floor'] = $floor_id;
        $enters = D('Marketenter')->where($maps)->select();

        $shop_ids = array();
        $types= array();
        $typessetting =$market ->getType();
        foreach ($enters as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
            $types[$val['shop_id']] = $typessetting[$val['cate_id']];
        }
        $map = array('audit' => 1, 'closed' => 0, 'city_id' => $this->city_id);
        $map['shop_id'] = array('IN', $shop_ids);
        $order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case 1:
                $orderby = array('score' => 'desc'); //评分
                break;
            case 2:
                $orderby = array('view' => 'desc'); //浏览
                break;
            case 3:
                $orderby = array('fans_num' => 'desc'); //关注
                break;
            default:
                $orderby = array('shop_id' => 'desc'); //默认
                break;
        }
        $count = D('Shop')->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 300); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = D('Shop')->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('types',$types);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function districtindex() {

        $market_id = (int) $this->_get('market_id');
        $market = D('Market');
        if (!$detail = $market->find($market_id)) {
            $this->error('没有该商场');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商场已经被删除');
            die;
        }
        $enters = D('Marketenter')->where(array('market_id' => $market_id))->select();
        $shop_ids = array();
        foreach ($enters as $k => $val) {
            $shop_ids[] = $val['shop_id'];
        }
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
        $map = array('audit' => 1, 'closed' => 0, 'city_id' => $this->city_id, 'end_date' => array('EGT', TODAY));
        $map['shop_id'] = array('IN', $shop_ids);
        $tuans = D('Tuan')->where($map)->order($orderby)->limit(0, 5)->select();
        $this->assign('tuans', $tuans);
        $this->assign('detail', $detail);
		$marketpic = D('Marketpic')->where(array('market_id' => $market_id))->order('pic_id desc')->select();
        $this->assign('marketpic', $marketpic);
        $this->display(); // 输出模板   
    }

    public function index() {
        $order = (int) $this->_param('order');
        $this->assign('order', $order);
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        //$areas = D('Area')->fetchAll();
        $area = (int) $this->_param('area');
        $this->assign('area_id', $area);
        $biz = D('Business')->fetchAll();
        $business = (int) $this->_param('business');
        $this->assign('business_id', $business);
        //$this->assign('areas', $areas);
        $this->assign('biz', $biz);
        $this->assign('nextpage', LinkTo('market/loaddata', array('area' => $area, 'business' => $business, 'order' => $order, 't' => NOW_TIME, 'keyword' => $keyword, 'p' => '0000')));
		
		
		
		
        $this->display(); // 输出模板   
    }

    public function loaddata() {

        $market = D('Market');
        import('ORG.Util.Page'); // 导入分页类
        //初始数据
        $map = array('closed' => 0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['market_name'] = array('LIKE', '%' . $keyword . '%');
        }

        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        } else {
            $map['city_id'] = $this->city_id;
        }
        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
        }
        $order = (int) $this->_param('order');
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        switch ($order) {
            case 2:
                $orderby = array('orderby' => 'asc');
                break;
            default:
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";

                break;
        }

        $count = $market->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $market->order($orderby)->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $val) {
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $market_ids = array();
        foreach ($list as $key => $v) {
            $market_ids[$v['market_id']] = $v['market_id'];
        }
        $marketdetails = D('Marketdetails')->itemsByIds($market_ids);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function detail() {

        $market_id = (int) $this->_get('market_id');
        $market = D('Market');
        if (!$detail = $market->find($market_id)) {
            $this->error('没有该商场');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商场已经被删除');
            die;
        }
        if (D('Marketfavorites')->check($market_id, $this->uid)) {
            $detail['favo'] = 1;
        } else {
            $detail['favo'] = 0;
        }
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
        $map = array('closed' => 0, 'market_id' => array('NEQ', $market_id));
        $markets = $market->where($map)->order($orderby)->limit(0, 4)->select();
        foreach ($markets as $k => $val) {
            $markets[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $huodong = D('Marketactivity')->where(array('market_id' => $market_id, 'closed' => 0))->order(array('id' => 'desc'))->limit(0, 4)->select();

        $marketpic = D('Marketpic')->where(array('market_id' => $market_id))->order('pic_id desc')->select();

        $this->assign('marketpic', $marketpic);
        $this->assign('markets', $markets);
        $this->assign('huodong', $huodong);
        $this->assign('detail', $detail);
        $this->assign('ex', D('Marketdetails')->find($market_id));

        $this->display();
    }

    public function favorites() {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
        $market_id = (int) $this->_get('market_id');
        if (!$detail = D('Market')->find($market_id)) {
            $this->error('没有该商场');
        }
        if ($detail['closed']) {
            $this->error('该商场已经被删除');
        }
        if (D('Marketfavorites')->check($market_id, $this->uid)) {
            $this->error('您已经收藏过了！');
        }
        $data = array(
            'market_id' => $market_id,
            'user_id' => $this->uid,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip()
        );
        if (D('Marketfavorites')->add($data)) {
            D('Market')->updateCount($market_id, 'fans_num');
            $this->success('恭喜您收藏成功！', U('market/detail', array('market_id' => $market_id)));
        }
        $this->error('收藏失败！');
    }

    public function gps($market_id) {
        $market_id = (int) $market_id;
        if (empty($market_id)) {
            $this->error('该卖场不存在');
        }
        if (!$market = D('Market')->find($market_id)) {
            $market = D('Shop')->find($market_id);
        }

        $this->assign('market', $market);
        $this->display();
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
        $Page = new Page($count, 1); // 实例化分页类 传入总记录数和每页显示的记录数

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

    public function event() {
        $market_id = (int)$this->_param('market_id');
        $this->assign('market_id',$market_id);
        $market = D('Market');
        if (!$detail = $market->find($market_id)) {
            $this->error('没有该商场');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商场已经被删除');
            die;
        }
        $this->assign('detail',$detail);
        $this->assign('nextpage', LinkTo('market/eventload', array('market_id'=>$market_id, 'p' => '0000')));
        $this->display();
    }

    public function eventload(){
        $market_id = (int) $this->_param('market_id');
        $market = D('Market');
        if (!$detail = $market->find($market_id)) {
            $this->error('没有该商场');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商场已经被删除');
            die;
        }
        $huodong = D('Marketactivity');
        import('ORG.Util.Page'); // 导入分页类
        $map = array( 'closed' => 0,'market_id'=>$market_id);
        
        $count = $huodong->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $huodong->where($map)->order(array('id'=>'desc','views'=>'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //dump($list);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('detail',$detail);
        $this->display(); // 输出模板
    }

    

    public function eventdetail() {//活动详情
        $id = (int) $this->_get('id');
        $marketactivity = D('Marketactivity');
        if (!$detail = $marketactivity->find($id)) {
            $this->error('没有该活动');
            die;
        }
        if ($detail['closed']) {
            $this->error('该活动已经被删除');
            die;
        }
        $this->assign('detail', $detail);
        $this->assign('details', D('Market')->find($detail['market_id']));
        $marketactivity->updateCount($id, 'views');
        $this->display();
    }

}
