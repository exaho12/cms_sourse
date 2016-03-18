<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class DingmenuAction extends CommonAction {

    private $create_fields = array('menu_name', 'cate_id', 'photo', 'price', 'ding_price', 'is_new', 'is_sale', 'is_tuijian');
    private $edit_fields = array('menu_name', 'cate_id', 'photo', 'price', 'ding_price', 'is_new', 'is_sale', 'is_tuijian');
    private $dingcates;
    public function _initialize() {
        parent::_initialize();
        if (empty($this->shop['is_ding'])) {
            $this->error('订座功能要和网站洽谈，由网站开通！');
        }  
        $cates = D('Shopdingcate')->where(array('shop_id' => $this->shop_id))->select();
        foreach($cates as $val){
            $this->dingcates [$val['cate_id']] = $val;
        }     
        $this->assign('dingcates', $this->dingcates);
    }

    public function index() {
        $dingmenu = D('Shopdingmenu');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['menu_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($shop_id = $this->shop_id) {
            $map['shop_id'] = $shop_id;
            $this->assign('shop_id', $shop_id);
        }
        $count = $dingmenu->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $dingmenu->where($map)->order(array('menu_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Shopdingmenu');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('dingmenu/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['menu_name'] = htmlspecialchars($data['menu_name']);
        if (empty($data['menu_name'])) {
            $this->baoError('菜品名称不能为空');
        }
        $data['shop_id'] = $this->shop_id;
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('菜品分类不能为空');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        }
        $data['price'] = (int) ($data['price'] * 100);
        if (empty($data['price'])) {
            $this->baoError('价格不能为空');
        }
        $data['ding_price'] = (int) ($data['ding_price'] * 100);
        if (empty($data['ding_price'])) {
            $this->baoError('优惠价格不能为空');
        }
        $data['is_new'] = (int) $data['is_new'];
        $data['is_sale'] = (int) $data['is_sale'];
        $data['is_tuijian'] = (int) $data['is_tuijian'];
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

    public function edit($menu_id = 0) {
        if ($menu_id = (int) $menu_id) {
            $obj = D('Shopdingmenu');
            if (!$detail = $obj->find($menu_id)) {
                $this->baoError('请选择要编辑的菜品设置');
            }
            if ($detail['shop_id'] != $this->shop_id) {
                $this->baoError('请不要操作其他商家的菜品设置');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['menu_id'] = $menu_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('dingmenu/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的菜品设置');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['product_name'] = htmlspecialchars($data['product_name']);
        if (empty($data['menu_name'])) {
            $this->baoError('菜品名称不能为空');
        }$data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('菜品分类不能为空');
        } $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        }
        $data['price'] = (int) ($data['price'] * 100);
        if (empty($data['price'])) {
            $this->baoError('价格不能为空');
        }
        $data['ding_price'] = (int) ($data['ding_price'] * 100);
        if (empty($data['ding_price'])) {
            $this->baoError('优惠价格不能为空');
        }
        $data['is_new'] = (int) $data['is_new'];
        $data['is_sale'] = (int) $data['is_sale'];
        $data['is_tuijian'] = (int) $data['is_tuijian'];
        return $data;
    }

    public function delete($menu_id = 0) {
        if (is_numeric($menu_id) && ($menu_id = (int) $menu_id)) {
            $obj = D('Shopdingmenu');
            if (!$detail = $obj->where(array('shop_id' => $this->shop_id, 'menu_id' => $menu_id))->find()) {
                $this->baoError('请选择要删除的菜品设置');
            }
            $obj->save(array('menu_id' => $menu_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('dingmenu/index'));
        }
        $this->baoError('请选择要删除的菜品设置');
    }

}
