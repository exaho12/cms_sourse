<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PostAction extends CommonAction {

    private $create_fields = array('title', 'user_id', 'cate_id', 'details','orderby','is_fine', 'create_time', 'create_ip');
    private $edit_fields = array('title', 'user_id', 'cate_id', 'details','orderby','is_fine');

    public function index() {
		$Sharecate = D('Sharecate');
        $list2 = $Sharecate->fetchAll();
        $Post = D('Post');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if($cate_id = (int)$this->_param('cate_id')){
            $map['cate_id'] = array('IN',D('Shopcate')->getChildren($cate_id));
            $this->assign('cate_id',$cate_id);
        }
     
        if($audit = (int)$this->_param('audit')){
            $map['audit'] = ($audit === 1 ? 1:0);
            $this->assign('audit',$audit);
        }
        $count = $Post->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Post->where($map)->order(array('post_id'=>'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
           $ids = array();
        foreach($list as $k=>$val){
        
            if($val['user_id']){
                $ids[$val['user_id']] = $val['user_id'];
            }
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;
        }
        $this->assign('users',D('Users')->itemsByIds($ids));
        $this->assign('list', $list); // 赋值数据集
		$this->assign('sharecate', $list2);
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', D('Shopcate')->fetchAll());
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Post');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('post/index'));
            }
            $this->baoError('操作失败！');
        } else {
			
            $this->assign('sharecate', D('Sharecate')->fetchAll());
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        } $data['user_id'] = (int) $data['user_id'];
        if (empty($data['user_id'])) {
            $this->baoError('用户不能为空');
        } $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('分类不能为空');
        } $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详细内容不能为空');
        } $data['create_time'] = NOW_TIME;

        $data['create_ip'] = get_client_ip();
        $data['orderby'] = (int)$data['orderby'];
        $data['is_fine'] = (int)$data['is_fine'];
        return $data;
    }

    public function edit($post_id = 0) {
        if ($post_id = (int) $post_id) {
            $obj = D('Post');
            if (!$detail = $obj->find($post_id)) {
                $this->baoError('请选择要编辑的消费分享');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['post_id'] = $post_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('post/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->assign('sharecate', D('Sharecate')->fetchAll());
                $this->assign('user',D('Users')->find($detail['user_id']));
             
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的消费分享');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        } $data['user_id'] = (int) $data['user_id'];
        if (empty($data['user_id'])) {
            $this->baoError('用户不能为空');
        } $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('分类不能为空');
        } $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详细内容不能为空');
        }
        $data['orderby'] = (int)$data['orderby'];
        $data['is_fine'] = (int)$data['is_fine'];
        return $data;
    }

    public function delete($post_id = 0) {
        if (is_numeric($post_id) && ($post_id = (int) $post_id)) {
            $obj = D('Post');
            $obj->delete($post_id);
            $this->baoSuccess('删除成功！', U('post/index'));
        } else {
            $post_id = $this->_post('post_id', false);
            if (is_array($post_id)) {
                $obj = D('Post');
                foreach ($post_id as $id) {
                    $obj->delete($id);
                }
                $this->baoSuccess('删除成功！', U('post/index'));
            }
            $this->baoError('请选择要删除的消费分享');
        }
    }

    public function audit($post_id = 0) {
        if (is_numeric($post_id) && ($post_id = (int) $post_id)) {
            $obj = D('Post');
            $detail = $obj->find($post_id);
            $obj->save(array('post_id' => $post_id, 'audit' => 1));
            D('Users')->integral($detail['user_id'],'share');
           // print_r($detail);die;
            $this->baoSuccess('审核成功！', U('post/index'));
        } else {
            $post_id = $this->_post('post_id', false);
            if (is_array($post_id)) {
                $obj = D('Post');
                foreach ($post_id as $id) {
                    $detail = $obj->find($id);
                    $obj->save(array('post_id' => $id, 'audit' => 1));
                    D('Users')->integral($detail['user_id'],'share');
                }
                $this->baoSuccess('审核成功！', U('post/index'));
            }
            $this->baoError('请选择要审核的消费分享');
        }
    }

}
