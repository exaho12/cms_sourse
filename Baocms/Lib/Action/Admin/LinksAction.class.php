<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LinksAction extends CommonAction {

    private $create_fields = array('link_name', 'link_url', 'orderby');
    private $edit_fields = array('link_name', 'link_url', 'orderby');

    public function index() {
        $Links = D('Links');
        $list = $Links->fetchAll();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Links');
            if ($obj->add($data)) {
                $obj->cleanCache();
                $this->baoSuccess('添加成功', U('links/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['link_name'] = htmlspecialchars($data['link_name']);
        if (empty($data['link_name'])) {
            $this->baoError('链接名称不能为空');
        } $data['link_url'] = htmlspecialchars($data['link_url']);
        if (empty($data['link_url'])) {
            $this->baoError('链接地址不能为空');
        } $data['orderby'] = (int) $data['orderby'];
        return $data;
    }

    public function edit($link_id = 0) {
        if ($link_id = (int) $link_id) {
            $obj = D('Links');
            if (!$detail = $obj->find($link_id)) {
                $this->baoError('请选择要编辑的友情链接');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['link_id'] = $link_id;
                if (false !== $obj->save($data)) {
                    $obj->cleanCache();
                    $this->baoSuccess('操作成功', U('links/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的友情链接');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['link_name'] = htmlspecialchars($data['link_name']);
        if (empty($data['link_name'])) {
            $this->baoError('链接名称不能为空');
        } $data['link_url'] = htmlspecialchars($data['link_url']);
        if (empty($data['link_url'])) {
            $this->baoError('链接地址不能为空');
        } $data['orderby'] = (int) $data['orderby'];
        return $data;
    }

    public function delete($link_id = 0) {
        if (is_numeric($link_id) && ($link_id = (int) $link_id)) {
            $obj = D('Links');
            $obj->delete($link_id);
            $obj->cleanCache();
            $this->baoSuccess('删除成功！', U('links/index'));
        } else {
            $link_id = $this->_post('link_id', false);
            if (is_array($link_id)) {
                $obj = D('Links');
                foreach ($link_id as $id) {
                    $obj->delete($id);
                }
                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('links/index'));
            }
            $this->baoError('请选择要删除的友情链接');
        }
    }

}
