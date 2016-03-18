
<?php

class EleAction extends CommonAction{

    protected $cart = array( );
    public function _initialize( ){
        parent::_initialize( );
        $this->cart = $this->getcart( );
        $this->assign( "cartnum", ( integer )array_sum( $this->cart ) );
        $cate = d( "Ele" )->getEleCate( );
        $this->assign( "elecate", $cate );
    }

    public function getcart( ) {
        $shop_id = ( integer )$this->_param( "shop_id" );
        $cart = ( array )json_decode( $_COOKIE['ele'] );
        $carts = array( );
        foreach ( $cart as $kk => $vv )
        {
            foreach ( $vv as $key => $v )
            {
                $carts[$kk][$key] = ( array )$v;
            }
        }
        $ids = $nums = array( );
        foreach ( $carts[$shop_id] as $k => $val )
        {
            $ids[$val['product_id']] = $val['product_id'];
            $nums[$val['product_id']] = $val['num'];
        }
        $eleproducts = d( "Eleproduct" )->itemsByIds( $ids );
        foreach ( $eleproducts as $k => $val )
        {
            $eleproducts[$k]['cart_num'] = $nums[$val['product_id']];
            $eleproducts[$k]['total_price'] = $nums[$val['product_id']] * $val['price'];
        }
        return $eleproducts;
    }

    public function main( )
    {
        $map = array(
            "is_open" => 1,
            "audit" => 1,
            "city_id" => $this->city_id
        );
        $lat = addslashes( cookie( "lat" ) );
        $lng = addslashes( cookie( "lng" ) );
        if ( empty( $lat ) || empty( $lng ) )
        {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $list = d( "Ele" )->where( $map )->order( " (ABS(lng - '".$lng."') +  ABS(lat - '{$lat}') ) asc " )->limit( 0, 10 )->select( );
        $shop_ids = array( );
        foreach ( $list as $k => $val )
        {
            if ( !empty( $val['shop_id'] ) )
            {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $list[$k]['d'] = getdistance( $lat, $lng, $val['lat'], $val['lng'] );
        }
        $this->assign( "shops", d( "Shop" )->itemsByIds( $shop_ids ) );
        $this->assign( "list", $list );
        $this->display( );
    }

    public function index( ){
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
        $this->assign('nextpage', LinkTo('ele/loaddata', $linkArr, array('t' => NOW_TIME, 'p' => '0000')));
        $this->assign('linkArr',$linkArr);
        $this->display(); // 输出模板 
    }

    public function loaddata( ) {
        $ele = D('Ele');
        import('ORG.Util.Page'); // 导入分页类
        //初始数据
        $map = array('audit'=>1,'city_id'=>$this->city_id);
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        }
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name'] = array('LIKE', '%' . $keyword . '%');
        }
        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
        }
        $order = $this->_param('order','htmlspecialchars');
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        switch ($order) {
            case 'a':
                $orderby = array("(ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') )"=>'asc','orderby' => 'asc','month_num'=>'desc','distribution'=>'asc','since_money'=>'asc');
                break;
            case 'p':
                $orderby = array('since_money'=>'asc');
                break;
            case 'v':
                $orderby = array('distribution'=>'asc');
                break;
            case 'd':
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
                break;
            case 's':
                $orderby = array('month_num'=>'desc');
                break;
        }
        $cate =  $this->_param('cate','htmlspecialchars');
        $lists = $ele->order($orderby)->where($map)->select();
        foreach ($lists as $k => $val) {
            if (!empty($cate)) {
                if (strpos($val['cate'],$cate) === false) {
                    unset($lists[$k]);
                }
            }
        }
        /*foreach ($lists as $k => $val) {
            if (!empty($lng) && !empty($lat)) {
                $lists[$k]['d'] = getDistanceNone($lat, $lng, $val['lat'], $val['lng']);
                if ($lists[$k]['d'] > 20000) { //大于2KM的要咔嚓掉
                    unset($lists[$k]);
                }
            }
        }*/
        $count = count($lists);  // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出  
        
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = array_slice($lists, $Page->firstRow, $Page->listRows);
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function shop( )
    {
        $shop_id = ( integer )$this->_param( "shop_id" );
        if ( !( $detail = d( "Ele" )->find( $shop_id ) ) )
        {
            $this->error( "该餐厅不存在" );
        }
        if ( !( $shop = d( "Shop" )->find( $shop_id ) ) )
        {
            $this->error( "该餐厅不存在" );
        }
        $Eleproduct = d( "Eleproduct" );
        $map = array(
            "closed" => 0,
            "audit" => 1,
            "shop_id" => $shop_id
        );
        $list = $Eleproduct->where( $map )->order( array( "sold_num" => "desc", "price" => "asc" ) )->select( );
        foreach ( $list as $k => $val )
        {
            $list[$k]['cart_num'] = $this->cart[$val['product_id']]['cart_num'];
        }
        $this->assign( "list", $list );
        $this->assign( "detail", $detail );
        $this->assign( "cates", d( "Elecate" )->where( array(
            "shop_id" => $shop_id,
            "closed" => 0
        ) )->select( ) );
        $this->assign( "shop", $shop );
        $this->display( );
    }

    public function cart( )
    {
        $cart = NULL;
        if ( $goods = cookie( "ele" ) )
        {
            $total = array( "num" => 0, "money" => 0 );
            $goods = ( array )json_decode( $goods );
            $ids = array( );
            foreach ( $goods as $shop_id => $items )
            {
                foreach ( $items as $k2 => $item )
                {
                    $item = ( array )$item;
                    $total['num'] += $item['num'];
                    $total['money'] += $item['price'] * $item['num'];
                    $ids[] = $item['product_id'];
                    $product_item_num[$item['product_id']] = $item['num'];
                }
            }
            $ids = implode( ",", $ids );
            $products = d( "Eleproduct" )->where( "closed=0" )->select( $ids );
            foreach ( $products as $k => $val )
            {
                $products[$k]['cart_num'] = $product_item_num[$val['product_id']];
            }
            $this->assign( "detail", d( "Ele" )->find( $shop_id ) );
            $this->assign( "total", $total );
            $this->assign( "shop_id", $shop_id );
            $this->assign( "cartgoods", $products );
        }
        $this->display( );
    }

    public function order( )
    {
        if ( empty( $this->uid ) )
        {
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $num = $this->_post( "num", FALSE );
        if ( empty( $num ) )
        {
            $this->error( "您还没有订餐呢" );
        }
        $shop_id = 0;
        $shops = array( );
        $products = array( );
        $total = array( "money" => 0, "num" => 0 );
        $product_name = array( );
        foreach ( $num as $key => $val )
        {
            $key = ( integer )$key;
            $val = ( integer )$val;
            if ( $val < 1 || 99 < $val )
            {
                $this->error( "请选择正确的购买数量" );
            }
            $product = d( "Eleproduct" )->find( $key );
            $product_name[] = $product['product_name'];
            if ( empty( $product ) )
            {
                $this->error( "产品不正确" );
            }
            $shop_id = $product['shop_id'];
            $product['buy_num'] = $val;
            $products[$key] = $product;
            $shops[$shop_id] = $shop_id;
            $total['money'] += $product['price'] * $val;
            $total['num'] += $val;
        }
        if ( 1 < count( $shops ) )
        {
            $this->error( "您购买的商品是2个商户的！" );
        }
        if ( empty( $shop_id ) )
        {
            $this->error( "商家不存在" );
        }
        $shop = d( "Ele" )->find( $shop_id );
        if ( empty( $shop ) )
        {
            $this->error( "该商家不存在" );
        }
        if ( !$shop['is_open'] )
        {
            $this->error( "商家已经打烊，实在对不住客官" );
        }
        $total['money'] += $shop['logistics'];
        $total['need_pay'] = $total['money'];
        if ( $total['money'] < $shop['since_money'] )
        {
            $this->error( "客官，您再订点吧！" );
        }
        if ( $shop['is_new'] && !d( "Eleorder" )->checkIsNew( $this->uid, $shop_id ) || $shop['full_money'] <= $total['money'] )
        {
            $num1 = ( integer )( ( $total['money'] - $shop['full_money'] ) / 1000 );
            $total['new_money'] = $shop['new_money'] + $num1 * 100;
            $total['need_pay'] = $total['need_pay'] - $total['new_money'];
        }
        $settlement_price = ( integer )( $total['need_pay'] - $total['need_pay'] * $shop['rate'] / 1000 );
        $month = date( "Ym", NOW_TIME );
        if ( $order_id = d( "Eleorder" )->add( array(
            "user_id" => $this->uid,
            "shop_id" => $shop_id,
            "total_price" => $total['money'],
            "need_pay" => $total['need_pay'],
            "num" => $total['num'],
            "new_money" => ( integer )$total['new_money'],
            "logistics" => $shop['logistics'],
            "settlement_price" => $settlement_price,
            "status" => 0,
            "create_time" => NOW_TIME,
            "create_ip" => get_client_ip( ),
            "is_pay" => 0,
            "month" => $month
        ) ) )
        {
            foreach ( $products as $val )
            {
                d( "Eleorderproduct" )->add( array(
                    "order_id" => $order_id,
                    "product_id" => $val['product_id'],
                    "num" => $val['buy_num'],
                    "total_price" => $val['price'] * $val['buy_num'],
                    "month" => $month
                ) );
            }
            cookie( "eleproduct", NULL );
            cookie( "ele", NULL );
            $this->success( "下单成功！您可以选择配送地址!", u( "ele/pay", array(
                "order_id" => $order_id
            ) ) );
        }
        $this->error( "创建订单失败！" );
    }

    public function pay( )
    {
        if ( empty( $this->uid ) )
        {
            header( "Location:".u( "passport/login" ) );
            exit( );
        }
        $this->check_mobile( );
        $order_id = ( integer )$this->_get( "order_id" );
        $order = d( "Eleorder" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid )
        {
            $this->error( "该订单不存在" );
            exit( );
        }
        $this->assign( "shop", d( "Ele" )->find( $order['shop_id'] ) );
        $ordergoods = d( "Eleorderproduct" )->where( array(
            "order_id" => $order_id
        ) )->select( );
        $goods = array( );
        foreach ( $ordergoods as $key => $val )
        {
            $goods[$val['product_id']] = $val['product_id'];
        }
        $products = d( "Eleproduct" )->itemsByIds( $goods );
        $this->assign( "products", $products );
        $this->assign( "ordergoods", $ordergoods );
        $this->assign( "useraddr", d( "Useraddr" )->where( array(
            "user_id" => $this->uid
        ) )->limit( )->select( ) );
        $this->assign( "order", $order );
        $this->assign( "payment", d( "Payment" )->getPayments( TRUE ) );
        $this->display( );
    }

    public function pay2( )
    {
        if ( empty( $this->uid ) )
        {
            $this->ajaxLogin( );
        }
        $order_id = ( integer )$this->_get( "order_id" );
        $order = d( "Eleorder" )->find( $order_id );
        if ( empty( $order ) || $order['status'] != 0 || $order['user_id'] != $this->uid )
        {
            $this->error( "该订单不存在" );
            exit( );
        }
        $addr_id = ( integer )$this->_post( "addr_id" );
        if ( empty( $addr_id ) )
        {
            $this->error( "请选择一个要配送的地址！" );
        }
        d( "Eleorder" )->save( array(
            "addr_id" => $addr_id,
            "order_id" => $order_id
        ) );
        if ( !( $code = $this->_post( "code" ) ) )
        {
            $this->error( "请选择支付方式！" );
        }
        $dv = d( "DeliveryOrder" );
        $ua = d( "UserAddr" );
        $uaddr = $ua->where( array(
            "addr_id" => $order['addr_id']
        ) )->find( );
        $shop = d( "Shop" );
        $fshop = $shop->where( "shop_id =".$order['shop_id'] )->find( );
        if ( $code == "wait" )
        {
            d( "Eleorder" )->save( array(
                "order_id" => $order_id,
                "status" => 1
            ) );
            $eleorder = d( "Eleorder" )->find( $order_id );
            $shops = d( "Shop" )->find( $eleorder['shop_id'] );
            if ( $fshop['is_pei'] == 0 )
            {
                $dv_data = array(
                    "type" => 1,
                    "type_order_id" => $order['order_id'],
                    "delivery_id" => 0,
                    "shop_id" => $order['shop_id'],
                    "city_id" => $fshop['city_id'],
                    "user_id" => $order['user_id'],
                    "shop_name" => $fshop['shop_name'],
                    "shop_addr" => $fshop['addr'],
                    "lat" => $fshop['lat'],
                    "lng" => $fshop['lng'],
                    "shop_mobile" => $fshop['tel'],
                    "user_name" => $this->member['nickname'],
                    "user_addr" => $uaddr['addr'],
                    "user_mobile" => $this->member['mobile'],
                    "create_time" => time( ),
                    "update_time" => 0,
                    "status" => 0
                );
                $dv->add( $dv_data );
            }
            d( "Sms" )->sendSms( "sms_ele", $this->member['mobile'], array(
                "nickname" => $this->member['nickname'],
                "shopname" => $shops['shop_name']
            ) );
            $product_ids = d( "Eleorderproduct" )->where( "order_id=".$order['order_id'] )->getField( "product_id", TRUE );
            $product_ids = implode( ",", $product_ids );
            $map = array(
                "product_id" => array(
                    "in",
                    $product_ids
                )
            );
            $product_name = d( "Eleproduct" )->where( $map )->getField( "product_name", TRUE );
            $product_name = implode( ",", $product_name );
            include_once( "Baocms/Lib/Net/Wxmesg.class.php" );
            $notice_data = array(
                "url" => "http://".$_SERVER['HTTP_HOST']."/mcenter/eleorder/detail/order_id/".$order_id.".html",
                "first" => "亲,您的订单创建成功!",
                "remark" => "详情请登录-http://".$_SERVER['HTTP_HOST'],
                "amount" => round( $order['need_pay'] / 100, 2 )."元",
                "order" => $order_id,
                "info" => $product_name
            );
            $notice_data = Wxmesg::notice( $notice_data );
            Wxmesg::net( $this->uid, "OPENTM206930158", $notice_data );
            d( "Sms" )->eleTZshop( $order_id );
            $this->success( "恭喜您下单成功！", u( "mcenter/eleorder/index" ) );
        }
        else
        {
            $payment = d( "Payment" )->checkPayment( $code );
            if ( empty( $payment ) )
            {
                $this->error( "该支付方式不存在" );
            }
            $logs = d( "Paymentlogs" )->getLogsByOrderId( "ele", $order_id );
            if ( empty( $logs ) )
            {
                $logs = array(
                    "type" => "ele",
                    "user_id" => $this->uid,
                    "order_id" => $order_id,
                    "code" => $code,
                    "need_pay" => $order['need_pay'],
                    "create_time" => NOW_TIME,
                    "create_ip" => get_client_ip( ),
                    "is_paid" => 0
                );
                $logs['log_id'] = d( "Paymentlogs" )->add( $logs );
            }
            else
            {
                $logs['need_pay'] = $order['need_pay'];
                $logs['code'] = $code;
                d( "Paymentlogs" )->save( $logs );
            }
            $product_ids = d( "Eleorderproduct" )->where( "order_id=".$order_id )->getField( "product_id", TRUE );
            $product_ids = implode( ",", $product_ids );
            $map = array(
                "product_id" => array(
                    "in",
                    $product_ids
                )
            );
            $product_name = d( "Eleproduct" )->where( $map )->getField( "product_name", TRUE );
            $product_name = implode( ",", $product_name );
            include_once( "Baocms/Lib/Net/Wxmesg.class.php" );
            $notice_data = array(
                "url" => "http://".$_SERVER['HTTP_HOST']."/mcenter/eleorder/detail/order_id/".$order_id.".html",
                "first" => "亲,您的订单创建成功!",
                "remark" => "详情请登录-http://".$_SERVER['HTTP_HOST'],
                "amount" => round( $order['need_pay'] / 100, 2 )."元",
                "order" => $order_id,
                "info" => $product_name
            );
            $notice_data = Wxmesg::notice( $notice_data );
            Wxmesg::net( $this->uid, "OPENTM206930158", $notice_data );
            $this->success( "选择支付方式成功！下面请进行支付！", u( "payment/payment", array(
                "log_id" => $logs['log_id']
            ) ) );
        }
    }

    public function ajax( )
    {
        $this->cart = cookie( "eleproduct" );
        $num = count( $this->cart );
        $num += 1;
        exit( "{$num}" );
    }

    private function getShop( $shop, $lng, $lat )
    {
        foreach ( $shop as $k => $v )
        {
            $shop[$k]['d'] = getdistancenone( $lat, $lng, $v['lat'], $v['lng'] );
            if ( 20000 < $shop[$k]['d'] )
            {
                unset( $shop[$k] );
            }
        }
        return $shop;
    }

    public function favorites( )
    {
        if ( empty( $this->uid ) )
        {
            $this->ajaxLogin( );
        }
        $shop_id = ( integer )$this->_get( "shop_id" );
        if ( !( $detail = d( "Shop" )->find( $shop_id ) ) )
        {
            $this->error( "没有该商家" );
        }
        if ( $detail['closed'] )
        {
            $this->error( "该商家已经被删除" );
        }
        if ( d( "Shopfavorites" )->check( $shop_id, $this->uid ) )
        {
            $this->error( "您已经收藏过了！" );
        }
        $data = array(
            "shop_id" => $shop_id,
            "user_id" => $this->uid,
            "create_time" => NOW_TIME,
            "create_ip" => get_client_ip( )
        );
        if ( d( "Shopfavorites" )->add( $data ) )
        {
            $this->success( "恭喜您收藏成功！", u( "ele/detail", array(
                "shop_id" => $shop_id
            ) ) );
        }
        $this->error( "收藏失败！" );
    }

    public function detail( )
    {
        $shop_id = ( integer )$this->_param( "shop_id" );
        if ( !( $detail = d( "Ele" )->find( $shop_id ) ) )
        {
            $this->error( "没有该商家" );
            exit( );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 )
        {
            $this->error( "该商家不存在" );
            exit( );
        }
        $this->assign( "detail", $detail );
        $this->assign( "shop", d( "Shop" )->find( $shop_id ) );
        $this->assign( "ex", d( "Shopdetails" )->find( $shop_id ) );
        $this->display( );
    }


	
	 public function dianping( ){
        $shop_id = ( int)$this->_get( "shop_id" );
        if ( !( $detail = D( "Ele" )->find( $shop_id ) ) ){
            $this->error( "没有该商家" );
            exit( );
        }
        if ( $detail['closed'] ){
            $this->error( "该商家已经被删除" );
            exit( );
        }
		
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function dianpingloading( ){
        $shop_id = ( integer )$this->_get( "shop_id" );
        if ( !( $detail = D( "Ele" )->find( $shop_id ) ) ) {
            exit( "0" );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            exit( "0" );
        }
        $Eledianping = D( "Eledianping" );
        import( "ORG.Util.Page" );
        $map = array("closed" => 0,"shop_id" => $shop_id,"show_date" => array("ELT",TODAY ));
        $count = $Eledianping->where( $map )->count( );
        $Page = new Page( $count, 5 );
        $var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
        $p = $_GET[$var];
        if ( $Page->totalPages < $p ){
            exit( "0" );
        }
        $show = $Page->show( );
        $list = $Eledianping->where( $map )->order( array( "order_id" => "desc" ) )->limit( $Page->firstRow.",".$Page->listRows )->select( );
        $user_ids = $order_ids = array( );
        foreach ( $list as $k => $val ) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
        }
        if ( !empty( $user_ids ) ){
            $this->assign( "users", d( "Users" )->itemsByIds( $user_ids ) );
        }
        if ( !empty( $order_ids ) ){
            $this->assign( "pics", d( "Eledianpingpics" )->where( array(
                "order_id" => array(
                    "IN",
                    $order_ids
                )
            ) )->select( ) );
        }
        $this->assign( "totalnum", $count );
        $this->assign( "list", $list );
        $this->assign( "detail", $detail );
        $this->display( );
    }
	
	
	 public function elecancle($order_id = 0) {
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            if (!$detial = D('Eleorder')->find($order_id)) {
                $this->error('您取消的订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->error('请不要操作别人的订单');
            }
            if ($detial['is_pay'] == 0) {
                $this->error('当前状态不能退款');
            }
            if ($detial['status'] != 1) {
                $this->error('当前状态不能退款');
            }
            $obj = D('Eleorder');
            $obj->save(array('order_id' => $order_id, 'closed' => 1));
            $this->success('删除成功！', U('mcenter/eleorder/index'));
        } else {
            $this->error('请选择要取消的订单');
        }
    }

}


