<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class EleAction extends CommonAction {

    public function index() {
        $Eleorder = D('Eleorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid, 'closed' => 0); //这里只显示 实物
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
        $Page = new Page($count,10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Eleorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $result = D('Eledianping')->where(array('user_id'=>$this->uid))->select();
        $orders = array();
        foreach($result as $v){
            $orders[] = $v['order_id'];
        }
        $user_ids = $order_ids = $addr_ids = $shops_ids = array();
        foreach ($list as $k => $val) {
            if(in_array($val['order_id'],$orders)){
                $list[$k]['dianping'] = 1;
            }else{
                $list[$k]['dianping'] = 0;
            }
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
            $user_ids[$val['user_id']] = $val['user_id'];
            $shops_ids[$val['shop_id']] = $val['shop_id'];
        }
        if (!empty($shops_ids)) {
            $this->assign('shop_s', D('Shop')->itemsByIds($shops_ids));
        }
        if (!empty($order_ids)) {
            $products = D('Eleorderproduct')->where(array('order_id' => array('IN', $order_ids)))->select();
            $product_ids = $shop_ids = array();
            foreach ($products as $val) {
                $product_ids[$val['product_id']] = $val['product_id'];
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $this->assign('products', $products);
            $this->assign('eleproducts', D('Eleproduct')->itemsByIds($product_ids));
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('cfg', D('Eleorder')->getCfg());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出    
        $this->display();
    }
	
	
    public function dianping($order_id) {
        $order_id = (int) $order_id;
        if (!$detail = D('Eleorder')->find($order_id)) {
            $this->error('没有该订单');
        } else {
            if ($detail['user_id'] != $this->uid) {
                $this->error('不要评价别人的订餐订单');
                die();
            }
        }
        if (D('Eledianping')->check($order_id, $this->uid)) {
            $this->error('已经评价过了');
        }
        if ($this->_Post()) {
            $data = $this->checkFields($this->_post('data', false), array('score', 'speed', 'contents'));
            $data['user_id'] = $this->uid;
            $data['shop_id'] = $detail['shop_id'];
            $data['order_id'] = $order_id;
            $data['score'] = (int) $data['score'];
            if (empty($data['score'])) {
                $this->baoError('评分不能为空');
            }
            if ($data['score'] > 5 || $data['score'] < 1) {
                $this->baoError('评分为1-5之间的数字');
            }
            $data['speed'] = (int) $data['speed'];
            if(empty($data['speed'])){
                $this->baoError('送餐时间不能为空');
            }
            $data['contents'] = htmlspecialchars($data['contents']);
            if (empty($data['contents'])) {
                $this->baoError('评价内容不能为空');
            }
            if ($words = D('Sensitive')->checkWords($data['contents'])) {
                $this->baoError('评价内容含有敏感词：' . $words);
            }
            $data['show_date'] = date('Y-m-d', NOW_TIME); //15天生效
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            if (D('Eledianping')->add($data)) {
                $photos = $this->_post('photos', false);
                $local = array();
                foreach ($photos as $val) {
                    if (isImage($val))
                        $local[] = $val;
                }
                if (!empty($local))
                    D('Eledianpingpics')->upload($order_id, $local);
                //D('Users')->prestige($this->uid, 'dianping');
                D('Users')->updateCount($this->uid, 'ping_num');
                $this->baoSuccess('恭喜您点评成功!', U('ele/index'));
            }
            $this->baoError('点评失败！');
        }else {
            $details = D('Shop')->find($detail['shop_id']);
            $this->assign('details', $details);
            $this->assign('order_id', $order_id);
            $this->display();
        }
    }
    
    //
    public function yes($order_id=0){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            if (!$detial = D('Eleorder')->find($order_id)) {
                $this->baoError('您确认收货的订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作别人的订单');
            }
         
            if ($detial['status'] != 2) {
                $this->baoError('当前状态不能确认收货');
            }
            $obj = D('Eleorder');
            $obj->save(array('order_id' => $order_id, 'status' => 3));
            $this->baoSuccess('确认收货成功！', U('ele/index'));
        } else {
            $this->baoError('请选择要确认收货的订单');
        }
    }


    public function elecancle($order_id = 0) {
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $member = D('Users')->find($this->uid);
            if (!$detial = D('Eleorder')->find($order_id)) {
                $this->baoError('您取消的订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作别人的订单');
            }
            if ($detial['is_pay'] == 0) {
                $this->baoError('当前状态不能退款');
            }
            if ($detial['status'] != 1) {
                $this->baoError('当前状态不能退款');
            }
			 if (D('Eleorder')->save(array('order_id' => $order_id, 'status' => 3))) {
                $this->baoSuccess('申请成功！等待网站客服处理！', U('ele/index'));
            }
        }
        $this->baoError('操作失败');

    }
	 public function eleqxtk($order_id = 0) {
		  if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $member = D('Users')->find($this->uid);
            if (!$detial = D('Eleorder')->find($order_id)) {
                $this->baoError('您取消的订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作别人的订单');
            }
			            
            if ($detial['status'] != 3) {
                $this->baoError('当前状态不能退款');
            }
			 if (D('Eleorder')->save(array('order_id' => $order_id, 'status' => 1))) {
                $this->baoSuccess('取消退款成功！', U('ele/index'));
            }
        }
        $this->baoError('操作失败');

 }

    public function delete($order_id = 0) {
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $member = D('Users')->find($this->uid);
            if (!$detial = D('Eleorder')->find($order_id)) {
                $this->baoError('您删除的订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作别人的订单');
            }
           
            if ($detial['status'] != 0 && $detial['status'] != 8 && $detial['status'] != 4) {
                $this->baoError('当前状态不能删除');
            }
			if (D('Eleorder')->save(array('order_id' => $order_id, 'closed' => 1))) {
          
            $this->baoSuccess('删除成功！', U('ele/index'));
         } 
        $this->baoError('操作失败');

     	}
    }
}