<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class EleAction extends CommonAction {

    private $create_fields = array('product_name','desc', 'cate_id', 'photo', 'price', 'is_new', 'is_hot', 'is_tuijian', 'create_time', 'create_ip');
    private $edit_fields = array('product_name', 'desc','cate_id', 'photo', 'price', 'is_new', 'is_hot', 'is_tuijian');
	
	
	public function _initialize() {
        parent::_initialize();
        $this->ele = D('Ele')->find($this->shop_id);
        if(empty($this->ele)&& ACTION_NAME != 'apply'){
            $this->error('您还没有入住外卖频道,即将为您跳转！', U('eleapply/apply'));
        }

		if($this->ele['audit'] == 0){
            $this->error('您的外卖还在审核中哦！', U('eleapply/apply'));
        }
		if($this->ele['audit'] == 2){
            $this->error('您的审核未通过哦', U('eleapply/apply'));
        }
		
        $this->assign('ele',  $this->ele);
		//调用分类
		$getEleCate = D('Ele')->getEleCate();
        $this->assign('getEleCate', $getEleCate);
        $this->elecates = D('Elecate')->where(array('shop_id'=>$this->shop_id,'closed'=>0))->select();
        $this->assign('elecates', $this->elecates);
    }
	
	
	
	public function elecate() {
        $Elecate = D('Elecate');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed'=>'0');
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['cate_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($shop_id = $this->shop_id) {
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }

        $count = $Elecate->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Elecate->where($map)->order(array('cate_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $this->display(); // 输出模板
		
    }
	
	
	
	public function create() {
        if (IS_AJAX) {
            $shop_id = $this->shop_id;
			$cate_name = I('cate_name','','trim,htmlspecialchars');
			if(empty($cate_name)){
				$this->ajaxReturn(array('status'=>'error','message'=>'分类名称不能为空！'));
			}
            $obj = D('Elecate');
			$data = array(
				'shop_id'=>$shop_id,
				'cate_name'=>$cate_name,
				'num'=>0,
				'closed'=>0
			);
            if ($obj->add($data)) {
				$this->ajaxReturn(array('status'=>'success','message'=>'添加成功！'));
            }
            $this->ajaxReturn(array('status'=>'error','message'=>'添加失败！'));
        }
    }



	public function edit(){

	    if(IS_AJAX){
			
			$cate_id = I('v','','intval,trim');
			
			if ($cate_id) {
				
				$obj = D('Elecate');
				
				if (!$detail = $obj->find($cate_id)) {
					$this->ajaxReturn(array('status'=>'error','message'=>'请选择要编辑的菜单分类！'));
				}
				if ($detail['shop_id'] != $this->shop_id) {
					$this->ajaxReturn(array('status'=>'error','message'=>'请不要操作其他商家的菜单分类！'));
				}
				$cate_name = I('cate_name','','trim,htmlspecialchars');
				if (empty($cate_name)) {
					$this->ajaxReturn(array('status'=>'error','message'=>'分类名称不能为空！'));
				}
				
				$data = array(
					'cate_name'=>$cate_name,
				);
				if (false !== $obj->where('cate_id ='.$cate_id)->setField($data)) {
					$this->ajaxReturn(array('status'=>'success','message'=>'操作成功！'));
				}
				$this->ajaxReturn(array('status'=>'error','message'=>'操作失败！'));
			} else {
				$this->ajaxReturn(array('status'=>'error','message'=>'请选择要编辑的菜单分类！'));
			}
		
		}
	
    }
	
	
	public function index() {
        $Eleproduct = D('Eleproduct');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed'=>0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['product_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($shop_id = $this->shop_id) {
            $map['shop_id'] = $shop_id;
            $this->assign('shop_id', $shop_id);
        }
        $count = $Eleproduct->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Eleproduct->where($map)->order(array('product_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $cate_ids= array();
        foreach ($list as $k => $val) {
            if($val['cate_id']){
                $cate_ids[$val['cate_id']] = $val['cate_id'];
            }
        }
        if($cate_ids){
            $this->assign('cates',D('Elecate')->itemsByIds($cate_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	
	public function shelves() {
        $Eleproduct = D('Eleproduct');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed'=>1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['product_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($shop_id = $this->shop_id) {
            $map['shop_id'] = $shop_id;
            $this->assign('shop_id', $shop_id);
        }
        $count = $Eleproduct->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Eleproduct->where($map)->order(array('product_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $cate_ids= array();
        foreach ($list as $k => $val) {

            if($val['cate_id']){
                $cate_ids[$val['cate_id']] = $val['cate_id'];
            }
        }

        if($cate_ids){
            $this->assign('cates',D('Elecate')->itemsByIds($cate_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	
	public function eleorder() {
		$status = I('status','','intval,trim');
        $this->status = $status;
		$this->assign('status',$status);
        $this->showdata();
        $this->display(); // 输出模板
    }


	public function status(){
		$status = I('s','','trim,intval');
		$order_id = I('o','','trim,intval');
		$eo = D('EleOrder');
		$reo = $eo -> where('order_id ='.$order_id)-> find();
		if($status == 1){
			if($reo['status'] == 0){
				$up = $eo -> where('order_id ='.$order_id) -> setField('status',1);
			}else{
				$this->error('错误!');
			}
		}elseif($status == 2){
			if($reo['status'] == 1){
				$up = $eo -> where('order_id ='.$order_id) -> setField('status',2);
			}else{
				$this->error('错误!');
			}
		}elseif($status == 8){
			if($reo['status'] == 2){
				$up = $eo -> where('order_id ='.$order_id) -> setField('status',8);
			}else{
				$this->error('错误!');
			}
		}else{
			$this->error('错误!');
		}
		
		if($up){
			$this->success('设置成功!');
		}else{
			$this->error('失败!');
		}
		
	}
	
	 public function detail(){
        
        $order_id = I('order_id','','intval,trim');
        if(!$order = D('EleOrder')->find($order_id)){
            $this->error('错误');
        }else{
            $op = D('EleOrderProduct') -> where('order_id ='.$order['order_id']) -> select();
            if($op){
                $ids = array();
                foreach($op as $k => $v){
                    $ids[$v['product_id']] = $v['product_id'];
                }
                $ep = D('EleProduct')->where(array('product_id'=>array('in',$ids)))->select();
                $ep2 = array();
                foreach($ep as $kk => $vv){
                   $ep2[$vv['product_id']] = $vv;
                }
                $this->assign('ep',$ep2);
                $this->assign('op',$op);
                $addr = D('UserAddr')->find($order['addr_id']);
                $this->assign('addr',$addr);
                $do = D('DeliveryOrder')->where(array('type'=>1,'type_order_id'=>$order['order_id']))->find();
                if($do){
                    if($do['delivery_id']>0){
                        $delivery = D('Delivery')->find($do['delivery_id']);
                        $this->assign('delivery',$delivery);
                    }
                    
                    $this->assign('do',$do);
                }
            }
            
            
            $this->assign('order',$order);
            $this->display();
        }
        
    }	
	
	private function showdata() {
        $Eleorder = D('Eleorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id, 'status' => $this->status);
        if (($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date);
            $map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
            $this->assign('bg_date', $bg_date);
            $this->assign('end_date', $end_date);
        } else {
            if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
                $bg_time = strtotime($bg_date);
                $this->assign('bg_date', $bg_date);
                $map['create_time'] = array('EGT', $bg_time);
            }
            if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
                $end_time = strtotime($end_date);
                $this->assign('end_date', $end_date);
                $map['create_time'] = array('ELT', $end_time);
            }
        }

        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['order_id'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
		
		 if (isset($_GET['st']) || isset($_POST['st'])) {
            $st = (int) $this->_param('st');
            if ($st != 999) {
                $map['status'] = $st;
            }
            $this->assign('st', $st);
        } else {
            $this->assign('st', 999);
        }
		
        $count = $Eleorder->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Eleorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $user_ids = $order_ids = $addr_ids = array();
        foreach ($list as $key => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
        }
        if (!empty($order_ids)) {
            $goods = D('Eleorderproduct')->where(array('order_id' => array('IN', $order_ids)))->select();
            $goods_ids = array();
            foreach ($goods as $val) {
                $goods_ids[$val['product_id']] = $val['product_id'];
            }
            $this->assign('goods', $goods);
            $this->assign('products', D('Eleproduct')->itemsByIds($goods_ids));
        }
        $this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
		
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

    }
    // 下架菜单
	public function delete($product_id = 0) {
            $product_id = (int) $product_id;
			if (empty($product_id)) {
		  	$this->ajaxReturn(array('status'=>'error','msg'=>'访问错误！'));
            }
            $obj = D('Eleproduct');
            if(!$detail = $obj->where(array('shop_id'=>$this->shop_id,'product_id'=>$product_id))->find()){
                $this->ajaxReturn(array('status'=>'error','msg'=>'请选择要下架的菜单管理！'));
            }
            D('Elecate')->updateNum($detail['cate_id']);
            $obj->save(array('product_id' => $product_id, 'closed' => 1));
			$this->ajaxReturn(array('status'=>'success','msg'=>'下架成功', U('store/ele/shelves')));

		
    }
	// 上架菜单
	public function updates($product_id = 0) {
            $product_id = (int) $product_id;
			if (empty($product_id)) {
		  	$this->ajaxReturn(array('status'=>'error','msg'=>'访问错误！'));
            }
            $obj = D('Eleproduct');
            if(!$detail = $obj->where(array('shop_id'=>$this->shop_id,'product_id'=>$product_id))->find()){
                $this->ajaxReturn(array('status'=>'error','msg'=>'请选择要上架的菜单管理！'));
            }
            D('Elecate')->updateNum($detail['cate_id']);
            $obj->save(array('product_id' => $product_id, 'closed' => 0));
			$this->ajaxReturn(array('status'=>'success','msg'=>'上架成功', U('store/ele/index')));

		
    }
	
	//确认订单
	  public function send($order_id) { 
        $order_id = (int) $order_id;
        if (!$detail = D('Eleorder')->find($order_id)) {
            $this->error('没有该订单');
        }
        if ($detail['shop_id'] != $this->shop_id) {
            $this->error('您无权管理该商家');
        }
        if ($detail['status'] != 2) {
            $this->error('该订单状态不正确');
        }
        D('Eleorder')->overOrder($order_id);
        $this->Success('配送完成，资金已经结算到账户！', U('ele/eleorder',array('status'=>1)));
    }
	
	
	 public function create2() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Eleproduct');
            if ($obj->add($data)) {
                D('Elecate')->updateNum($data['cate_id']);
                $this->niuMsg('添加成功', U('ele/index'));
            }
            $this->niuMsg('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['product_name'] = htmlspecialchars($data['product_name']);
        if (empty($data['product_name'])) {
            $this->niuMsg('菜名不能为空');
        } 
		$data['desc'] = htmlspecialchars($data['desc']);
        if (empty($data['desc'])) {
            $this->niuMsg('菜单介绍不能为空');
        }
		
        $data['shop_id'] = $this->shop_id;
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->niuMsg('分类不能为空');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->niuMsg('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->niuMsg('缩略图格式不正确');
        }
        $data['price'] = (int) ($data['price']*100);
        if (empty($data['price'])) {
            $this->niuMsg('价格不能为空');
        } 
        
        $data['settlement_price'] = (int)( $data['price'] - ($data['price'] *  $this->ele['rate'] /1000));
        
        $data['is_new'] = (int) $data['is_new'];
        $data['is_hot'] = (int) $data['is_hot'];
        $data['is_tuijian'] = (int) $data['is_tuijian'];
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['audit'] = 0;
        return $data;
    }

    public function edit2($product_id = 0) {
        if ($product_id = (int) $product_id) {
            $obj = D('Eleproduct');
            if (!$detail = $obj->find($product_id)) {
                $this->error('请选择要编辑的菜单管理');
            }
            if($detail['shop_id'] != $this->shop_id){
                $this->error('请不要操作其他商家的菜单管理');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['product_id'] = $product_id;
                if (false !== $obj->save($data)) {
                    D('Elecate')->updateNum($data['cate_id']);
                    $this->niuMsg('操作成功', U('ele/index'));
                }
                $this->niuMsg('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->error('请选择要编辑的菜单管理');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['product_name'] = htmlspecialchars($data['product_name']);
        if (empty($data['product_name'])) {
            $this->niuMsg('菜名不能为空');
        }
		$data['desc'] = htmlspecialchars($data['desc']);
        if (empty($data['desc'])) {
            $this->niuMsg('菜单介绍不能为空');
        }
		$data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->niuMsg('分类不能为空');
        } 
		$data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->niuMsg('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->niuMsg('缩略图格式不正确');
        } $data['price'] = (int) ($data['price']*100);
        if (empty($data['price'])) {
            $this->niuMsg('价格不能为空');
        }
        $data['settlement_price'] = (int)( $data['price'] - ($data['price'] *  $this->ele['rate'] /1000));
        $data['is_new'] = (int) $data['is_new'];
        $data['is_hot'] = (int) $data['is_hot'];
        $data['is_tuijian'] = (int) $data['is_tuijian'];
        return $data;
    }
}
