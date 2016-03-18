<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class DianpingAction extends CommonAction {

    public function index() {
        $Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id);
        $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $shop_ids = array();
        foreach ($list as $k => $val) {
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function reply($dianping_id) {
        $dianping_id = (int) $dianping_id;
        $detail = D('Shopdianping')->find($dianping_id);
        if (empty($detail) || $detail['shop_id'] != $this->shop_id) {
            $this->error('没有该内容');
        }
        if ($this->isPost()) {
            if ($reply = $this->_param('reply', 'htmlspecialchars')) {
                $data = array('dianping_id' => $dianping_id, 'reply' => $reply);
                if (D('Shopdianping')->save($data)) {
                    $this->error('回复成功', U('dianping/index'));
                }
            }
            $this->error('请填写回复');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function waimai() {
        $eledianping = D('Eledianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id, 'show_date' => array('ELT', TODAY));
        $count = $eledianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $eledianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //dump($eledianping->getLastSql());
        
        $user_ids = $order_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($order_ids)) {
            $this->assign('pics', D('Eledianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
        }
        
        foreach($list as $key=>$v){
            if(in_array($v['order_id'], $order_ids)){
                $list[$key]['pichave'] = 1;
            }
        }
        
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    
    
    public function tuan() {
        $tuandianping = D('Tuandianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id, 'show_date' => array('ELT', TODAY));
        $count = $tuandianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $tuandianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($order_ids)) {
            $this->assign('pics', D('Tuandianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
        }
        foreach($list as $key=>$v){
            if(in_array($v['order_id'], $order_ids)){
                $list[$key]['pichave'] = 1;
            }
        }
        
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

	 public function ding() {
        $dianping = D('Shopdingdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id, 'show_date' => array('ELT', TODAY));
        $count = $dianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $dianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($order_ids)) {
            $this->assign('pics', D('Shopdingdianpingpic')->where(array('order_id' => array('IN', $order_ids)))->select());
        }
        foreach($list as $key=>$v){
            if(in_array($v['order_id'], $order_ids)){
                $list[$key]['pichave'] = 1;
            }
        }
        
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

	public function tuanreply($order_id) {
        $order_id = (int) $order_id;
        $detail = D('Tuandianping')->find($order_id);
        if (empty($detail) || $detail['shop_id'] != $this->shop_id) {
            $this->error('没有该内容');
        }
        if ($this->isPost()) {
            if ($reply = $this->_param('reply', 'htmlspecialchars')) {
                $data = array('order_id' => $order_id, 'reply' => $reply);
                if (D('Tuandianping')->save($data)) {
                    $this->error('回复成功', U('dianping/tuan'));
                }
            }
            $this->error('请填写回复');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

	

	public function elereply($order_id) {
        $order_id = (int) $order_id;
        $detail = D('Eledianping')->find($order_id);
        if (empty($detail) || $detail['shop_id'] != $this->shop_id) {
            $this->error('没有该内容');
        }
        if ($this->isPost()) {
            if ($reply = $this->_param('reply', 'htmlspecialchars')) {
                $data = array('order_id' => $order_id, 'reply' => $reply);
                if (D('Eledianping')->save($data)) {
                    $this->error('回复成功', U('dianping/waimai'));
                }
            }
            $this->error('请填写回复');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

	public function dingreply($order_id) {
        $order_id = (int) $order_id;
        $detail = D('Shopdingdianping')->find($order_id);
        if (empty($detail) || $detail['shop_id'] != $this->shop_id) {
            $this->error('没有该内容');
        }
        if ($this->isPost()) {
            if ($reply = $this->_param('reply', 'htmlspecialchars')) {
                $data = array('order_id' => $order_id, 'reply' => $reply);
                if (D('Shopdingdianping')->save($data)) {
                    $this->error('回复成功', U('dianping/ding'));
                }
            }
            $this->error('请填写回复');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

}
