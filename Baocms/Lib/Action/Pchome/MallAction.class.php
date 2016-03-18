<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MallAction extends CommonAction {

    protected $goodscate = array('select1', 'select2', 'select3', 'select4', 'select5');


    public function _initialize() {
        parent::_initialize();
        $this->goodscate = D('Goodscate')->fetchAll();
        $this->assign('goodscate', $this->goodscate);
        $this->type = D('Keyword')->fetchAll();
        $this->assign('types', $this->type);
        $goods = cookie('goods');
        $this->assign('cartnum', (int) array_sum($goods));
				$this->assign('attrs', D('Goodscateattr')->fetchAll());

    }

    public function main() {
        
        
        /*
        $mcates = $this->_CONFIG['mall'];
        $cates = array();
        for ($i = 1; $i <= count($mcates); $i+=1) {
            if ($i % 2 == 0) {
                $ii = $i / 2;
                if (!empty($mcates['ming' . $ii]) && !empty($mcates['dian' . $ii])) {
                    $cates[$mcates['dian' . $ii]] = array('cate_name' => $mcates['ming' . $ii], 'cate_id' => $mcates['dian' . $ii]);
                }
            }
        }
        $this->assign('cates', $cates);
        
        $channels = $this->template_setting['setting']['floor'];
        $goods = array();
        foreach ($channels as $k => $val) {
            $cate_ids = D('Goodscate')->getChildren($val['value'], false);
            $cates = D('Goodscate')->where(array('cate_id' => array('IN', $cate_ids)))->order(array('orderby' => 'asc'))->select();
            $channels[$k]['cates'] = $cates;
            foreach ($cate_ids as $cate) {

                $goods[$cate] = D('Goods')->where(array('cate_id' => $cate, 'audit' => 1, 'closed' => 0))->order(array('orderby' => 'asc', 'goods_id' => 'desc'))->limit(0,8)->select();
            }
        }
 
        $this->assign('goods', $goods);
        $this->assign('channels', $channels);
        $this->display(); // 输出模板 
         * /
         */
        $this->index();
    }

    public function index() {
        $Goods = D('Goods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1, 'city_id' => $this->city_id, 'end_date' => array('EGT', TODAY));
        $linkArr = array();
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
            $linkArr['keywrod'] = $map['title'];
        }
        $cat = (int) $this->_param('cat');
        $cate_id = (int) $this->_param('cate_id');
		$cates = (int) $this->_param('cates');
        if ($cat) {	
            if (!empty($cates)) {
				$catids = D('Goodscate')->getChildren($cates);
                $map['cate_id'] = array('IN', $catids);
                $this->seodatas['cate_name'] = $this->goodscate[$cates]['cate_name'];
				$linkArr['cat'] = $cat;
                $linkArr['cates'] = $cates;				
			}
			
			 else {				
                $catids = D('Goodscate')->getChildren($cat);
                if (!empty($catids)) {
                    $map['cate_id'] = array('IN', $catids);
                }
				
                $this->seodatas['cate_name'] = $this->goodscate[$cat]['cate_name'];
                $linkArr['cat'] = $cat;
            }
			 if (!empty($cate_id)) {
				if ($cate = $this->goodscate[$cate_id]) {
                $map['cate_id'] = $cate_id;
                $this->seodatas['cate_name'] = $this->goodscate[$cate_id]['cate_name'];
				$linkArr['cat'] = $cat;
				$linkArr['cates'] = $cates;	
                $linkArr['cate_id'] = $cate_id;
				$this->assign('cate', $cate);
				$attrs = D('Goodscateattr')->getAttrs($cate_id);
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($cate['select' . $i])) {
                        $s{$i} = (int) $this->_param('s' . $i);
                        if ($attrs['select' . $i][$s{$i}]) {
							$map['select' . $i] = $s{$i};//解决搜索问题
                            $this->assign('s' . $i, $s{$i});
                            $linkArr['s' . $i] = $s{$i};
                       }
                    }
                }
                $this->assign('attrs', $attrs);
            }
			}   	   	
        }
        $this->assign('cat', $cat);
		$this->assign('cates', $cates);
        $this->assign('cate_id', $cate_id);		
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

        $order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case 's':
                $orderby = array('sold_num' => 'desc');
                $linkArr['order'] = $order;
                break;
            case 'p':
                $orderby = array('mall_price' => 'asc');
                $linkArr['order'] = $order;
                break;
            case 'v':
                $orderby = array('views' => 'asc');
                $linkArr['order'] = $order;
                break;
            default:
                $orderby = array('orderby' => 'asc', 'sold_num' => 'desc', 'goods_id' => 'desc');
                $linkArr['order'] = $order;
                break;
        }
        $this->assign('order', $order);
        $count = $Goods->where($map)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Goods->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $v) {
            $list[$k] = $Goods->_format($v);
        }
        $selArr = $linkArr;
        foreach ($selArr as $k => $val) {
            if ($k == 'order') {
                unset($selArr[$k]);
            }
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('selArr', $selArr);
        $this->assign('linkArr', $linkArr);
        $this->display('index');
    }

    public function shoplist() {
        $Shop = D('Shop');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1, 'is_mall' => 1);
        $count = $Shop->where($map)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shop->where($map)->order(array('orderby' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }

    public function shop() {
        if (!$shop_id = (int) $this->_param('shop_id')) {
            $this->error('该商家不存在');
        }
        if (!$shop = D('Shop')->find($shop_id)) {
            $this->error('该商家不存在');
        }
        if (!$shop['is_mall']) {
            $this->error('该商家不存在');
        }
        $this->assign('shop_id', $shop_id);
        $this->assign('shop', $shop);
        $this->assign('details', D('Shopdetails')->find($shop_id));
        $this->assign('cates', D('Goodsshopcate')->where(array('shop_id' => $shop_id))->select());
        D('Shop')->updateCount($shop_id, 'view');
        $this->seodatas['shop_name'] = $shop['shop_name'];
        $Goods = D('Goods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'audit' => 1);
        $linkArr = array('shop_id' => $shop_id);

        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['shopcate_id'] = $cat;
            $linkArr['cat'] = $cat;
        }
        $this->assign('cat', $cat);


        $linkArr['order'] = $order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case 's':
                $orderby = array('sold_num' => 'desc');
                break;
            case 'p':
                $orderby = array('mall_price' => 'desc');
                break;
            case 'v':
                $orderby = array('views' => 'desc');
            default:
                $order = 'd';
                $orderby = array('orderby' => 'asc', 'sold_num' => 'desc', 'goods_id' => 'desc');
                break;
        }
        $this->assign('order', $order);


        $count = $Goods->where($map)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Goods->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $v) {
            $list[$k] = $Goods->_format($v);
        }

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('linkArr', $linkArr);
        $this->display();
    }

    public function cartdel() {
        $goods_id = (int) $_POST['goods_id'];
        $goods = cookie('goods');
        if (isset($goods[$goods_id])) {
            unset($goods[$goods_id]);
            cookie('goods', $goods, 604800);
            $this->ajaxReturn(array('status'=>'success','msg'=>'删除成功'));
        }else{
            $this->ajaxReturn(array('status'=>'error','msg'=>'删除失败'));
        }
    }



    public function cart() {
        $order_id = (int) $_GET['order_id'];
        if (!empty($order_id)) {
            $order = D('Order')->find($order_id);
            if($order['user_id'] != $this->uid){
                $this->error("不能修改别人的订单!", U('mall/index'));
            }
            if($order['status'] != 0){
                $this->error("该订单不能修改!", U('mall/index'));
            }
            $order_goods = D('Ordergoods')->where(array('order_id' => $order_id))->select();
            $goods_ids = $nums = array();
            foreach ($order_goods as $key => $v) {
                $goods_ids[$v['goods_id']] = $v['goods_id'];
                $nums[$v['goods_id']] = $v['num'];
            }
            $cart_goods = D('Goods')->itemsByIds($goods_ids);
            $shop_ids = array();
            foreach ($cart_goods as $k => $val) {
                $cart_goods[$k]['buy_num'] = $nums[$k];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $this->assign('order_id', $order_id);
            $this->assign('cart_shops', D('Shop')->itemsByIds($shop_ids));
            $this->assign('cart_goods', $cart_goods);
            $this->display('change_cart');
        } else {
            $goods = cookie('goods');
            if (empty($goods)) {
                $this->error("亲还没有选购产品呢!", U('mall/index'));
            }
            $goods_ids = array_keys($goods);
            $cart_goods = D('Goods')->itemsByIds($goods_ids);
            $shop_ids = array();
            foreach ($cart_goods as $k => $val) {
                $cart_goods[$k]['buy_num'] = $goods[$k];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $this->assign('cart_shops', D('Shop')->itemsByIds($shop_ids));
            $this->assign('cart_goods', $cart_goods);
            $this->display();
        }
    }

    public function ajaxcart() {
        $goods = cookie('goods');
        if (!empty($goods)) {
            $goods_ids = array_keys($goods);
            $cart_goods = D('Goods')->itemsByIds($goods_ids);
            $shop_ids = array();
            foreach ($cart_goods as $k => $val) {
                $cart_goods[$k]['buy_num'] = $goods[$k];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $this->assign('cart_shops', D('Shop')->itemsByIds($shop_ids));
            $this->assign('cart_goods', $cart_goods);
        }
        $this->display();
    }

    public function ajaxcartlist() {
        $goods = cookie('goods');
        if (!empty($goods)) {
            $goods_ids = array_keys($goods);
            $cart_goods = D('Goods')->itemsByIds($goods_ids);
            $shop_ids = array();
            foreach ($cart_goods as $k => $val) {
                $cart_goods[$k]['buy_num'] = $goods[$k];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $this->assign('cart_shops', D('Shop')->itemsByIds($shop_ids));
            $this->assign('cart_goods', $cart_goods);
        }
        $this->display();
    }

    public function detail($goods_id) {
        $goods_id = (int) $goods_id;
        if (!$detail = D('Goods')->find($goods_id)) {
            $this->error('您访问的产品不存在！');
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('您访问的产品不存在！');
        }

        $shop = D('Shop')->find($detail['shop_id']);
        if (!$favo = D('Shopfavorites')->where(array('user_id' => $this->uid, 'shop_id' => $shop_id))->find()) {
            $shop['favo'] = 0;
        } else {
            $shop['favo'] = 1;
        }
        $this->assign('shop', $shop);
        $shop_id = $detail['shop_id'];
        $this->assign('ex', D('Shopdetails')->find($shop_id));
        //开启cookie记录用户行为习惯，展示到“猜你喜欢”
        $cate_id = (int) $detail['cate_id'];
        $cookie = unserialize($_COOKIE['iLikegoods']); //取出cookie数组
        $cookie[] = $cate_id;
        $cookie = array_flip(array_flip($cookie));
        $cate_arr = serialize($cookie);
        cookie('iLikegoods', $cate_arr, 86400); // 指定cookie保存时间
        $like_where = array();
        $like_where['cate_id'] = array('in', $cookie);
        $like_where['audit'] = 1;
        $like_where['closed'] = 0;

        $like = D('Goods')->where($like_where)->order('rand()')->limit(5)->select();

        $this->assign('like', $like);
        $goodsdianping = D('Goodsdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'goods_id' => $goods_id, 'show_date' => array('ELT', TODAY));
        $count = $goodsdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $goodsdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
            $this->assign('pics', D('Goodsdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
        }
        $viewArr = cookie('viewgoods');
        $cooarr = array('goods_id' => $goods_id, 'title' => $detail['title'], 'price' => $detail['price'], 'mall_price' => $detail['mall_price'], 'photo' => $detail['photo']);
        if (!$viewArr) {
            cookie('viewgoods', serialize($cooarr[$detail['goods_id']]));
        } else {
            $viewArr = unserialize($viewArr);
            if (count($viewArr) == 5) {
                $arr = array_pop($viewArr);
                unset($arr);
            }
            if (!isset($viewArr[$detail['goods_id']])) {
                $viewArr[$detail['goods_id']] = $cooarr;
                cookie('viewgoods', serialize($viewArr));
            }
        }
        $viewgoods = unserialize(cookie('viewgoods'));
        $viewgoods = array_reverse($viewgoods, TRUE);
        $this->assign('viewgoods', $viewgoods);
        $this->assign('totalnum', $count);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        //分店  + 
        $maps = array('closed' => 0, 'audit' => 1, 'shop_id' => $detail['shop_id']);
        $lists = D('Shopbranch')->where($maps)->order(array('orderby' => 'asc'))->select();
        $shop_arr = array(
            'name' => '总店',
            'score' => $shop['score'],
            'score_num' => $shop['score_num'],
            'lng' => $shop['lng'],
            'lat' => $shop['lat'],
            'telephone' => $shop['tel'],
            'addr' => $shop['addr'],
        );
        if (!empty($lists)) {
            array_unshift($lists, $shop_arr);
        } else {
            $lists[] = $shop_arr;
        }
        $counts = count($lists);
        if ($counts % 5 == 0) {
            $num = $counts / 5;
        } else {
            $num = (int) ($counts / 5) + 1;
        }
        $this->assign('count', $counts);
        $this->assign('totalnums', $num);
        $this->assign('lists', $lists);
        //分店end
		 //丶carrot 全局递归用。
		$goodsids = $detail['cate_id'];
		$this->assign('goodsids', $goodsids);
		
		//----------------。
        $this->seodatas['shop_name'] = $shop['shop_name'];
        $this->seodatas['title'] = $detail['title'];
        D('Goods')->updateCount($goods_id, 'views');
		$this->assign('pics', D('Goodsphoto')->getPics($detail['goods_id']));
		 //丶carrot 。
		$this->cates = D('Goodscate')->fetchAll();
        $this->assign('cates', $this->cates);
		$this->assign('cate', $this->cates[$detail['cate_id']]);
        $this->assign('detail', $detail);
		$attrs = D('Goodscateattr')->getAttrs($detail['cate_id']);
		 $this->assign('attrs', $attrs);
	 //丶carrot 。
	 
	  /** 修复评论用户等级不显示 */
       $userrank = D('user_rank')-> select();
       $this -> assign('userrank',$userrank);
       /**修复等级不显示 end**/
	   
	   
        $this->assign('height_num', 675);
        $this->display();
    }

    public function emptygoods() {
        cookie('viewgoods', null);
        $this->ajaxReturn(array('status' => 'success', 'msg' => '清空成功'));
    }

    public function get_like() {

        if (IS_AJAX) {

            $cookie = unserialize($_COOKIE['iLikegoods']); //取出cookie数组
            //查询我喜欢的内容
            $like_where = array();
            $like_where['cate_id'] = array('in', $cookie);

            $likes = D('Goods')->where($like_where)->order('rand()')->limit(5)->select();

            if ($likes) {
                $this->ajaxReturn(array('status' => 'success', 'likes' => $likes));
            } else {
                $this->ajaxReturn(array('status' => 'error', 'message' => '换一换失败！'));
            }
        }
    }

    public function cartadd() {
        $goods_id = (int) $this->_param('goods_id');
        if (empty($goods_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '请选择产品'));
        }
        if (!$detail = D('Goods')->find($goods_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
        }
        if ($detail['end_date'] < TODAY) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品已经过期，暂时不能购买'));
        }		
		if ($detail['num'] <= 0) {
           $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！没有库存了！'));
        }
        $goods = cookie('goods');
        $num = (int) $this->_get('num');
        if (empty($num) || $num <= 0) {
            $num = 1;
        }
        if ($num > 99) {
            $num = 99;
        }		
		if ($detail['num'] <= $num) {
           $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！该商品只剩'.$detail['num'].'件了，少买点吧！'));
        }			
        if (isset($goods[$goods_id])) {
            $goods[$goods_id] = $goods[$goods_id] + $num;
        } else {
            $goods[$goods_id] = $num;
        }
        cookie('goods', $goods, 604800);		
        $this->ajaxReturn(array('status' => 'success', 'msg' => '添加购物车成功'));
    }
	public function cartadd3() {
        $goods_id = (int) $this->_param('goods_id');
        if (empty($goods_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '请选择产品'));
        }
        if (!$detail = D('Goods')->find($goods_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
        }
        if ($detail['end_date'] < TODAY) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品已经过期，暂时不能购买'));
        }
		if ($detail['num'] <= 0) {
           $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！没有库存了！'));
        }
		
        $goods = cookie('goods');
        if (isset($goods[$goods_id])) {
            $goods[$goods_id] = $goods[$goods_id] + 1;
        } else {
            $goods[$goods_id] = 1;
        }
		if ($detail['num'] <= $num) {
           $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！该商品只剩'.$detail['num'].'件了，少买点吧！'));
        }		
        cookie('goods', $goods, 604800);
		$this->ajaxReturn(array('status' => 'success', 'msg' => '添加购物车成功'));	
			
        die('0');
		
    }

    public function cartadd2($goods_id) {
        $goods_id = (int) $goods_id;
        if (empty($goods_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '请选择产品'));
        }
        if (!$detail = D('Goods')->find($goods_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
        }
        if ($detail['end_date'] < TODAY) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品已经过期，暂时不能购买'));
        }
			if ($detail['num'] <= 0) {
           $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！没有库存了！'));
        }
        $goods = cookie('goods');
        $num = (int) $this->_get('num');
        if (empty($num) || $num <= 0) {
            $num = 1;
        }
        if ($num > 99) {
            $num = 99;
        }
	
		if ($detail['num'] <= $num) {
           $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！该商品只剩'.$detail['num'].'件了，少买点吧！'));
        }			
        if (isset($goods[$goods_id])) {
            $goods[$goods_id] = $goods[$goods_id] + $num;
        } else {
            $goods[$goods_id] = $num;
        }
        cookie('goods', $goods, 604800);
        $this->ajaxReturn(array('status' => 'success', 'msg' => '加入购物车成功,正在跳转到购物车', 'url' => U('mall/cart')));
    }

    public function neworder() {
        $goods = $this->_get('goods');
        $goods = explode(',', $goods);
        if (empty($goods)) {
            $this->error('亲购买点吧');
        }
        $datas = array();
        foreach ($goods as $val) {
            $good = explode('-', $val);
            $good[1] = (int) $good[1];
            if (empty($good[0]) || empty($good[1])) {
                $this->error('亲购买点吧');
            }
            if ($good[1] > 99 || $good[1] < 0) {
                $this->error('本店不支持批发');
            }
            $datas[$good[0]] = $good[1];
        }
        cookie('goods', $datas, 604800);
        header("Location:" . U('mall/cart'));
        die;
    }

    public function order_change() {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login'));
        }
        $order_id = (int) $_POST['order_id'];
        $order = D('Order')->find($order_id);
        if($order['user_id'] != $this->uid){
            $this->ajaxReturn(array('status' => 'error', 'msg' => '不能修改别人的订单'));
        }
        if($order['status'] != 0){
            $this->error("该订单不能修改!", U('mall/index'));
        }
        $num = $this->_post('num', false);
        $goods_ids = array();
        foreach ($num as $k => $val) {
            $val = (int) $val;
            if (empty($val)) {
                unset($num[$k]);
            } elseif ($val < 1 || $val > 99) {
                unset($num[$k]);
            } else {
                $goods_ids[$k] = (int) $k;
            }
        }
        if (empty($goods_ids))
            $this->ajaxReturn(array('status' => 'error', 'msg' => '很抱歉请填写正确的购买数量'));
        $goods = D('Goods')->itemsByIds($goods_ids);
        foreach ($goods as $key => $val) {
            if ($val['closed'] != 0 || $val['audit'] != 1 || $val['end_date'] < TODAY || $val['num'] < 0 ) {
                unset($goods[$key]);
            }
        }
        if (empty($goods)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '很抱歉，您提交的产品暂时不能购买！'));
        }
		if ($val['num'] < $num[$val['goods_id']]) {
            $this->baoError('亲！商品<'.$val['title'].'>库存不够了,只剩'.$val['num'].'件了！');
        }
        $tprice = 0;
        $ip = get_client_ip();
        $ordergoods = $total_price = array();
        $can_use_integral = 0;
        foreach ($goods as $val) {
            $price = $val['mall_price'] * $num[$val['goods_id']];
            $can_use_integral+= $val['use_integral'] * $num[$val['goods_id']];
            $js_price = $val['settlement_price'] * $num[$val['goods_id']];
            $tprice+= $price;
            $ordergoods = array(
                'num' => $num[$val['goods_id']],
                'price' => $val['mall_price'],
                'total_price' => $price,
                'js_price' => $js_price,
                'update_time' => NOW_TIME,
                'update_ip' => $ip,
            );
            D('Ordergoods')->where(array('order_id' => $order_id, 'goods_id' => $val['goods_id']))->setField($ordergoods); //忽略报错
        }
        if(false !== D('Order')->save(array('order_id'=>$order_id,'can_use_integral'=>$can_use_integral,'total_price'=>$tprice,'update_time'=>NOW_TIME,'update_ip'=>$ip))){
            $this->ajaxReturn(array('status' => 'success', 'msg' => '成功修改订单，正在跳转到支付页面','url'=>U('mall/pay', array('order_id' => $order_id))));
        }else{
            $this->ajaxReturn(array('status' => 'error', 'msg' => '修改订单失败'));
        }
        
    }

    public function order() {
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        $num = $this->_post('num', false);
        $goods_ids = array();
        foreach ($num as $k => $val) {
            $val = (int) $val;
            if (empty($val)) {
                unset($num[$k]);
            } elseif ($val < 1 || $val > 99) {
                unset($num[$k]);
            } else {
                $goods_ids[$k] = (int) $k;
            }
        }
        if (empty($goods_ids))
            $this->baoError('很抱歉请填写正确的购买数量');
        $goods = D('Goods')->itemsByIds($goods_ids);
        foreach ($goods as $key => $val) {
            if ($val['closed'] != 0 || $val['audit'] != 1 || $val['end_date'] < TODAY || $val['num'] <= 0) {
                unset($goods[$key]);
            }
        }
        if (empty($goods)) {
            $this->baoError('很抱歉，您提交的产品暂时不能购买！');
        }
		if ($val['num'] < $num[$val['goods_id']]) {
            $this->baoError('亲！商品<'.$val['title'].'>库存不够了,只剩'.$val['num'].'件了！');
        }
		
        $tprice = 0;
        $ip = get_client_ip();
        $ordergoods = $total_price = array();
        $canuserintegral = array();
        foreach ($goods as $val) {
            $price = $val['mall_price'] * $num[$val['goods_id']];
            $js_price = $val['settlement_price'] * $num[$val['goods_id']];
            $tprice+= $price;
            $canuserintegral[$val['shop_id']] += $val['use_integral'] * $num[$val['goods_id']];
            $ordergoods[$val['shop_id']][] = array(
                'goods_id' => $val['goods_id'],
                'shop_id' => $val['shop_id'],
                'num' => $num[$val['goods_id']],
                'price' => $val['mall_price'],
                'total_price' => $price,
                'js_price' => $js_price,
                'create_time' => NOW_TIME,
                'create_ip' => $ip
            );
            $total_price[$val['shop_id']] += $price;
        }

        //总订单
        $order = array(
            'user_id' => $this->uid,
            'create_time' => NOW_TIME,
            'create_ip' => $ip,
            'total_price' => 0
        );
        $order_ids = array();
        foreach ($ordergoods as $k => $val) {
            $shop = D('Shop')->find($k);
            $order['shop_id'] = $k;
            $order['can_use_integral'] = $canuserintegral[$k];
            $order['total_price'] = $total_price[$k];
            $order['is_shop'] = (int) $shop['is_pei']; //是否由商家自己配送

            if ($order_id = D('Order')->add($order)) {
                $order_ids[] = $order_id;
                foreach ($val as $k1 => $val1) {
                    $val1['order_id'] = $order_id;
                    D('Ordergoods')->add($val1);
                }
            }
        }
        cookie('goods', null);
        if (count($order_ids) > 1) {//如果大于1 那么形成一个 支付记录 来合并付款！如果其他条件可以直接去付款
            $need_pay = D('Order')->useIntegral($this->uid,$order_ids);
            $logs = array(
                'type' => 'goods',
                'user_id' => $this->uid,
                'order_id' => 0,
                'order_ids' => join(',', $order_ids),
                'code' => '',
                'need_pay' => $need_pay, //使用积分后的支付金额
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
                'is_paid' => 0
            );
            $logs['log_id'] = D('Paymentlogs')->add($logs);
            $this->baoJump(U('mall/paycode', array('log_id' => $logs['log_id'])));
        } else {
            $this->baoJump(U('mall/pay', array('order_id' => $order_id)));
        }
		
    }

    public function change_addr() {
        if (IS_AJAX) {
            $order_id = (int) $_POST['order_id'];
            $addr_id = (int) $_POST['addr_id'];
            $data = array(
                'order_id' => $order_id,
                'addr_id' => $addr_id,
            );
            if (false !== D('Order')->save($data)) {
                $thisaddr = D('Useraddr')->find($addr_id);
                $addrs = D('Useraddr')->where(array('user_id' => $this->uid, 'addr_id' => array('NEQ', $addr_id)))->order('addr_id DESC')->limit(0, 4)->select();
                if (empty($addrs)) {
                    $addrs[] = $thisaddr;
                } else {
                    array_unshift($addrs, $thisaddr);
                }
                $addr_array = array();
                foreach ($addrs as $k => $val) {
                    $addr_array[$k]['addr_id'] = $val['addr_id'];
                    $addr_array[$k]['city_id'] = $val['city_id'];
                    $addr_array[$k]['area_id'] = $val['area_id'];
                    $addr_array[$k]['business_id'] = $val['business_id'];
                    $addr_array[$k]['city'] = $this->citys[$val['city_id']]['name'];
                    $addr_array[$k]['area'] = $this->areas[$val['area_id']]['area_name'];
                    $addr_array[$k]['bizs'] = $this->bizs[$val['business_id']]['business_name'];
                    $addr_array[$k]['name'] = $val['name'];
                    $addr_array[$k]['addr'] = $val['addr'];
                    $addr_array[$k]['mobile'] = $val['mobile'];
                }
                $this->ajaxReturn(array('status' => 'success', 'msg' => '更换成功', 'res' => $addr_array));
            } else {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '更换失败'));
            }
        }
    }

    public function paycode() {
        $log_id = (int) $this->_get('log_id');
        if (empty($log_id)) {
            $this->error('没有有效支付记录！');
        }
        if (!$detail = D('Paymentlogs')->find($log_id)) {
            $this->error('没有有效的支付记录！');
        }
        if ($detail['is_paid'] != 0 || empty($detail['order_ids']) || !empty($detail['order_id']) || empty($detail['need_pay'])) {
            $this->error('没有有效的支付记录！');
        }
        $order_ids = explode(',', $detail['order_ids']);
        $ordergood = D('Ordergoods')->where(array('order_id' => array('IN', $order_ids)))->select();

        $goods_id = $shop_ids = array();

        foreach ($ordergood as $k => $val) {
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('goods', D('Goods')->itemsByIds($goods_id));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('ordergoods', $ordergood);
        if (!empty($order['addr_id'])) {
            $thisaddr = D('Useraddr')->find($order['addr_id']);
            $addrs = D('Useraddr')->where(array('user_id' => $this->uid, 'addr_id' => array('NEQ', $order['addr_id'])))->order('addr_id DESC')->limit(0, 4)->select();
            if (empty($addrs)) {
                $addrs[] = $thisaddr;
            } else {
                array_unshift($addrs, $thisaddr);
            }
        } else {
            $addrs = D('Useraddr')->where(array('user_id' => $this->uid))->order(array('is_default' => 'desc', 'addr_id' => 'desc'))->limit(0, 5)->select();
        }
        $this->assign('useraddr', $addrs);
        $this->assign('payment', D('Payment')->getPayments());
        $this->assign('logs', $detail);
        $this->display();
    }

    public function pay() {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
        $order_id = (int) $this->_get('order_id');
        $order = D('Order')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->error('该订单不存在');
            die;
        }
        $ordergood = D('Ordergoods')->where(array('order_id' => $order_id))->select();

        $goods_id = $shop_ids = array();

        foreach ($ordergood as $k => $val) {
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('goods', D('Goods')->itemsByIds($goods_id));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('ordergoods', $ordergood);
        if (!empty($order['addr_id'])) {
            $thisaddr = D('Useraddr')->find($order['addr_id']);
            $addrs = D('Useraddr')->where(array('user_id' => $this->uid, 'addr_id' => array('NEQ', $order['addr_id'])))->order('addr_id DESC')->limit(0, 4)->select();
            if (empty($addrs)) {
                $addrs[] = $thisaddr;
            } else {
                array_unshift($addrs, $thisaddr);
            }
        } else {
            $addrs = D('Useraddr')->where(array('user_id' => $this->uid))->order(array('is_default' => 'desc', 'addr_id' => 'desc'))->limit(0, 5)->select();
        }
        $this->assign('useraddr', $addrs);
        $this->assign('order', $order);
        $this->assign('payment', D('Payment')->getPayments());
        $this->display();
    }

    public function paycode2() { //这里是因为原来的是按订单付，这里是合并付款逻辑部分 
        $log_id = (int) $this->_get('log_id');
        if (empty($log_id)) {
            $this->error('没有有效支付记录！');
        }
        if (!$detail = D('Paymentlogs')->find($log_id)) {
            $this->error('没有有效的支付记录！');
        }
        if ($detail['is_paid'] != 0 || empty($detail['order_ids']) || !empty($detail['order_id']) || empty($detail['need_pay'])) {
            $this->error('没有有效的支付记录！');
        }
        $order_ids = explode(',', $detail['order_ids']);
        $addr_id = (int) $this->_post('addr_id');
        if (empty($addr_id)) {
            $this->baoError('请选择一个要配送的地址！');
        }
        D('Order')->where(array('order_id' => array('IN', $order_ids)))->save(array('addr_id' => $addr_id));
        if (!$code = $this->_post('code')) {
            $this->baoError('请选择支付方式！');
        }
        if ($code == 'wait') { //如果是货到付款
            D('Order')->save(array(
                'is_daofu' => 1,
                    ), array('where' => array('order_id' => array('IN', $order_ids))));
            D('Ordergoods')->save(array(
                'is_daofu' => 1,
                    ), array('where' => array(
                    'order_id' => array('IN', $order_ids)
            )));
            D('Sms')->mallTZshop($order_ids);
             D('Payment')->mallSold($order_ids);
            D('Payment')->mallPeisong(array($order_ids),1);
            $this->baoSuccess('恭喜您下单成功！', U('member/order/goods'));
        } else {
            $payment = D('Payment')->checkPayment($code);
            if (empty($payment)) {
                $this->baoError('该支付方式不存在');
            }
            $detail['code'] = $code;
            D('Paymentlogs')->save($detail);
            $this->baoJump(U('mall/combine', array('log_id' => $detail['log_id'])));
        }
    }

    public function combine() {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
        $log_id = (int) $this->_get('log_id');
        if (!$detail = D('Paymentlogs')->find($log_id)) {
            $this->error('没有有效的支付记录！');
        }
        if ($detail['is_paid'] != 0 || empty($detail['order_ids']) || !empty($detail['order_id']) || empty($detail['need_pay'])) {
            $this->error('没有有效的支付记录！');
        }
        $url =  U('mall/paycode',array('order_id'=>$logs['order_id']));
        $this->assign('url',$url);
        $this->assign('button', D('Payment')->getCode($detail));
        $this->assign('logs', $detail);
        $this->assign('types', D('Payment')->getTypes());
        $this->assign('paytype', D('Payment')->getPayments());
        
        $this->display();
    }

    public function pay2() {
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        $order_id = (int) $this->_get('order_id');
        $order = D('Order')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->baoError('该订单不存在');
            die;
        }
        $addr_id = (int) $this->_post('addr_id');
        if (empty($addr_id)) {
            $this->baoError('请选择一个要配送的地址！');
        }
        D('Order')->save(array('addr_id' => $addr_id, 'order_id' => $order_id));
        if (!$code = $this->_post('code')) {
            $this->baoError('请选择支付方式！');
        }
        $ua = D('UserAddr');
        $uaddr = $ua -> where(array('addr_id'=>$order['addr_id'])) -> find();
        if ($code == 'wait') { //如果是货到付款
            D('Order')->save(array(
                'order_id' => $order_id,
                'is_daofu' => 1,
            ));
            D('Ordergoods')->save(array(
                'is_daofu' => 1,
                    ), array('where' => array(
                    'order_id' => $order_id
            )));
            D('Payment')->mallSold($order_id);
            D('Payment')->mallPeisong(array($order_id),1);
            $goods_ids   = D('Ordergoods')->where("order_id=".$order['order_id'])->getField('goods_id',true);
            $goods_ids   = implode(',', $goods_ids);
            $map         = array('goods_id'=>array('in',$goods_ids));
            $goods_name  = D('Goods')->where($map)->getField('title',true);
            $goods_name  = implode(',', $goods_name);
            //====================微信支付通知===========================
             
            include_once "Baocms/Lib/Net/Wxmesg.class.php";
            $_data_pay = array(
                'url'       =>  "http://".$_SERVER['HTTP_HOST']."/mcenter/goods/index/aready/".$order['order_id'].".html",
                'topcolor'  =>  '#F55555',
                'first'     =>  '亲,您的货到付款订单我们已经收到,我们马上发货！',
                'remark'    =>  '更多信息,请登录http://www.baocms.cn！再次感谢您的惠顾！',
                'money'     =>  round($order['total_price']/100,2).'元',
                'orderInfo' =>  $goods_name,
                'addr'      =>  $uaddr['addr'],
                'orderNum'  =>  $order['order_id']
            );
            $pay_data = Wxmesg::pay($_data_pay);
            $return   = Wxmesg::net($this->uid, 'OPENTM201490088', $pay_data);

            //====================微信支付通知==============================
	            $this->baoSuccess('恭喜您下单成功！', U('member/order/goods'));
        } else {
            $payment = D('Payment')->checkPayment($code);
            if (empty($payment)) {
                $this->baoError('该支付方式不存在');
            }
            
            $logs = D('Paymentlogs')->getLogsByOrderId('goods', $order_id);
            $need_pay = D('Order')->useIntegral($this->uid,array($order_id));
            if (empty($logs)) {
                $logs = array(
                    'type' => 'goods',
                    'user_id' => $this->uid,
                    'order_id' => $order_id,
                    'code' => $code,
                    'need_pay' => $need_pay,
                    'create_time' => NOW_TIME,
                    'create_ip' => get_client_ip(),
                    'is_paid' => 0
                );
                $logs['log_id'] = D('Paymentlogs')->add($logs);
            } else {
                $logs['need_pay'] = $need_pay;
                $logs['code'] = $code;
                D('Paymentlogs')->save($logs);
            }

            $goods_ids   = D('Ordergoods')->where("order_id=".$order['order_id'])->getField('goods_id',true);
            $goods_ids   = implode(',', $goods_ids);
            $map         = array('goods_id'=>array('in',$goods_ids));
            $goods_name  = D('Goods')->where($map)->getField('title',true);
            $goods_name  = implode(',', $goods_name);
            //====================微信支付通知===========================
             
            include_once "Baocms/Lib/Net/Wxmesg.class.php";
            $_data_pay = array(
                'url'       =>  "http://".$_SERVER['HTTP_HOST']."/mcenter/goods/index/aready/".$order['order_id'].".html",
                'topcolor'  =>  '#F55555',
                'first'     =>  '亲,您的在线支付订单已创建,请尽快支付！',
                'remark'    =>  '更多信息,请登录http://'.$_SERVER['HTTP_HOST'].'！再次感谢您的惠顾！',
                'money'     =>  round($order['total_price']/100,2).'元',
                'orderInfo' =>  $goods_name,
                'addr'      =>  $uaddr['addr'],
                'orderNum'  =>  $order['order_id']
            );
            $pay_data = Wxmesg::pay($_data_pay);
            $return   = Wxmesg::net($this->uid, 'OPENTM201490088', $pay_data);

            //====================微信支付通知==============================

            $this->baoJump(U('payment/payment', array('log_id' => $logs['log_id'])));
        }
    }

}
