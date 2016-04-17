<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class OrderAction extends CommonAction {

    public function index() {
        $Tuanorder = D('Tuanorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid,'closed' =>0); //这里只显示 实物
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
        $count = $Tuanorder->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuanorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
        $shop_ids = $tuan_ids = $order_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val['shop_id'])) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $order_ids[$val['order_id']] = $val['order_id'];
            $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
        }
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('tuan', D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('dianping', D('Tuandianping')->itemsByIds($order_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出    
        $this->display();
    }
	//PC团购详情
	 public function detail($order_id){
            $order_id = (int) $order_id;
            if(empty($order_id) || !$detail = D('Tuanorder')->find($order_id)){
                $this->error('该订单不存在');
            }
            if($detail['user_id'] != $this->uid){
                $this->error('请不要操作他人的订单');
            }
            if(!$dianping = D('Tuandianping')->where(array('order_id'=>$order_id,'user_id'=>$this->uid))->find()){
                $detail['dianping'] = 0;
            }else{
                $detail['dianping'] = 1;
            }
            $this->assign('tuans',D('Tuan')->find($detail['tuan_id']));
            $this->assign('detail',$detail);
            $this->display();
        }
		
		//PC商城详情
	public function details($order_id){
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

    public function noindex() {
        $Tuanorder = D('Tuanorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid,'closed' =>0); //这里只显示 实物
        $lists = $Tuanorder->where($map)->order(array('order_id' => 'desc'))->select();
        $dianping = D('Tuandianping')->where(array('user_id' => $this->uid))->select();
        $orders = array();
        foreach ($dianping as $key => $v) {
            $orders[] = $v['order_id'];
        }
        foreach ($lists as $kk => $vv) {
            if (in_array($vv['order_id'], $orders)) {
                unset($lists[$kk]);
            }
        }
        $count = count($lists);  // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出  
        $list = array_slice($lists, $Page->firstRow, $Page->listRows);
        $shop_ids = $tuan_ids = $order_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val['shop_id'])) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $order_ids[$val['order_id']] = $val['order_id'];
            $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
        }
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('tuan', D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('dianping', D('Tuandianping')->itemsByIds($order_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出    
        $this->display();
    }

    public function delete($order_id = 0) {//根据订单id删除订单,删除订单后应该返回使用的积分
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $obj = D('Tuanorder');
            if (!$detial = $obj->find($order_id)) {
                $this->baoError('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作他人的订单');
            }
            if ($detial['status'] != 0) {
                $this->baoError('该订单暂时不能删除');
            }
			
			if ($detial['closed'] == 1) {//如果是关闭状态，防止刷分
                $this->baoError('该订单已关闭');die;
            }
		
            //先返还积分才行
			
			if($obj->save(array('order_id'=>$order_id,'closed'=>1))){
                    D('Users')->addIntegral($detial['user_id'],$detial['use_integral'],'取消抢购订单'.$detial['order_id'].'积分退还');
            }
			
            //$obj->delete($order_id);
            $this->baoSuccess('删除成功！', U('order/index'));
        } else {
            $this->baoError('请选择要删除的订单');
        }
    }

    //我的订单
    public function goods() {
        $Order = D('Order');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'user_id' => $this->uid);
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

        // var_dump($map);die();
        $count = $Order->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Order->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
        $user_ids = $order_ids = $shop_ids = $addr_ids = array();
        foreach ($list as $key => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
		
		
		
		
        if (!empty($shop_ids)) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
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
        $this->display(); // 输出模板
    }
    
    public function goodsshou($order_id=0){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
             $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->baoError('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作他人的订单');
            }
            if ($detial['status'] != 2) {
                $this->baoError('该订单暂时不能确定收货');
            }
             if($obj->save(array('order_id'=>$order_id,'status'=>3))){
				  D('Order')->overOrder($order_id); //确认到账入口
				  
				 
                  $this->baoSuccess('确认订单成功！', U('order/goods'));
             }
        } else {
            $this->baoError('请选择要确认收货的订单');
        }
    }
	
	public function daofu_goodsshou($order_id=0){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
             $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->baoError('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作他人的订单');
            }
            if ($detial['status'] != 2) {
                $this->baoError('该订单暂时不能确定收货');
            }
             if($obj->save(array('order_id'=>$order_id,'status'=>3))){
                  $this->baoSuccess('确认到付订单成功！', U('order/goods'));
             }
        } else {
            $this->baoError('请选择要确认收货的到付订单');
        }
    }
	
	

    public function goodsdel($order_id = 0) {

        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->baoError('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作他人的订单');
            }
            if ($detial['status'] != 0) {
                $this->baoError('该订单暂时不能取消');
            }
            if($obj->save(array('order_id'=>$order_id,'closed'=>1))){
                if($detial['use_integral']){
                    D('Users')->addIntegral($detial['user_id'],$detial['use_integral'],'取消订单'.$detial['order_id'].'积分退还');
                }
            }
            $this->baoSuccess('取消订单成功！', U('order/goods'));
        } else {
            $this->baoError('请选择要取消的订单');
        }
    }
	
	 public function dianping($order_id) {
        $order_id = (int) $order_id;
        if (!$detail = D('Order')->find($order_id)) {
            $this->baoError('没有该商品');
        } else {
            if ($detail['user_id'] != $this->uid) {
                $this->baoError('不要评价别人的商品');
                die();
            }
        }
        if (D('Goodsdianping')->check($order_id, $this->uid)) {
            $this->baoError('已经评价过了');
        }
		$goodss = D('Ordergoods')->where('order_id ='.$detail['order_id']) -> find();
		$goods_id = $goodss['goods_id'];
		
        if ($this->_Post()) {
            $data = $this->checkFields($this->_post('data', false), array('score', 'cost', 'contents'));
            $data['user_id'] = $this->uid;
            $data['shop_id'] = $detail['shop_id'];
            $data['goods_id'] = $goods_id;
            $data['order_id'] = $order_id;
            $data['score'] = (int) $data['score'];
            if (empty($data['score'])) {
                $this->baoError('评分不能为空');
            }
            if ($data['score'] > 5 || $data['score'] < 1) {
                $this->baoError('评分为1-5之间的数字');
            }
            $data['cost'] = (int) $data['cost'];
            $data['contents'] = htmlspecialchars($data['contents']);
            if (empty($data['contents'])) {
                $this->baoError('评价内容不能为空');
            }
            if ($words = D('Sensitive')->checkWords($data['contents'])) {
                $this->baoError('评价内容含有敏感词：' . $words);
            }
			$data_mall_dianping = $this->_CONFIG['mobile']['data_mall_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_mall_dianping * 86400); //15天生效

            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
			
            if (D('Goodsdianping')->add($data)) {
                $photos = $this->_post('photos', false);
                $local = array();
                foreach ($photos as $val) {
                    if (isImage($val))
                        $local[] = $val;
                }
                if (!empty($local))
                    D('Goodsdianpingpics')->upload($order_id, $local);
                D('Order')->save(array('order_id'=>$order_id,'is_dianping'=>1));
                D('Users')->prestige($this->uid, 'dianping');
                D('Users')->updateCount($this->uid, 'ping_num');
                $this->baoSuccess('恭喜您点评商品成功!', U('member/order/goods'));
            }
            $this->baoError('点评失败！');
        }else {
            $goodsdetails = D('Goods')->where('goods_id ='.$goods_id) -> find();
            $this->assign('goodsdetails', $goodsdetails);
            $this->assign('order_id', $order_id);
            $this->display();
        }
    }
	

}
