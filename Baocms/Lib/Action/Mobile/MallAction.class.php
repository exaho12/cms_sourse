
<?php

class MallAction extends CommonAction
{

    public function _initialize( ){
        parent::_initialize( );
        $goods = session( "goods" );
        $this->assign( "cartnum", ( int )array_sum( $goods ) );
    }

    public function index( ){
        $keyword = $this->_param( "keyword", "htmlspecialchars" );
        $this->assign( "keyword", $keyword );
        $cat = ( int )$this->_param( "cat" );
        $area = ( int )$this->_param( "area" );
        $cate_id = ( int )$this->_param( "cate_id" );
        $order = ( int )$this->_param( "order" );
        $this->assign( "area", $area );
        $this->assign( "cate_id", $cate_id );
        $this->assign( "order", $order );
        $this->assign( "cat", $cat );
        $this->assign( "nextpage", linkto( "mall/loaddata", array( "cat" => $cat,"order" => $order,"area" => $area,"cate_id" => $cate_id,"keyword" => $keyword,
 "p" => "0000") ) );
        $this->display( );
    }

    public function main( )
    {
        $cate_id = i( "cate_id", "", "trim,intval" );
        $gc = d( "GoodsCate" );
        if ( $cate_id ){
            $map['cate_id'] = array("eq",$cate_id);
            $gc_name = $gc->where( "cate_id =".$cate_id )->getField( "cate_name" );
            $this->assign( "gc_name", $gc_name );
        }
        $this->assign( "cate_id", $cate_id );
        $where = array( );
        $where['cate_id'] = array( "in", "1,14,2,6,8,21,25" );
        $rgc = $gc->where( $where )->select( );
        $all_gc = $gc->where( "parent_id = 0" )->select( );
        $this->assign( "all_gc", $all_gc );
        $this->assign( "rgc", $rgc );
        $map['city_id'] = $this->city_id;
        $map['audit'] = 1;
        $map['closed'] = 0;
        $map['end_date'] = array( "egt", TODAY );
        $order = ( int )$this->_param( "order" );
        switch ( $order ){
        case 2 :
            $orderby = array( "sold_num" => "desc" );
            break;
        case 3 :
            $orderby = array( "goods_id" => "desc" );
            break;
            $orderby = array( "mall_price" => "asc", "orderby" => "asc" );
        }
        $this->assign( "order", $order );
        $list = d( "Goods" )->order( $orderby )->where( $map )->limit( 0, 10 )->select( );
        $this->assign( "list", $list );
        $this->display( );
    }

    public function loaddata( ){
        $Goods = d( "Goods" );
        import( "ORG.Util.Page" );
        $area = ( int )$this->_param( "area" );
        $order = ( int )$this->_param( "order" );
        if ( $area ){
            $map['area_id'] = $area;
        }
        $cate_id = ( int )$this->_param( "cate_id" );
        if ( $cate_id ){
            $map['cate_id'] = $cate_id;
        }
        $map['audit'] = 1;
        $map['closed'] = 0;
        $map['end_date'] = array("egt",TODAY);
        if ( $keyword = $this->_param( "keyword", "htmlspecialchars" ) ){
            $map['title'] = array( "LIKE", "%".$keyword."%");
        }
        $cat = ( int )$this->_param( "cat" );
        if ( $cat ){
            $catids = d( "Goodscate" )->getChildren( $cat );
            if ( !empty( $catids ) ){
                $map['cate_id'] = array("IN",$catids);
            }
            else{
                $map['cate_id'] = $cat;
            }
        }
        $map['city_id'] = $this->city_id;
        $count = $Goods->where( $map )->count( );
        
        $Page = new Page( $count, 10 );
        $show = $Page->show( );
        $var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
        $p = $_GET[$var];
        if ( $Page->totalPages < $p ){
            exit( "0" );
        }
        if ( $order == "1" ){
            $order_arr = "create_time desc";
        }
        else if ( $order == "2" ){
            $order_arr = "sold_num desc";
        }
        else{
            $order_arr = "orderby desc";
        }
        $list = $Goods->where( $map )->order( $order_arr )->limit( $Page->firstRow.",".$Page->listRows )->select( );
        foreach ( $list as $k => $val ){
            if ( $val['shop_id'] ){
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val['end_time'] = strtotime( $val['end_date'] ) - NOW_TIME + 86400;
            $list[$k] = $val;
        }
        $this->assign( "list", $list );
        $this->assign( "page", $show );
        $this->display( );
    }

    public function buy( $goods_id ){
        $goods_id = ( int )$goods_id;
        if ( empty( $goods_id ) ){
            $this->error( "请选择产品" );
        }
        if ( !( $detail = d( "Goods" )->find( $goods_id ) ) ){
            $this->error( "改商品不存在" );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            $this->error( "该商品不存在" );
        }
        if ( $detail['end_date'] < TODAY ){
            $this->error( "该商品已经过期，暂时不能购买" );
        }
        $goods = session( "goods" );
        if ( isset( $goods[$goods_id] ) ){
            $goods[$goods_id] = $goods[$goods_id] + 1;
        }
        else{
            $goods[$goods_id] = 1;
        }
        session( "goods", $goods );
        header( "Location:".u( "mall/cart" ) );
        exit( );
    }

    public function cartadd( $goods_id )
    {
        $shop_id = ( int )$this->_param( "shop_id" );
        $goods_id = ( int )$goods_id;
        if ( empty( $goods_id ) ){
            exit( "请选择产品" );
        }
        if ( !( $detail = d( "Goods" )->find( $goods_id ) ) ){
            exit( "该商品不存在" );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            exit( "该商品不存在" );
        }
        if ( $detail['end_date'] < TODAY ){
            exit( "该商品已经过期，暂时不能购买" );
        }
        $goods = session( "goods" );
        if ( isset( $goods[$goods_id] ) ){
            $goods[$goods_id] = $goods[$goods_id] + 1;
        }
        else{
            $goods[$goods_id] = 1;
        }
        session( "goods", $goods );
        exit( "0" );
    }

    public function cartadd2( ){
        if ( IS_AJAX ){
            $shop_id = ( int )$_POST['shop_id'];
            $goods_id = ( int )$_POST['goods_id'];
            if ( empty( $goods_id ) ){
                $this->ajaxReturn( array( "status" => "error", "msg" => "请选择商品" ) );
            }
            if ( !( $detail = d( "Goods" )->find( $goods_id ) ) ){
                $this->ajaxReturn( array( "status" => "error", "msg" => "该商品不存在" ) );
            }
            if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
                $this->ajaxReturn( array( "status" => "error", "msg" => "该商品不存在" ) );
            }
            if ( $detail['end_date'] < TODAY ){
                $this->ajaxReturn( array( "status" => "error", "msg" => "该商品已经过期，暂时不能购买" ) );
            }
            $goods = session( "goods" );
            if ( isset( $goods[$goods_id] ) ){
                $goods[$goods_id] = $goods[$goods_id] + 1;
            }
            else{
                $goods[$goods_id] = 1;
            }
            session( "goods", $goods );
            $this->ajaxReturn( array( "status" => "success", "msg" => "加入购物车成功" ) );
        }
    }

    public function cart( )
    {
        $goods = session( "goods" );
        $back = end( $goods );
        $back = key( $goods );
        $this->assign( "back", $back );
        if ( empty( $goods ) )
        {
            $this->error( "亲还没有选购产品呢!", u( "mall/index" ) );
        }
        $goods_ids = array_keys( $goods );
        $cart_goods = d( "Goods" )->itemsByIds( $goods_ids );
        $shop_ids = array( );
        foreach ( $cart_goods as $k => $val )
        {
            $cart_goods[$k]['buy_num'] = $goods[$k];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign( "cart_shops", d( "Shop" )->itemsByIds( $shop_ids ) );
        $this->assign( "cart_goods", $cart_goods );
        $this->display( );
    }

    public function detail( $goods_id ){
        $goods_id = ( int )$goods_id;
        if ( empty( $goods_id ) ){
            $this->error( "商品不存在" );
        }
        if ( !( $detail = d( "Goods" )->find( $goods_id ) ) ){
            $this->error( "商品不存在" );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            $this->error( "商品不存在" );
        }
        $shop_id = $detail['shop_id'];
        $recom = d( "Goods" )->where( array("shop_id" => $shop_id,"goods_id" => array("neq",$goods_id)))->select();
        $record = d( "Usersgoods" );
        $insert = $record->getRecord( $this->uid, $goods_id );
        $this->assign( "recom", $recom );
        $this->assign( "detail", $detail );
        $this->assign( "shop", d( "Shop" )->find( $shop_id ) );
		
		//修复商城评分不显示
		$pingnum = D('Goodsdianping')->where(array('goods_id'=>$goods_id))->count();
		$this->assign('pingnum', $pingnum);
		//p($pingnum);
		$score = (int) D('Goodsdianping')->where(array('goods_id'=>$goods_id))->avg('score');
		if($score == 0){
			$score = 5;
		}
		$this->assign('score', $score);
		//修复结束		
		
        $this->display( );
    }

    public function cartdel( ){
        $goods_id = ( int )$this->_get( "goods_id" );
        $goods = session( "goods" );
        if ( isset( $goods[$goods_id] ) ){
            unset( $goods[$goods_id] );
            session( "goods", $goods );
        }
        exit( "0" );
    }

    public function cartdel2( ){
        $goods_id = ( int )$this->_get( "goods_id" );
        $goods = session( "goods" );
        if ( isset( $goods[$goods_id] ) ){
            unset( $goods[$goods_id] );
            session( "goods", $goods );
        }
        header( "Location:".u( "mall/cart" ) );
    }

    public function neworder( ){
        $goods = $this->_get( "goods" );
        $goods = explode( ",", $goods );
        if ( empty( $goods ) ){
            $this->error( "亲购买点吧" );
        }
        $datas = array( );
        foreach ( $goods as $val ){
            $good = explode( "-", $val );
            $good[1] = ( int )$good[1];
            if ( empty( $good[0] ) || empty( $good[1] ) ){
                $this->error( "亲购买点吧" );
            }
            if ( 99 < $good[1] || $good[1] < 0 ){
                $this->error( "本店不支持批发" );
            }
            $datas[$good[0]] = $good[1];
        }
        session( "goods", $datas );
        header( "Location:".u( "mall/cart" ) );
        exit( );
    }

    public function order( ){
        if ( empty( $this->uid ) ){
            $this->error('登录状态失效!', U('passport/login'));
			
        }
        $num = $this->_post( "num", FALSE );
        $goods_ids = array( );
        foreach ( $num as $k => $val ){
            $val = ( int )$val;
            if ( empty( $val ) ){
                unset( $num[$k] );
            }
            else if ( $val < 1 || 99 < $val ){
                unset( $num[$k] );
            }
            else{
                $goods_ids[$k] = ( int )$k;
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
        $total_mobile = 0;
        $ip = get_client_ip( );
        $ordergoods = $total_price = array( );
        foreach ( $goods as $val ){
            $price = $val['mall_price'] * $num[$val['goods_id']];
            $js_price = $val['settlement_price'] * $num[$val['goods_id']];
            $mobile_fan = $val['mobile_fan'] * $num[$val['goods_id']];
            $m_price = $price - $mobile_fan;
            $tprice += $m_price;
            $total_mobile += $mobile_fan;
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
        $tui = cookie( "tui" );
        if ( !empty( $tui ) )
        {
            $tui = explode( "_", $tui );
            $tuiguang = array(
                "uid" => ( int )$tui[0],
                "goods_id" => ( int )$tui[1]
            );
        }
        $order_ids = array( );
        foreach ( $ordergoods as $k => $val )
        {
            $order['shop_id'] = $k;
            $order['total_price'] = $total_price[$k];
            $shop = d( "Shop" )->find( $k );
            $order['is_shop'] = ( int )$shop['is_pei'];
            if ( $order_id = d( "Order" )->add( $order ) )
            {
                $order_ids[] = $order_id;
                foreach ( $val as $k1 => $val1 )
                {
                    $val1['order_id'] = $order_id;
                    if ( !empty( $tuiguang ) || $tuiguang['goods_id'] == $val1['goods_id'] )
                    {
                        $val1['tui_uid'] = $tuiguang['uid'];
                    }
                    d( "Ordergoods" )->add( $val1 );
                }
            }
        }
        session( "goods", NULL );
        if ( 1 < count( $order_ids ) )
        {
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
            $this->niuMsg('下单成功，接下来选择支付方式和配送地址1！',U('mall/paycode', array('log_id' => $logs['log_id'])));
            exit( );
        }
       $this->error('下单成功，接下来选择支付方式和配送地址2！',U('mall/pay', array('order_id' => $order_id)));
        exit( );
    }

    public function paycode( ){
        $log_id = ( int )$this->_get( "log_id" );
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
        $ordergood = d( "Ordergoods" )->where( array("order_id" => array("IN",$order_ids)))->select();
        $goods_id = $shop_ids = array( );
        foreach ( $ordergood as $k => $val ){
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign( "goods", d( "Goods" )->itemsByIds( $goods_id ) );
        $this->assign( "shops", d( "Shop" )->itemsByIds( $shop_ids ) );
        $this->assign( "ordergoods", $ordergood );
        $this->assign( "useraddr", d( "Useraddr" )->where( array("user_id" => $this->uid))->limit()->select());
        $this->assign( "payment", d( "Payment" )->getPayments( ) );
        $this->assign( "logs", $detail );
        $this->display( );
    }

    public function pay( ) {
        if ( empty( $this->uid ) ){
            $this->error('登录状态失效!', U('passport/login'));
            exit( );
        }
        $this->check_mobile( );
        $order_id = ( int )$this->_get( "order_id" );
        $order = d( "Order" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid ){
            $this->error( "该订单不存在" );
            exit( );
        }
        $ordergood = d( "Ordergoods" )->where( array(
            "order_id" => $order_id
        ) )->select( );
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
        $this->assign( "order", $order );
        $this->assign( "payment", d( "Payment" )->getPayments( TRUE ) );
        $this->display( );
    }

    public function paycode2( ){
        $log_id = ( int )$this->_get( "log_id" );
        if ( empty( $log_id ) ){
            $this->niuMsg( "没有有效支付记录！" );
        }
        if ( !( $detail = d( "Paymentlogs" )->find( $log_id ) ) ){
            $this->niuMsg( "没有有效的支付记录！" );
        }
        if ( $detail['is_paid'] != 0 || empty( $detail['order_ids'] ) || !empty( $detail['order_id'] ) && empty( $detail['need_pay'] ) ){
            $this->niuMsg( "没有有效的支付记录！" );
        }
        $order_ids = explode( ",", $detail['order_ids'] );
        $addr_id = ( int )$this->_post( "addr_id" );
        if ( empty( $addr_id ) ){
            $this->niuMsg( "请选择一个要配送的地址！" );
        }
        d( "Order" )->where( array("order_id" => array( "IN",$order_ids)) )->save( array("addr_id" => $addr_id) );
        if ( !( $code = $this->_post( "code" ) ) ){
            $this->niuMsg( "请选择支付方式！" );
        }
        if ( $code == "wait" ){
            d( "Order" )->save( array("is_daofu" => 1), array("where" => array("order_id" => array("IN",$order_ids))) );
            d( "Ordergoods" )->save( array("is_daofu" => 1), array("where" => array("order_id" => array("IN",$order_ids))) );
            d( "Payment" )->mallSold( $order_ids );
            d( "Payment" )->mallPeisong( array($order_ids), 1 );
            d( "Sms" )->mallTZshop( $order_ids );
            $this->niuMsg( "恭喜您下单成功！", u( "mcenter/goods/index" ) );
        }
        else{
            $payment = d( "Payment" )->checkPayment( $code );
            if ( empty( $payment ) ){
                $this->niuMsg( "该支付方式不存在" );
            }
            $detail['code'] = $code;
            d( "Paymentlogs" )->save( $detail );
           $this->niuMsg('订单设置完成，即将进入付款。',U('mall/combine', array('log_id' => $detail['log_id'])));
        }
    }

    public function combine( ){
        if ( empty( $this->uid ) ){
            $this->error('登录状态失效!', U('passport/login'));
            exit( );
        }
        $log_id = ( int )$this->_get( "log_id" );
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
            $this->error('登录状态失效!', U('passport/login'));
            exit( );
        }
        $order_id = ( int )$this->_get( "order_id" );
        $order = d( "Order" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid ){
            $this->niuMsg( "该订单不存在" );
        }
        $addr_id = ( int )$this->_post( "addr_id" );
        if ( empty( $addr_id ) ){
            $this->niuMsg( "请选择一个要配送的地址！" );
        }
        d( "Order" )->save( array("addr_id" => $addr_id,"order_id" => $order_id));
        if ( !( $code = $this->_post( "code" ) ) ){
            $this->niuMsg( "请选择支付方式！" );
        }
        $ua = d( "UserAddr" );
        $uaddr = $ua->where( array(
            "addr_id" => $order['addr_id']
        ) )->find( );
        if ( $code == "wait" )
        {
            d( "Order" )->save( array(
                "order_id" => $order_id,
                "is_daofu" => 1
            ) );
            d( "Ordergoods" )->save( array(
                "is_daofu" => 1
            ), array(
                "where" => array(
                    "order_id" => $order_id
                )
            ) );
            d( "Payment" )->mallSold( $order_id );
            d( "Payment" )->mallPeisong( array(
                $order_id
            ), 1 );
            $goods_ids = d( "Ordergoods" )->where( "order_id=".$order_id )->getField( "goods_id", TRUE );
            $goods_ids = implode( ",", $goods_ids );
            $map = array(
                "goods_id" => array(
                    "in",
                    $goods_ids
                )
            );
            $goods_name = d( "Goods" )->where( $map )->getField( "title", TRUE );
            $goods_name = implode( ",", $goods_name );
            include_once( "Baocms/Lib/Net/Wxmesg.class.php" );
            $notice_data = array(
                "url" => "http://".$_SERVER['HTTP_HOST']."/mcenter/goods/index/aready/".$order_id.".html",
                "first" => "亲,您的订单创建成功!",
                "remark" => "详情请登录-http://".$_SERVER['HTTP_HOST'],
                "amount" => round( $order['total_price'] / 100, 2 )."元",
                "order" => $order_id,
                "info" => $goods_name
            );
            $notice_data = Wxmesg::notice( $notice_data );
            Wxmesg::net( $this->uid, "OPENTM206930158", $notice_data );
            $this->niuMsg( "恭喜您下单成功！", u( "mcenter/goods/index" ) );
        }
        else
        {
            $payment = d( "Payment" )->checkPayment( $code );
            if ( empty( $payment ) )
            {
                $this->niuMsg( "该支付方式不存在" );
            }
            $logs = d( "Paymentlogs" )->getLogsByOrderId( "goods", $order_id );
            $need_pay = d( "Order" )->useIntegral( $this->uid, array( $order_id));
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
            $map = array(
                "goods_id" => array(
                    "in",
                    $goods_ids
                )
            );
            $goods_name = d( "Goods" )->where( $map )->getField( "title", TRUE );
            $goods_name = implode( ",", $goods_name );
            include_once( "Baocms/Lib/Net/Wxmesg.class.php" );
            $notice_data = array(
                "url" => "http://".$_SERVER['HTTP_HOST']."/mcenter/goods/index/aready/".$order_id.".html",
                "first" => "亲,您的订单创建成功!",
                "remark" => "详情请登录-http://".$_SERVER['HTTP_HOST'],
                "amount" => round( $order['total_price'] / 100, 2 )."元",
                "order" => $order_id,
                "info" => $goods_name
            );
            $notice_data = Wxmesg::notice( $notice_data );
            Wxmesg::net( $this->uid, "OPENTM206930158", $notice_data );
            $this->niuMsg('订单设置完成，即将进入付款。', U('payment/payment', array('log_id' => $logs['log_id'])));
        }
    }
	
	
	 //团购点评

    public function dianping() {
        $goods_id = (int) $this->_get('goods_id');

        if (!$detail = D('Goods')->find($goods_id)) {
            $this->error('没有该商品');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商品已经被删除');
            die;
        }
		$this->assign('next', LinkTo('mall/dianpingloading', $linkArr, array('goods_id' => $goods_id, 't' => NOW_TIME, 'p' => '0000')));
        $this->assign('detail', $detail);
        $this->display();
    }



    public function dianpingloading() {
        $goods_id = (int) $this->_get('goods_id');
        if (!$detail = D('Goods')->find($goods_id)) {
            die('0');
        }
        if ($detail['closed']) {
            die('0');
        }
		$Goodsdianping = D('Goodsdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'tuan_id' => $tuan_id, 'show_date' => array('ELT', TODAY));
        $count = $Goodsdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Goodsdianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $orders_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $orders_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($orders_ids)) {
            $this->assign('pics', D('Goodsdianpingpics')->where(array('order_id' => array('IN', $orders_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('detail', $detail);
        $this->display();

    }

	

}

