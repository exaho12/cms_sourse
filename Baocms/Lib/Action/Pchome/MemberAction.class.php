<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MemberAction extends CommonAction { //用户中心

    public function _initialize() {
        parent::_initialize();
        if (empty($this->uid)) {
            header("Location: " . U('passport/login'));
            die;
        }
        $this->assign('uid', $this->uid);
        $this->assign('ranks', D('Userrank')->fetchAll());
    }

    public function error($message = '', $jumpUrl = '', $ajax = false) {
        $this->assign('message', $message);
        $this->assign('jumpUrl', $jumpUrl);
        $this->display('error');
        die;
    }

    public function myshop() {
        $shop = D('Shop');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('tui_uid' => $this->uid, 'closed' => 0);
        $count = $shop->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $shop->where($map)->order(array('shop_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function tongji() {
        $shopIds = D('Shop')->getShopIdsByTuiId($this->uid);
        if (empty($shopIds)) {
            $this->error('您还没有推广的商户', U('member/myshop'));
        }
        $bg_date = $this->_param('bg_date', 'htmlspecialchars');
        $end_date = $this->_param('end_date', 'htmlspecialchars');
        if (empty($bg_date) || empty($end_date)) {
            $bg_date = date('Y-m-d', NOW_TIME - 86400 * 30);
            $end_date = TODAY;
        }
        $this->assign('bg_date', $bg_date);
        $this->assign('end_date', $end_date);

        $this->assign('total', D('Shopmoney')->sumByIds($bg_date, $end_date, $shopIds));
        $shops = D('Shop')->itemsByIds($shopIds);
        $datas = D('Shopmoney')->sumByIdsTop10($bg_date, $end_date, $shopIds);
        $showdatas = array();
        foreach ($datas as $k => $val) {
            if (!empty($val['shop_id'])) {
                $showdatas['shop'][] = '"' . $shops[$val['shop_id']]['shop_name'] . '"';
                $showdatas['money'][] = round($val['money'] / 100, 2);
            }
        }
        $this->assign('shops', join(',', $showdatas['shop']));
        $this->assign('moneys', join(',', $showdatas['money']));
        $this->display();
    }

    public function goldlogs() {
        $Usergoldlogs = D('Usergoldlogs');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Usergoldlogs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Usergoldlogs->where($map)->order(array('log_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function main() {
        $lifecate = D('Lifecate')->fetchAll();
        $this->assign('lifecate', $lifecate);
        $this->display();
    }

    public function gold() {

        $this->assign('payment', D('Payment')->getPayments());
        $this->display();
    }

    public function goldpay() { //后期优化
        $gold = (int) $this->_post('gold');
        $code = $this->_post('code', 'htmlspecialchars');
        if ($gold <= 0) {
            $this->error('请填写正确的金块数！');
            die;
        }
        $payment = D('Payment')->checkPayment($code);
        if (empty($payment)) {
            $this->baoError('该支付方式不存在');
            die;
        }
        $logs = array(
            'user_id' => $this->uid,
            'type' => 'gold',
            'code' => $code,
            'order_id' => 0,
            'need_pay' => $gold * 100,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip(),
        );
        $logs['log_id'] = D('Paymentlogs')->add($logs);

        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('gold', $gold);
        $this->display();
    }

    public function lifetop() {
        if (!$life_id = (int) $this->_get('life_id')) {
            $this->baoError('参数错误');
        }
        if (!$detail = D('Life')->find($life_id)) {
            $this->baoError('参数错误');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->baoError('参数错误');
        }

        $day = (int) $this->_get('day');
        $mday = 0;
        switch ($day) {
            case 7:
                $mday = $day = 7;
                break;
            default:
                $day = 30;
                $mday = 27;
                break;
        }
        $gold = $mday * $this->_CONFIG['shop']['life']['top'];
        if ($this->member['gold'] < $gold) {
            $this->baoErrorJump('金块余额不足', U('member/gold'));
        }
        $top_date = date('Y-m-d', NOW_TIME + $day * 86400);
        if ($detail['top_date'] > TODAY) {
            $top_date = date('Y-m-d', strtotime($detail['top_date']) + $day * 86400);
        }

        if (D('Users')->addGold($this->uid, -$gold, '置顶信息' . $day . '天')) {
            D('Life')->save(array('top_date' => $top_date, 'life_id' => $life_id));
            $this->baoSuccess('您的信息已经在同频道置顶了！', U('member/life'));
        }

        $this->baoError('操作失败！');
    }

    public function lifeflush() {
        if (!$life_id = (int) $this->_get('life_id')) {
            $this->baoError('参数错误');
        }
        if (!$detail = D('Life')->find($life_id)) {
            $this->baoError('参数错误');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->baoError('参数错误');
        }
        if (NOW_TIME - $detail['last_time'] < 86400) {
            $this->baoError('您已经刷新过了！');
        }
        if (NOW_TIME - $detail['last_time'] > (86400 * 30)) {
            $this->baoError('该信息已经超过30天了，不能在进行免费刷新！');
        }

        $data = array(
            'life_id' => $life_id,
            'last_time' => NOW_TIME
        );
        if ($detail['top_date'] < TODAY) {
            $data['top_date'] = TODAY;
        }
        if (D('Life')->save($data)) {
            $this->baoSuccess('刷新成功!', U('member/life'));
        }
        $this->baoError('操作失败');
    }

    public function lifeurgent() {
        if (!$life_id = (int) $this->_get('life_id')) {
            $this->baoError('参数错误');
        }
        if (!$detail = D('Life')->find($life_id)) {
            $this->baoError('参数错误');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->baoError('参数错误');
        }

        $day = (int) $this->_get('day');
        $mday = 0;
        switch ($day) {
            case 7:
                $mday = $day = 7;
                break;
            default:
                $day = 30;
                $mday = 27;
                break;
        }
        $gold = $mday * $this->_CONFIG['shop']['life']['urgent'];
        if ($this->member['gold'] < $gold) {
            $this->baoErrorJump('金块余额不足', U('member/gold'));
        }
        $urgent_date = date('Y-m-d', NOW_TIME + $day * 86400);
        if ($detail['urgent_date'] > TODAY) {
            $urgent_date = date('Y-m-d', strtotime($detail['urgent_date']) + $day * 86400);
        }

        if (D('Users')->addGold($this->uid, -$gold, '加急信息' . $day . '天')) {
            D('Life')->save(array('urgent_date' => $urgent_date, 'life_id' => $life_id));
            $this->baoSuccess('您的信息已经加急！', U('member/life'));
        }

        $this->baoError('操作失败！');
    }

    public function life(){
        $Life = D('Life');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid); //分类信息是关联到UID 的 
        $count = $Life->where($map)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Life->where($map)->order(array('last_time' => 'desc'))->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('cates', D('Lifecate')->fetchAll());
        $this->assign('channelmeans', D('Lifecate')->getChannelMeans());
        $this->display(); // 输出模板
    }
	public function edit($life_id) {
        if ($life_id = (int) $life_id) {
            $obj = D('Life');
            if (!$detail = $obj->find($life_id)) {
                $this->baoError('请选择要编辑的生活信息');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['life_id'] = $life_id;
                $details = $this->_post('details', 'SecurityEditorHtml');
				$data['audit']=0;
                if ($words=D('Sensitive')->checkWords($details)) {
                    $this->baoError('商家介绍含有敏感词：' . $words);
                }
                if (false !== $obj->save($data)){
                    if ($details){
                        D('Lifedetails')->updateDetails($life_id, $details);
                    }
                    $photos = $this->_post('photos', false);
                    if (!empty($photos)){
                        D('Lifephoto')->upload($life_id, $photos);
                    }
                    $this->baoSuccess('操作成功');
                }
                $this->baoError('操作失败');
            }else{
                $this->assign('detail', $detail);
                $this->assign('cates', D('Lifecate')->fetchAll());
                $this->assign('channelmeans', D('Lifecate')->getChannelMeans());
                $this->assign('cate', D('Lifecate')->find($detail['cate_id']));
                $this->assign('areas', D('Area')->fetchAll());
                $this->assign('business', D('Business')->fetchAll());
                $this->assign('ex', D('Lifedetails')->find($life_id));
                $this->assign('attrs', D('Lifecateattr')->order(array('orderby' => 'asc'))->where(array('cate_id' => $detail['cate_id']))->select());
                $this->assign('user', D('Users')->find($detail['user_id']));
                $this->assign('photos', D('Lifephoto')->getPics($life_id));
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的生活信息');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        }
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('分类不能为空');
        }
        $data['area_id'] = (int) $data['area_id'];
        if (empty($data['area_id'])) {
            $this->baoError('地区不能为空');
        }
        $data['business_id'] = (int) $data['business_id'];
        if (empty($data['business_id'])) {
            $this->baoError('商圈不能为空');
        }
        $data['user_id'] =$this->uid;
        $data['is_shop'] = (int)$data['is_shop'];
        $data['text1'] = htmlspecialchars($data['text1']);
        $data['text2'] = htmlspecialchars($data['text2']);
        $data['text3'] = htmlspecialchars($data['text3']);
        $data['num1'] = (int) $data['num1'];
        $data['num2'] = (int) $data['num2'];
        $data['select1'] = (int) $data['select1'];
        $data['select2'] = (int) $data['select2'];
        $data['select3'] = (int) $data['select3'];
        $data['select4'] = (int) $data['select4'];
        $data['select5'] = (int) $data['select5'];
        $data['urgent_date'] = htmlspecialchars($data['urgent_date']);
        $data['urgent_date'] = $data['urgent_date'] ? $data['urgent_date'] : TODAY;
        if (!empty($data['urgent_date']) && !isDate($data['urgent_date'])) {
            $this->baoError('火急日期格式不正确');
        }
        $data['top_date'] = htmlspecialchars($data['top_date']);
        $data['lng'] = htmlspecialchars(trim($data['lng']));
        $data['lat'] = htmlspecialchars(trim($data['lat']));
        $data['top_date'] = $data['top_date'] ? $data['top_date'] : TODAY;
        if (!empty($data['top_date']) && !isDate($data['top_date']) && $data['top_date'] != '0000-00-00') {
            $this->baoError('置顶日期格式不正确');
        } $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        } $data['contact'] = htmlspecialchars($data['contact']);
        if (empty($data['contact'])) {
            $this->baoError('联系人不能为空');
        } $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
            $this->baoError('电话不能为空');
        }
        if (!isMobile($data['mobile']) && !isPhone($data['mobile'])) {
            $this->baoError('电话格式不正确');
        } $data['qq'] = htmlspecialchars($data['qq']);
        $data['addr'] = htmlspecialchars($data['addr']);
        $data['views'] = (int) $data['views'];
        return $data;
    }
    public function pay() {
        $logs_id = (int)$this->_get('logs_id');
        if (empty($logs_id)) {
            $this->error('没有有效的支付');
        }
        //if (!D('Lock')->lock($this->uid)) { //上锁
          //  $this->error('服务器繁忙，1分钟后再试');
       // }
        if (!$detail = D('Paymentlogs')->find($logs_id)) {
           // D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        if ($detail['code'] != 'money') {
           // D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        $member = D('Users')->find($this->uid);

        if ($detail['is_paid']) {
           // D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        if ($member['money'] < $detail['need_pay']) {
           // D('Lock')->unlock();
            $this->error('很抱歉您的账户余额不足', U('member/index'));
        }
        $member['money'] = $member['money'] - $detail['need_pay'];

        if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
            D('Usermoneylogs')->add(array(
                'user_id' => $this->uid,
                'money' => -$detail['need_pay'],
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
                'intro' => '余额支付' . $logs_id,
            ));
            D('Payment')->logsPaid($logs_id);
          //  D('Lock')->unlock();
            $this->success('支付成功！', U('member/index'));
        }
    }

    public function rechargecard() { //代金券使用列表
        $Rechargecard = D('Rechargecard');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid, 'is_used' => 1);
        $count = $Rechargecard->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Rechargecard->where($map)->order(array('card_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function recharge() { //代金券充值
        if ($this->isPost()) {
            $card_key = $this->_post('card_key', htmlspecialchars);
            //if (!D('Lock')->lock($this->uid)) { //上锁
              //  $this->baoError('服务器繁忙，1分钟后再试');
           // }
            if (!$detail = D('Rechargecard')->where(array('card_key' => $card_key))->find()) {
               // D('Lock')->unlock();
                $this->baoError('该充值卡不存在');
            }
            if ($detail['is_used'] == 1) {
               // D('Lock')->unlock();
                $this->baoErrorJump('该充值卡已经使用过了', U('member/recharge'));
            }
            $member = D('Users')->find($this->uid);
            $member['money'] += $detail['value'];
            if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
                D('Usermoneylogs')->add(array(
                    'user_id' => $this->uid,
                    'money' => +$detail['value'],
                    'create_time' => NOW_TIME,
                    'create_ip' => get_client_ip(),
                    'intro' => '代金券充值' . $detail['card_id'],
                ));
                $res = D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'is_used' => 1));
                if (!empty($res)) {
                    D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'user_id' => $this->uid, 'used_time' => NOW_TIME));
                }
                $this->baoSuccess('充值成功！', U('member/rechargecard'));
            }
            //D('Lock')->unlock();
        } else {
            $this->display();
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

    //积分兑换记录
    public function exchange() {
        $Integralexchange = D('Integralexchange');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Integralexchange->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Integralexchange->where($map)->order(array('exchange_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = $good_ids = $addr_ids = array();
        foreach ($list as $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
            $good_ids[$val['goods_id']] = $val['goods_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
        }
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('goods', D('Integralgoods')->itemsByIds($good_ids));
        $this->assign('addrs', D('Useraddr')->itemsByIds($addr_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function money() {

        $this->assign('payment', D('Payment')->getPayments());
        $this->display();
    }

    public function moneypay() { //后期优化
        $money = (int) ($this->_post('money') * 100);
        $code = $this->_post('code', 'htmlspecialchars');
        if ($money <= 0) {
            $this->error('请填写正确的充值金额！', U('member/money'));
            die;
        }
        $payment = D('Payment')->checkPayment($code);
        if (empty($payment)) {
            $this->error('该支付方式不存在', U('member/money'));
            die;
        }
        $logs = array(
            'user_id' => $this->uid,
            'type' => 'money',
            'code' => $code,
            'order_id' => 0,
            'need_pay' => $money,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip(),
        );
        $logs['log_id'] = D('Paymentlogs')->add($logs);

        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('money', $money);
        $this->display();
    }

    public function bbs() {
        $Post = D('Post');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid);
        $count = $Post->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $Post->where($map)->order('post_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            $ids = array();
            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
                $ids[$val['last_id']] = $val['last_id'];
            }
            $list[$k] = $val;
        }
        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function yuyue() {
        $Shopyuyue = D('Shopyuyue');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Shopyuyue->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shopyuyue->where($map)->order(array('yuyue_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;

            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        if (!empty($shop_ids)) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    //
    public function moneylogs() {
        $Usermoneylogs = D('Usermoneylogs');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Usermoneylogs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Usermoneylogs->where($map)->order(array('log_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }

    public function coupon() {
        $Coupondownload = D('Coupondownload');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Coupondownload->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Coupondownload->where($map)->order(array('download_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = $coupons = array();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            if ($val['coupon_id']) {
                $coupons[$val['coupon_id']] = $val['coupon_id'];
            }

            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $val['used_ip_area'] = $this->ipToArea($val['used_ip']);
            $list[$k] = $val;
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        if ($coupons) {
            $this->assign('coupons', D('Coupon')->itemsByIds($coupons));
        }

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function dianping() {
        $Shopdianping = D('Shopdianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'user_id' => $this->uid);
        $count = $Shopdianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shopdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $shop_ids = array();
        foreach ($list as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        if (!empty($shop_ids)) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
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
            $data['show_date'] = date('Y-m-d', NOW_TIME + 15 * 86400); //15天生效
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
                D('Users')->prestige($this->uid, 'dianping');
                D('Users')->updateCount($this->uid, 'ping_num');
                $this->baoSuccess('恭喜您点评成功!', U('member/order'));
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
            $data['show_date'] = $detail['show_date']; //15天生效
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

    public function favorites() {
        $Shopfavorites = D('Shopfavorites');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid);
        $count = $Shopfavorites->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $Shopfavorites->where($map)->order('favorites_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('prices', D('Shopdetails')->itemsByIds($shop_ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function deletefavo($favorites_id) {
        $favorites_id = (int) $favorites_id;
        if ($detial = D('Shopfavorites')->find($favorites_id)) {
            if ($detial['user_id'] == $this->uid) {
                D('Shopfavorites')->delete($favorites_id);
                $this->baoSuccess('取消收藏成功!', U('member/favorites'));
            }
        }
        $this->baoError('参数错误');
    }
    public function postedit($post_id = 0) {
        if ($post_id = (int) $post_id) {
            $obj = D('Post');
            if (!$detail = $obj->find($post_id)) {
                $this->baoError('请选择要编辑的消费分享');
            }
            if ($detail['user_id'] != $this->uid) {
                $this->error('请不要试图操作其他人的内容');
                die;
            }
            if ($this->isPost()) {
                $data = $this->postCheck();
                $data['post_id'] = $post_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('member/bbs'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->assign('cates', D('Shopcate')->fetchAll());
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的消费分享');
        }
    }

    private function postCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'cate_id', 'details'));
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        }
        $data['user_id'] = (int) $this->uid;
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('分类不能为空');
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }

    public function post() {

        if ($this->isPost()) {
            $data = $this->postCheck();
            $obj = D('Post');
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            if ($obj->add($data)) {
                D('Users')->prestige($this->uid, 'share');
                $this->baoSuccess('添加成功', U('member/bbs'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('cates', D('Shopcate')->fetchAll());
            $this->display();
        }
    }

    public function postreply($post_id = 0) {
        if ($post_id = (int) $post_id) {
            $obj = D('Post');
            if (!$detail = $obj->find($post_id)) {
                $this->baoError('请选择要编辑的消费分享');
            }
            if ($this->isPost()) {
                $data = $this->checkFields($this->_post('data', false), array('contents'));
                $data['post_id'] = $post_id;
                $data['user_id'] = $this->uid;
                $data['contents'] = SecurityEditorHtml($data['contents']);
                if (empty($data['contents'])) {
                    $this->baoError('内容不能为空');
                }
                if ($words = D('Sensitive')->checkWords($data['contents'])) {
                    $this->baoError('详细内容含有敏感词：' . $words);
                }
                $data['create_time'] = NOW_TIME;
                $data['create_ip'] = get_client_ip();
                $obj = D('Postreply');
                if ($obj->add($data)) {
                    D('Post')->save(array('post_id' => $post_id, 'last_id' => $this->uid, 'last_time' => NOW_TIME));
                    D('Users')->prestige($this->uid, 'reply');
                    $this->baoSuccess('回复成功', U('share/detail', array('post_id' => $post_id)));
                }
                $this->baoError('操作失败！');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->error('请选择要编辑的消费分享');
        }
    }

    public function face() {

        if ($this->isPost()) {
            $face = $this->_post('face', 'htmlspecialchars');
            if (empty($face)) {
                $this->baoError('请上传头像');
            }
            if (!isImage($face)) {
                $this->baoError('头像格式不正确');
            }
            $data = array('user_id' => $this->uid, 'face' => $face);
            if (D('Users')->save($data)) {
                $this->baoSuccess('上传头像成功！', U('member/face'));
            }
            $this->baoError('更新头像失败');
        } else {
            $this->display();
        }
    }

    public function nickname() {

        if ($this->isPost()) {
            $nickname = $this->_post('nickname', 'htmlspecialchars');
            if (empty($nickname)) {
                $this->baoError('请填写昵称');
            }
            $data = array('user_id' => $this->uid, 'nickname' => $nickname);
            if (D('Users')->save($data)) {
                $this->baoSuccess('昵称设置成功！', U('member/nickname'));
            }
            $this->baoError('昵称设置失败');
        } else {
            $this->display();
        }
    }

    public function mobile() {
        if ($this->isPost()) {
            $mobile = $this->_post('mobile');
            $yzm = $this->_post('yzm');
            if (empty($mobile) || empty($yzm))
                $this->baoError('请填写正确的手机及手机收到的验证码！');
            $s_mobile = session('mobile');
            $s_code = session('code');
            if ($mobile != $s_mobile)
                $this->baoError('手机号码和收取验证码的手机号不一致！');
            if ($yzm != $s_code)
                $this->baoError('验证码不正确');
            $data = array(
                'user_id' => $this->uid,
                'mobile' => $mobile
            );
            if (D('Users')->save($data)) {
                D('Users')->integral($this->uid, 'mobile');
                D('Users')->prestige($this->uid, 'mobile');
                $this->baoSuccess('恭喜您通过手机认证', U('member/mobile'));
            }
            $this->baoError('更新数据失败！');
        } else {

            $this->display();
        }
    }

    public function mobile2() {
        if ($this->isPost()) {
            $mobile = $this->_post('mobile');
            $yzm = $this->_post('yzm');
            if (empty($mobile) || empty($yzm))
                $this->baoError('请填写正确的手机及手机收到的验证码！');
            $s_mobile = session('mobile');
            $s_code = session('code');
            if ($mobile != $s_mobile)
                $this->baoError('手机号码和收取验证码的手机号不一致！');
            if ($yzm != $s_code)
                $this->baoError('验证码不正确');
            $data = array(
                'user_id' => $this->uid,
                'mobile' => $mobile
            );
            if (D('Users')->save($data)) {
                $this->baoSuccess('恭喜您成功更换绑定手机号', U('member/mobile'));
            }
            $this->baoError('更新数据失败！');
        } else {

            $this->display();
        }
    }
    
    public function cashlog() {
        $Userscash = D('Userscash');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id'=>$this->uid);
        $count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }
    
    public function cash()
    {
        $Users = D('Users');
        $data = $Users->find($this->uid);
        if (IS_POST)
        {
            $money = (int)$_POST['money'];
            if ($money == 0)
            {
                $this->error('提现金额不能为0');
            }
            $money *= 100;
            if ($money > $data['money'] || $data['money'] == 0)
            {
                $this->error('余额不足，无法提现');
            }
            $arr = array();
            $arr['user_id'] = $this->uid;
            $arr['money']   = $money;
            $arr['addtime'] = NOW_TIME;
            $arr['account'] = $data['account'];
            $arr['bank_name'] = $data['bank_name'];
            $arr['bank_num'] = $data['bank_num'];
            $arr['bank_realname'] = $data['bank_realname'];
            $arr['bank_branch'] = $data['bank_branch'];
            D('Userscash')->add($arr);
            //扣除余额
            $Users->addMoney($data['user_id'], -$money, '申请提现，扣款');
            $this->success('申请成功', U('member/cash'));
        }
        else
        {
            $this->assign('money', $data['money'] / 100);
            $this->display();
        }
    }

    public function myactivity() {
        $Activity = D('Activity');
        $Activitysign = D('Activitysign');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Activitysign->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Activitysign->where($map)->order(array('sign_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $activitys_ids = array();
        foreach ($list as $k => $val) {
            $activitys_ids[$val['activity_id']] = $val['activity_id'];
        }
        $this->assign('activity', $Activity->itemsByIds($activitys_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $this->display(); // 输出模板
    }

    public function myaddress() {
        $Useraddr = D('Useraddr');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid, 'closed' => 0);
        $count = $Useraddr->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Useraddr->where($map)->order(array('addr_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $area_ids = $business_ids = array();
        foreach ($list as $k => $val) {
            $area_ids[$val['area_id']] = $val['area_id'];
            $business_ids[$val['business_id']] = $val['business_id'];
        }
        //var_dump( D('Business')->itemsByIds($business_ids));die();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('areas', D('Area')->itemsByIds($area_ids));
        $this->assign('business', D('Business')->itemsByIds($business_ids));
        $this->display(); // 输出模板
    }

    private function addressCheck() {
        $data = $this->checkFields($this->_post('data', false), array('addr_id', 'area_id', 'business_id', 'name', 'mobile', 'addr'));
        $data['name'] = htmlspecialchars($data['name']);
        if (empty($data['name'])) {
            $this->baoError('收货人不能为空');
        }
        $data['user_id'] = (int) $this->uid;
        $data['area_id'] = (int) $data['area_id'];
        $data['business_id'] = (int) $data['business_id'];
        if (empty($data['area_id'])) {
            $this->baoError('地区不能为空');
        }
        if (empty($data['business_id'])) {
            $this->baoError('商圈不能为空');
        }
        $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
            $this->baoError('手机号码不能为空');
        }
        if (!isMobile($data['mobile'])) {
            $this->baoError('手机号码格式不正确');
        }
        $data['addr'] = htmlspecialchars($data['addr']);
        if (empty($data['addr'])) {
            $this->baoError('具体地址不能为空');
        }
        return $data;
    }

    public function addressadd() {
        if ($this->isPost()) {
            $data = $this->addressCheck();
            $obj = D('Useraddr');
            $data['is_default'] = 0;
            if ($obj->add($data)) {
                $backurl = $this->_post('backurl', 'htmlspecialchars');
                $this->baoSuccess('新增收货地址成功', $backurl);
            }
            $this->baoError('操作失败！');
        } else {
            if (!empty($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
                $backurl = $_SERVER['HTTP_REFERER'];
            } else {
                $backurl = U('member/myaddress');
            }
            $default = D('Useraddr')->where(array('user_id' => $this->uid, 'is_default' => 1, 'closed' => 0))->find();
            $this->assign('default', $default);
            $this->assign('backurl', $backurl);
            $this->assign('areas', D('Area')->fetchAll());
            $this->assign('business', D('Business')->fetchAll());
            $this->display();
        }
    }

    public function addressdel($addr_id) {
        $addr_id = (int) $addr_id;
        if (empty($addr_id)) {
            $this->baoError('收货地址不存在');
        }
        if (!$detail = D('Useraddr')->find($addr_id)) {
            $this->baoError('收货地址不存在');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->baoError('请不要操作别人的收货地址');
        }
        $obj = D('Useraddr');
        $obj->save(array('addr_id' => $addr_id, 'closed' => 1));
        $this->baoSuccess('删除成功！', U('member/myaddress'));
    }

    public function editaddr($addr_id = 0) {
        if ($addr_id = (int) $addr_id) {
            $obj = D('Useraddr');
            if (!$detail = $obj->find($addr_id)) {
                $this->baoError('请选择要编辑的收货地址');
            }
            if ($detail['user_id'] != $this->uid) {
                $this->error('请不要试图操作其他人的内容');
                die;
            }
            if ($this->isPost()) {
                // var_dump($addr_id);die();
                $data = $this->addressCheck();
                $data['addr_id'] = $addr_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('member/myaddress'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->assign('areas', D('Area')->fetchAll());
                $this->assign('business', D('Business')->fetchAll());
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的收货地址');
        }
    }

    public function deleteaddr($addr_id = 0) {
        if (is_numeric($addr_id) && ($addr_id = (int) $addr_id)) {

            $obj = D('Useraddr');
            $detail = $obj->find($addr_id);
            if (empty($detail) || $detail['user_id'] != $this->uid) {
                $this->baoError('没有您要设置的地址');
            }
            $obj->save(array('addr_id' => $addr_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('member/myaddress'));
        } else {

            $this->baoError('请选择要删除的收货地址');
        }
    }

    public function updatedefault($addr_id = 0) {
        if (is_numeric($addr_id) && ($addr_id = (int) $addr_id)) {

            $obj = D('Useraddr');
            $detail = $obj->find($addr_id);
            if (empty($detail) || $detail['user_id'] != $this->uid) {
                $this->baoError('没有您要设置的地址');
            }
            $obj->save(array('is_default' => 0), array("where" => array('user_id' => $this->uid)));
            //print_r($obj->getLastSql());die();
            $obj->save(array('is_default' => 1), array("where" => array('addr_id' => $addr_id)));
            $this->baoSuccess('设置成功！', U('member/myaddress'));
        } else {
            $this->baoError('请选择要设置的收货地址');
        }
    }

    public function cancel($addr_id = 0) {
        if (is_numeric($addr_id) && ($addr_id = (int) $addr_id)) {

            $obj = D('Useraddr');
            $detail = $obj->find($addr_id);
            if (empty($detail) || $detail['user_id'] != $this->uid) {
                $this->baoError('没有您要设置的地址');
            }
            $obj->save(array('is_default' => 0), array("where" => array('user_id' => $this->uid)));
            $this->baoSuccess('设置成功！', U('member/myaddress'));
        } else {
            $this->baoError('请选择要设置的收货地址');
        }
    }

    public function child($area_id = 0) {
        $datas = D('Business')->fetchAll();
        $str = '<option value="0">请选择</option>';
        foreach ($datas as $val) {
            if ($val['area_id'] == $area_id) {
                $str.='<option value="' . $val['business_id'] . '">' . $val['business_name'] . '</option>';
            }
        }
        echo $str;
        die;
    }

    public function usercard() {
        $Usercard = D('Usercard');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid); //这里只显示 实物
        $count = $Usercard->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Usercard->where($map)->order(array('card_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $cards_ids = array();
        $shop_ids = $user_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $Usercard->_format($val);
            $cards_ids[$val['card_id']] = $val['card_id'];

            if (!empty($val['shop_id'])) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
                $user_ids[$val['user_id']] = $val['user_id'];
            }
        }
        if ($shop_id = (int) $this->_param('shop_id')) {
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('shopdetails', D('Shopdetails')->itemsByIds($shop_ids));
        $this->display(); // 输出模板
    }

    public function sendsms() {
        if (!$mobile = $this->_post('mobile')) {
            die('请输入正确的手机号码');
        }
        if (!isMobile($mobile)) {
            die('请输入正确的手机号码');
        }
        if ($user = D('Users')->where(array('mobile' => $mobile))->find()) {
            die('手机号码已经存在！');
        }
        session('mobile', $mobile);
        $randstring = session('code');
        if (empty($randstring)) {
            $randstring = rand_string(6, 1);
            session('code', $randstring);
        }
        D('Sms')->sendSms('sms_code', $mobile, array('code' => $randstring));
        die('1');
    }

    public function email() {

        $this->display();
    }

    public function sendemail() {
        $email = $this->_post('email');
        if (isEmail($email)) {
            $link = 'http://' . $_SERVER['HTTP_HOST'];
            $uid = $this->uid;
            $local = array(
                'email' => $email,
                'uid' => $uid,
                'time' => NOW_TIME,
                'sig' => md5($uid . $email . NOW_TIME . C('AUTH_KEY'))
            );
            $link .=U('public/email', $local);
            D('Email')->sendMail('email_rz', $email, $this->_CONFIG['site']['sitename'] . '邮件认证', array('link' => $link));
        }
    }

    public function password() {
        if ($this->isPost()) {
            $oldpwd = $this->_post('oldpwd', 'htmlspecialchars');
            if (empty($oldpwd)) {
                $this->baoError('旧密码不能为空！');
            }
            $newpwd = $this->_post('newpwd', 'htmlspecialchars');
            if (empty($newpwd)) {
                $this->baoError('请输入新密码');
            }
            $pwd2 = $this->_post('pwd2', 'htmlspecialchars');
            if (empty($pwd2) || $newpwd != $pwd2) {
                $this->baoError('两次密码输入不一致！');
            }
            if ($this->member['password'] != md5($oldpwd)) {
                $this->baoError('原密码不正确');
            }
            if (D('Passport')->uppwd($this->member['account'], $oldpwd, $newpwd)) {
                session('uid', null);
                $this->baoSuccess('更改密码成功！', U('passport/login'));
            }
            $this->baoError('修改密码失败！');
        } else {
            $this->display();
        }
    }

    public function index() {
        $this->assign('menus', $this->getMenus());
        $this->display();
    }

    public function shoplist() {
        $Shop = D('Shop');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name|tel'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }

        $count = $Shop->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shop->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('cates', D('Shopcate')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function order() {
        $Tuanorder = D('Tuanorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid); //这里只显示 实物
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
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
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
        $this->display('order');
    }
public function delete($order_id=0){//根据订单id删除订单
if (is_numeric($order_id)&&($order_id =(int)$order_id)) {
            $obj = D('Tuanorder');
            if (!$detial = $obj->find($order_id)) {
                $this->baoError('该订单不存在');
            }
            if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作他人的订单');
            }
            if ($detial['status'] != 0){
                $this->baoError('该订单暂时不能删除');
            }
            $obj->delete($order_id);
            $this->baoSuccess('删除成功！');
        }else{
            $this->baoError('请选择要删除的订单');
        }


}
    public function goodsdel($order_id=0){
		if (is_numeric($order_id)&&($order_id =(int)$order_id)) {
            $obj = D('Order');
            if (!$detial = $obj->find($order_id)) {
                $this->baoError('该订单不存在');
            }
		  if ($detial['user_id'] != $this->uid) {
                $this->baoError('请不要操作他人的订单');
            }
            $obj->delete($order_id);
            $this->baoSuccess('删除成功！');
        }else{
            $this->baoError('请选择要删除的订单');
        }
	}
    public function eleorder() {
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
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Eleorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids = $addr_ids = $shops_ids = array();
        foreach ($list as $k => $val) {
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
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('cfg', D('Eleorder')->getCfg());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出    
        $this->display('eleorder');
    }

    public function ordercode() {
        $Tuancode = D('Tuancode');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid); //这里只显示 实物
        $count = $Tuancode->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuancode->where($map)->order(array('code_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $val) {
            $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
        }
        $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function coderefund($code_id) {
        $code_id = (int) $code_id;
        if ($detail = D('Tuancode')->find($code_id)) {
            if ($detail['user_id'] != $this->uid) {
                $this->baoError('非法操作');
            }
            if ($detail['status'] != 0 || $detail['is_used'] != 0) {
                $this->baoError('该抢购券不能申请退款');
            }
            if (D('Tuancode')->save(array('code_id' => $code_id, 'status' => 1))) {
                $this->baoSuccess('申请成功！等待网站客服处理！', U('member/ordercode'));
            }
        }
        $this->baoError('操作失败');
    }

    public function integral() {
        $Userintegrallogs = D('Userintegrallogs');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Userintegrallogs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userintegrallogs->where($map)->order(array('log_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

}
