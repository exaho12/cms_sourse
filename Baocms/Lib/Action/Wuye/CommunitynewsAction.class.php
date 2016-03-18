<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CommunitynewsAction extends CommonAction {

    public function index() {
        $this->assign('nextpage', LinkTo('communitynews/loaddata', array('t' => NOW_TIME, 'community_id' => $this->community_id, 'p' => '0000')));
        $this->display(); // 输出模板 
    }

    public function loaddata() {
        $communitynews = D('Communitynews');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'community_id' => $this->community_id);
        $count = $communitynews->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $communitynews->order(array('news_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function create() {
        if (empty($this->uid)) {
            $this->error('您还未登录', U('passport/login'));
        }
        if ($this->isPost()) {
            $data = $this->checkCreate();

            $obj = D('Communitynews');
            if ($obj->add($data)) {
                $this->success('物业通知发布成功', U('communitynews/index'));
            }
            $this->error('操作失败！');
        } else {
            $this->display();
        }
    }

    public function checkCreate() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'intro', 'details'));
        $data['community_id'] = (int) $this->community_id;
        $data['title'] = $data['title'];
        if (empty($data['title'])){
            $this->error('标题不能为空');
        }
        $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->error('物业通知简介不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['intro'])) {
            $this->error('物业通知简介含有敏感词：' . $words);
        }
        $data['details'] = htmlspecialchars($data['details']);
        if (empty($data['details'])) {
            $this->error('物业通知内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->error('物业通知内容含有敏感词：' . $words);
        }
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['closed'] = 0;
        $data['audit'] = $this->_CONFIG['site']['xiaoqu_news_audit'];//回帖是否免审核
        return $data;
    }

    public function edit($news_id) {
        $news_id = (int) $news_id;
        $communitynews = D('Communitynews');
        if (!$detail = $communitynews->find($news_id)) {
            $this->error('该通知不存在');
        }
        if ($detail['closed'] != 0) {
            $this->error('该通知已被删除');
        }
        if ($detail['community_id'] != $this->community_id) {
            $this->error('请不要操作别人的物业管理');
        }
        if ($this->isPost()) {
            $data = $this->editCheck($news_id);
            $data['news_id'] = $news_id;
            if (false != $communitynews->save($data)) {
                $this->success('操作成功', U('communitynews/index'));
            }
            $this->error('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function editCheck($news_id) {
        $data = $this->checkFields($this->_post('data', false), array('title', 'intro', 'details'));
        $data['community_id'] = (int) $this->community_id;
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->error('标题不能为空');
        }$data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->error('简介不能为空');
        }if ($words = D('Sensitive')->checkWords($data['intro'])) {
            $this->error('简介含有敏感词：' . $words);
        }$data['details'] = htmlspecialchars($data['details']);
        if (empty($data['details'])) {
            $this->error('详情不能为空');
        }if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->error('详情含有敏感词：' . $words);
        }
        return $data;
    }

    public function delete($news_id = 0) {
        if (is_numeric($news_id) && ($news_id = (int) $news_id)) {
            $obj = D('Communitynews');
			
			if (!$detail = $obj->find($news_id)) {
            $this->error('该通知不存在！');
			}
			if ($detail['community_id'] != $this->community_id) {
				$this->error('请不要删除其他小区的文章');
			}
			
			
            $obj->save(array('news_id' => $news_id, 'closed' => 1));
            $this->success('删除成功！', U('communitynews/index'));
        }
    }

    public function audit($news_id = 0) {
        if (is_numeric($news_id) && ($news_id = (int) $news_id)) {
            $obj = D('Communitynews');
            $obj->save(array('news_id' => $news_id, 'audit' => 1));
            $this->success('发布成功！', U('communitynews/index'));
        }
    }

    public function detail() {
        $news_id = (int)$this->_param('news_id');
        $news = D('Communitynews');
        if(!$detail = $news->find($news_id)){
            $this->error('该通知不存在');
        }
        if($detail['closed'] != 0){
            $this->error('该通知已被删除');
        }
        $this->assign('detail',$detail);
        $this->display();
    }

}
