<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class OwnerAction extends CommonAction {

    public function index() {
        $owner = D('Communityowner');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('community_id' => $this->community_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['number|location'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword',$keyword);
        }
        $count = $owner->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $owner->order(array('owner_id' => 'desc'))->where($map)->select();
        $user_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('list', $list);
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
    }

    public function audit($owner_id) {

        $owner_id = (int) $owner_id;
        if (empty($owner_id)) {
            $this->error('该业主不存在');
        }
        if (!$detail = D('Communityowner')->find($owner_id)) {
            $this->error('该业主不存在');
        }
        if ($detail['community_id'] != $this->community_id) {
            $this->error('不能操作其他小区业主');
        }

        if ($this->isPost()) {
            $data['number'] = (int) $_POST['number'];
            if (empty($data['number'])) {
                $this->baoError('户号不能为空');
            }
            $data['owner_id'] = $owner_id;
            $data['audit'] = 1;
            $obj = D('Communityowner');
            if (false !== $obj->save($data)) {
                $this->baoSuccess('审核成功', U('owner/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function delete() {
        $owner_id = (int) $this->_get('owner_id');
        $obj = D('Communityowner');
        $detail = $obj->find($owner_id);
        if (!empty($detail) && $detail['community_id'] == $this->community_id) {
            $obj->delete($owner_id);
            $this->success('删除成功！', U('owner/index'));
        }
        $this->error('操作失败');
    }


}
