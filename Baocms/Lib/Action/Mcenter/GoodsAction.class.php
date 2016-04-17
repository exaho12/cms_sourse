<?php

//深度开发

class GoodsAction extends CommonAction {

    public function index() {
        $aready = (int) $this->_param('aready');
        
        
        $this->assign('aready', $aready);
        $this->display(); // 输出模板
    }

    public function goodsloaddata() {
        $Order = D('Order');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'user_id' => $this->uid);

        $aready = (int) $this->_param('aready');
        if ($aready == 1) {
            $map['status'] = 0;
        } elseif ($aready == 3) {
            $map['status'] = array('IN',array(1,2));
        }  elseif($aready == 4) {
            $map['status'] = array('IN',array(3,8));
        }


        $count = $Order->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Order->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids = $addr_ids = array();
        foreach ($list as $key => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
        }
        if (!empty($order_ids)) {
            $goods = D('Ordergoods')->where(array('order_id' => array('IN', $order_ids)))->select();
            $goods_ids = $shop_ids = array();
            foreach ($goods as $val) {
                $goods_ids[$val['goods_id']] = $val['goods_id'];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $this->assign('goods', $goods);
            $this->assign('products', D('Goods')->itemsByIds($goods_ids));
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('types', D('Order')->getType());
        $this->assign('goodtypes', D('Ordergoods')->getType());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function detail($order_id){
            $order_id = (int) $order_id;
            if(empty($order_id) || !$detail = D('Order')->find($order_id)){
                $this->error('该订单不存在');
            }
            if($detail['user_id'] != $this->uid){
                $this->error('请不要操作他人的订单');
            }
            $order_goods = D('Ordergoods')->where(array('order_id'=>$order_id))->select(); 
            $goods_ids = array();
            foreach($order_goods as $k=>$val){
                $goods_ids[$val['goods_id']] = $val['goods_id'];
            }
            if(!empty($goods_ids)){
                $this->assign('goods',D('Goods')->itemsByIds($goods_ids));
            }
            $this->assign('ordergoods',$order_goods);
            $this->assign('addr',D('Useraddr')->find($detail['addr_id']));
            $this->assign('types', D('Order')->getType());
            $this->assign('goodtypes', D('Ordergoods')->getType());
            $this->assign('detail',$detail);
            $this->display();
        }
//正常付款走3
    public function queren($order_id = 0){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->error('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->error('请不要操作他人的订单');
            }
            if ($detial['status'] != 2) {
                $this->error('该订单暂时不能确定收货');
            }
			
			
            if($obj->save(array('order_id'=>$order_id,'status'=>3))){
				D('Order')->overOrder($order_id); //确认到账入口
                $this->success('确认订单成功！', U('goods/index'));
            }
        } else {
            $this->error('请选择要确认收货的订单');
        }
    }
	//货到付款走8
	 public function daofu_queren($order_id = 0){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->error('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->error('请不要操作他人的订单');
            }
            if ($detial['status'] != 2) {
                $this->error('该订单暂时不能确定收货');
            }
            if($obj->save(array('order_id'=>$order_id,'status'=>3))){
                $this->success('确认订单成功！', U('goods/index'));
            }
        } else {
            $this->error('请选择要确认收货的订单');
        }
    }
        
        
    public function orderdel($order_id = 0) {
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->error('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->error('请不要操作他人的订单');
            }
            if ($detial['status'] != 0) {
                $this->error('该订单暂时不能取消');
            }

            if($obj->save(array('order_id' => $order_id, 'closed' => 1))){
                if($detail['use_integral']){
                    D('Users')->addIntegral($detail['user_id'],$detail['use_integral'],'取消订单'.$detail['order_id'].'积分退还');
                }
                
            }
            $this->success('取消成功！', U('goods/index'));
        } else {
            $this->error('请选择要取消的订单');
        }
    }
	
	
	 public function dianping($order_id ){
        $order_id = (int)$order_id;
        if ( empty( $order_id )|| !( $detail = D("Order")->find( $order_id ))){
            $this->error( "该订单不存在" );
        }
        if ($detail['user_id'] != $this->uid){
            $this->niuMsg( "请不要操作他人的订单");
        }
        if ( $detail['is_dianping'] != 0 ){
            $this->niuMsg( "您已经点评过了" );
        }

		$goodss = D('Ordergoods')->where('order_id ='.$detail['order_id']) -> find();
		$goods_id = $goodss['goods_id'];
	
        if ( $this->isPost( ) ){
            $data = $this->checkFields( $this->_post( "data", FALSE ), array( "score", "cost", "contents" ) );
            $data['user_id'] = $this->uid;
			$data['order_id'] = $detail['order_id'];
            $data['shop_id'] = $detail['shop_id'];
            $data['goods_id'] = $goods_id;
            $data['score'] = ( integer )$data['score'];
            if ( $data['score'] <= 0 || 5 < $data['score'] ){
                $this->niuMsg( "请选择评分" );
            }
            $data['contents'] = htmlspecialchars( $data['contents'] );
            if ( empty( $data['contents'] ) ){
                $this->niuMsg( "不说点什么么" );
            }
            $data['create_time'] = NOW_TIME;
            $data_mall_dianping = $this->_CONFIG['mobile']['data_mall_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_mall_dianping * 86400); //15天生效
            $data['create_ip'] = get_client_ip( );
            $obj = D( "Goodsdianping" );
			
			
			
			
            if ( $dianping_id = $obj->add( $data ) ){
                $photos = $this->_post( "photos", FALSE );
                $local = array();
                foreach ( $photos as $val ){
                    if ( isimage( $val ) ){
                        $local[] = $val;
                    }
                }
                if (!empty( $local ) ){
                    D( "Goodsdianpingpics" )->upload( $order_id, $local,$goods_id );
                }
                D( "Order" )->save( array( "order_id" => $order_id,"is_dianping" => 1));
                D( "Shop" )->updateCount( $detail['shop_id'], "score_num" );
                D( "Users" )->updateCount( $this->uid, "ping_num" );
                D( "Users" )->prestige( $this->uid, "dianping" );
				$this->niuMsg( "评价成功", U( "mcenter/goods/index/"));
                
            }
			
            $this->niuMsg( "操作失败！" );
        }
        else{
            $this->assign( "detail", $detail );
			$goods = D('Goods')->where('goods_id ='.$goods_id) -> find();
            $this->assign( "goods", $goods );
            $this->display( );
        }
    }



}
