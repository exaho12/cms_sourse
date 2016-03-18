<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class WeidianAction extends CommonAction {

    public function index() {
        $weidian = D('WeidianDetails');
        import('ORG.Util.Page'); // 导入分页类
        $map = array( 'audit' => 1,  'city_id' => $this->city_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['weidian_name'] = array('LIKE', '%' . $keyword . '%');
        }
        $count = $weidian->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $weidian->order(array('id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $cweidian = D('Communityweidian')->where(array('community_id' => $this->community_id))->select();
        foreach ($list as $k => $val) {
            foreach ($cweidian as $kk => $v) {
                if ($v['weidian_id'] == $val['id']) {
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
            $weidian_id = (int) $_POST['weidian_id'];
            if (empty($weidian_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该微店不存在'));
            }
            if (!$detail = D('WeidianDetails')->find($weidian_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该微店不存在'));
            }
            if ($detail['audit'] != 1) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该微店不存在'));
            }
            $orderby = (int) $_POST['orderby'];
            $obj = D('Communityweidian');
            if (!$res = $obj->where(array('weidian_id' => $weidian_id, 'community_id' => $this->community_id))->find()) {
                if ($obj->add(array('weidian_id' => $weidian_id, 'community_id' => $this->community_id, 'orderby' => $orderby))) {
                    $this->ajaxReturn(array('status' => 'success', 'msg' => '添加微店成功'));
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
            $weidian_id = (int) $_POST['weidian_id'];
            if (empty($weidian_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该微店不存在'));
            }
            if (!$detail = D('WeidianDetails')->find($weidian_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该微店不存在'));
            }
            if ($detail['audit'] != 1) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该微店不存在'));
            }
            $obj = D('Communityweidian');
            $data = array('community_id' => $this->community_id, 'weidian_id' => $weidian_id);
            if ($obj->delete($data)) {
                $this->ajaxReturn(array('status' => 'success', 'msg' => '删除成功'));
            }
            $this->ajaxReturn(array('status' => 'error', 'msg' => '操作失败'));
        }
    }

}
