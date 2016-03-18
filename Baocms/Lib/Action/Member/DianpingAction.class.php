<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class DianpingAction extends CommonAction {

    public function index() {
        import('ORG.Util.Page'); // 导入分页类
        $status = (int) $this->_param('status');
        $this->assign('status', $status);

        $st = (int) $this->_param('st');
        $this->assign('st', $st);
        if ($status == 1 || empty($status)) {
            $Shopdianping = D('Shopdianping');
            $map = array('closed' => 0, 'user_id' => $this->uid);
            $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
            $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show(); // 分页显示输出
            $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $dianping_ids = $shop_ids = array();
            foreach ($list as $k => $val) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
                $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
            }
            if (!empty($shop_ids)) {
                $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
            }
            if (!empty($dianping_ids)) {
                $this->assign('pics', D('Shopdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
            }
            foreach ($list as $key => $v) {
                    if (in_array($v['dianping_id'], $dianping_ids)) {
                        $list[$key]['pichave'] = 1;
                    }
                }
            $this->assign('list', $list); // 赋值数据集
            $this->assign('page', $show); // 赋值分页输出
            $this->display('index'); // 输出模板
        } elseif ($status == 2) {
            $Tuanorder = D('Tuanorder');
            if ($st == 1 || empty($st)) {
                $map = array('user_id' => $this->uid, 'status' => 1); //这里只显示 实物
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
                $shop_ids = $tuan_ids = array();
                foreach ($list as $k => $val) {
                    if (!empty($val['shop_id'])) {
                        $shop_ids[$val['shop_id']] = $val['shop_id'];
                    }
                    $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
                }
                $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                $this->assign('tuan', D('Tuan')->itemsByIds($tuan_ids));
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
                //dump($list);
            } elseif ($st == 2) {
                $map = array('closed' => 0, 'user_id' => $this->uid);
                $count = D('Tuandianping')->where($map)->count(); // 查询满足要求的总记录数 
                $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
                $show = $Page->show(); // 分页显示输出
                $list = D('Tuandianping')->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $order_ids = $shop_ids = $tuan_ids = array();
                foreach ($list as $k => $val) {
                    $shop_ids[$val['shop_id']] = $val['shop_id'];
                    $order_ids[$val['order_id']] = $val['order_id'];
                    $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
                }
                if (!empty($shop_ids)) {
                    $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                }
                if (!empty($tuan_ids)) {
                    $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
                }
                if (!empty($order_ids)) {
                    $this->assign('pics', D('Tuandianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
                }
                foreach ($list as $key => $v) {
                    if (in_array($v['order_id'], $order_ids)) {
                        $list[$key]['pichave'] = 1;
                    }
                }
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
            }
            $this->display('tuan');
        } elseif ($status == 3) { //订餐
            $Eleorder = D('Eleorder');
            if ($st == 1 || empty($st)) {
                $map = array('user_id' => $this->uid, 'status' => 8); //这里只显示 实物
                $lists = $Eleorder->where($map)->order(array('order_id' => 'desc'))->select();
                $dianping = D('Eledianping')->where(array('user_id' => $this->uid))->select();
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
                $shop_ids = $order_ids = array();
                foreach ($list as $k => $val) {
                    if (!empty($val['shop_id'])) {
                        $shop_ids[$val['shop_id']] = $val['shop_id'];
                    }
                    $order_ids[$val['order_id']] = $val['order_id'];
                }
                if (!empty($shop_ids)) {
                    $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                }
                if (!empty($order_ids)) {
                    $products = D('Eleorderproduct')->where(array('order_id' => array('IN', $order_ids)))->select();
                    $product_ids = $shop_ids = array();
                    foreach ($products as $val) {
                        $product_ids[$val['product_id']] = $val['product_id'];
                    }
                    $this->assign('products', $products);
                    $this->assign('eleproducts', D('Eleproduct')->itemsByIds($product_ids));
                }
                $this->assign('cfg', D('Eleorder')->getCfg());
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
            } elseif ($st == 2) {
                $map = array('closed' => 0, 'user_id' => $this->uid);
                $count = D('Eledianping')->where($map)->count(); // 查询满足要求的总记录数 
                $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
                $show = $Page->show(); // 分页显示输出
                $list = D('Eledianping')->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $order_ids = $shop_ids = array();
                foreach ($list as $k => $val) {
                    $shop_ids[$val['shop_id']] = $val['shop_id'];
                    $order_ids[$val['order_id']] = $val['order_id'];
                }
                if (!empty($shop_ids)) {
                    $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                }
                if (!empty($order_ids)) {
                    $this->assign('pics', D('Eledianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
                    $products = D('Eleorderproduct')->where(array('order_id' => array('IN', $order_ids)))->select();
                    $product_ids = $shop_ids = array();
                    foreach ($products as $val) {
                        $product_ids[$val['product_id']] = $val['product_id'];
                    }
                    $this->assign('products', $products);
                    $this->assign('eleproducts', D('Eleproduct')->itemsByIds($product_ids));
                }

                foreach ($list as $key => $v) {
                    if (in_array($v['order_id'], $order_ids)) {
                        $list[$key]['pichave'] = 1;
                    }
                }
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
            }
            $this->display('ele');
        }elseif($status == 4){
            $Shopdingorder = D('Shopdingorder');
            if ($st == 1 || empty($st)) {
                $map = array('user_id' => $this->uid, 'status' => 2,'is_dianping'=>0); //这里只显示 实物
                $list = $Shopdingorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $ding_ids = $order_ids = array();
                foreach ($list as $k => $val) {
                    if (!empty($val['shop_id'])) {
                        $shop_ids[$val['shop_id']] = $val['shop_id'];
                    }
                    $order_ids[$val['order_id']] = $val['order_id'];
                }
                $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                $this->assign('tuan', D('Tuan')->itemsByIds($tuan_ids));
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
                //dump($list);
            } elseif ($st == 2) {
                $map = array('closed' => 0, 'user_id' => $this->uid);
                $count = D('Shopdingdianping')->where($map)->count(); // 查询满足要求的总记录数 
                $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
                $show = $Page->show(); // 分页显示输出
                $list = D('Shopdingdianping')->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $order_ids = $shop_ids = array();
                foreach ($list as $k => $val) {
                    $shop_ids[$val['shop_id']] = $val['shop_id'];
                    $order_ids[$val['order_id']] = $val['order_id'];
                }
                if (!empty($shop_ids)) {
                    $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                }
                if (!empty($order_ids)) {
                    $this->assign('pics', D('Tuandianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
                }
                foreach ($list as $key => $v) {
                    if (in_array($v['order_id'], $order_ids)) {
                        $list[$key]['pichave'] = 1;
                    }
                }
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
            }
            $this->display('ding'); // 输出模板

		}elseif ($status == 6) {
            $order = D('Order');
            if ($st == 1 || empty($st)) {
                $map = array('user_id' => $this->uid, 'status' => 1); //这里只显示 实物
                $lists = $order->where($map)->order(array('order_id' => 'desc'))->select();
                $dianping = D('Goodsdianping')->where(array('user_id' => $this->uid))->select();
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
                $shop_ids = $goods_ids = array();
                foreach ($list as $k => $val) {
                    if (!empty($val['shop_id'])) {
                        $shop_ids[$val['shop_id']] = $val['shop_id'];
                    }
                    $goods_ids[$val['goods_id']] = $val['goods_id'];
                }
                $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                $this->assign('goods', D('Goods')->itemsByIds($goods_ids));
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
                //dump($list);
            } elseif ($st == 2) {
                $map = array('closed' => 0, 'user_id' => $this->uid);
                $count = D('Goodsdianping')->where($map)->count(); // 查询满足要求的总记录数 
                $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
                $show = $Page->show(); // 分页显示输出
                $list = D('Goodsdianping')->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $order_ids = $shop_ids = $goods_ids = array();
                foreach ($list as $k => $val) {
                    $shop_ids[$val['shop_id']] = $val['shop_id'];
                    $order_ids[$val['order_id']] = $val['order_id'];
                    $goods_ids[$val['goods_id']] = $val['goods_id'];
                }
                if (!empty($shop_ids)) {
                    $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
                }

                if (!empty($goods_ids)) {
                    $this->assign('goodss', D('Goods')->itemsByIds($goods_ids));
                }
                if (!empty($order_ids)) {
                    $this->assign('pics', D('Goodsdianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
                }
                foreach ($list as $key => $v) {
                    if (in_array($v['order_id'], $order_ids)) {
                        $list[$key]['pichave'] = 1;
                    }
                }
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
            }
            $this->display('mall');
		
		}
		
		elseif($status == 5){
                $Lifeservicedianping = D('Lifeservicedianping');//家政点评模型
				$map = array('closed' => 0, 'user_id' => $this->uid);
                $count = D('Lifeservicedianping')->where($map)->count(); // 查询满足要求的总记录数 
                $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
                $show = $Page->show(); // 分页显示输出
                $list = D('Lifeservicedianping')->where($map)->order(array('id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $lifeservice_ids = $shop_ids = array();
                foreach ($list as $k => $val) {
                    $shop_ids[$val['shop_id']] = $val['shop_id'];
                    $lifeservice_ids[$val['id']] = $val['id'];
                }
                if (!empty($lifeservice_ids)) {
                    $this->assign('houseworksetting', D('Houseworksetting')->itemsByIds($lifeservice_ids));
                }
                if (!empty($lifeservice_ids)) {
                    $this->assign('pics', D('Lifeservicedianpingpics')->where(array('aid' => array('IN', $lifeservice_ids)))->select());
                }
                foreach ($list as $key => $v) {
                    if (in_array($v['id'], $lifeservice_ids)) {
                        $list[$key]['pichave'] = 1;
                    }
                }
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出    
                $this->display('lifeservice'); // 输出家政模板
			//设计师结束
        }
		
		
		
    }

    public function tuandianping($order_id) {
        $order_id = (int) $order_id;
        if (!$detail = D('Tuanorder')->find($order_id)) {
            $this->baoError('没有该抢购');
        } else {
            if ($detail['user_id'] != $this->uid) {
                $this->baoError('不要评价别人的抢购');
                die();
            }
        }
        if (D('Tuandianping')->check($order_id, $this->uid)) {
            $this->baoError('已经评价过了');
        }
        if ($this->_Post()) {
            $data = $this->checkFields($this->_post('data', false), array('score', 'cost', 'contents'));
            $data['user_id'] = $this->uid;
            $data['shop_id'] = $detail['shop_id'];
            $data['tuan_id'] = $detail['tuan_id'];
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
			$data_tuan_dianping = $this->_CONFIG['mobile']['data_tuan_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_tuan_dianping * 86400); //15天生效

            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            if (D('Tuandianping')->add($data)) {
                $photos = $this->_post('photos', false);
                $local = array();
                foreach ($photos as $val) {
                    if (isImage($val))
                        $local[] = $val;
                }
                if (!empty($local))
                    D('Tuandianpingpics')->upload($order_id, $local);
                D('Tuanorder')->save(array('order_id'=>$order_id,'is_dainping'=>1));
                D('Users')->prestige($this->uid, 'dianping');
                D('Users')->updateCount($this->uid, 'ping_num');
                $this->baoSuccess('恭喜您点评成功!', U('member/order/index'));
            }
            $this->baoError('点评失败！');
        }else {
            $tuandetails = D('Tuan')->find($detail['tuan_id']);
            $this->assign('tuandetails', $tuandetails);
            $this->assign('order_id', $order_id);
            $this->display();
        }
    }

    public function tuandpedit($order_id) {
        $order_id = (int) $order_id;
        $obj = D('Tuandianping');

        if ($this->_Post()) {
            if (!$detail = $obj->find($order_id)) {
                $this->baoError('请选择要编辑的抢购点评');
            }
            if (!$detail = $obj->find($order_id)) {
                $this->baoError('没有该抢购点评');
            } else {
                if ($detail['user_id'] != $this->uid) {
                    $this->baoError('不要编辑别人的抢购');
                }
                if ($detail['show_date'] < '$today 00:00:00') {
                    $this->baoError('点评已过期');
                }
            }
            $data = $this->checkFields($this->_post('data', false), array('score', 'cost', 'contents'));
            $data['user_id'] = $this->uid;
            $data['tuan_id'] = $detail['tuan_id'];
            $data['shop_id'] = $detail['shop_id'];
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
            $data_tuan_dianping = $this->_CONFIG['mobile']['data_tuan_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_tuan_dianping * 86400); //15天生效
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();

            if (false !== $obj->save($data)) {
                $photos = $this->_post('photos', false);
                $local = array();
                foreach ($photos as $val) {
                    if (isImage($val))
                        $local[] = $val;
                }
                if (!empty($local))
                    D('Tuandianpingpics')->upload($order_id, $local);
                $this->baoSuccess('恭喜您编辑点评成功!', U('member/order'));
            }
            $this->baoError('点评编辑失败！');
        }else {
            $this->assign('detail', $obj->find($order_id));
            $this->assign('tuandetails', D('Tuan')->find($detail['tuan_id']));
            $this->assign('photos', D('Tuandianpingpics')->getPics($order_id));
            $this->display();
        }
    }
	
	//增加家政点评开始
	public function lifeservicedianping($id) {
        $id = (int) $id;
        if (!$detail = D('Houseworksetting')->find($id)) {
            $this->error('没有该设计师');
        } 
        if (D('Lifeservicedianping')->check($id, $this->uid)) {
            $this->error('已经评价过了');
        }
        if ($this->_Post()) {
            $data = $this->checkFields($this->_post('data', false), array('score', 'cost', 'contents'));
            $data['user_id'] = $this->uid;
            $data['id'] = $id;
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
            $data_lifeservice_dianping = $this->_CONFIG['mobile']['data_lifeservice_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_lifeservice_dianping * 86400); //15天生效
			
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            if (D('Lifeservicedianping')->add($data)) {
                $photos = $this->_post('photos', false);
                $local = array();
                foreach ($photos as $val) {
                    if (isImage($val))
                        $local[] = $val;
                }
                if (!empty($local))
                    D('Lifeservicedianpingpics')->upload($id, $local);
                D('Users')->prestige($this->uid, 'dianping');//后期二次开发
                D('Users')->updateCount($this->uid, 'ping_num');//后期二次开发
                $this->baoSuccess('恭喜您点评工人成功!', U('member/mylifeservice/index'));
            }
            $this->baoError('点评失败！');
        }else {
            $Houseworksettings = D('Houseworksetting')->find($detail['id']);
			$this->assign('houseworksettings', $Houseworksettings);
            $this->assign('id', $id);
            $this->display();
        }
    }
    //增加家政点评结束


}
