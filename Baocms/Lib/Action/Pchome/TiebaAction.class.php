<?php
class TiebaAction extends CommonAction {
    protected  $sharecates = array();
    public function _initialize() {
        parent::_initialize();
		$tieba = (int)$this->_CONFIG['operation']['tieba'];
		if ($tieba == 0) {
				$this->error('此功能已关闭');
				die;
		}
        $this->sharecates = D('Sharecate')->fetchAll();
        $this->assign('sharecates', $this->sharecates);
        $cache = cache(array('type' => 'File', 'expire' => 600));
        if (!$counts = $cache->get('index_count')) {
            $counts['shop'] = D('Shop')->count();
            $counts['coupon'] = D('Coupon')->count();
            $counts['users'] = D('Users')->count();
            $counts['life'] = D('Life')->count();
            $counts['post'] = D('Post')->count();
			$counts['community'] = D('Community')->count();
            $cache->set('index_count', $counts);
        }
		$this->assign('total', $counts);
    }

    public function index() {
        $Post = D('Post');
        import('ORG.Util.Pageabc'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1);
        $parent_id = 0;
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Sharecate')->getChildren($cat);
            if (!empty($catids)) {
               $map['cate_id'] = array('IN', $catids);
               $parent_id= $cat;
            } else {
                $parent_id = $this->sharecates[$cat]['parent_id'];
                $map['cate_id'] = $cat;
            }
            $this->seodatas['cate_name'] = $this->sharecates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
        $this->assign('parent_id',$parent_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }

        $linkArr['order'] = $order = $this->_param('order', 'htmlspecialchars');
		$orderby = '';
        switch ($order) {
            case 'd':
                $orderby = array('views' => 'desc', 'reply_num' => 'desc', 'zan_num' => 'desc');
                break;
            case 'x':
                $orderby = array('zan_num' => 'desc');
                break;
            default:
                $orderby = array('orderby' => 'asc', 'post_id' => 'desc');
                break;
        }
		
        $this->assign('order', $order);
        $count = $Post->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Post->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $ids = array();
        foreach ($list as $k => $val) {
            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
            }
        }
        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('counts',$count);
		$this->assign('linkArr', $linkArr);
        $this->display(); // 输出模板
    }

    public function detail() {
        $post_id = (int) $this->_get('post_id');
        $detail = D('Post')->find($post_id);
        $puser = D('Users')->find($detail['user_id']);
        $detail['nickname'] = $puser['nickname'];
        if (empty($detail) || $detail['audit'] != 1) {
            $this->error('您查看的内容不存在！');
            die;
        }
        D('Post')->updateCount($post_id, 'views');
        $Postreply = D('Postreply');
        import('ORG.Util.Pageabc'); // 导入分页类
        $map = array('post_id' => $post_id,'closed' => 0, 'audit' => 1);
        $count = $Postreply->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Postreply->where($map)->order(array('reply_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = array();
        $user_ids[$detail['user_id']] = $detail['user_id'];
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $list[$k] = $val;
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
		
		
		$cates = $this->sharecates;
		$this->seodatas['title'] = $detail['title'];
		$this->seodatas['cate'] = $cates[$detail['cate_id']]['cate_name'];
		$this->seodatas['desc'] = niuMsubstr($detail['details'],0,200,false);;
		
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('detail', $detail);
        $this->assign('count',$count);
        $this->display(); // 输出模板
    }

    public function zan() {
        $post_id = (int) $this->_get('post_id');
        $detail = D('Post')->find($post_id);
        if (empty($detail) || $detail['audit'] != 1) {
            $this->niuError('您查看的内容不存在！');
        }
        $data = array(
            'post_id' => $post_id,
            'user_id' => $this->uid,
            'create_ip' => get_client_ip(),
            'create_time' => NOW_TIME
        );
        if (D('Postzan')->checkIsZan($data['post_id'], $data['create_ip'])) {
            $this->niuError('亲已经赞过了哦！');
        }
        D('Postzan')->add($data);
        D('Post')->updateCount($post_id, 'zan_num');
        $this->niuSuccess('恭喜您，点赞成功！', U('tieba/detail', array('post_id' => $post_id)));
    }
	
    public function zans() {
        $reply_id = (int) $this->_get('reply_id');
        $detail = D('Postreply')->find($reply_id);
        if (empty($detail)) {
            $this->niuError('您查看的内容不存在！');
        }
        D('Postreply')->updateCount($reply_id, 'zan_num');
        $this->niuSuccess('恭喜您，点赞成功！', U('tieba/detail', array('post_id' => $detail['post_id'])));
    }
	

    public function reply($post_id) {
		
		
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }

        $post_id = (int) $post_id;
        $detail = D('Post')->find($post_id);
        if (empty($detail) || $detail['post_id'] != $post_id) {
            $this->niuError('没有该帖子');
        }
        if ($this->isPost()) {
            $data = $this->checkReply();
            $data['post_id'] = $post_id;
			$photos = $this->_post('photo');
			$photo = $val = '';
			if(!empty($photos)){
				foreach($photos as $val){
					if (isImage($val) && $val !=''){
						$photo = $photo.','.$val;
					}
				}
			}
			$data['pic'] = ltrim($photo,',');
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $data['audit'] = $this->_CONFIG['site']['replyaudit'];//回帖是否免审核
            $obj = D('Postreply');
            if ($obj->add($data)) {
                D('Post')->updateCount($post_id, 'reply_num');
                $this->niuSuccess('回复成功', U('tieba/detail', array('post_id' => $post_id)));
            }
            $this->niuError('操作失败！');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function checkReply() {
        $data = $this->checkFields($this->_post('data', false), array('contents'));
        $data['user_id'] = (int) $this->uid;
        $data['contents'] = SecurityEditorHtml($data['contents']);
        if (empty($data['contents'])) {
            $this->niuError('评论内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['contents'])) {
            $this->niuError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }

    public function post() {
        
        if ($this->isPost()) {
            if (empty($this->uid)) {
            $this->ajaxLogin();
        }
            $data = $this->shareCheck();
            $obj = D('Post');
			
			
			$tupian = $this->_post('data');
			$photos = explode(',',$tupian['photo']);
	
			$photo = $val = '';
			if(!empty($photos)){
				foreach($photos as $val){
					if (isImage($val) && $val !=''){
						$photo = $photo.','.$val;
					}
				}
			}
			$data['pic'] = ltrim($photo,',');
			
            $data['create_time'] = NOW_TIME;
			//$data['audit'] = 1;
			$data['audit'] = $this->_CONFIG['site']['postaudit'];
            $data['create_ip'] = get_client_ip();
            if ($obj->add($data)) {
                D('Users')->updateCount($this->uid, 'post_num');
                $this->niuSuccess('恭喜您，发帖成功！', U('tieba/index'));
            }
            $this->niuError('操作失败！');
        } else {
            $this->niuError('不支持该操作！');
        }
    }

    private function shareCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'cate_id', 'details'));
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->niuError('标题不能为空');
        }
        $data['user_id'] = (int) $this->uid;
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->niuError('分类不能为空');
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
	
	
	
	
	
	
    public function ajaxHot() {
        $Post = D('Post');
        import('ORG.Util.Pageabc'); // 导入分页类
        $map = array('audit' => 1);
        $parent_id = 0;
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Sharecate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
               $parent_id= $cat;
            } else {
                $parent_id = $this->sharecates[$cat]['parent_id'];
                $map['cate_id'] = $cat;
            }
            $this->seodatas['cate_name'] = $this->sharecates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
         $this->assign('parent_id',$parent_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }

        $linkArr['order'] = $order = $this->_param('order', 'htmlspecialchars');
		$orderby = '';
        switch ($order) {
            case 't':
                $orderby = array('views' => 'desc', 'reply_num' => 'desc', 'zan_num' => 'desc');
                break;
            case 'x':
                $orderby = array('zan_num' => 'desc');
                break;
            case 'r':
                $orderby = array('reply_num' => 'desc');
                break;
            default:
                $orderby = array('orderby' => 'asc', 'post_id' => 'desc');
                break;
        }
		
		
        $this->assign('order', $order);

        $count = $Post->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Post->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $ids = array();
        foreach ($list as $k => $val) {

            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
            }
        }

        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', $cates);
        $this->assign('counts',$count);
		$this->assign('linkArr', $linkArr);
       // $this->display(); // 输出模板
		
		echo json_encode($list);

		
    }
	
	
	public function Login(){
		
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
		
	}
	
	
	
	
	
	
	

}
