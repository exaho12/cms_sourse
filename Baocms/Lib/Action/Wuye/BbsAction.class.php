<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class BbsAction extends CommonAction {
	
	
     public function index() {
        $this->assign('nextpage', LinkTo('bbs/loaddata', array('t' => NOW_TIME, 'community_id' => $this->community_id, 'p' => '0000')));
        $this->display(); // 输出模板 
    }
	
    public function loaddata() {
        $bbs = D('Communityposts');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('community_id' => $this->community_id,'closed'=>0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $count = $bbs->where($map)->count(); // 查询满足要求的总记录数 
        
		$Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
        $list = $bbs->order(array('post_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $last_ids =  array();
        foreach ($list as $k => $val) {
            $last_ids[$val['last_id']] = $val['last_id'];
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $this->assign('lasts', D('Users')->itemsByIds($last_ids));
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('list', $list);
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }
public function detail() {
        $post_id = (int)$this->_param('post_id');
       // $post_id = (int) $this->_get('post_id');
        $tie = D('Communityposts')->find($post_id);
        $puser = D('Users')->find($tie['user_id']);
        $tie['nickname'] = $puser['nickname'];
        if (empty($tie)) {
            $this->error('您查看的内容不存在！');
            die;
        }
        $community = D('Community');
        if (empty($tie['community_id'])) {
            $this->error('没有该小区');
            die;
        }
        if (!$detail = $community->find($tie['community_id'])) {
            $this->error('没有该小区');
            die;
        }
        if ($detail['closed']) {
            $this->error('该小区已经被删除');
            die;
        }
        D('Communityposts')->updateCount($post_id, 'view_num');
		$this->assign('puser', $puser);
		$this->assign('cate', $cate);
        $this->assign('detail', $detail);
		$this->assign('tie', $tie);
        $this->assign('count',$count);
        $this->seodatas['title'] = $detail['title'];
		$this->assign('nextpage', LinkTo('bbs/loadreply', array('post_id' => $tie['post_id'], 't' => NOW_TIME, 'p' => '0000')));
        $this->display(); // 输出模板
    }
	//贴吧回复加载
	public function loadreply(){
		$post_id = (int) $this->_param('post_id');
        $Postreply = D('Communityreplys');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('post_id' => $post_id);
        $count = $Postreply->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Postreply->where($map)->order(array('reply_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = array();
      //  $user_ids[$detail['user_id']] = $detail['user_id'];
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $list[$k] = $val;
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
		
		$this->display(); // 输出模板
	}

    public function delete($post_id = 0) {
        if (is_numeric($post_id) && ($post_id = (int) $post_id)) {
            $obj = D('Communityposts');
			
			if (!$detail = $obj->find($post_id)) {
            $this->error('该通知不存在！');
			}
			if ($detail['community_id'] != $this->community_id) {
				$this->error('请不要删除其他小区的帖子');
			}
			
			
            $obj->save(array('post_id' => $post_id, 'closed' => 1));
            $this->success('删除成功！', U('bbs/index'));
        }
    }

    public function audit($post_id = 0) {
        if (is_numeric($post_id) && ($post_id = (int) $post_id)) {
            $obj = D('Communityposts');
            $obj->save(array('post_id' => $post_id, 'audit' => 1));
            $this->success('发布成功！', U('bbs/index'));
        }
    }
       
    
}
