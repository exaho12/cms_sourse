<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class NewsAction extends CommonAction {

    public function index() {
        $news = D('Communitynews');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' =>0,'community_id' => $this->community_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title|intro'] = array('LIKE', '%' . $keyword . '%');
        }
        $count = $news->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $news->order(array('news_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->checkCreate();

            $obj = D('Communitynews');
            if ($obj->add($data)) {
                $this->baoSuccess('物业通知发布成功', U('news/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    public function checkCreate() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'intro', 'details'));
        $data['community_id'] = $this->community_id;
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])){
            $this->baoError('标题不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['title'])) {
            $this->baoError('物业通知标题含有敏感词：' . $words);
        }
        $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('物业通知简介不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['intro'])) {
            $this->baoError('物业通知简介含有敏感词：' . $words);
        }
        $data['details'] = htmlspecialchars($data['details']);
        if (empty($data['details'])) {
            $this->baoError('物业通知内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('物业通知内容含有敏感词：' . $words);
        }
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['closed'] = 0;
        $data['audit'] = $this->_CONFIG['site']['xiaoqu_news_audit'];//回帖是否免审核
        return $data;
    }

    public function edit($news_id) {
        $news_id = (int) $news_id;
        $obj = D('Communitynews');
        if (!$detail = $obj->find($news_id)) {
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
            if (false !== $obj->save($data)) {
                $this->baoSuccess('操作成功', U('news/index'));
            }
            $this->baoError('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function editCheck($news_id) {
        $data = $this->checkFields($this->_post('data', false), array('title', 'intro', 'details'));
        $data['community_id'] = $this->community_id;
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        }$data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('简介不能为空');
        }if ($words = D('Sensitive')->checkWords($data['intro'])) {
            $this->baoError('简介含有敏感词：' . $words);
        }$data['details'] = htmlspecialchars($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详情不能为空');
        }if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('详情含有敏感词：' . $words);
        }
        return $data;
    }

    public function delete($news_id = 0) {
        if (is_numeric($news_id) && ($news_id = (int) $news_id)) {
            $obj = D('Communitynews');
            $obj->save(array('news_id' => $news_id, 'closed' => 1));
            //$this->baoSuccess('删除成功！', U('news/index'));
			
			$this->ajaxReturn(array('status' => 'success', 'msg' => '删除成功', U('news/index')));
        }
    }
    
}
