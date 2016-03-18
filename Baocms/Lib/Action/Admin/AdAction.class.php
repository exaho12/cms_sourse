<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class AdAction extends CommonAction {

    private $create_fields = array('site_id', 'title','city_id', 'link_url', 'photo', 'code', 'bg_date', 'end_date', 'orderby');
    private $edit_fields = array('site_id', 'title','city_id', 'link_url', 'photo', 'code', 'bg_date', 'end_date', 'orderby');

     public function _initialize() {
        parent::_initialize();
        $this->citys = D('City')->fetchAll();
        $this->assign('citys', $this->citys);
    }
    
    public function index() {
        $Ad = D('Ad');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        if ($keyword) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword',$keyword);
        }
        if ($site_id = (int) $this->_param('site_id')) {
            $map['site_id'] = $site_id;
            $this->assign('site_id', $site_id);
        }

        $count = $Ad->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Ad->where($map)->order(array('ad_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('sites', D('Adsite')->fetchAll());
        $this->assign('types', D('Adsite')->getType());
        $this->display(); // 输出模板
    }

    public function create($site_id = 0) {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Ad');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('ad/index',array('site_id'=>$site_id)));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('site_id',$site_id);
            $this->assign('sites', D('Adsite')->fetchAll());
            $this->assign('types', D('Adsite')->getType());
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['site_id'] = (int) $data['site_id'];
        if (empty($data['site_id'])) {
            $this->baoError('所属广告位不能为空');
        }$data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('广告名称不能为空');
        } $data['link_url'] = htmlspecialchars($data['link_url']);
        $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->baoError('广告图片格式不正确');
        } $data['code'] = $data['code'];
        $data['bg_date'] = htmlspecialchars($data['bg_date']);
        if (empty($data['bg_date'])) {
            $this->baoError('开始时间不能为空');
        }
        if (!isDate($data['bg_date'])) {
            $this->baoError('开始时间格式不正确');
        } $data['end_date'] = htmlspecialchars($data['end_date']);
        if (empty($data['end_date'])) {
            $this->baoError('结束时间不能为空');
        }
        if (!isDate($data['end_date'])) {
            $this->baoError('结束时间格式不正确');
        } 
        $data['orderby'] = (int) $data['orderby'];
        $data['city_id'] = (int) $data['city_id'];
        return $data;
    }

    public function edit($ad_id = 0) {
        if ($ad_id = (int) $ad_id) {
            $obj = D('Ad');
            if (!$detail = $obj->find($ad_id)) {
                $this->baoError('请选择要编辑的广告');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['ad_id'] = $ad_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('ad/index',array('site_id'=>$data['site_id'])));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->assign('sites', D('Adsite')->fetchAll());
                $this->assign('types', D('Adsite')->getType());
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的广告');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['site_id'] = (int) $data['site_id'];
        if (empty($data['site_id'])) {
            $this->baoError('所属广告位不能为空');
        } $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('广告名称不能为空');
        } $data['link_url'] = htmlspecialchars($data['link_url']);
        $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->baoError('广告图片格式不正确');
        } $data['code'] = $data['code'];
        $data['bg_date'] = htmlspecialchars($data['bg_date']);
        if (empty($data['bg_date'])) {
            $this->baoError('开始时间不能为空');
        }
        if (!isDate($data['bg_date'])) {
            $this->baoError('开始时间格式不正确');
        } $data['end_date'] = htmlspecialchars($data['end_date']);
        if (empty($data['end_date'])) {
            $this->baoError('结束时间不能为空');
        }
        if (!isDate($data['end_date'])) {
            $this->baoError('结束时间格式不正确');
        }
        $data['orderby'] = (int) $data['orderby'];
        $data['city_id'] = (int) $data['city_id'];
        return $data;
    }

    public function delete($ad_id = 0) {
        if (is_numeric($ad_id) && ($ad_id = (int) $ad_id)) {
            $obj = D('Ad');
            $obj->save(array('ad_id' => $ad_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('ad/index'));
        } else {
            $ad_id = $this->_post('ad_id', false);
            if (is_array($ad_id)) {
                $obj = D('Ad');
                foreach ($ad_id as $id) {
                    $obj->save(array('ad_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('删除成功！', U('ad/index'));
            }
            $this->baoError('请选择要删除的广告');
        }
    }

}
