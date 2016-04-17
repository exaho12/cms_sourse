<?php
class TiebaAction extends CommonAction
{
    protected function _initialize()
    {
        parent::_initialize();
        $tieba = (int) $this->_CONFIG['operation']['tieba'];
        if ($tieba == 0) {
            $this->error('此功能已关闭');
            die;
        }
    }
    public function index()
    {
        $aready = (int) $this->_param('aready');
        $this->assign('aready', $aready);
        $this->display();
    }
    public function loaddata()
    {
        $Post = D('Post');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid);
        //查出当前用户名
        $aready = (int) $this->_param('aready');
        //排序重写
        if ($aready == 1) {
            $map['audit'] = 0;
        } elseif ($aready == 0) {
            $map['audit'] = array('IN', array(0, 1));
        } elseif ($aready == 2) {
            $map['audit'] = 1;
        }
        $count = $Post->where($map)->count();
        $Page = new Page($count, 5);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();
        // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Post->where($map)->order(array('post_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            $ids = array();
            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
                $ids[$val['last_id']] = $val['last_id'];
            }
            $list[$k] = $val;
        }
        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function delete()
    {
        $post_id = (int) $this->_param('post_id');
        $obj = D('Post');
        if (empty($post_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '帖子不存在'));
        }
        if (!($detail = D('Post')->find($post_id))) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '帖子不存在'));
        }
        if ($detail['user_id'] != $this->uid) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '不要操作别人的帖子'));
        }
        if (D('Post')->delete($post_id)) {
            //if(D('Post')->save(array('post_id' => $post_id, 'closed' => 1))){
            $this->ajaxReturn(array('status' => 'success', 'msg' => '恭喜您删除成功'));
        }
    }
}