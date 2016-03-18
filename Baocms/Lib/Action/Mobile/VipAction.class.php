<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class VipAction extends CommonAction {

    public function index() {
        $cat = (int) $this->_param('cat');
        $this->assign('cat', $cat);
        $order = (int) $this->_param('order');
        $this->assign('order', $order);
        $areas = D('Area')->fetchAll();
        $area = (int) $this->_param('area');
        $this->assign('area_id', $area);
        $biz = D('Business')->fetchAll();
        $business = (int) $this->_param('business');
        $this->assign('business_id', $business);
        $this->assign('areas', $areas);
        $this->assign('biz', $biz);
        $this->assign('nextpage', LinkTo('vip/loaddata', array('cat' => $cat, 'area' => $area, 'business' => $business, 'order' => $order, 't' => NOW_TIME, 'p' => '0000')));
        $this->display(); // 输出模板
    }

    public function loaddata() {
        $Shop = D('Shop');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('card_date' => array('EGT', TODAY));

        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Shopcate')->getChildren($cat);

            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }
        $this->assign('cat', $cat);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name|addr'] = array('LIKE', '%' . $keyword . '%');
        }
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }

        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
            $this->assign('area_id', $area);
        }

        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
            $this->assign('business_id', $business);
        }

        $order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case 2:
                $orderby = array('orderby' => 'asc', 'ranking' => 'desc');
                break;
            default:
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
                break;
        }
        $this->assign('order', $order);
        $count = $Shop->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Shop->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $key => $v) {
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }

        $shopdetails = D('Shopdetails')->itemsByIds($shop_ids);
        foreach ($list as $k => $val) {
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('shopdetails', $shopdetails);
        $this->assign('page', $show); // 赋值分页输出

        $this->display();
    }

    public function join() {
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }

        $shop_id = (int) $this->_get('shop_id');
        if (empty($shop_id)) {
            $this->error('该店铺不存在！');
        }

        $detail = D('Shop')->find($shop_id);
        if (empty($detail)) {
            $this->error('该店铺不存在！');
        }
        $Shop = D('Shop');
        $detail = $Shop->find($shop_id);
        if (!$detail) {
            $this->error('该会员卡不存在！');
        }

        $card = D('Usercard')->checkCard($this->uid, $shop_id);
        if ($card) {
            $this->error('你已经拥有此会员卡', U('vip/index'));
        }
        $data = array();
        $data['user_id'] = $this->uid;
        $data['shop_id'] = $shop_id;
        $data['integral'] = 0;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();

        if (D('Usercard')->add($data)) {
            $this->success('领取会员卡成功', U('member/usercard'));
        }
        $this->error('操作失败！');
    }

    public function detail() {
        $Shop = D('Shop');
        $shop_id = (int) $this->_param('shop_id');
        $detail = $Shop->find($shop_id);
        $this->assign('detail', $detail); // 赋值数据集
        $this->assign('ex',D('Shopdetails')->find($shop_id));
        $this->display();
    }

}
