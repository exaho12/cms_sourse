<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ListsAction extends CommonAction {

    public function index() {
        if(!cookie('DL')){
		header("Location: " . U('login/index'));
		$keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
		
		}else{
			$cid = $this->reid();
			$dv = D('DeliveryOrder');
			//条件开始先删除多城市
			/*$map = array(
                "city_id" => $this->city_id
            );*/
			
	
			
            $ss = i( "ss", 0, "intval,trim" );
            $this->assign( "ss", $ss );
			
			//增加搜索开始
			if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
			$map['shop_name|addr'] = array('LIKE', '%' . $keyword . '%');
			}
			$area = (int) $this->_param('area');//搜索地区
			if ($area) {
				$map['area_id'] = $area;
			}
			$business = (int) $this->_param('business');//搜索商圈
			if ($business) {
				$map['business_id'] = $business;
			}
		    //增加搜索结束
		
            if ( $ss == 2 ) {
                $map['status'] = 2;
                $map['delivery_id'] = $cid;
            }
            else if ( $ss == 8 ){
                $map['status'] = 8;
                $map['delivery_id'] = $cid;
            }
            else{
                $map['status'] = array( "lt", 2 );
                $map['delivery_id'] = 0;
            }
			//条件结束
			//计算那个距离开始
			$lat = addslashes( cookie( "lat" ) );
            $lng = addslashes( cookie( "lng" ) );
            if ( empty( $lat ) || empty( $lng ) )
            {
                $lat = $this->city['lat'];
                $lng = $this->city['lng'];
            }
            $orderby = " (ABS(lng - '".$lng."') +  ABS(lat - '{$lat}') ) asc ";
            $rdv = $dv->where( $map )->order( $orderby )->select( );//赋值过程
            $shop_ids = array( );
            foreach ( $rdv as $k => $val )
            {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
                $rdv[$k]['d'] = getdistance( $lat, $lng, $val['lat'], $val['lng'] );
            }
			
			
			$this->assign( "ex", d( "Shopdetails" )->itemsByIds( $shop_ids ) );
			//计算那个距离结

            $this->assign('rdv',$rdv);
		}
		
		$this->display();      
    }
    
  
	
    public function handle(){
        
        if(IS_AJAX){
            
            $id = I('order_id',0,'trim,intval');
            
            $dvo = D('DeliveryOrder');
            
            if(!cookie('DL')){
                $this->ajaxReturn(array('status'=>'error','message'=>'您还没有登录或登录超时!'));
            }else{
                $f = $dvo -> where('order_id ='.$id) -> find();
                if(!$f){
                    $this->ajaxReturn(array('status'=>'error','message'=>'错误!'));
                }else{
                    $cid = $this->reid(); //获取配送员ID
                    $data = array(
                        'delivery_id' => $cid,
                        'status' => 2,
						 'update_time' => time()
                    );
					
					
                    $up = $dvo -> where('order_id ='.$id) -> setField($data);
                    if($up){
                        
                        if($f['type'] == 0){//商城
                            $old = D('Order');
                        }elseif($f['type'] == 1){//外卖
                            $old = D('EleOrder');
                        }

                        $old_up = $old -> where('order_id ='.$f['type_order_id']) -> setField('status',2);
						$old_up = D('Ordergoods') -> where('order_id ='.$f['type_order_id']) -> setField('status',1);
					
                        $this->ajaxReturn(array('status'=>'success','message'=>'恭喜您！接单成功！请尽快进行配送！'));
                    }else{
                        $this->ajaxReturn(array('status'=>'error','message'=>'接单失败！错误！'));
                    }
                }
            }
            
        }
        
        
    }
    

    
    public function set_ok(){
        if(IS_AJAX){
            $id = I('order_id',0,'trim,intval');
            $dvo = D('DeliveryOrder');
            if(!cookie('DL')){
                $this->ajaxReturn(array('status'=>'error','message'=>'您还没有登录或登录超时!'));
            }else{
                $f = $dvo -> where('order_id ='.$id) -> find();
                if(!$f){
                    $this->ajaxReturn(array('status'=>'error','message'=>'错误1!'));
                }else{
                    $cid = $this->reid(); //获取配送员ID
                    if($cid == 5){
                       $this->ajaxReturn(array('status'=>'error','message'=>'演示站不提供数据操作!'));
                    }
                    if($f['delivery_id'] != $cid){
                        $this->ajaxReturn(array('status'=>'error','message'=>'错误2!'));
                    }else{
                        $up = $dvo -> where('order_id ='.$id)-> setField('status',8);
                        if(!$up){
                            $this->ajaxReturn(array('status'=>'error','message'=>'操作失败!'));
                        }else{
                            
                            if($f['type'] == 0){
                                $old = D('Order');
                            }elseif($f['type'] == 1){
                                $old = D('EleOrder');
                            }
                            $old_up = D('EleOrder') -> where('order_id ='.$f['type_order_id']) -> setField('status',2);//更新外卖,暂时给一步骤确认
							$old_up = D('Order') -> where('order_id ='.$f['type_order_id']) -> setField('status',2);//商城暂时不更新，进入用户确认
							
                            $this->ajaxReturn(array('status'=>'success','message'=>'操作成功!'));
                            
                        }
                       
                    }
                }
            }
        }
    }
	
	  //快递众包开始
	public function express( ){
        if ( !cookie( "DL" ) ){
            header( "Location: ".u( "login/index" ) );
        }
        else{
            $cid = $this->reid( );
            $express = d( "Express" );
            $map = array(
                "city_id" => $this->city_id
            );
            $ss = i( "ss", 0, "intval,trim" );
            $this->assign( "ss", $ss );
            if ( $ss == 1 ){
                $map['status'] = 1;
                $map['cid'] = $cid;
            }
            else if ( $ss == 2 ){
                $map['status'] = 2;
                $map['cid'] = $cid;
            }
            else{
                $map['status'] = 0;
                $map['cid'] = 0;
            }
            $lat = addslashes( cookie( "lat" ) );
            $lng = addslashes( cookie( "lng" ) );
            if ( empty( $lat ) || empty( $lng ) ){
                $lat = $this->city['lat'];
                $lng = $this->city['lng'];
            }
            $orderby = " (ABS(lng - '".$lng."') +  ABS(lat - '{$lat}') ) asc ";
            $rdv = $express->where( $map )->order( $orderby )->select( );
            foreach ( $rdv as $k => $val ){
                $rdv[$k]['d'] = getdistance( $lat, $lng, $val['lat'], $val['lng'] );
            }
			
            $this->assign( "rdv", $rdv );
		
        }
        $this->display( );
    }
	
	//快递众包结束
	
	
	
   //强快递开始
	public function qiang( ){
        if ( IS_AJAX ){
            $express_id = i( "express_id", 0, "trim,intval" );
            $express = d( "Express" );
            if ( !cookie( "DL" ) ){
                $this->ajaxReturn( array( "status" => "error", "message" => "您还没有登录或登录超时!" ) );
            }
            else{
                $detail = $express->find( $express_id );
                if ( !$detail ){
                    $this->ajaxReturn( array( "status" => "error", "message" => "快递不存在!" ) );
                }
                if ( $detail['status'] != 0 || $detail['closed'] != 0 ){
                    $this->ajaxReturn( array( "status" => "error", "message" => "该快递状态不支持抢单!" ) );
                }
                $cid = $this->reid( );
                $data = array(
                    "express_id" => $express_id,
                    "cid" => $cid,
                    "status" => 1,
                    "update_time" => NOW_TIME
                );
                if ( FALSE !== $express->save( $data ) ){
                    $this->ajaxReturn( array( "status" => "success", "message" => "恭喜您！接单成功！请尽快进行配送！" ) );
                }
                else{
                    $this->ajaxReturn( array( "status" => "error", "message" => "接单失败！错误！" ) );
                }
            }
        }
    }

	//快递确认
	public function express_ok( ){
        if ( IS_AJAX ){
            $express_id = i( "express_id", 0, "trim,intval" );
            $express = d( "Express" );
            if ( !cookie( "DL" ) ){
                $this->ajaxReturn( array( "status" => "error", "message" => "您还没有登录或登录超时!" ) );
            }
            else
            {
                $detail = $express->find( $express_id );
                if ( !$detail ){
                    $this->ajaxReturn( array( "status" => "error", "message" => "快递不存在!" ) );
                }
                if ( $detail['status'] != 1 || $detail['closed'] != 0 ){
                    $this->ajaxReturn( array( "status" => "error", "message" => "该快递状态不能完成!" ) );
                }
                $cid = $this->reid( );
                if ( $detail['cid'] != $cid ){
                    $this->ajaxReturn( array( "status" => "error", "message" => "不能操作别人的快递!" ) );
                }
                if ( FALSE !== $express->save( array(
                    "express_id" => $express_id,
                    "status" => 2
                ) ) ){
                    $this->ajaxReturn( array( "status" => "success", "message" => "恭喜您完成订单" ) );
                }
                else{
                    $this->ajaxReturn( array( "status" => "error", "message" => "操作失败！" ) );
                }
            }
        }
    }

	//快递确认结束


//语音通知

 public function get_message(){
        
        if(IS_AJAX){
            $last_time = cookie('last_time');
            cookie('last_time',time(),86400*30); //存一个月 
            if(empty($last_time)){  
                $this->ajaxReturn(array('status'=>'0','message'=>'开始抢单了!'));
            }
            else{
                $cid = $this->reid();
            $delivery_type = D('Delivery')->where('id='.$cid)->getField('delivery_type');
			//$dv = D('DeliveryOrder');
            $t_e = C('DB_PREFIX').'ele_order';
            $t_d = C('DB_PREFIX').'delivery_order';
            $t_o = C('DB_PREFIX').'order';
            $dv = D('DeliveryOrder')->join($t_e.' on '.$t_d.'.type_order_id = '.$t_e.'.order_id');
            $dv = $dv->join($t_o.' on '.$t_d.'.type_order_id = '.$t_o.'.order_id');
			$map = array();
            if($delivery_type == 0){
                $map['_string'] = '('.$t_e.'.is_pay = 1 or '.$t_o.'.is_daofu = 0) ';
            }
            elseif($delivery_type == 1){
			    $map['_string'] = '('.$t_e.'.is_pay = 0 or '.$t_o.'.is_daofu = 1 ) ';
            }
            $map['_string'] = $map['_string'].'and '.$t_d.'.create_time>='.$last_time.' and '.$t_d.'.status <2 and '.$t_d.'.delivery_id =0';
            $count = $dv -> where($map) -> count();
           
            if($count>0)
                $this->ajaxReturn(array('status'=>'2','message'=>'有新的订单了!'));
            else
                $this->ajaxReturn(array('status'=>'1','message'=>''));
            }
        }
        
    }

}