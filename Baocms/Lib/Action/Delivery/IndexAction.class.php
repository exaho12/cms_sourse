<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class IndexAction extends CommonAction {

    public function index() {
        
            if(!cookie('DL')){
                header("Location: " . U('login/index'));
            }else{
                 $cid = $this->reid();
                 $dv = D('Delivery');
                 $rdv = $dv -> where('id ='.$cid) -> find();
                 if(!$rdv){
                     header("Location: " . U('login/logout'));
                 }else{
                     $this->assign('rdv',$rdv);
                 }
                 
                 //未配送订单
                 $do = D('DeliveryOrder');

                 
                 $today = strtotime(date('Y-m-d'));
                 
                 //今日配送
                 $today_p = $do -> where('update_time >='.$today.' and delivery_id ='.$cid.' and status =2') -> count();
				 
                 $this->assign('today_p',$today_p);
                 //p($today_p);
                 //今日完成
                 $today_ok = $do -> where('update_time >='.$today.' and delivery_id ='.$cid.' and status =8') -> count();
                 $this->assign('today_ok',$today_ok);
                 
                 //总计完成
                 $all_ok = $do -> where('delivery_id ='.$cid.' and status =8') -> count();
                 $this->assign('all_ok',$all_ok);
                 
                 //抢新单
                 $new = $do -> where('status <2 and delivery_id =0') ->count();
                 $this->assign('new',$new);
                 
                 //配送中
                 $ing = $do -> where('status = 2 and delivery_id ='.$cid) -> count();
                 $this->assign('ing',$ing);
                 
                 //已完成
                 $ed = $do -> where('status = 8 and delivery_id ='.$cid) -> count();
                 $this->assign('ed',$ed);
                 
                 $this->display();
            }

    }
	
	//快递众包统计
	
	 public function express( ){
        if ( !cookie( "DL" ) ){
            header( "Location: ".u( "login/index" ) );
        }
        else{
            $cid = $this->reid( );
            $dv = d( "Delivery" );
            $rdv = $dv->where( "id =".$cid )->find( );
            if ( !$rdv ){
                header( "Location: ".u( "login/logout" ) );
            }
            else{
                $this->assign( "rdv", $rdv );
            }
            $express = d( "Express" );
            $today = strtotime( date( "Y-m-d" ) );
            $today_p = $express->where( "update_time >=".$today." and cid =".$cid." and status =1 and city_id =".$this->city_id )->count( );
            $this->assign( "today_p", $today_p );
            $today_ok = $express->where( "update_time >=".$today." and cid =".$cid." and status =2 and city_id =".$this->city_id )->count( );
            $this->assign( "today_ok", $today_ok );
            $all_ok = $express->where( "cid =".$cid." and status =2 and city_id =".$this->city_id )->count( );
            $this->assign( "all_ok", $all_ok );
            $new = $express->where( "status = 0 and cid =0 and city_id =".$this->city_id )->count( );
            $this->assign( "new", $new );
            $ing = $express->where( "status = 1 and cid =".$cid." and city_id =".$this->city_id )->count( );
            $this->assign( "ing", $ing );
            $ed = $express->where( "status = 2 and cid =".$cid." and city_id =".$this->city_id )->count( );
            $this->assign( "ed", $ed );
		
            $this->display( );
        }
    }
	
    
	//定位
	
	 public function dingwei(){
        $lat = $this->_get( "lat", "htmlspecialchars" );
        $lng = $this->_get( "lng", "htmlspecialchars" );
        cookie( "lat", $lat );
        cookie( "lng", $lng );
        echo NOW_TIME;
    }
	
	//资金
	
	 public function money(){
         if(!cookie('DL')){
                header("Location: " . U('login/index'));
            }else{
                 $cid = $this->reid();
                 $dv = D('Delivery');
                 $rdv = $dv -> where('id ='.$cid) -> find();
                 if(!$rdv){
                     header("Location: " . U('login/logout'));
                 }else{
                     $this->assign('rdv',$rdv);
                 }
				 
				$do = D('DeliveryOrder');
				$deliveryOrder = $do -> where('delivery_id ='.$cid) -> count();
				
				$ex = d( "Express" );
				$express = $ex -> where('cid ='.$cid) -> count();
				$statistics = $deliveryOrder+ $express;//一共配送多少
				$this->assign('statistics',$statistics);
				
				$price = $this->_CONFIG['mobile']['delivery_price'];//单价
				$this->assign('price',$price);
				$total= $statistics*$price;//总价
				$this->assign('total',$total);

                $this->display();
            }
    }


	 public function lists( ){
		 
        $id = i( "id", "", "intval,trim" );
		
		$cid = $this->reid();
        $dv = D('Delivery');
        $rdv = $dv -> where('id ='.$cid) -> find();
        if(!$rdv){
       		  header("Location: " . U('login/logout'));
        }else{
        		$this->assign('rdv',$rdv);
         }
		 
		 
        if ( !$id ){
            $this->error( "没有选择！" );
        } 			 
        else{
			
			
            $this->assign( "delivery", d( "Delivery" )->where( "id =".$id )->find( ) );
            $dvo = d( "DeliveryOrder" );
            import( "ORG.Util.Page" );
			
			$count = $dvo->where( "delivery_id =".$id )->count( );
			$Page = new Page( $count, 5 );
			$show = $Page->show( );
			$var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
			$p = $_GET[$var];
			if ( $Page->totalPages < $p ){
				exit( "0" );
			}
			
            $list = $dvo->where( "delivery_id =".$id )->order( "order_id desc" )->limit( $Page->firstRow.",".$Page->listRows )->select( );
            $this->assign( "list", $list );
            $this->assign( "page", $show );
			$this->assign( "count", $count );
            $this->display( );
        }
    }
	
	 public function expresslists( ){
        $id = i( "id", "", "intval,trim" );
		
		$cid = $this->reid();
        $dv = D('Delivery');
        $rdv = $dv -> where('id ='.$cid) -> find();
        if(!$rdv){
       		  header("Location: " . U('login/logout'));
        }else{
        		$this->assign('rdv',$rdv);
         }
		 
        if ( !$id ){
            $this->error( "没有选择！" );
        }
		
		
        else{
            $this->assign( "delivery", d( "Delivery" )->where( "id =".$id )->find( ) );
            $express = d( "Express" );
            import( "ORG.Util.Page" );
			
			$count = $express->where( "cid =".$id )->count( );
			$Page = new Page( $count, 5 );
			$show = $Page->show( );
			$var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
			$p = $_GET[$var];
			if ( $Page->totalPages < $p ){
				exit( "0" );
			}
			
            $list = $express->where( "cid =".$id )->order( "express_id desc" )->limit( $Page->firstRow.",".$Page->listRows )->select( );
            $this->assign( "list", $list );
            $this->assign( "page", $show );
			$this->assign( "count", $count );
            $this->display( );
        }
    }
}