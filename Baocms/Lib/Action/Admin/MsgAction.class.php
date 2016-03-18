<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MsgAction extends CommonAction {

    private $create_fields = array('user_id', 'type', 'title', 'intro', 'link_url', 'create_time', 'create_ip', 'details');
    private $edit_fields = array('user_id', 'type', 'title', 'intro', 'link_url', 'details');

    public function index() {
        $Msg = D('Msg');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Msg->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Msg->where($map)->order(array('msg_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $this->assign('types', $Msg->getType());
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Msg');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('msg/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('types',  D('Msg')->getType());
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['user_id'] = (int) $data['user_id'];
       
        $data['type'] = htmlspecialchars($data['type']);
        if (empty($data['type'])) {
            $this->baoError('类型不能为空');
        }
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        }
        $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('描述不能为空');
        }
        $data['link_url'] = htmlspecialchars($data['link_url']);
        $data['create_time'] = NOW_TIME;

        $data['create_ip'] = get_client_ip();

        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }

    public function edit($msg_id = 0) {
        if ($msg_id = (int) $msg_id) {
            $obj = D('Msg');
            if (!$detail = $obj->find($msg_id)) {
                $this->baoError('请选择要编辑的手机消息');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['msg_id'] = $msg_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('msg/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->assign('types',  D('Msg')->getType());
                $this->assign('user',D('Users')->find($detail['user_id']));
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的手机消息');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['user_id'] = (int) $data['user_id'];
        
        $data['type'] = htmlspecialchars($data['type']);
        if (empty($data['type'])) {
            $this->baoError('类型不能为空');
        }
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        }
        $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('描述不能为空');
        }
        $data['link_url'] = htmlspecialchars($data['link_url']);
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }

    public function delete($msg_id = 0) {
        if (is_numeric($msg_id) && ($msg_id = (int) $msg_id)) {
            $obj = D('Msg');
            $obj->delete($msg_id);
            $this->baoSuccess('删除成功！', U('msg/index'));
        } else {
            $msg_id = $this->_post('msg_id', false);
            if (is_array($msg_id)) {
                $obj = D('Msg');
                foreach ($msg_id as $id) {
                    $obj->delete($id);
                }
                $this->baoSuccess('删除成功！', U('msg/index'));
            }
            $this->baoError('请选择要删除的手机消息');
        }
    }

}
