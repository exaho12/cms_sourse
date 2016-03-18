<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class FeedbackAction extends CommonAction {
    public function index() {
        $this->assign('nextpage', LinkTo('feedback/loaddata', array('t' => NOW_TIME, 'community_id' => $this->community_id, 'p' => '0000')));
        $this->display(); // 输出模板 
    }

    public function loaddata() {
        $feedback = D('Feedback');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'community_id' => $this->community_id);
        $count = $feedback->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE')?C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $feedback->order(array('feed_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }
    
    public function reply($feed_id) {
        $feed_id = (int)$feed_id;
        $feedback = D('Feedback');
        if(!$detail = $feedback->find($feed_id)){
            $this->error('该问题不存在');
        }
         if($detail['closed'] != 0){
            $this->error('该问题已被删除');
        }
        if($detail['community_id'] != $this->community_id){
            $this->error('请不要回复其他物业的反馈问题');
        }
        if ($this->isPost()) {
            $data = $this->replyCheck($feed_id);
            $data['feed_id'] = $feed_id;
            if (false !== $feedback->save($data)) {
                $this->success('回复成功', U('feedback/index'));
            }
            $this->error('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }
    
    public function replyCheck($feed_id) {
        $data = $this->checkFields($this->_post('data', false), array('reply'));
        $data['community_id'] = (int) $this->community_id;
        $data['reply'] = htmlspecialchars($data['reply']);
        if (empty($data['reply'])) {
            $this->error('回复内容不能为空');
        }if ($words = D('Sensitive')->checkWords($data['reply'])) {
            $this->error('回复内容含有敏感词：' . $words);
        }
        $data['reply_time'] = NOW_TIME;
        $data['reply_ip'] = get_client_ip();
        return $data;
    }
    
    public function detail($feed_id) {
        $feed_id = (int)$feed_id;
        if(!$detail = D('Feedback')->find($feed_id)){
            $this->error('该问题不存在');
        }
        if($detail['closed'] != 0){
            $this->error('该问题已被删除');
        }
        if($detail['community_id'] != $this->community_id){
            $this->error('请不要操作他人的问题反馈');
        }
        $this->assign('detail',$detail);
        $this->display();
    }
}