<?php

class DingAction extends CommonAction {

    public function index() {
		$aready = $this->_get('aready');
		if(!$aready){
			$aready = 999;
		}
		$this->assign('aready', $aready);
        $this->display();
    }

	public function loaddata()
	{
		$dingorder = D('Shopdingorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $dingorder->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count,10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		
		if(intval($this->_get('status')) != 999 ){
			$map['status'] = $this->_get('status');
		}
		if(intval($this->_get('status')) == '-2' ){
			$map['status'] = 0;
		}
		
        $list = $dingorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids  = $shops_ids = array();
        foreach ($list as $k => $val) {
            $order_ids[$val['order_no']] = $val['order_no'];
            $shops_ids[$val['shop_id']] = $val['shop_id'];
        }
        if (!empty($shops_ids)) {
            $this->assign('shop_s', D('Shop')->itemsByIds($shops_ids));
        }
        if (!empty($order_ids)) {
            $yuyue = D('Shopdingyuyue')->where(array('order_no' => array('IN', $order_ids)))->select();
            $shop_ids = array();
            foreach ($yuyue as $val) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
				$yuyues[$val['ding_id']] = $val;
            }
			$yuyue_d = $dingorder->get_d($yuyues);
            $this->assign('yuyue', $yuyue_d);
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
	}

	public function detail($order_id)
	{
		$dingorder = D('Shopdingorder');
		$dingyuyue = D('Shopdingyuyue');
		$dingmenu = D('Shopdingmenu');
		if(!$order = $dingorder->where('order_id = '.$order_id)->find()){
			$this->error('该订单不存在');
		}else if(!$yuyue = $dingyuyue->where('ding_id = '.$order['ding_id'])->find()){
			$this->error('该订单不存在');
		}else if($yuyue['user_id'] != $this->uid){
			$this->error('非法操作');
		}else{
			$arr = $dingorder->get_detail($yuyue['shop_id'],$order,$yuyue);
			$menu = $dingmenu->shop_menu($yuyue['shop_id']);
			$this->assign('yuyue', $yuyue);
			$this->assign('order', $order);
			$this->assign('order_id', $order_id);
			$this->assign('arr', $arr);
			$this->assign('menu', $menu);
			$this->display();
		}
	}
	
	
	 public function dianping( $order_id ){
        $order_id = (int)$order_id;
        $dingorder = D( "Shopdingorder" );
        if ( !( $detail = $dingorder->find( $order_id ) ) ){
            $this->error( "没有该订单" );
        }
        else{
            if ( $detail['user_id'] != $this->uid ){
                $this->error( "不要评价别人的订座订单" );
                exit( );
            }
        }
        $dingsetting = D( "Shopdingsetting" )->find( $detail['shop_id'] );
        $yuyue = D( "Shopdingyuyue")->where( "ding_id =".$detail['ding_id'] )->find( );
        $room = D( "Shopdingroom")->find( $yuyue['room_id'] );
        if ($detail['is_dianping']){
            $this->niuMsg( "已经评价过了", U( "ding/index" ));
        }
        if ($this->_Post()){
            $data = $this->checkFields( $this->_post( "data", FALSE ), array( "score", "speed", "contents" ) );
            $data['user_id'] = $this->uid;
            $data['shop_id'] = $yuyue['shop_id'];
            $data['order_id'] = $order_id;
            $data['score'] = ( integer )$data['score'];
            if ( empty( $data['score'] ) ){
                $this->niuMsg( "评分不能为空" );
            }
            if ( 5 < $data['score'] || $data['score'] < 1 ){
                $this->niuMsg( "评分为1-5之间的数字" );
            }
            $data['contents'] = htmlspecialchars( $data['contents'] );
            if ( empty( $data['contents'] ) ){
                $this->niuMsg( "评价内容不能为空" );
            }
            if ( $words = d( "Sensitive" )->checkWords( $data['contents'] ) ){
                $this->niuMsg( "评价内容含有敏感词：".$words );
            }
            $data_ding_dianping = $this->_CONFIG['mobile']['data_ding_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_ding_dianping * 86400); //15天生效
			
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip( );
            if ( d( "Shopdingdianping" )->add( $data ) ){
                $photos = $this->_post( "photos", FALSE );
                $local = array( );
                foreach ( $photos as $val ){
                    if ( isimage( $val ) ){
                        $local[] = $val;
                    }
                }
                if ( !empty( $local ) ){
                    D( "Shopdingdianpingpic" )->upload( $order_id, $local );
                }
                D( "Shopdingorder" )->updateCount( $order_id, "is_dianping" );
                D( "Users" )->updateCount( $this->uid, "ping_num" );
                $this->niuMsg( "恭喜您点评成功!", u( "ding/index" ) );
            }
            $this->niuMsg( "点评失败！" );
        }
        else{
            $this->assign( "detail", $detail );
            $details = d( "Shop" )->find( $detail['shop_id'] );
            $this->assign( "details", $details );
            $this->assign( "dingsetting", $dingsetting );
            $this->assign( "room", $room );
            $this->assign( "order_id", $order_id );
            $this->display( );
        }
    }
	
	
}
