<?php
class NewsAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
		$news = (int)$this->_CONFIG['operation']['news'];
		if ($news == 0) {
				$this->error('此功能已关闭');
				die;
		}
		
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
		
        $this->assign('counts', $counts);
    }



    public function index() {
        $Article = D('Article');
        import('ORG.Util.Pageam'); // 导入分页类
        $map = array('city_id' => $this->city_id,'closed' => 0);
		//搜索开始
		if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
		
		
        $cat = (int) $this->_param('cat');
        $cates = D('Articlecate')->fetchAll();
        if ($cates[$cat]) {
            $catids = D('Articlecate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);

		
		
        $order = (int) $this->_param('order');
        switch ($order) {
           
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                $orderby = array('article_id' => 'desc');
                break;
        }


        $count = $Article->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Article->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$shop_ids = array();
			foreach ($list as $k => $val) {
				if (!empty($val['shop_id'])) {
					$shop_ids[$val['shop_id']] = $val['shop_id'];
				}
				
			}
			
		$this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', $cates);
        $this->display(); // 输出模板
    }

    public function detail($article_id = 0) {

        if ($article_id = (int) $article_id) {
            $obj = D('Article');
            if (!$detail = $obj->find($article_id)) {
                $this->error('没有该文章');
            }
			if ($detail['closed'] != 0 ) {
            $this->error('该文章已删除');
             }

            $cates = D('Articlecate')->fetchAll();
            $obj->updateCount($article_id, 'views');
			$shop_id = $detail['shop_id'];
            $shop = D('Shop')->find($shop_id);
			$this->assign('shops', $shop);
			
			
			//回复列表
			$Articlecomment = D('Articlecomment');
			import('ORG.Util.Pageam'); // 导入分页类
			$map = array('city_id' => $this->city_id,'post_id'=>$article_id);
			$count = $Articlecomment->where($map)->count(); // 查询满足要求的总记录数 
			$Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
			$show = $Page->show(); // 分页显示输出
			$this ->assign('count',$count);

			$list = $Articlecomment->where($map)->order()->limit($Page->firstRow . ',' . $Page->listRows)->select();
			
			$user_ids = array();
			foreach ($list as $k => $val) {
				if (!empty($val['user_id'])) {
					$user_ids[$val['user_id']] = $val['user_id'];
				}			}
			
			$this->assign('users', D('Users')->itemsByIds($user_ids));
			$this->assign("list",$list);
			$this->assign('page', $show); // 赋值分页输出
			
            $this->assign('detail', $detail);
            $this->assign('parent_id', D('Articlecate')->getParentsId($detail['cate_id']));
            $this->assign('cates', $cates);
            $this->assign('cate',$cates[$detail['cate_id']]);
            $this->seodatas['title'] = $detail['title'];
            $this->seodatas['cate_name'] = $cates[$detail['cate_id']];
            $this->seodatas['keywords'] = $detail['keywords'];
			if(!empty($detail['desc'])){
				$this->seodatas['desc'] = $detail['desc'];
			}else{
				$this->seodatas['desc'] = bao_msubstr($detail['details'],0,200,false);
			}
            
            $this->assign('articlecomments', D('Articlecomment')->where(array('post_id'=>$article_id))->count());
            $this->display();
        } else {
            $this->error('没有该文章');
        }
    }

    public function system() {
        $content_id = (int) $this->_get('content_id');
        if (empty($content_id)) {
            $this->error('该内容不存在');
            die;
        }
        $contents = D('Systemcontent')->fetchAll();
        if (!$contents[$content_id]) {
            $this->error('该内容不存在');
            die;
        }
        $this->assign('detail', $contents[$content_id]);
        $this->assign('contents', $contents);
        $this->assign('content_id', $content_id);
        $this->seodatas['title'] = $contents[$content_id]['title'];
        $this->display();
    }
	
	
	public function post(){
		if (empty($this->uid)) {
           $this->baoSuccess('登录状态失效!', U('passport/login'));
        }
		$data = $this->checkFields($this->_post('data', false), array('post_id', 'parent_id', 'content'));
	
		
		
		if (empty($data['content'])) {
			$this->baoError('评论内容不能为空');
		}
		if ($words = D('Sensitive')->checkWords($content)) {
          $this->baoError('商家介绍含有敏感词：' . $words);
        }
		
		
		
		if (empty($data['post_id'])) {
			$this->baoError('文章编号不正确');
		}
		if (!$detail = D('Article')->find($data['post_id'])) {
			$this->error('没有该文章');
		}			
		$data['nickname'] = $this->member['nickname'];
		$data['user_id'] = $this-> uid;
		$data['zan'] =0;
		$data['audit'] = $this->_CONFIG['site']['article_reply_audit'];//评论是免审核
		$data['create_time'] = NOW_TIME;
		$data['create_ip'] = get_client_ip();
		
		
		if (D('Articlecomment')->where(array('post_id' => $this->uid))->find()) {
            $this->baoError('不可重复评价');
        }
		
		if (D('Articlecomment')->add($data)) {
			$this->baoSuccess('回复成功！', U('news/detail', array('article_id' => $data['post_id'])));
		}
	}
	
	
    public function zans() {
        $comment_id = (int) $this->_get('comment_id');
        $detail = D('Articlecomment')->find($comment_id);
        if (empty($detail)) {
            $this->error('您点赞的内容不存在！');
        }
        D('Articlecomment')->updateCount($comment_id, 'zan');
		//$this->ajaxReturn(array('status' => 'success', 'msg' => '恭喜您，点赞成功！', U('news/detail', array('article_id' => $detail['post_id']))));	
        $this->error('恭喜您，点赞成功！', U('news/detail', array('article_id' => $detail['post_id'])));
    }
	
	
	
    public function cate() {
		$cates = D('Articlecate')->fetchAll();
        $Article = D('Article');
        import('ORG.Util.Pageam'); // 导入分页类
        $map = array('closed' => 0);
        $cat = (int) $this->_param('cat');
        $cates = D('Articlecate')->fetchAll();
        if ($cates[$cat]) {
            $catids = D('Articlecate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);

        $order = (int) $this->_param('order');
        switch ($order) {
           
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                $orderby = array('article_id' => 'desc');
                break;
        }

		$this->assign('cate',$cates[$cat]);
        $count = $Article->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Article->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', $cates);
        $this->display(); // 输出模板
    }
	
	
	

	
    /**
	*递归获取评论列表
   
    protected function getCommlist($post_id,$parent_id = 0,$start,$end,&$result = array()){
		$map = array('audit'=>1);
		$map['post_id'] = $post_id;
		$map['parent_id'] = $parent_id;
		if($parent_id != 0){
			$arr = D('Articlecomment')->where($map)->order("zan desc")->select();
		}else{
			$arr = D('Articlecomment')->where($map)->order("zan desc")->limit($start.','.$end)->select();
		}
    	
    	if(empty($arr)){
    		return array();
    	}
    	foreach ($arr as $cm) {  
    		$thisArr=&$result[];
    		$cm["children"] = $this->getCommlist($cm["post_id"],$cm["comment_id"],$thisArr);    
    		$thisArr = $cm; 				    	    		
    	}
    	return $result;
    } */
	

}