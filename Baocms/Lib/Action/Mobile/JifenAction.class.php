<?php
class JifenAction extends CommonAction {
	
	  protected function _initialize() {
        parent::_initialize();
		$jifen = (int)$this->_CONFIG['operation']['jifen'];
		if ($jifen == 0) {
				$this->error('此功能已关闭');
				die;
		}
     }

    public function index() {
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case x:
                $orderby = array('exchange_num' => 'desc');
                break;
            case t:
                $orderby = array('orderby' => 'asc');
                break;
            default:
                $orderby = array('exchange_num' => 'desc', 'orderby' => 'asc');
                break;
        }

        $shop_id = (int) $this->_param('shop_id');
        $this->assign('order', $order);
        $this->assign('nextpage', LinkTo('jifen/loaddata', array('t' => NOW_TIME, 'shop_id' => $shop_id, 'order' => $order, 'keyword' => $keyword, 'p' => '0000')));
        $this->display(); // 输出模板
    }

    public function loaddata() {

        $Integralgoods = D('Integralgoods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1);

        if ($shop_id = (int) $this->_param('shop_id')) {
            $map['shop_id'] = $shop_id;
        }

        $count = $Integralgoods->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Integralgoods->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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

        $this->display();
    }

    public function detail($goods_id) {
        $goods_id = (int) $goods_id;
        if (!$detail = D('Integralgoods')->find($goods_id)) {
            $this->error('该积分商品不存在或者已经下架！');
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('该积分商品不存在或者已经下架！');
        }


        $this->assign('shop', D('Shop')->find($detail['shop_id']));
        $other_goods = D('Integralgoods')->where(array('audit' => 1, 'closed' => 0, 'shop_id' => $detail['shop_id'], 'goods_id' => array('NEQ', $goods_id)))->limit(0, 4)->select();
        $this->assign('othergoods', $other_goods);
        $this->assign('detail', $detail);
        $this->display();
    }

    public function exchange($goods_id) {

        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }
        $goods_id = (int) $goods_id;
        if (!$detail = D('Integralgoods')->find($goods_id)) {
            $this->error('该积分商品不存在或者已经下架！', U('jifen/index'));
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('该积分商品不存在或者已经下架！');
        }



        if ($this->isPost()) {
            if ($detail['num'] <= 0) {
                $this->error('该商品已经兑换完了！');
            }
            $addr_id = (int) $this->_post('addr_id');
            if (empty($addr_id)) {
                $this->error('请选择收货地址！');
            }
            if (!$addr = D('Useraddr')->find($addr_id)) {
                $this->error('请选择收货地址！');
            }
            if ($addr['user_id'] != $this->uid) {
                $this->error('请选择收货地址！');
            }
            //if (!D('Lock')->lock($this->uid)) { //上锁
              //  $this->error('服务器繁忙，1分钟后再试');
          //  }


            $member = D('Users')->find($this->uid);
            if ($member['integral'] < $detail['integral']) {
               // D('Lock')->unlock();
                $this->error('您的积分不足！该商品您兑换不了！');
            }
            $ip = get_client_ip();
            if (D('Users')->save(array('user_id' => $this->uid, 'integral' => $member['integral'] - $detail['integral']))) {
                D('Userintegrallogs')->add(array(
                    'user_id' => $this->uid,
                    'integral' => -$detail['integral'],
                    'intro' => "兑换积分产品" . $goods_id,
                    'create_time' => NOW_TIME,
                    'create_ip' => $ip
                ));
                D('Integralexchange')->add(array(
                    'user_id' => $this->uid,
                    'shop_id' => $detail['shop_id'],
                    'addr_id' => $addr_id,
                    'goods_id' => $detail['goods_id'],
                    'create_time' => NOW_TIME,
                    'create_ip' => $ip
                ));
               // D('Lock')->unlock();
                D('Integralgoods')->save(array(
                    'goods_id' => $goods_id,
                    'num' => $detail['num'] - 1,
                    'exchange_num' => $detail['exchange_num'] + 1
                ));
                $this->success('兑换成功！', U('mcenter/exchange/index'));
            }


           // D('Lock')->unlock();
            $this->error('兑换失败');
        } else {
            $useraddr = D('Useraddr')->where(array('user_id' => $this->uid))->limit(0, 5)->select();
            $this->assign('useraddr', $useraddr);
            $this->assign('detail', $detail);
            $this->display();
        }
    }

}
