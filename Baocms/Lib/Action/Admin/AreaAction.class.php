<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class AreaAction extends CommonAction {

    private $create_fields = array('area_name','city_id', 'orderby');
    private $edit_fields = array('area_name','city_id', 'orderby');

    public function index() {
         $Area = D('Area');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        $keyword = $this->_param('keyword','htmlspecialchars');
        if($keyword){
            $map['area_name'] = array('LIKE', '%'.$keyword.'%');
        }    
        $this->assign('keyword',$keyword);
                
        $city_id = (int)$this->_param('city_id');
        if($city_id){
            $map['city_id'] = $city_id;
        }
        $this->assign('city_id',$city_id);
        $count = $Area->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Area->where($map)->order(array('area_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
         $this->assign('citys',D('City')->fetchAll());
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Area');
            if ($obj->add($data)) {
                $obj->cleanCache();
                $this->baoSuccess('添加成功', U('area/index'));
            }
            $this->baoError('操作失败！');
        } else {
             $this->assign('citys',D('City')->fetchAll());
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['area_name'] = htmlspecialchars($data['area_name']);
        if (empty($data['area_name'])) {
            $this->baoError('区域名称不能为空');
        } $data['orderby'] = (int) $data['orderby'];
        $data['city_id'] = (int) $data['city_id'];
        return $data;
    }

    public function edit($area_id = 0) {
        if ($area_id = (int) $area_id) {
            $obj = D('Area');
            if (!$detail = $obj->find($area_id)) {
                $this->baoError('请选择要编辑的区域管理');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['area_id'] = $area_id;
                if (false !== $obj->save($data)) {
                    $obj->cleanCache();
                    $this->baoSuccess('操作成功', U('area/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                 $this->assign('citys',D('City')->fetchAll());
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的区域管理');
        }
    }
    
 

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['area_name'] = htmlspecialchars($data['area_name']);
        if (empty($data['area_name'])) {
            $this->baoError('区域名称不能为空');
        } 
        $data['orderby'] = (int) $data['orderby'];
        $data['city_id'] = (int) $data['city_id'];
        return $data;
    }

    public function delete($area_id = 0) {
        if (is_numeric($area_id) &&( $area_id = (int) $area_id) ){
            $obj = D('Area');
            $obj->delete($area_id);
            $obj->cleanCache();
            $this->baoSuccess('删除成功！', U('area/index'));
        } else {
            $area_id = $this->_post('area_id', false);
            if (is_array($area_id)) {
                $obj = D('Area');
                foreach ($area_id as $id) {
                    $obj->delete($id);
                }
                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('area/index'));
            }
            $this->baoError('请选择要删除的区域管理');
        }
    }

}
