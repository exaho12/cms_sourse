<?php
class LifeserviceAction extends CommonAction {
protected $Activitycates = array();

    public function _initialize() {
        parent::_initialize();
		$lifeservice = (int)$this->_CONFIG['operation']['lifeservice'];//赋值分销开关
		if ($lifeservice == 0) {
				$this->error('此功能已关闭');
				die;
		}
        $this->lifeservicecates = D('Housekeepingcate')->fetchAll();
        $this->assign('lifeservicecates', $this->lifeservicecates);
    }
    public function index() {

        $houseworksetting = D('Houseworksetting');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['cate_id'] = $cat;
            $this->seodatas['cate_name'] = $this->Activitycates[$cat]['cate_name'];
        }
        $count = $houseworksetting->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $houseworksetting->where($map)->order(array('id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
       
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



    public function detail($id) {
        $id = (int) $id;
		
        $this->assign('cates', D('Housekeepingcate')->fetchAll());//查询服务项目
		if (!$detail = D('Houseworksetting')->find($id)) {
            $this->error('该家政项目不存在！');
            die;
        }
        
        $detail = D('Houseworksetting')->find($id);
        $this->assign('detail', $detail);
        $h = date('H',NOW_TIME) + 1;
        $this->assign('h',$h);
        $cfg = D('Shopdingsetting')->getCfg(); //调用定做的时间设置
        $this->assign('cfg',$cfg);
		//更新点击量
		$sign = D('Housework')->where(array('user_id' => $this->uid, 'id' => $id))->select();
        if (!empty($sign)) {
            $detail['sign'] = 1;
        } else {
            $detail['sign'] = 0;
        }
		D('Houseworksetting')->updateCount($id, 'views');
		//$this->assign('citys', D('City')->fetchAll());
		//$this->assign('areas', D('Area')->fetchAll());	
		$ids = D('Houseworksetting')->find($id);//ids是等于项目里面的id
		$shops = $ids['shop_id'];//$shops是等于项目商家ID
		$this->assign('shops', D('Shop')->itemsByIds($shops));//查询商家名字
		
		
		$detail['thumb'] = unserialize($detail['thumb']);
		
		
		
		// 点评开始
		$Lifeservicedianping = D('Lifeservicedianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'activity_id' => $id, 'show_date' => array('ELT', TODAY));
        $count = $Lifeservicedianping->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Lifeservicedianping->where($map)->order(array('id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $id_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $id_ids[$val['id']] = $val['id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($id_ids)) {
            $this->assign('pics', D('Lifeservicedianpingpics')->where(array('id' => array('IN', $id_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
		/** 修复评论用户等级不显示 */
       $userrank = D('user_rank')-> select();
       $this -> assign('userrank',$userrank);
       /**修复等级不显示 end**/
		
		
		
		
		$this->assign('detail', $detail);
        $this->display();

    }



    public function create($id) {
		
		if (empty($this->uid)) {
            $this->ajaxLogin();
        }
		
        if (!$id = (int) $id) {
            $this->baoError('服务类型不能为空');
        }

	
		
			
		$ids = D('Houseworksetting')->find($id);//ids是等于项目里面的id
		$idss = $ids['cate_id'];//idss是等于项目里cate_id
		$shops = $ids['shop_id'];//$shops是等于项目商家ID		
        $cates = D('Housekeepingcate')->fetchAll();
        if (!isset($cates[$idss])) {
            $this->baoError('暂时没有该服务类型');//查询不到
        }
		//取出商家的邮箱
		$shopsss = D('Shop')->find($shops);
		$shopsss_user = $shopsss['user_id'];
		$shangjias_email = D('Users')->find($shopsss_user);
		$shangjia_email = $shangjias_email['email'];
		$shangjia_user_id = $shangjias_email['user_id'];//商家ID
		
		$data['id'] = $id;
		$data['user_id'] = (int) $this->uid;
		
        $data['cate_id'] = $idss;
		$data['shop_id'] = $shops;
        $data['date'] = htmlspecialchars($_POST['date']);
        $data['time'] = htmlspecialchars($_POST['time']);
        if(empty($data['date'])|| empty($data['time'])){
            $this->baoError('服务时间不能为空');
        }
        $data['svctime'] = $data['date']. $data['time']; 
        if (!$data['addr'] = $this->_post('addr', 'htmlspecialchars')) {
            $this->baoError('服务地址不能为空');
        }
        if (!$data['name'] = $this->_post('name', 'htmlspecialchars')) {
            $this->baoError('联系人不能为空');
        }
        if (!$data['tel'] = $this->_post('tel', 'htmlspecialchars')) {
            $this->baoError('联系电话不能为空');
        }
        if (!isMobile($data['tel']) && !isPhone($data['tel'])) {
            $this->baoError('电话号码不正确');
        }
        $data['contents'] = $this->_post('contents', 'htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
		
			
        if (D('Housework')->add($data)) {
			D('Houseworksetting')->updateCount($id, 'yuyue_num');
	
			//邮件通知管理员
			$lifeservice = $this->_CONFIG['site']['config_email'];			
			D('Email')->sendMail('email_lifeservice_yuyue', $lifeservice, '你好，管理员：有客户预约家政服务了！', array(
				'name'=>$data['name'],
				'date'=>$data['date'],
				'time'=>$data['time'],
				'addr'=>$data['addr'],
				'tel'=>$data['tel'],
				'contents'=>$data['contents']
			));
			//邮件通知商家

			if(!empty($shangjia_email)){		
			D('Email')->sendMail('email_sj_lifeservice_yuyue', $shangjia_email, '尊敬的商家，有客户预约家政服务了！', array(
				'name'=>$data['name'],
				'date'=>$data['date'],
				'time'=>$data['time'],
				'addr'=>$data['addr'],
				'tel'=>$data['tel'],
				'contents'=>$data['contents']
				));
			}
			
			
		 //====================家政预约成功微信通知商家=========================
            $tuan     = D('Tuan')->find($order['tuan_id']);
            $uaddr = D('UserAddr') -> where('user_id ='.$order['user_id']) -> find();
            include_once "Baocms/Lib/Net/Wxmesg.class.php";
			
            $_data_yuyue = array(//整体变更
                'url'       =>  "http://".$_SERVER['HTTP_HOST']."/mcenter/tuan/detail/order_id/".$order_id.".html",
                'topcolor'  =>  '#F55555',
                'first'     =>  '亲,您预约成功了！',
                'remark'    =>  '更多信息,请登录http://'.$_SERVER['HTTP_HOST'].'！感谢您的惠顾！',
				'name'     =>  $data['name'],//预约人姓名时间
                'date'     =>  $data['date'],//预约人时间
                'tel' =>  $tuan['tel'],//预约人电话号码
                'contents' =>  $tuan['contents']//预约人详细内容
            );
            $order_data = Wxmesg::yuyue($_data_yuyue);
            $return   = Wxmesg::net($shangjia_user_id, 'OPENTM202297666', $order_yuyue);//传值，一个是商家的USER_ID

            //====================家政预约微信同事结束==============================
			
			
			
            $this->baoSuccess('恭喜您预约家政服务成功！网站会推荐给您最优秀的阿姨帮忙！', U('lifeservice/index'));
        }
        $this->baoError('服务器繁忙');
    }
}

