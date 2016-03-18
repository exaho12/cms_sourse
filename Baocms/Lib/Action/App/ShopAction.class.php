<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
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
    
    
    
    public function branch(){
        
        $shop_id = I('shop_id',0,'intval,trim');
        $s = D('Shop');
        $rs = $s -> where('shop_id ='.$shop_id) -> find();
        $this->assign('rs',$rs);
   
        $sb = D('ShopBranch');
        $rsb = $sb -> where('shop_id ='.$shop_id) -> select();
        $count = $sb -> where('shop_id ='.$shop_id) -> count();
        
        $this->assign('count',$count);
        
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        
        foreach ($rsb as $k => $val) {
            $rsb[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        
        $this->assign('rsb',$rsb);

        
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
            $data['status'] = self::BAO_DB_ERROR;
            $data['msg'] = '没有该商家';
            $this->stringify($data);
            die;
        }
        if ($detail['closed']) {
            $data['status'] = self::BAO_DB_ERROR;
            $data['msg'] = '该商家已经被删除';
            $this->stringify($data);
            die;
        }
        $Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));
        $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
        $data['totalnum'] = $count;
        $Page = new Page($count, 4); // 实例化分页类 传入总记录数和每页显示的记录数
       // $show = $Page->show(); // 分页显示输出
        $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit(0,4)->select();
        $all_ping = $Shopdianping->where('shop_id ='.$shop_id)->count();

        $this->assign('all_ping',$all_ping);
        $user_ids = $dianping_ids = array();
        foreach ($list as $k => $val) {
            $_list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
        }

        $data['list'] = $_list;
        $data['all_ping'] = $all_ping;
        if (!empty($user_ids)) {
            $data['users'] = D('Users')->itemsByIds($user_ids);
        }
        if (!empty($dianping_ids)) {
            $data['pics'] = D('Shopdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select();
        }
        $data['detail'] = $detail;
        $data['ex'] = D('Shopdetails')->find($shop_id);
        $data['tuans'] =D('Tuan')->order(' tuan_id desc ')->where(array('shop_id' => $shop_id, 'audit' => 1, 'closed' => 0, 'bg_date' => array('ELT', TODAY), 'end_date' => array('EGT', TODAY)))->limit(0, 4)->select();
        D('Shop')->updateCount($shop_id, 'view');
        $sb = D('ShopBranch');
        $rsb = $sb -> where('shop_id ='.$detail['shop_id']) -> count();
        $data['rsb'] = $rsb;
        if ($this->uid) {
            D('Userslook')->look($this->uid, $shop_id);
        }
        $data['status'] = self::BAO_REQUEST_SUCCESS;
        $this->stringify($data);
    }

    public function favorites() {
        if (empty($this->uid)) {
            $data = array('status'=>self::BAO_LOGIN_ERROR,'msg'=>'登录状态失效');
            $this->stringify($data);
        }
        $shop_id = (int) $this->_get('shop_id');
        if (!$detail = D('Shop')->find($shop_id)) {
            $data = array('status'=>self::BAO_DB_ERROR,'msg'=>'没有该商家');
            $this->stringify($data);
        }
        if ($detail['closed']) {
            $data = array('status'=>self::BAO_DB_ERROR,'msg'=>'该商家已经被删除');
            $this->stringify($data);
        }
        if (D('Shopfavorites')->check($shop_id, $this->uid)) {
            $data = array('status'=>self::BAO_DB_ERROR,'msg'=>'您已经收藏过了！');
            $this->stringify($data);
        }
        $data = array(
            'shop_id' => $shop_id,
            'user_id' => $this->uid,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip()
        );
        if (D('Shopfavorites')->add($data)) {
          $data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>'恭喜您，收藏成功!');
          $this->stringify($data);
        }
        $data = array('status'=>self::BAO_DB_ERROR,'msg'=>'收藏失败!');
        $this->stringify($data);
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

}
