<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HuodongAction extends CommonAction {

    protected $Activitycates = array();

    public function _initialize() {
        parent::_initialize();
        $this->Activitycates = D('Activitycate')->fetchAll();
        $this->assign('activitycates', $this->Activitycates);
    }

    public function index() {
        $Activity = D('Activity');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('audit' => 1, 'closed' => 0,'city_id'=>$this->city_id,'end_date' => array('EGT', TODAY));
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['cate_id'] = $cat;
            $this->seodatas['cate_name'] = $this->Activitycates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
        $areas = D('Area')->fetchAll();
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
            $this->seodatas['area_name'] = $areas[$area]['area_name'];
        }
        $this->assign('area_id', $area);
        $shop_id = (int) $this->_get('shop_id');
        if (!empty($shop_id)) {
            $map['shop_id'] = $shop_id;
        }
        $count = $Activity->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Activity->where($map)->order(array('activity_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            $sign = D('Activitysign')->where(array('user_id' => $this->uid, 'activity_id' => $val['activity_id']))->find();
            if (!empty($sign)) {
                $list[$k]['sign'] = 1;
            } else {
                $list[$k]['sign'] = 0;
            }
        }
        $shop_ids = array();
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
        $this->display(); // 输出模板
    }

    public function detail() {
        $activity_id = (int) $this->_get('activity_id');

        if (empty($activity_id)) {
            $this->error('该活动信息不存在！');
            die;
        }
        if (!$detail = D('Activity')->find($activity_id)) {
            $this->error('该活动信息不存在！');
            die;
        }
        if ($detail['closed']) {
            $this->error('该活动信息不存在！');
            die;
        }
        $sign = D('Activitysign')->where(array('user_id' => $this->uid,'sign_end' => array('EGT', TODAY), 'activity_id' => $activity_id))->select();
        if (!empty($sign)) {
            $detail['sign'] = 1;
        } else {
            $detail['sign'] = 0;
        }
        $detail = D('Activity')->_format($detail);
		
        $detail['end_time'] = strtotime($detail['sign_end']) - NOW_TIME + 86400;

		//$detail['end_time'] = strtotime($detail['sign_end']) - NOW_TIME + 86400;
		

		
		$detail['thumb'] = unserialize($detail['thumb']);
		
		
        $this->assign('detail', $detail);
        $shop_id = $detail['shop_id'];
        $shop = D('Shop')->find($shop_id);
        $ex = D('Shopdetails')->find($shop_id);

		$t = D('Tuan');
		$tuan = $t -> where('activity_id ='.$detail['activity_id']) -> select();
		        
		$this->assign('tuan',$tuan);
		
		
		
        $this->assign('shop', $shop);
        $this->assign('ex', $ex);
        $this->seodatas['title'] = $detail['title'];
        $this->seodatas['shop_name'] = $shop['shop_name'];
        $this->assign('host',__HOST__);
        $this->display();
    }

    public function sign() {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status'=>'login'));
        }
        $activity_id = (int) $this->_get('activity_id');
        $detail = D('Activity')->find($activity_id);
		
		$shops = $detail['shop_id'];//查询商家UID
		$users_id = D('Shop')->find($shops);
		$userss_id = $users_id['user_id'];//查询会员UID
		$users_email = D('Users')->find($userss_id);//查询会员详细资料
		$huodong_email=$users_email['email'];//查询会员邮箱$huodong_email
		$huodong_moblie=$users_email['moblie'];//查询会员手机$huodong_moblie
		

		
        if (empty($detail)) {
             $this->ajaxReturn(array('status'=>'error','msg'=>'报名的活动不存在'));
        }
        if (IS_AJAX) {
            $data = $this->checkSign();
            $data['activity_id'] = $activity_id;
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $obj = D('Activitysign');
            if ($obj->add($data)) {
                D('Activity')->updateCount($activity_id, 'sign_num');
				if(!empty($huodong_email)){	//如果不等于空发邮件email_huodong_email	
				D('Email')->sendMail('email_huodong_email', $huodong_email, '尊敬的商家，有客户报名活动！', 
					array(
					'name'=>$data['name'],
					'mobile'=>$data['mobile'],
					'num'=>$data['num']
					)
				);
			}
                $this->ajaxReturn(array('status'=>'success','msg'=>'报名成功'));
            }
             $this->ajaxReturn(array('status'=>'error','msg'=>'操作失败！'));
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function checkSign() {
        $data = $this->checkFields($this->_post('data', false), array('name', 'mobile', 'num'));
        $data['user_id'] = (int) $this->uid;
        $data['name'] = $data['name'];
        if (empty($data['name'])) {
             $this->ajaxReturn(array('status'=>'error','msg'=>'联系人不能为空'));
        }
        $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
             $this->ajaxReturn(array('status'=>'error','msg'=>'联系电话不能为空'));
        }
        if (!isPhone($data['mobile']) && !isMobile($data['mobile'])) {
             $this->ajaxReturn(array('status'=>'error','msg'=>'联系电话格式不正确'));
        }
        $data['num'] = (int) $data['num'];
        if (empty($data['num'])) {
             $this->ajaxReturn(array('status'=>'error','msg'=>'活动人数不能为空'));
        }
        return $data;
    }

}
