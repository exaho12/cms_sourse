<?php

class CouponAction extends CommonAction {

    private $create_fields = array( 'title', 'photo', 'expire_date','num','limit_num', 'intro');
    private $edit_fields = array( 'title', 'photo', 'expire_date','num','limit_num', 'intro');
    
    
    public function sale() {
        
        $Coupon = D('Coupon');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id'=>  $this->shop_id,'closed'=>0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Coupon->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Coupon->where($map)->order(array('coupon_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	 public function download(){
        /*
        if ($this->shop['card_date'] < TODAY) {
            $this->error('亲还没有和' . $this->_CONFIG['site']['sitename'] . '合作会员卡推广！',U('expand/buy'));
        }
         * 
         */
        $Coupondownload = D('Coupondownload');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id'=>  $this->shop_id);
        $count = $Coupondownload->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Coupondownload->where($map)->order(array('download_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = $coupons = array();
        foreach ($list as $k => $val) {
            if ($val['coupon_id']) {
                $coupons[$val['coupon_id']] = $val['coupon_id'];
            }
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;
        } 
        if ($coupons) {
            $this->assign('coupons', D('Coupon')->itemsByIds($coupons));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Coupon');
            if ($obj->add($data)) {
                $this->niuMsg('添加成功，请等待网站管理员审核', U('store/coupon/sale'));
            }
            $this->niuMsg('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['shop_id'] = $this->shop_id;
        $data['cate_id'] = $this->shop['cate_id'];
        $data['area_id'] = $this->shop['area_id'];
        $data['city_id'] = $this->shop['city_id'];
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->niuMsg('标题不能为空');
        } $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->niuMsg('请上传优惠券图片');
        }
        if (!isImage($data['photo'])) {
            $this->niuMsg('优惠券图片格式不正确');
        } $data['expire_date'] = htmlspecialchars($data['expire_date']);
        if (empty($data['expire_date'])) {
            $this->niuMsg('过期日期不能为空');
        }
        if (!isDate($data['expire_date'])) {
            $this->niuMsg('过期日期格式不正确');
        } $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->niuMsg('优惠券描述不能为空');
        }
        $data['num'] = (int)$data['num'];
        $data['limit_num'] = (int)$data['limit_num'];
        $data['create_time'] = NOW_TIME;
        $data['create_ip']  = get_client_ip();
        return $data;
    }

    public function edit($coupon_id = 0) {
        if ($coupon_id = (int) $coupon_id) {
            $obj = D('Coupon');
            if (!$detail = $obj->find($coupon_id)) {
                $this->error('请选择要编辑的优惠券');die;
            }
            if($detail['shop_id'] != $this->shop_id){
                $this->error('请选择要编辑的优惠券');die;
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['coupon_id'] = $coupon_id;
                if (false !== $obj->save($data)) {
                    $this->niuMsg('编辑成功', U('store/coupon/sale'));
                }
                $this->niuMsg('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->error('请选择要编辑的优惠券');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['shop_id'] = $this->shop_id;
         
        $data['cate_id'] = $this->shop['cate_id'];
        $data['area_id'] = $this->shop['area_id'];
        $data['city_id'] = $this->shop['city_id'];
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->niuMsg('标题不能为空');
        } $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->niuMsg('请上传优惠券图片');
        }
        if (!isImage($data['photo'])) {
            $this->niuMsg('优惠券图片格式不正确');
        } $data['expire_date'] = htmlspecialchars($data['expire_date']);
        if (empty($data['expire_date'])) {
            $this->niuMsg('过期日期不能为空');
        }
        if (!isDate($data['expire_date'])) {
            $this->niuMsg('过期日期格式不正确');
        } $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->niuMsg('优惠券描述不能为空');
        }
		$data['audit'] = 0;
        $data['num'] = (int)$data['num'];
        $data['limit_num'] = (int)$data['limit_num'];
        return $data;
    }
    
	 // 删除优惠劵
	public function delete($coupon_id = 0) {
            $coupon_id = (int) $coupon_id;
			if (empty($coupon_id)) {
		  	$this->ajaxReturn(array('status'=>'error','msg'=>'访问错误！'));
            }
            $obj = D('Coupon');
            if(!$detail = $obj->where(array('shop_id'=>$this->shop_id,'coupon_id'=>$coupon_id))->find()){
                $this->ajaxReturn(array('status'=>'error','msg'=>'非法操作！'));
            }
            $obj->save(array('coupon_id' => $coupon_id, 'closed' => 1));
			$this->ajaxReturn(array('status'=>'success','msg'=>'删除成功', U('store/coupon/sale')));

		
    }
	//优惠劵详情
	public function weixin($download_id = 0) {
        $download_id = $this->_get('download_id');
        if (!$detail = D('Coupondownload')->find($download_id)) {
            $this->error('没有该优惠券');
        }
		
        if ($detail['shop_id'] != $this->shop_id) {
            $this->error("非法操作");
        }
        if ( $detail['is_used'] != 0) {
            $this->error('该优惠券属于不可消费的状态');
        }
        $url = U('/worker/weixin/coupon', array('download_id' => $download_id, 't' => NOW_TIME, 'sign' => md5($download_id . C('AUTH_KEY') . NOW_TIME)));
	
        $token = 'couponcode_' . $download_id;
        $file = baoQrCode($token, $url);
        $this->assign('file', $file);
        $this->assign('detail', $detail);
        $this->display();
    }

    public function index() {
        if ($this->isPost()) {
            $code = $this->_post('code', false);
            foreach ($code as $v) {
                if (empty($v)) {
					$this->error('请输入电子优惠券');
                }
            }
            $obj = D('Coupondownload');
            $ip = get_client_ip();
            $return = array();
            foreach ($code as $key => $var) {
                $var = trim(htmlspecialchars($var));
                if (!empty($var)) {
                    $data = $obj->find(array('where' => array('code' => $var)));
                    if (!empty($data) && (int) $data['shop_id'] == $this->shop_id && (int) $data['is_used'] == 0) {
                        if (false !== $obj->save(array('download_id' => $data['download_id'], 'is_used' => 1, 'used_ip' => $ip, 'used_time' => NOW_TIME))) {
                            $return[$var] = $var;
                        }
                    } else {
                        continue;
                    }
                }
            }
            if (!empty($return)) {
                $msg = join(',',$return);
                $this->error("恭喜您，您成功消费的优惠券如下：".$msg); //放入foreach内循环一次后便会退出
            }else{
				$this->error('无效的电子优惠券');
            }
        } else {
            $this->display();
        }
    }
   

}

