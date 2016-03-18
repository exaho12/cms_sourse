<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class DingAction extends CommonAction {

    public function index() {
        $dingorder = D('Shopdingorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
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
            $map['order_no'] = array('LIKE', '%' . $keyword . '%');
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
        $count = $dingorder->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count,10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
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

	public function dianping($order_id) {
        $order_id = (int) $order_id;
		$dingorder = D('Shopdingorder');
        if (!$detail = $dingorder->find($order_id)) {
            $this->baoError('没有该订单');
        } else {
            if ($detail['user_id'] != $this->uid) {
                $this->baoError('不要评价别人的订座订单');
                die();
            }
        }
		$yuyue = D('Shopdingyuyue')->where('ding_id ='.$detail['ding_id'])->find();
        if ($detail['is_dianping']) {
            $this->baoError('已经评价过了');
        }
        if ($this->_Post()) {
            $data = $this->checkFields($this->_post('data', false), array('score', 'speed', 'contents'));
            $data['user_id'] = $this->uid;
            $data['shop_id'] = $yuyue['shop_id'];
            $data['order_id'] = $order_id;
            $data['score'] = (int) $data['score'];
            if (empty($data['score'])) {
                $this->baoError('评分不能为空');
            }
            if ($data['score'] > 5 || $data['score'] < 1) {
                $this->baoError('评分为1-5之间的数字');
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
            if (D('Shopdingdianping')->add($data)) {
                $photos = $this->_post('photos', false);
                $local = array();
                foreach ($photos as $val) {
                    if (isImage($val))
                        $local[] = $val;
                }
                if (!empty($local)){
					D('Shopdingdianpingpic')->upload($order_id, $local);
				}
				D('Shopdingorder')->updateCount($order_id, 'is_dianping');
                D('Users')->updateCount($this->uid, 'ping_num');
                $this->baoSuccess('恭喜您点评成功!', U('ding/index'));
            }
            $this->baoError('点评失败！');
        }else {
            $details = D('Shop')->find($detail['shop_id']);
            $this->assign('details', $details);
            $this->assign('order_id', $order_id);
            $this->display();
        }
    }

}
