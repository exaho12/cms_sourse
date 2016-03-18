<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class DingorderAction extends CommonAction {

    private $edit_fields = array('status', 'last_date', 'last_t', 'menu', 'number');

    public function _initialize() {
        parent::_initialize();
        if (empty($this->shop['is_ding'])) {
            $this->error('订座功能要和网站洽谈，由网站开通！');
        }
        $this->dingcates = D('Shopdingcate')->where(array('shop_id' => $this->shop_id))->select();
        $this->assign('dingcates', $this->dingcates);
    }

    public function index() {
        $dingorder = D('Shopdingorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        if ($order_no = $this->_param('order_no')) {
            $map['order_no'] = array('LIKE', '%' . $order_no . '%');
            $this->assign('order_no', $order_no);
        }

        $map['shop_id'] = $this->shop_id;
        $this->assign('shop_id', $this->shop_id);

        if ($status = $this->_param('status')) {
            $map['status'] = $status - 2;
            $this->assign('status', $status);
        } else {
            $map['status'] = 1;
        }
        $count = $dingorder->where($map)->count(); // 查询满足要求的总记录数 
      //  print_r($dingorder->getLastSql());die;
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $dingorder->where($map)->order(array('create_time' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $arr = $dingorder->get_ding($shop_id, $list);
        $this->assign('arr', $arr);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function detail($order_id) {
        $dingorder = D('Shopdingorder');
        $dingyuyue = D('Shopdingyuyue');
        $dingmenu = D('Shopdingmenu');
        if (!$order = $dingorder->where('order_id = ' . $order_id)->find()) {
            $this->error('该订单不存在');
        } else if (!$yuyue = $dingyuyue->where('ding_id = ' . $order['ding_id'])->find()) {
            $this->error('该订单不存在');
        } else if ($yuyue['shop_id'] != $this->shop_id) {
            $this->error('非法操作');
        } else {
            $arr = $dingorder->get_detail($this->shop_id, $order, $yuyue);
            $menu = $dingmenu->shop_menu($this->shop_id);
            $this->assign('yuyue', $yuyue);
            $this->assign('order', $order);
            $this->assign('order_id', $order_id);
            $this->assign('arr', $arr);
            $this->assign('menu', $menu);
            $this->display();
        }
    }

    public function edit($order_id) {
        $dingorder = D('Shopdingorder');
        $dingyuyue = D('Shopdingyuyue');
        $dingmenu = D('Shopdingmenu');
        if (!$order = $dingorder->where('order_id = ' . $order_id)->find()) {
            $this->baoError('该订单不存在');
        } else if (!$yuyue = $dingyuyue->where('ding_id = ' . $order['ding_id'])->find()) {
            $this->baoError('该订单不存在');
        } else if ($yuyue['shop_id'] != $this->shop_id) {
            $this->baoError('非法操作');
        } else {
            if (!$status = $this->_post('status')) {
                $this->baoError('非法操作');
            } else if ($status == 2) {
                $data['status'] = $status;
                D('Shopdingorder')->where('order_id=' . $order_id)->save($data);
                $shopmoney = D('Shopmoney');
                $shopmoney->add(array(
                    'shop_id' => $yuyue['shop_id'],
                    'money' => $order['need_price'],
                    'create_ip' => $ip,
                    'type' => 'ding',
                    'create_time' => NOW_TIME,
                    'order_id' => $order['order_id'],
                    'intro' => '订座已消费',
                ));

                $shop = D('Shop')->find($yuyue['shop_id']);
                D('Users')->addMoney($shop['user_id'], $order['need_price'], '订座已消费');
                D('Users')->gouwu($order['user_id'],$order['need_price'],'订座消费');
                $this->baoSuccess('订单修改成功', U('dingorder/detail', array('order_id' => $order_id)));
            } else if ($status == -1) {
                $data['status'] = $status;
                D('Shopdingorder')->where('order_id=' . $order_id)->save($data);
                D('Users')->addMoney($order['user_id'], $order['need_price'], '订座退款');
                $this->baoSuccess('退款成功', U('dingorder/detail', array('order_id' => $order_id)));
            } else {
                $this->baoError('非法操作');
            }
        }
    }

}
