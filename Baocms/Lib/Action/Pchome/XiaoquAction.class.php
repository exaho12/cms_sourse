<?php
class XiaoquAction extends CommonAction {

    public function index() {
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);

        $areas = D('Area')->fetchAll();
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
            $this->seodatas['area_name'] = $areas[$area]['area_name'];
            $linkArr['area'] = $area;
        }
        $this->assign('area_id', $area);


        $community = D('Community');
        import('ORG.Util.Pageabc'); // 导入分页类
        //初始数据
        $map = array('city_id' => $this->city_id,'closed' => 0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['name|addr'] = array('LIKE', '%' . $keyword . '%');
        }
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        }
        $orderby = "community_id asc ";
        $count = $community->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $community->order($orderby)->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $val) {
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
		
		
        $this->display(); // 输出模板 
    }


    public function detail($community_id) {
        $community_id = (int) $community_id;
		$act = $this->_param('act');
        $community = D('Community');
        if (empty($community_id)) {
            $this->error('没有该小区');
            die;
        }
        if (!$detail = $community->find($community_id)) {
            $this->error('没有该小区');
            die;
        }
        if ($detail['closed']) {
            $this->error('该小区已经被删除');
            die;
        }
		
        cookie('community_id', $community_id, 365 * 86400);
        $phone = D('Convenientphonemaps')->where(array('community_id' => $community_id))->limit(0, 6)->select();
        $phone_ids = array();
        foreach ($phone as $val) {
            $phone_ids[$val['phone_id']] = $val['phone_id'];
        }
        if (!empty($phone_ids)) {
            $this->assign('phones', D('Convenientphone')->itemsByIds($phone_ids));
        }
        $map = array('community_id' => $community_id, 'closed' => 0, 'audit' => 1);
        $news = D('Communitynews')->where($map)->limit(0, 6)->select();
		$shenhe = array('community_id' => $community_id, 'closed' => 0, 'audit' => 1);
		$posts = D('Communityposts')->where($shenhe)->limit(0, 10)->select();
		
		//统计信息
		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		
		// 小区邻居
		$users = D('Communityusers')->where(array('community_id' => $community_id))->select();
        $ids = array();
        foreach ($users as $k => $val) {
            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
            }
        }
		$this->assign('users', D('Users')->itemsByIds($ids));
		
		// 附近的信息调用 ---》POIS
		$map=array();
        $lat = $detail['lat'];
        $lng = $detail['lng'];
        if (empty($lat) || empty($lng)) {
            $lat = $this->_CONFIG['site']['lat'];
            $lng = $this->_CONFIG['site']['lng'];
        }
        $orderby = "(ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
		
	
		
		/*if($act=='market'){
			$key = '超市';
			$map['name|tag'] = array('LIKE',array('%'.$key.'%','%'.$key,$key.'%','OR'));
			$pois = D('Near') ->order($orderby)->where($map)->limit(0,40)->select();
		}*/
		
		//修改开始，举例超时修改为附近优惠劵
		
		
		//修改结束
		
		
		// 附近的团购
		if($act=='tuan'){
			$map = array('audit' => 1,'closed' => 0);
			$orderby = "create_time desc , (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
			$tuan = D('Tuan') ->order($orderby)->where($map)->limit(0,12)->select();
			$this->assign('tuan', $tuan);
		}
		//p($tuan);die;
		
		
		// 附近的团购
		if($act=='shop'){
			$map = array('audit' => 1,);
			$orderby = "create_time desc , (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
			$shop = D('Shop') ->order($orderby)->where($map)->limit(0,12)->select();
			$this->assign('shop', $shop);
		}
		//p($tuan);die;
		
		
		
		// 附近的生活信息
		if($act=='life'){
			$orderby = "create_time desc , (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
			$life = D('Life') ->order($orderby)->where()->limit(0,42)->select();
			$this->assign('life', $life);
		}
		
		// 附近的生活信息
		if($act=='coupon'){
			$orderby = "create_time desc";
			$coupon = D('Coupon') ->order($orderby)->where()->limit(0,12)->select();
			$this->assign('coupon', $coupon);
		}
		
		$this->seodatas['name'] = $detail['name'];
		$this->seodatas['addr'] = $detail['addr'];
		$this->assign('counts', $counts);
        $this->assign('news', $news);
		$this->assign('act', $act);
		$this->assign('posts', $posts);
		$this->assign('pois', $pois);
        $this->assign('detail', $detail);
        $this->display('detail');
    }



    public function newslist() {
        $community_id = (int) $this->_param('community_id');
        $community = D('Community');
        if (empty($community_id)) {
            $this->error('没有该小区');
            die;
        }
        if (!$detail = $community->find($community_id)) {
            $this->error('没有该小区');
            die;
        }
        if ($detail['closed']) {
            $this->error('该小区已经被删除');
            die;
        }
		
		
        $list = D('Communitynews');
        import('ORG.Util.Page'); // 导入分页类
        $map['community_id'] = $community_id;
		$map['closed'] = 0;
        $count = $list->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $list->order(array('news_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		
		$this->assign('counts', $counts);
		$this->seodatas['name'] = $detail['name'];
		$this->seodatas['addr'] = $detail['addr'];
		$this->assign('detail', $detail);
        $this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }


    public function news() {
        $news_id = (int) $this->_param('news_id');
        if (!$news = D('Communitynews')->find($news_id)) {
            $this->error('没有该物业通知');
            die;
        }
        if ($news['closed']) {
            $this->error('该物业通知已经被删除');
            die;
        }
        if (!$news['audit']) {
            $this->error('该物业通知未通过审核');
            die;
        }
		$community_id = (int) $news['community_id'];
        $community = D('Community');
        if (empty($community_id)) {
            $this->error('没有该小区');
            die;
        }
        if (!$detail = $community->find($community_id)) {
            $this->error('没有该小区');
            die;
        }
        if ($detail['closed']) {
            $this->error('该小区已经被删除');
            die;
        }
		
        D('Communitynews')->updateCount($news_id, 'views');
		
		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		
		$this->assign('counts', $counts);
		$this->seodatas['title'] = $news['title'];
		$this->seodatas['name'] = $detail['name'];
		$this->seodatas['addr'] = $detail['addr'];
		$this->seodatas['desc'] = niuMsubstr($news['intro'],0,200,false);
		$this->assign('detail', $detail);
        $this->assign('news', $news);
        $this->display();
    }
	
	
    public function feedback() {
        if (empty($this->uid)) {
            $this->ajaxLogin(); //提示异步登录
        }
        $community_id = (int) $this->_get('community_id');

        if (!$detail = D('Community')->find($community_id)) {
            $this->niuError('要反馈的小区不存在！');
        }
		
        if (!empty($detail['closed'])) {
            $this->niuError('要反馈的小区状态不正常！');
        }
        if ($this->isPost()) {
            $data = $this->checkFeed();
			$data['user_id'] = (int) $this->uid;
            $data['community_id'] = $community_id;
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $obj = D('Feedback');
            if ($obj->add($data)) {
                $this->niuSuccess('反馈提交成功', U('xiaoqu/detail', array('community_id' => $community_id)));
            }
            $this->niuError('操作失败！');
        } else {
            $this->assign('detail', $detail);
			$this->assign('community_id', $community_id);
            $this->display();
        }
    }

    public function checkFeed() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'details'));
        if (empty($data['title'])) {
            $this->niuError('标题不能为空');
        }
        $data['details'] = htmlspecialchars($data['details']);
        if (empty($data['details'])) {
            $this->niuError('反馈内容不能为空2');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->niuError('反馈内容含有敏感词：' . $words);
        }
        return $data;
    }
    public function join() {
        if (empty($this->uid)) {
            $this->ajaxLogin(); //提示异步登录
        }
		$community_id = (int) $this->_get('community_id');
        if (!$detail = D('Community')->find($community_id)) {
            $this->niuError('没有该小区');
        }
        if ($detail['closed']) {
            $this->niuError('该小区已经被删除');
        }
		$count = D('Communityusers')->where(array('community_id' => $community_id , 'user_id' => $this->uid))->count();
        if ( $count > 0) {
            $this->niuError('您已经入驻了该小区！');
        }
        $data = array(
            'community_id' => $community_id,
            'user_id' => $this->uid,
        );
        if (D('Communityusers')->add($data)) {
            $this->niuSuccess('欢迎您加入'.$detail['name'].'小区！', U('xiaoqu/detail', array('community_id' => $community_id)));
        }
        $this->niuError('加入'.$detail['name'].'失败！');
    }
	
    public function out() {

        if (empty($this->uid)) {
            $this->ajaxLogin(); //提示异步登录
        }
		$community_id = (int) $this->_get('community_id');
        if (!$detail = D('Community')->find($community_id)) {
            $this->niuError('没有该小区');
        }
        if ($detail['closed']) {
            $this->niuError('该小区已经被删除');
        }
		$count = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
        if ( $count == 0) {
            $this->niuError('您还没有入驻了该小区！');
        }
        $map = array(
            'community_id' => $community_id,
            'user_id' => $this->uid,
        );

        if (D('Communityusers')->where($map)->delete()) {
            $this->niuSuccess('您已经退出'.$detail['name'].'小区！', U('xiaoqu/detail', array('community_id' => $community_id)));
        }
        $this->niuError('退出'.$detail['name'].'失败！');
    }
	
	
    public function post() {
        
        if ($this->isPost()) {
            if (empty($this->uid)) {
            $this->ajaxLogin();
        }
            $data = $this->postCheck();
			
		
			$photos = $this->_post('photo');
			$photo = $val = '';
			if(!empty($photos)){
				foreach($photos as $val){
					if (isImage($val) && $val !=''){
						$photo = $photo.','.$val;
					}
				}
			}
			$data['gallery'] = ltrim($photo,',');
			
			$data['audit'] = $this->_CONFIG['site']['xiaoqu_post_audit'];//回帖是否免审核。
            $obj = D('Communityposts');
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            if ($obj->add($data)) {
                $this->niuSuccess('发布帖子成功', U('xiaoqu/tieba',array('community_id' => $data['community_id'])));
            }
            $this->niuError('操作失败！');
        } else {
            $this->niuError('操作失败！');
        }
    }
	
    private function postCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'community_id', 'details'));
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->niuError('标题不能为空');
        }
		
		$count = D('Communityusers')->where(array('community_id' => $data['community_id'] , user_id => $this->uid))->count();
        if ( $count == 0) {
            $this->niuError('您还没有入驻了该小区！');
        }
		
		$user = D('Users')->find($this->uid);
        $data['user_id'] = (int) $this->uid;
		$data['username'] = $user['nickname'];
        $data['community_id'] = (int) $data['community_id'];
        if (empty($data['community_id'])) {
            $this->niuError('小区不能为空');
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->niuError('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->niuError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }
	
	
	
    public function tieba($community_id) {
        $community_id = (int) $community_id;
        $community = D('Community');
        if (empty($community_id)) {
            $this->error('没有该小区贴吧');
            die;
        }
        if (!$detail = $community->find($community_id)) {
            $this->error('没有该小区贴吧');
            die;
        }
        if ($detail['closed']) {
            $this->error('该小区贴吧被删除');
            die;
        }	
        cookie('community_id', $community_id, 365 * 86400);
		$map_tieba = array('community_id'=>$community_id,'closed'=>0,'audit'=>1);
		$posts = D('Communityposts')->where($map_tieba)->limit(0, 10)->select();		
		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		
        $list = D('Communityposts');
        import('ORG.Util.Page'); // 导入分页类

		$map = array('community_id'=>$community_id,'closed' => 0, 'audit' => 1);//增加显示开关
        $count = $list->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $list->order(array('post_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
        $ids = array();
        foreach ($list as $k => $val) {
            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
            }
        }

		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		
		$this->assign('counts', $counts);
		$this->seodatas['name'] = $detail['name'];
		$this->seodatas['addr'] = $detail['addr'];
        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
		$this->assign('counts', $counts);
        $this->assign('detail', $detail);
        $this->display('tieba');
    }
	
   public function tie($post_id) {
        $post_id = (int) $post_id;
		$post = D('Communityposts');
		
		
        if (!$tie = $post->find($post_id)) {
            $this->error('没有该帖子');
            die;
        }
		//未审核帖子不显示
		if ($tie['audit'] != 1) {
            $this->error('该信息不存在或者未审核');
        }
		
		
		
		$community_id = (int) $tie['community_id'];

        $community = D('Community');
        if (empty($community_id)) {
            $this->error('没有该小区贴吧');
            die;
        }
        if (!$detail = $community->find($community_id)) {
            $this->error('没有该小区贴吧');
            die;
        }
        if ($detail['closed']) {
            $this->error('该小区贴吧被删除');
            die;
        }
		
		
		
        cookie('community_id', $community_id, 365 * 86400);
		$map = array('community_id' => $community_id,'closed' => 0, 'audit' => 1);
		
		
	
		
		
		$posts = D('Communityposts')->where($map)->limit(0, 10)->select();
		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		$author = D('Users')-> find($tie['user_id']);
		
		
        $list = D('Communityreplys');
        import('ORG.Util.Page'); // 导入分页类
        $map['community_id'] = $community_id;
		$map['post_id'] = $post_id;
        $count = $list->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
	
        $list = $list->order(array('reply_id' => 'asc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
        $ids = array();
        foreach ($list as $k => $val) {

            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
            }
        }

		D('Communityposts')->updateCount($post_id, 'view_num');
		
		
		$counts['post'] = D('Communityposts')->where(array('community_id' => $community_id))->count();
		$counts['user'] = D('Communityusers')->where(array('community_id' => $community_id))->count();
		$counts['isin'] = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
		
		$this->assign('counts', $counts);
		$this->seodatas['title'] = $tie['title'];
		$this->seodatas['name'] = $detail['name'];
		$this->seodatas['addr'] = $detail['addr'];
		$this->seodatas['desc'] = niuMsubstr($tie['details'],0,200,false);
        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
		$this->assign('author', $author);
	
        $this->assign('tie', $tie);
		$this->assign('counts', $counts);
        $this->assign('detail', $detail);
        $this->display();
    }
	

	
    public function reply() {
        
        if ($this->isPost()) {
            if (empty($this->uid)) {
            $this->ajaxLogin();
        }
            $data = $this->replyCheck();
            $obj = D('Communityreplys');
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
			$data['audit'] = $this->_CONFIG['site']['xiaoqu_reply_audit'];//回帖是否免审核
			$photos = $this->_post('photo');
			$photo = $val = '';
			if(!empty($photos)){
				foreach($photos as $val){
					if (isImage($val) && $val !=''){
						$photo = $photo.','.$val;
					}
				}
			}
			$data['gallery'] = ltrim($photo,',');
			
            if ($obj->add($data)) {
				D('Communityposts')->updateCount($data['post_id'], 'reply_num');
                $this->niuSuccess('发布帖子成功', U('xiaoqu/tie',array('post_id' => $data['post_id'])));
            }

            $this->niuError('操作失败！');
        } else {
            $this->niuError('操作失败！');
        }
    }
	
    private function replyCheck() {
        $data = $this->checkFields($this->_post('data', false), array('post_id', 'details'));
        $post_id = (int) $data['post_id'];
		$post = D('Communityposts');
        if (!$tie = $post->find($post_id)) {
            $this->error('没有该帖子');
            die;
        }
		$community_id = (int) $tie['community_id'];
		$count = D('Communityusers')->where(array('community_id' => $community_id , user_id => $this->uid))->count();
        if ( $count == 0) {
            $this->niuError('您还没有入驻了该小区！');
        }
		
        $data['user_id'] = (int) $this->uid;
        $data['community_id'] = $community_id;
        if (empty($community_id)) {
            $this->niuError('小区不能为空');
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->niuError('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->niuError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }
	
	
    public function zan() {
        $post_id = (int) $this->_get('post_id');
        $detail = D('Communityposts')->find($post_id);
        if (empty($detail)) {
            $this->niuError('您查看的内容不存在！');
        }
        D('Communityposts')->updateCount($post_id, 'zan_num');
        $this->niuSuccess('恭喜您，点赞成功！', U('xiaoqu/tie', array('post_id' => $post_id)));
    }
	
	
}