<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class DingcateAction extends CommonAction {

    private $create_fields = array('cate_name', 'orderby');
    private $edit_fields = array('cate_name', 'orderby');

    public function _initialize() {
        parent::_initialize();
        if (empty($this->shop['is_ding'])) {
            $this->error('订座功能要和网站洽谈，由网站开通！');
        }
    }

    public function index() {
        $dingcate = D('Shopdingcate');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id'=>$this->shop_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['cate_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $dingcate->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $dingcate->where($map)->order(array('cate_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Shopdingcate');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('dingcate/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['shop_id'] = $this->shop_id;
        $data['cate_name'] = htmlspecialchars($data['cate_name']);
        if (empty($data['cate_name'])) {
            $this->baoError('分类名称不能为空');
        }
        $data['orderby'] = (int)$data['orderby'];
        return $data;
    }

    public function edit($cate_id = 0) {
        if ($cate_id = (int) $cate_id) {
            $obj = D('Shopdingcate');
            if (!$detail = $obj->find($cate_id)) {
                $this->error('请选择要编辑的菜品分类');
            }
            if ($detail['shop_id'] != $this->shop_id) {
                $this->error('请不要操作其他商家的菜品分类');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['cate_id'] = $cate_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('dingcate/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->error('请选择要编辑的菜品分类');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['cate_name'] = htmlspecialchars($data['cate_name']);
        if (empty($data['cate_name'])) {
            $this->baoError('分类名称不能为空');
        }
        $data['orderby'] = (int)$data['orderby'];
        return $data;
    }

    public function delete($cate_id = 0) {
        if (is_numeric($cate_id) && ($cate_id = (int) $cate_id)) {
            $obj = D('Shopdingcate');
            if (!$detail = $obj->where(array('shop_id' => $this->shop_id, 'cate_id' => $cate_id))->find()) {
                $this->baoError('请选择要删除的菜品分类');
            }
            $obj->delete($cate_id);
            $this->baoSuccess('删除成功！', U('dingcate/index'));
        }
        $this->baoError('请选择要删除的菜品分类');
    }

}
