<?php 

class ApplyAction extends CommonAction{
public function index() {
        if (empty($this->uid)) {
            header("Location:" . U('passport/login'));
            die;
        }
        if (D('Shop')->find(array('where' => array('user_id' => $this->uid)))) {

            $this->error('您已经拥有一家店铺了！', U('store/index/index'));
        }
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Shop');
            $details = $this->_post('details', 'htmlspecialchars');
            if ($words = D('Sensitive')->checkWords($details)) {
                $this->niuMsg('商家介绍含有敏感词：' . $words);
            }

            $ex = array(
                'details' => $details,
                'near' => $data['near'],
                'price' => $data['price'],
                'business_time' => $data['business_time'],
            );
            unset($data['near'], $data['price'], $data['business_time']);
            if ($shop_id = $obj->add($data)) {
                $wei_pic = D('Weixin')->getCode($shop_id, 1);
                $ex['wei_pic'] = $wei_pic;
                D('Shopdetails')->upDetails($shop_id, $ex);
                $this->niuMsg('恭喜您申请成功！请登录电脑版完善商家详细信息！稍后有网站负责人将联系您！', U('mcenter/member/index'));
            }
            $this->niuMsg('申请失败！');
        } else {
            $lat = addslashes(cookie('lat'));
            $lng = addslashes(cookie('lng'));
            if (empty($lat) || empty($lng)) {
                $lat = $this->_CONFIG['site']['lat'];
                $lng = $this->_CONFIG['site']['lng'];
            }
            if ($business_id = (int) $this->_param('business_id')) {
                $map['business_id'] = $business_id;
                $this->assign('business_id', $business_id);
            }

            $this->assign('business', D('Business')->fetchAll());
            $this->assign('lat', $lat);
            $this->assign('lng', $lng);
            $areas = D('Area')->fetchAll();

            $this->assign('cates', D('Shopcate')->fetchAll());

            $this->assign('areas', $areas);

            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), array('cate_id', 'tel', 'logo', 'photo', 'shop_name', 'contact', 'details', 'business_time', 'area_id', 'addr', 'lng', 'lat'));
        $data['shop_name'] = htmlspecialchars($data['shop_name']);
        if (empty($data['shop_name'])) {
            $this->niuMsg('店铺名称不能为空');
        }
        $data['lng'] = htmlspecialchars($data['lng']);
        $data['lat'] = htmlspecialchars($data['lat']);
        if (empty($data['lng']) || empty($data['lat'])) {
            $this->niuMsg('店铺坐标需要设置');
        }
        $data['cate_id'] = (int) $data['cate_id'];
        $data['area_id'] = (int) $data['area_id'];
        if (empty($data['area_id'])) {
            $this->niuMsg('地区不能为空');
        }
        $data['contact'] = htmlspecialchars($data['contact']);
        if (empty($data['contact'])) {
            $this->niuMsg('联系人不能为空');
        }$data['business_time'] = htmlspecialchars($data['business_time']);
        if (empty($data['business_time'])) {
            $this->niuMsg('营业时间不能为空');
        }
       /* if (!isImage($data['logo'])) {
            $this->niuMsg('请上传正确的LOGO');
        }*/
       
        $data['addr'] = htmlspecialchars($data['addr']);
        if (empty($data['addr'])) {
            $this->niuMsg('地址不能为空');
        }
        $data['tel'] = htmlspecialchars($data['tel']);
        if (empty($data['tel'])) {
            $this->niuMsg('联系方式不能为空');
        }

        if (!isPhone($data['tel']) && !isMobile($data['tel'])) {
            $this->niuMsg('联系方式格式不正确');
        }
        if (isMobile($data['tel'])) {
            $data['phone'] = $data['tel'];
        }
        $detail = D('Shop')->where(array('user_id' => $this->uid))->find();
        if (!empty($detail)) {
            $this->niuMsg('您已经是商家了');
        }
        $data['user_id'] = $this->uid;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }
}
?>