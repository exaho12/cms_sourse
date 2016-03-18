<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanAction extends CommonAction {

    public function index() {
        $tuan = D('Tuan');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1, 'end_date' => array('EGT', TODAY), 'city_id' => $this->city_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $count = $tuan->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $tuan->order(array('tuan_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $ctuan = D('Communitytuan')->where(array('community_id' => $this->community_id))->select();
        foreach ($list as $k => $val) {
            foreach ($ctuan as $kk => $v) {
                if ($v['tuan_id'] == $val['tuan_id']) {
                    $list[$k]['join'] = 1;
                }
            }
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function add() {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login', 'msg' => '您还未登录', 'url' => U('login/index')));
        }
        if (IS_AJAX) {
            $tuan_id = (int) $_POST['tuan_id'];
            if (empty($tuan_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该抢购不存在'));
            }
            if (!$detail = D('Tuan')->find($tuan_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该抢购不存在'));
            }
            if ($detail['audit'] != 1 || $detail['closed'] != 0 || $detail['end_date'] < TODAY) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该抢购不存在'));
            }
            $orderby = (int) $_POST['orderby'];
            $obj = D('Communitytuan');
            if (!$res = $obj->where(array('tuan_id' => $tuan_id, 'community_id' => $this->community_id))->find()) {
                if ($obj->add(array('tuan_id' => $tuan_id, 'community_id' => $this->community_id, 'orderby' => $orderby))) {
                    $this->ajaxReturn(array('status' => 'success', 'msg' => '添加抢购成功'));
                }
            }
            $this->ajaxReturn(array('status' => 'error', 'msg' => '操作失败'));
        }
    }

    public function remove() {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login', 'msg' => '您还未登录', 'url' => U('login/index')));
        }
        if (IS_AJAX) {
            $tuan_id = (int) $_POST['tuan_id'];
            if (empty($tuan_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该抢购不存在'));
            }
            if (!$detail = D('Tuan')->find($tuan_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该抢购不存在'));
            }
            if ($detail['audit'] != 1 || $detail['closed'] != 0 || $detail['end_date'] < TODAY) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该抢购不存在'));
            }
            $obj = D('Communitytuan');
            $data = array('community_id' => $this->community_id, 'tuan_id' => $tuan_id);
            if ($obj->delete($data)) {
                $this->ajaxReturn(array('status' => 'success', 'msg' => '删除成功'));
            }
            $this->ajaxReturn(array('status' => 'error', 'msg' => '操作失败'));
        }
    }

}
