<?php
class LifeserviceAction extends CommonAction {
protected $Lifeservicecates = array();

    public function _initialize() {
        parent::_initialize();
		$life = (int)$this->_CONFIG['operation']['life'];
		if ($life == 0) {
				$this->error('此功能已关闭');
				die;
		}
        $this->lifeservicecates = D('Housekeepingcate')->fetchAll();
        $this->assign('lifeservicecates', $this->lifeservicecates);
    }
    public function index() {
		$cat = (int) $this->_param('cat');
        $this->assign('cat',$cat);
        $linkArr['cat'] = $cat;
		$id = (int)$this->_param('id');
		$this->assign('nextpage', LinkTo('lifeservice/loaddata',$linkArr, array('t' => NOW_TIME, 'p' => '0000')));
		 $this->assign('linkArr',$linkArr);
        $this->display(); // 输出模板
		

    }
	public function loaddata() {
		$houseworksetting = D('Houseworksetting');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
		//$map = array('audit' => 1, 'closed' => 0,'city_id'=>$this->city_id);//开启多城市
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['cate_id'] = $cat;
            $this->seodatas['cate_name'] = $this->Activitycates[$cat]['cate_name'];
        }
		
		//排序重写
	    $orderby = '';
        switch ($order) {
			
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                 $orderby = array('yuyue_num' => 'desc');
                break;
        }
		
		
        $count = $houseworksetting->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
		
        $list = $houseworksetting->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
       
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
        $this->assign('cates', D('Housekeepingcate')->fetchAll());//查询服务项目
        $this->assign('page', $show); // 赋值分页输出
		$this->assign('linkArr',$linkArr);
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
		//更新点击量
		
		$detail['thumb'] = unserialize($detail['thumb']);
		
		
		//修复家政评分不显示
		$pingnum = D('Lifeservicedianping')->where(array('id'=>$id))->count();
		$this->assign('pingnum', $pingnum);
		//p($pingnum);
		$score = (int) D('Lifeservicedianping')->where(array('id'=>$id))->avg('score');
		if($score == 0){
			$score = 5;
		}
		$this->assign('score', $score);
		
       //修复结束	


	
		D('Houseworksetting')->updateCount($id, 'views');
		//$this->assign('citys', D('City')->fetchAll());
		//$this->assign('areas', D('Area')->fetchAll());	
		$ids = D('Houseworksetting')->find($id);//ids是等于项目里面的id
		$shops = $ids['shop_id'];//$shops是等于项目商家ID
		$this->assign('shops', D('Shop')->itemsByIds($shops));//查询商家名字
		$this->assign('detail', $detail);		
        $this->display();

    }
	
	
	 //家政点评开始
    public function dianping() {
        $id = (int) $this->_get('id');
        if (!$detail = D('Houseworksetting')->find($id)) {
            $this->error('没有该家政');
            die;
        }
        if ($detail['closed']) {
            $this->error('该家政已经被删除');
            die;
        }
		$this->assign('next', LinkTo('lifeservice/dianpingloading', $linkArr, array('id' => $id, 't' => NOW_TIME, 'p' => '0000')));
        $this->assign('detail', $detail);
        $this->display();
    }

    public function dianpingloading() {
        $id = (int) $this->_get('id');
        if (!$detail = D('Houseworksetting')->find($id)) {
            die('0');
        }
        if ($detail['closed']) {
            die('0');
        }
		
		$Lifeservicedianping = D('Lifeservicedianping');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'id' => $id);
        $count = $Lifeservicedianping->where($map)->count(); // 查询满足要求的总记录数 
		
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
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
        $this->assign('detail', $detail);
        $this->display();
    }
	
	//家政点评结束
	public function yuyue($id) {
		$id = (int) $id;
		$this->assign('cates', D('Housekeepingcate')->fetchAll());//查询服务项目

		if (!$detail = D('Houseworksetting')->find($id)) {
            $this->niuMsg('该家政项目不存在！');
            die;
        }
		$this->assign('detail', $detail);
        $this->display();
    }


    public function create($id) {
		
		if (empty($this->uid)) {
           $this->niuMsg('登录状态失效!', U('passport/login'));
        }
		$id = (int) $id;
        if (!$id = (int) $id) {
            $this->niuMsg('服务类型不能为空');
        }
		
	
		$ids = D('Houseworksetting')->find($id);//ids是等于项目里面的id
		$idss = $ids['cate_id'];//idss是等于项目里cate_id
		$shops = $ids['shop_id'];//$shops是等于项目商家ID
	
        $cates = D('Housekeepingcate')->fetchAll();
        if (!isset($cates[$idss])) {
            $this->niuMsg('暂时没有该服务类型');//查询不到
        }
		
		//取出商家的邮箱
		$shopsss=D('Shop')->find($shops);
		$shopsss_user=$shopsss['user_id'];
		$shangjias_email=D('Users')->find($shopsss_user);
		$shangjia_email=$shangjias_email['email'];
		
		$data['id'] = $id;
		$data['user_id'] = (int) $this->uid;
        $data['cate_id'] = $idss;
		$data['shop_id'] = $shops;
        $data['date'] = htmlspecialchars($_POST['date']);
        
        $data['svctime'] = $data['date']. $data['time']; 
        if (!$data['addr'] = $this->_post('addr', 'htmlspecialchars')) {
            $this->niuMsg('服务地址不能为空');
        }
        if (!$data['name'] = $this->_post('name', 'htmlspecialchars')) {
            $this->niuMsg('联系人不能为空');
        }
        if (!$data['tel'] = $this->_post('tel', 'htmlspecialchars')) {
            $this->niuMsg('联系电话不能为空');
        }
        if (!isMobile($data['tel']) && !isPhone($data['tel'])) {
            $this->niuMsg('电话号码不正确');
        }
        $data['contents'] = $this->_post('contents', 'htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();

			
        if (D('Housework')->add($data)) {
			D('Houseworksetting')->updateCount($id, 'yuyue_num');
			D('Houseworksetting')->updateCount($id, 'yuyue_num');
			//邮件通知管理员
			$lifeservice = $this->_CONFIG['site']['config_email'];			
			D('Email')->sendMail('email_lifeservice_yuyue', $lifeservice, '你好，管理员：有客户预约家政服务了！', array('name'=>$data['name'],'date'=>$data['date'],'time'=>$data['time'],'addr'=>$data['addr'],'tel'=>$data['tel'],'contents'=>$data['contents']));
			//邮件通知商家
			
			

			if(!empty($shangjia_email)){		
			D('Email')->sendMail('email_sj_lifeservice_yuyue', $shangjia_email, '尊敬的商家，有客户预约家政服务了！', array('name'=>$data['name'],'date'=>$data['date'],'time'=>$data['time'],'addr'=>$data['addr'],'tel'=>$data['tel'],'contents'=>$data['contents']));
			}
            $this->niuMsg('恭喜您预约家政服务成功！网站会推荐给您最优秀的阿姨帮忙！', U('lifeservice/index'));
        }
        $this->niuMsg('服务器繁忙');
    }
}

