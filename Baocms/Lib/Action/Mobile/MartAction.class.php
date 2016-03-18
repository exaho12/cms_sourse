
<?php

class MartAction extends CommonAction{

    public function _initialize(){
        parent::_initialize();
        $this->_cart = $this->getcart();
        $cates = d( "Weidiancate" )->fetchAll();
        $this->assign( "cates", $cates );
    }
	
	public function main() {
        $map = array('audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY));
        $order = (int) $this->_param('order');
        switch ($order) {
            case 2:
                $orderby = array('sold_num' => 'desc');
                break;
            case 3:
                $orderby = array('goods_id' => 'desc');
                break;
            default:
                $orderby = array('mall_price' => 'asc' ,'orderby' => 'asc' );
                break;
        }
        $this->assign('order',$order);
        $list = D('Weidian')->order($orderby)->where($map)->limit(0, 10)->select();
        $this->assign('list',$list);
        $this->display();
    }
	
    public function getcart( ){
        $id = ( integer )$this->_param( "id" );
        $wd = d( "WeidianDetails" )->find( $id );
        $cart = ( array )json_decode( $_COOKIE['mall'] );
        $carts = array( );
        foreach ( $cart as $kk => $vv ){
            foreach ( $vv as $key => $v ){
                $v = ( array )$v;
                $carts[$kk][$key] = $v;
                if ( $v['num'] == 0 ){
                    unset( $Var_168[$key] );
                }
            }
        }
        $ids = $nums = array( );
        foreach ( $carts[$wd['shop_id']] as $k => $val ){
            $ids[$val['goods_id']] = $val['goods_id'];
            $nums[$val['goods_id']] = $val['num'];
        }
        $goods = d( "Goods" )->itemsByIds( $ids );
        foreach ( $goods as $k => $val ){
            $goods[$k]['cart_num'] = $nums[$val['goods_id']];
            $goods[$k]['total_price'] = $nums[$val['goods_id']] * $val['mall_price'];
        }
        return $goods;
    }

    public function index( ){
        $cat = ( integer )$this->_param( "cat" );
        $this->assign( "cat", $cat );
        $linkArr['cat'] = $cat;
        $this->assign( "nextpage", linkto( "mart/loaddata", $linkArr, array("t" => NOW_TIME,"p" => "0000")));
        $this->assign( "linkArr", $linkArr );
        $this->display( );
    }

    public function loaddata( ){
        $weidian = d( "WeidianDetails" );
        import( "ORG.Util.Page" );
		
		
        $map = array("audit" => 1,'closed' => 0,"city_id" => $this->city_id );
        
		//微店二级分类开始
		$cat = (int) $this->_param('cat');
        $cates = D('Weidiancate')->fetchAll();
        if ($cates[$cat]) {
            $catids = D('Weidiancate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
		//微店二级分类结束
		
		
        $lat = addslashes( cookie( "lat" ) );
        $lng = addslashes( cookie( "lng" ) );
        if ( empty( $lat ) || empty( $lng ) ){
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $count = $weidian->where( $map )->count( );
        
        $Page = new Page( $count, 8 );
        $show = $Page->show( );
        $var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
        $p = $_GET[$var];
        if ( $Page->totalPages < $p ){
            exit( "0" );
        }
        $list = $weidian->order( "(ABS(lng - '".$lng."') +  ABS(lat - '{$lat}') ) asc" )->where( $map )->limit( $Page->firstRow.",".$Page->listRows )->select( );
        foreach ( $list as $k => $val ){
            $list[$k]['d'] = getdistance( $lat, $lng, $val['lat'], $val['lng'] );
        }
        $shop_ids = array( );
        foreach ( $list as $key => $v ){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }
        $shopdetails = d( "Shopdetails" )->itemsByIds( $shop_ids );
        foreach ( $list as $k => $val ){
            $list[$k]['price'] = $shopdetails[$val['shop_id']]['price'];
        }
        $this->assign( "linkArr", $linkArr );
        $this->assign( "list", $list );
        $this->assign( "page", $show );
        $this->display( );
    }

    public function lists( ){
        $id = ( integer )$this->_get( "id" );
        $wd = d( "WeidianDetails" )->find( $id );
        if ( !( $detail = d( "WeidianDetails" )->find( $id ) ) ) {
            $this->error( "没有该微店商家" );
            exit( );
        }
        if ( $detail['audit'] != 1 ){
            $this->error( "没有该微店商家" );
            exit( );
        }
        $autocates = d( "Goodsshopcate" )->order( array("orderby" => "asc") )->where( array("shop_id" => $wd['shop_id']))->select( );
        $Goods = d( "Goods" );
        $map = array("closed" => 0,"audit" => 1,"city_id" => $this->city_id,"shop_id" => $wd['shop_id'],"end_date" => array("EGT",TODAY));
        $list = $Goods->order( array( "sold_num" => "desc", "goods_id" => "desc" ) )->where( $map )->select( );
        foreach ( $list as $k => $val ){
            $list[$k]['cart_num'] = $this->_cart[$val['goods_id']]['cart_num'];
        }
        $this->assign( "list", $list );
        $this->assign( "autocates", $autocates );
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function detail( $goods_id ){
        $goods_id = ( integer )$goods_id;
        $detail = d( "Goods" )->find( $goods_id );
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function cart( ){
        $id = ( integer )$this->_param( "id" );
        if ( empty( $id ) ){
            $this->error( "参数不正确" );
        }
        $detail = d( "WeidianDetails" )->find( $id );
        if ( empty( $detail ) ){
            $this->error( "微店不存在" );
        }
        $goods = $this->_cart;
        if ( empty( $goods ) ){
            $this->error( "亲还没有选购产品呢!", u( "mart/lists", array("id" => $id)));
        }
        $this->assign( "cart_goods", $goods );
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function order( ){
        if ( empty( $this->uid ) ){
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $num = $this->_post( "num", FALSE );
        $goods_ids = array( );
        foreach ( $num as $k => $val ){
            $val = ( integer )$val;
            if ( empty( $val ) ){
                unset( $num[$k] );
            }
            else if ( $val < 1 || 99 < $val ){
                unset( $num[$k] );
            }
            else{
                $goods_ids[$k] = ( integer )$k;
            }
        }
        if ( empty( $goods_ids ) ){
            $this->error( "很抱歉请填写正确的购买数量" );
        }
		
		$goods = D('Goods')->itemsByIds($goods_ids);
        foreach ($goods as $key => $val) {
            if ($val['closed'] != 0 || $val['audit'] != 1 || $val['end_date'] < TODAY) {
                unset($goods[$key]);
            }
        }

        if (empty($goods)) {
            $this->error('很抱歉，您提交的产品暂时不能购买！');
        }
       
        $tprice = 0;
        $ip = get_client_ip( );
        $ordergoods = $total_price = array( );
        foreach ( $goods as $val ){
            $price = $val['mall_price'] * $num[$val['goods_id']];
            $js_price = $val['settlement_price'] * $num[$val['goods_id']];
            $mobile_fan = $val['mobile_fan'] * $num[$val['goods_id']];
            $m_price = $val['mall_price'] * $num[$val['goods_id']] - $val['mobile_fan'] * $num[$val['goods_id']];
            $tprice += $m_price;
            $ordergoods[$val['shop_id']][] = array(
                "goods_id" => $val['goods_id'],
                "shop_id" => $val['shop_id'],
                "num" => $num[$val['goods_id']],
                "price" => $val['mall_price'],
                "total_price" => $price,
                "mobile_fan" => $mobile_fan,
                "js_price" => $js_price,
                "create_time" => NOW_TIME,
                "create_ip" => $ip
            );
            $total_price[$val['shop_id']] += $m_price;
            $mm_price[$val['shop_id']] += $mobile_fan;
        }
        $order = array(
            "user_id" => $this->uid,
            "create_time" => NOW_TIME,
            "create_ip" => $ip,
            "total_price" => $total_price,
            "mobile_fan" => $mm_price
        );
        $order_ids = array( );
        foreach ( $ordergoods as $k => $val ){
            $order['shop_id'] = $k;
            $order['total_price'] = $total_price[$k];
            $shop = d( "Shop" )->find( $k );
            $order['is_shop'] = ( integer )$shop['is_pei'];
            if ( $order_id = d( "Order" )->add( $order ) ){
                $order_ids[] = $order_id;
                foreach ( $val as $k1 => $val1 ){
                    $val1['order_id'] = $order_id;
                    d( "Ordergoods" )->add( $val1 );
                }
            }
        }
        cookie( "mall", NULL );
        if ( 1 < count( $order_ids ) ){
            $need_pay = d( "Order" )->useIntegral( $this->uid, $order_ids );
            $logs = array(
                "type" => "goods",
                "user_id" => $this->uid,
                "order_id" => 0,
                "order_ids" => join( ",", $order_ids ),
                "code" => "",
                "need_pay" => $need_pay,
                "create_time" => NOW_TIME,
                "create_ip" => get_client_ip( ),
                "is_paid" => 0
            );
            $logs['log_id'] = d( "Paymentlogs" )->add( $logs );
			
			
            header( "Location:".u( "mart/paycode", array("log_id" => $logs['log_id'] ) ) );
			
			
            exit( );
        }
		
        header( "Location:".u( "mart/pay", array("order_id" => $order_id ) ) );exit( );
    }

    public function paycode( ){
        $log_id = ( integer )$this->_get( "log_id" );
        if ( empty( $log_id ) ){
            $this->error( "没有有效支付记录！" );
        }
        if ( !( $detail = d( "Paymentlogs" )->find( $log_id ) ) ){
            $this->error( "没有有效的支付记录！" );
        }
        if ( $detail['is_paid'] != 0 || empty( $detail['order_ids'] ) || !empty( $detail['order_id'] ) && empty( $detail['need_pay'] ) ){
            $this->error( "没有有效的支付记录！" );
        }
        $order_ids = explode( ",", $detail['order_ids'] );
        $ordergood = d( "Ordergoods" )->where( array("order_id" => array( "IN",$order_ids)) )->select( );
        $goods_id = $shop_ids = array( );
        foreach ( $ordergood as $k => $val )
        {
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign( "goods", d( "Goods" )->itemsByIds( $goods_id ) );
        $this->assign( "shops", d( "Shop" )->itemsByIds( $shop_ids ) );
        $this->assign( "ordergoods", $ordergood );
        $this->assign( "useraddr", d( "Useraddr" )->where( array(
            "user_id" => $this->uid
        ) )->limit( )->select( ) );
        $this->assign( "payment", d( "Payment" )->getPayments( ) );
        $this->assign( "logs", $detail );
        $this->display( );
    }

    public function pay( ){
        if ( empty( $this->uid ) ){
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $this->check_mobile( );
        $order_id = ( integer )$this->_get( "order_id" );
        $order = d( "Order" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid ){
            $this->error( "该订单不存在" );
            exit( );
        }
        $ordergood = d( "Ordergoods" )->where( array("order_id" => $order_id) )->select( );
        $goods_id = $shop_ids = array( );
        foreach ( $ordergood as $k => $val ){
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign( "goods", d( "Goods" )->itemsByIds( $goods_id ) );
        $this->assign( "shops", d( "Shop" )->itemsByIds( $shop_ids ) );
        $this->assign( "ordergoods", $ordergood );
        $this->assign( "useraddr", d( "Useraddr" )->where( array("user_id" => $this->uid) )->limit( )->select( ) );
        $this->assign( "order", $order );
        $this->assign( "payment", d( "Payment" )->getPayments( TRUE ) );
        $this->display( );
    }

    public function paycode2( ){
        $log_id = ( integer )$this->_get( "log_id" );
        if ( empty( $log_id ) ){
            $this->error( "没有有效支付记录！" );
        }
        if ( !( $detail = d( "Paymentlogs" )->find( $log_id ) ) ){
            $this->error( "没有有效的支付记录！" );
        }
        if ( $detail['is_paid'] != 0 || empty( $detail['order_ids'] ) || !empty( $detail['order_id'] ) && empty( $detail['need_pay'] ) ){
            $this->error( "没有有效的支付记录！" );
        }
        $order_ids = explode( ",", $detail['order_ids'] );
        $addr_id = ( integer )$this->_post( "addr_id" );
        if ( empty( $addr_id ) ) {
            $this->error( "请选择一个要配送的地址！" );
        }
        d( "Order" )->where( array( "order_id" => array("IN",$order_ids)) )->save( array( "addr_id" => $addr_id ) );
        if ( !( $code = $this->_post( "code" ) ) ){
            $this->error( "请选择支付方式！" );
        }
        if ( $code == "wait" ){
            d( "Order" )->save( array( "is_daofu" => 1 ), array("where" => array("order_id" => array("IN", $order_ids))) );
            d( "Ordergoods" )->save( array("is_daofu" => 1 ), array( "where" => array( "order_id" => array( "IN",$order_ids ) ) ) );
            d( "Payment" )->mallSold( $order_ids );
            d( "Payment" )->mallPeisong( array( $order_ids), 1 );
            d( "Sms" )->mallTZshop( $order_ids );
            $this->success( "恭喜您下单成功！", u( "mcenter/goods/index" ) );
        }
        else{
            $payment = d( "Payment" )->checkPayment( $code );
            if ( empty( $payment ) ){
                $this->error( "该支付方式不存在" );
            }
            $detail['code'] = $code;
            d( "Paymentlogs" )->save( $detail );
			
            header( "Location:".u( "cart/combine", array("log_id" => $detail['log_id']) ) );
        }
    }

    public function combine( ){
        if ( empty( $this->uid ) ){
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $log_id = ( integer )$this->_get( "log_id" );
        if ( !( $detail = d( "Paymentlogs" )->find( $log_id ) ) ){
            $this->error( "没有有效的支付记录！" );
        }
        if ( $detail['is_paid'] != 0 || empty( $detail['order_ids'] ) || !empty( $detail['order_id'] ) && empty( $detail['need_pay'] ) ){
            $this->error( "没有有效的支付记录！" );
        }
        $this->assign( "button", d( "Payment" )->getCode( $detail ) );
        $this->assign( "logs", $detail );
        $this->display( );
    }

    public function pay2( ){
        if ( empty( $this->uid ) ){
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $order_id = ( integer )$this->_get( "order_id" );
        $order = d( "Order" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid ){
            $this->error( "该订单不存在" );
        }
        $addr_id = ( integer )$this->_post( "addr_id" );
        if ( empty( $addr_id ) ){
            $this->error( "请选择一个要配送的地址！" );
        }
        d( "Order" )->save( array("addr_id" => $addr_id,"order_id" => $order_id) );
        if ( !( $code = $this->_post( "code" ) ) ){
            $this->error( "请选择支付方式！" );
        }
        $ua = d( "UserAddr" );
        $uaddr = $ua->where( array(
            "addr_id" => $order['addr_id']
        ) )->find( );
        if ( $code == "wait" ){
            d( "Order" )->save( array("order_id" => $order_id,"is_daofu" => 1) );
            d( "Ordergoods" )->save( array("is_daofu" => 1), array("where" => array("order_id" => $order_id)) );
            d( "Payment" )->mallSold( $order_id );
            d( "Payment" )->mallPeisong( array($order_id ), 1 );
            $goods_ids = d( "Ordergoods" )->where( "order_id=".$order_id )->getField( "goods_id", TRUE );
            $goods_ids = implode( ",", $goods_ids );
            $map = array("goods_id" => array("in",$goods_ids) );
            $goods_name = d( "Goods" )->where( $map )->getField( "title", TRUE );
            $goods_name = implode( ",", $goods_name );
            include_once( "Baocms/Lib/Net/Wxmesg.class.php" );
            $notice_data = array(
                "url" => "http://".$_SERVER['HTTP_HOST']."/mcenter/goods/detail/order_id/".$order_id.".html",
                "first" => "亲,您的订单创建成功!",
                "remark" => "详情请登录-http://".$_SERVER['HTTP_HOST'],
                "amount" => round( $order['total_price'] / 100, 2 )."元",
                "order" => $order_id,
                "info" => $goods_name
            );
            $notice_data = Wxmesg::notice( $notice_data );
            Wxmesg::net( $this->uid, "OPENTM206930158", $notice_data );
            $this->success( "恭喜您下单成功！", u( "mcenter/goods/index" ) );
        }
        else{
            $payment = d( "Payment" )->checkPayment( $code );
            if ( empty( $payment ) ){
                $this->error( "该支付方式不存在" );
            }
            $logs = d( "Paymentlogs" )->getLogsByOrderId( "goods", $order_id );
            $need_pay = d( "Order" )->useIntegral( $this->uid, array( $order_id) );
            if ( empty( $logs ) ){
                $logs = array(
                    "type" => "goods",
                    "user_id" => $this->uid,
                    "order_id" => $order_id,
                    "code" => $code,
                    "need_pay" => $need_pay,
                    "create_time" => NOW_TIME,
                    "create_ip" => get_client_ip( ),
                    "is_paid" => 0
                );
                $logs['log_id'] = d( "Paymentlogs" )->add( $logs );
            }
            else{
                $logs['need_pay'] = $need_pay;
                $logs['code'] = $code;
                d( "Paymentlogs" )->save( $logs );
            }
            $goods_ids = d( "Ordergoods" )->where( "order_id=".$order_id )->getField( "goods_id", TRUE );
            $goods_ids = implode( ",", $goods_ids );
            $map = array("goods_id" => array( "in",$goods_ids));
            $goods_name = d( "Goods" )->where( $map )->getField( "title", TRUE );
            $goods_name = implode( ",", $goods_name );
            include_once( "Baocms/Lib/Net/Wxmesg.class.php" );
            $notice_data = array(
                "url" => "http://".$_SERVER['HTTP_HOST']."/mcenter/goods/detail/order_id/".$order_id.".html",
                "first" => "亲,您的订单创建成功!",
                "remark" => "详情请登录-http://".$_SERVER['HTTP_HOST'],
                "amount" => round( $order['total_price'] / 100, 2 )."元",
                "order" => $order_id,
                "info" => $goods_name
            );
            $notice_data = Wxmesg::notice( $notice_data );
            Wxmesg::net( $this->uid, "OPENTM206930158", $notice_data );
			
			
            header( "Location:".u( "mart/payment", array(
                "order_id" => $order_id
            ) ) );
			
			
        }
        exit( );
    }

    public function payment( $order_id ) {
        if ( empty( $this->uid ) ){
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $order_id = ( integer )$this->_get( "order_id" );
        $order = d( "Order" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid ){
            $this->error( "该订单不存在" );
            exit( );
        }
        $logs = d( "Paymentlogs" )->getLogsByOrderId( "goods", $order_id );
        if ( empty( $logs ) ){
            $this->error( "没有有效的支付记录！" );
            exit( );
        }
        $this->assign( "button", d( "Payment" )->getCode( $logs ) );
        $this->assign( "order", $order );
        $this->display( );
    }

    public function shopdetail( ){
        $id = ( integer )$this->_param( "id" );
        if ( !( $wshop = d( "WeidianDetails" )->find( $id ) ) ){
            $this->error( "没有该微店商家" );
            exit( );
        }
        if ( $wshop['closed'] != 0 || $wshop['audit'] != 1 ){
            $this->error( "该微店商家不存在" );
            exit( );
        }
        if ( !( $detail = d( "Shop" )->find( $wshop['shop_id'] ) ) ){
            $this->error( "没有该商家" );
            exit( );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            $this->error( "该商家不存在" );
            exit( );
        }
        $this->assign( "wshop", $wshop );
        $this->assign( "detail", $detail );
        $this->assign( "ex", d( "Shopdetails" )->find( $wshop['shop_id'] ) );
        $this->display( );
    }

    public function dianping( ){
        $id = ( integer )$this->_param( "id" );
        if ( !( $wshop = d( "WeidianDetails" )->find( $id ) ) ){
            $this->error( "没有该微店商家" );
            exit( );
        }
        if ( $wshop['closed'] != 0 || $wshop['audit'] != 1 ){
            $this->error( "该微店商家不存在" );
            exit( );
        }
        if ( !( $detail = d( "Shop" )->find( $wshop['shop_id'] ) ) ){
            $this->error( "没有该商家" );
            exit( );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            $this->error( "该商家不存在" );
            exit( );
        }
        $this->assign( "wshop", $wshop );
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function dianpingloading( ){
        $id = ( integer )$this->_get( "id" );
        if ( !( $wshop = d( "WeidianDetails" )->find( $id ) ) ){
            exit( "0" );
        }
        if ( $wshop['closed'] != 0 || $wshop['audit'] != 1 ){
            exit( "0" );
        }
        if ( !( $detail = d( "Shop" )->find( $wshop['shop_id'] ) ) ){
            exit( "0" );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            exit( "0" );
        }
        $shopdianping = d( "Shopdianping" );
        import( "ORG.Util.Page" );
        $map = array("closed" => 0,"shop_id" => $detail['shop_id'],"show_date" => array("ELT",TODAY ));
        $count = $shopdianping->where( $map )->count( );
        
        $Page = new Page( $count, 5 );
        $var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
        $p = $_GET[$var];
        if ( $Page->totalPages < $p ){exit( "0" );}
        $show = $Page->show( );
        $list = $shopdianping->where( $map )->order( array( "dianping_id" => "desc" ) )->limit( $Page->firstRow.",".$Page->listRows )->select( );
		
		
		
        $user_ids = $dianping_ids = array( );
        foreach ( $list as $k => $val ){
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
        }
        if ( !empty( $user_ids ) ) {
            $this->assign( "users", d( "Users" )->itemsByIds( $user_ids ) );
        }
        if ( !empty( $dianping_ids ) ){
            $this->assign( "pics", d( "Shopdianpingpics" )->where( array("dianping_id" => array("IN", $dianping_ids) ) )->select( ) );
        }
        $this->assign( "totalnum", $count );
        $this->assign( "list", $list );
        $this->assign( "detail", $detail );
        $this->assign( "wshop", $wshop );
        $this->display( );
    }

}

