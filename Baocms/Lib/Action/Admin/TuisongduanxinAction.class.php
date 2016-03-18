<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuisongduanxinAction extends CommonAction {

    private $create_fields = array('tuisong_id',  'title', 'create_time');
    private $edit_fields = array('tuisong_id',  'title', 'create_time');

    public function index() {
        $Tuisongduanxin = D('Tuisongduanxin');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Tuisongduanxin->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuisongduanxin->where($map)->order(array('tuisong_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Tuisongduanxin');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('Tuisongduanxin/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['user_id'] = (int) $data['user_id'];
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        }
        $data['create_time'] = NOW_TIME;
        return $data;
    }

   

    public function delete($tuisong_id = 0) {
        if (is_numeric($tuisong_id) && ($tuisong_id = (int) $tuisong_id)) {
            $obj = D('Tuisongduanxin');
            $obj->delete($tuisong_id);
            $this->baoSuccess('删除成功！', U('Tuisongduanxin/index'));
        } else {
            $tuisong_id = $this->_post('tuisong_id', false);
            if (is_array($tuisong_id)) {
                $obj = D('Tuisongduanxin');
                foreach ($tuisong_id as $id) {
                    $obj->delete($id);
                }
                $this->baoSuccess('删除成功！', U('Tuisongduanxin/index'));
            }
            $this->baoError('请选择要删除的手机消息');
        }
    }

}
