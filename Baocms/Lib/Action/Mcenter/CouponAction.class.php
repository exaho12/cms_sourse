<?php
class CouponAction extends CommonAction {

	
	public function index() {
        $aready = (int) $this->_param('aready');
		$this->assign('aready', $aready);
		$this->display();
	}

	public function couponloading() {
		$Coupondownloads = D('Coupondownload');
		import('ORG.Util.Page');
		$map = array('user_id' => $this->uid);
                
                $aready = (int) $this->_param('aready');

		if ($aready == 2) {
			$map['is_used'] = array('egt',1);
		}elseif ($aready == 1) {
			$map['is_used'] = 0;
                }else{
                    $aready == null;
                }
                
		$count = $Coupondownloads->where($map)->count();
		$Page = new Page($count, 25);
		$show = $Page->show();
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		
		
		
		$list = $Coupondownloads->where($map)->order('is_used asc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$coupon_ids = array();
		foreach ($list as $k => $val) {
			$coupon_ids[$val['coupon_id']] = $val['coupon_id'];
		}
		$shops = D('Shop')->itemsByIds($shop_ids);
		$coupon = D('Coupon')->itemsByIds($coupon_ids);
		$this->assign('coupon', $coupon);
		$this->assign('shops', $shops);
		$this->assign('list', $list);
		$this->assign('page', $show);
		
		
		
		
		$this->display();
	}

	public function coupondel($download_id) {
		$download_id = (int) $download_id;
		if (empty($download_id)) {
			$this->error('该优惠券不存在');
		}
		if (!$detail = D('Coupondownload')->find($download_id)) {
			$this->error('该优惠券不存在');
		}
		if ($detail['user_id'] != $this->uid) {
			$this->error('请不要操作别人的优惠券');
		}
		D('Coupondownload')->delete($download_id);
		$this->success('删除成功！', U('coupon/index'));
	}
	
	
    public function weixin() {
        $download_id = $this->_get('download_id');
        if (!$detail = D('Coupondownload')->find($download_id)) {
            $this->error('没有该优惠券');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->error("优惠券不存在！");
        }
        if ( $detail['is_used'] != 0) {
            $this->error('该优惠券属于不可消费的状态');
        }
        $url = U('weixin/coupon', array('download_id' => $download_id, 't' => NOW_TIME, 'sign' => md5($download_id . C('AUTH_KEY') . NOW_TIME)));
        $token = 'couponcode_' . $download_id;
        $file = baoQrCode($token, $url);
        $this->assign('file', $file);
        $this->assign('detail', $detail);
        $this->display();
    }
	
	
}