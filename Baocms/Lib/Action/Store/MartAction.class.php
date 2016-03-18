<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MartAction extends CommonAction {

    private $create_fields = array('title', 'photo', 'cate_id', 'price', 'shopcate_id', 'mall_price', 'commission','instructions', 'details', 'end_date');
    private $edit_fields = array('title', 'photo', 'cate_id', 'price', 'shopcate_id', 'mall_price', 'commission','instructions', 'details', 'end_date');
	
    
    public function _initialize() {
        parent::_initialize();
        $this->autocates = D('Goodsshopcate')->where(array('shop_id' => $this->shop_id))->select();
        $this->assign('autocates', $this->autocates);

    }

     public function index() {

        
        $this->check_weidian();
        
        $Goods = D('Goods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id, 'is_mall' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = array('IN', D('Goodscate')->getChildren($cate_id));
            $this->assign('cate_id', $cate_id);
        }

        if ($audit = (int) $this->_param('audit')) {
            $map['audit'] = ($audit === 1 ? 1 : 0);
            $this->assign('audit', $audit);
        }
        $count = $Goods->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count,10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Goods->where($map)->order(array('goods_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val = $Goods->_format($val);
            $list[$k] = $val;
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('cates', D('Goodscate')->fetchAll());

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    
    
    
    public function goodscate() {
        $this->check_weidian();
        $autocates = D('Goodsshopcate')->order(array('orderby' => 'asc'))->where(array('shop_id' => $this->shop_id))->select();
        $this->assign('autocates',$autocates);

        $this->display();
    }
	
    
    
    public function order(){
        $this->check_weidian();
        $Ordergoods = D('Ordergoods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id' => $this->shop_id);
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
        
        $count = $Ordergoods->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Ordergoods->where($map)->order(array('id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $goods_ids = array();
        $order_ids = array();
        foreach($list as $val){
            $goods_ids[$val['goods_id']] = $val['goods_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
        }
        $this->assign('orders',D('Order')->itemsByIds($order_ids));
        $this->assign('goods',D('Goods')->itemsByIds($goods_ids));
        $this->assign('types',D('Ordergoods')->getType());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    
    
    
    
    public function wait() {
        $this->check_weidian();
        if(empty($this->shop['is_pei'])){
            $this->error('您签订的是由配送员配送！您管理不了订单！');
        }
        $Order = D('Order');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'status' => 1,'shop_id'=>  $this->shop_id,'is_shop'=>1);
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        if ($keyword) {
            $map['order_id'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }

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
        
         if (isset($_GET['st']) || isset($_POST['st'])) {
            $st = (int) $this->_param('st');
            if ($st != 999) {
                $map['status'] = $st;
            }
            $this->assign('st', $st);
        } else {
            $this->assign('st', 999);
        }
        
        // var_dump($map);die();
        $count = $Order->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Order->where($map)->order(array('order_id' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //print_r($Order->getLastSql());
        $user_ids = $order_ids  = $addr_ids = array();
        foreach ($list as $key => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
 
        }
        if (!empty($order_ids)) {
            $goods = D('Ordergoods')->where(array('order_id' => array('IN', $order_ids)))->select();
            $goods_ids = array();
            foreach ($goods as $val) {
                $goods_ids[$val['goods_id']] = $val['goods_id'];
            }
            $this->assign('goods', $goods);
            $this->assign('products', D('Goods')->itemsByIds($goods_ids));
        }
        $this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('types', D('Order')->getType());
        $this->assign('goodtypes', D('Ordergoods')->getType());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('picks', session('order'));
        $this->display(); // 输出模板
    }
    
    
    public function wait2() {
        $this->check_weidian();
          if(empty($this->shop['is_pei'])){
            $this->error('您签订的是由配送员配送！您管理不了订单！');
        }
        $Order = D('Order');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' =>0, 'status' =>0, 'is_daofu' =>1,'shop_id'=>$this->shop_id,'is_shop'=>1);
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        if($keyword){
            $map['order_id'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }

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
        
         if (isset($_GET['st']) || isset($_POST['st'])) {
            $st = (int) $this->_param('st');
            if ($st != 999) {
                $map['status'] = $st;
            }
            $this->assign('st', $st);
        } else {
            $this->assign('st', 999);
        }
        
        // var_dump($map);die();
        $count = $Order->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Order->where($map)->order(array('order_id' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids  = $addr_ids = array();
        foreach ($list as $key => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
 
        }
        if (!empty($order_ids)) {
            $goods = D('Ordergoods')->where(array('order_id' => array('IN', $order_ids)))->select();
            $goods_ids = array();
            foreach ($goods as $val) {
                $goods_ids[$val['goods_id']] = $val['goods_id'];
            }
            $this->assign('goods', $goods);
            $this->assign('products', D('Goods')->itemsByIds($goods_ids));
        }
        $this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('types', D('Order')->getType());
        $this->assign('goodtypes', D('Ordergoods')->getType());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('picks', session('order'));
        $this->display(); // 输出模板
    }
    
    
    
    
    
    
	
	private function check_weidian(){
        
        $wd = D('WeidianDetails');
        $wd_res = $wd->where('shop_id ='.($this->shop_id)) -> find();
        if(!$wd_res){
            $this->error('请用电脑登录，先完善微店资料！');
        }elseif($wd_res['audit'] == 0){
            $this->error('您的微店正在审核中，请耐心等待！');
        }elseif($wd_res['audit'] == 2){
            $this->error('您的微店未通过审核！');
        }
        
    }
	
	
	
    public function create() {
        if (IS_AJAX) {
            $obj = D('Goodsshopcate');
            $data['shop_id'] = $this->shop_id;
			$cate_name = I('cate_name','','trim,htmlspecialchars');
                        $orderby = I('orderby','','trim,intval');

			if (empty($cate_name)) {
				$this->ajaxReturn(array('status'=>'error','message'=>'分类不能为空!'));
			}
			$detail = D('Goodsshopcate')->where(array('shop_id'=>$this->shop_id,'cate_name'=>$cate_name))->select();
			if(!empty($detail)){
				$this->ajaxReturn(array('status'=>'error','message'=>'分类名称已存在!'));
			}
                        
			$data['orderby'] = $orderby;
			$data['cate_name'] = $cate_name;

            if ($obj->add($data)) {
				$this->ajaxReturn(array('status'=>'success','message'=>'添加成功!'));
            }
            $this->ajaxReturn(array('status'=>'error','message'=>'操作失败!'));
        } else {
            $this->display();
        }
    }


    
    public function edit($cate_id=0) {
	$cate_id = I('v','','intval,trim');
        

        if (IS_AJAX) {
        
            if ($cate_id) {
                $obj = D('Goodsshopcate');
                if (!$detail = $obj->find($cate_id)) {
                    $this->ajaxReturn(array('status'=>'error','message'=>'请选择要编辑的商家分类!'));
                }
                if($detail['shop_id'] != $this->shop_id){
                    $this->ajaxReturn(array('status'=>'error','message'=>'不可以修改别人的内容!'));
                }

                    $data = $this->editCheck();
                    $data['cate_id'] = $cate_id;
                    $data['shop_id'] = $this->shop_id;
            
                    if (false !== $obj->save($data)) {
                            $this->ajaxReturn(array('status'=>'success','message'=>'操作成功!'));
                    }
                    $this->ajaxReturn(array('status'=>'success','message'=>'操作失败!'));

            } else {
                    $this->ajaxReturn(array('status'=>'success','message'=>'请选择要编辑的商家分类!'));
            }
        
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    private function editCheck() {
        $data['shop_id'] = $this->shop_id;
        $cate_name = I('cate_name','','trim,htmlspecialchars');

        if (empty($cate_name)) {
           $this->ajaxReturn(array('status'=>'error','message'=>'分类不能为空!'));
        }

        $detail = D('Goodsshopcate')->where(array('shop_id'=>$this->shop_id,'cate_name'=>$cate_name))->find();

        if(!empty($detail) && ($detail['cate_id'] != $cate_id)){
            $this->ajaxReturn(array('status'=>'error','message'=>'分类名称已存在!'));
        }
        
        $data['orderby'] = I('orderby','','trim,intval');
        
        $data['cate_name'] = $cate_name;
        if (empty($data['orderby'])) {
           $data['orderby'] = 100;
        }
        
        return $data;
        
    }
    
    public function delete($cate_id=0){
        if (is_numeric($cate_id) && ($cate_id = (int) $cate_id)) {
            $obj = D('Goodsshopcate');
            if (!$detail = $obj->find($cate_id)) {
                $this->baoError('改分类不存在');
            }
            if($detail['shop_id'] != $this->shop_id){
                $this->baoError('改分类不存在');
            }
            
            $obj->delete($cate_id);
            $this->success('删除成功！', U('goodsshopcate/index'));
        }
    }
    
	
	
}
