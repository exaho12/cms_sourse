
<?php 
class CloudAction extends CommonAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->types = d('Cloudgoods')->getType();
        $this->assign('types', $this->types);
    }
    public function index()
    {
        $linkArr = array();
        $type = (int) $this->_param('type');
        if (!empty($type)) {
            $this->assign('type', $type);
            $linkArr['type'] = $type;
        }
		$order = $this->_param('order','htmlspecialchars');
        $this->assign('order', $order);
        $linkArr['order'] = $order;
		
        $this->assign('nextpage', linkto('cloud/loaddata', $linkArr, array('t' => NOW_TIME, 'p' => '0000')));
        $this->assign('linkArr', $linkArr);
        $this->display();
    }
    public function loaddata()
    {
        $goods = d('Cloudgoods');
        import('ORG.Util.Page');
	    $map = array('audit' => 1, 'closed' => 0);
       // $map = array('audit' => 1, 'closed' => 0, 'city_id' => $this->city_id);
        $type = (int) $this->_param('type');
        if (!empty($type)) {
            $map['type'] = $type;
            $this->assign('type', $type);
            $linkArr['type'] = $type;
        }
		
		//排序重写
		
		$order = $this->_param('order','htmlspecialchars');
		
		switch ($order) {
            case 'p':
                $orderby = array('create_time' => 'desc');
                break;
            case 'v':
                $orderby = array('price' => 'asc', 'goods_id' => 'desc');
                break;
            case 's':
                $orderby = array('join' => 'desc');
                break;
        }
		
        $count = $goods->where($map)->count();
        $Page = new Page($count, 5);
        $show = $Page->show();
        $var = c('VAR_PAGE') ? c('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $goods->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function cloudbuy()
    {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login'));
        }
        $goods_id = (int) $_POST['goods_id'];
        $detail = d('Cloudgoods')->find($goods_id);
        if (empty($detail)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该云购商品不存在'));
        }
        $obj = d('Cloudgoods');
        $logs = d('Cloudlogs');
        if (IS_AJAX) {
            $num = (int) $_POST['num'];
            if (empty($num)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '数量不能为空'));
            }
            if ($num < $this->types[$detail['type']]['num'] || $num % $this->types[$detail['type']]['num'] != 0) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '数量不正确'));
            }
            $count = $logs->where(array('goods_id' => $goods_id, 'user_id' => $this->uid))->sum('num');
            $left = $detail['max'] - $count;
            $lefts = $detail['price'] - $detail['join'];
            $left <= $lefts ? $limit = $left : ($limit = $lefts);
            if ($limit < $num) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '您最多能购买' . $limit . '人次'));
            }
            if ($this->member['money'] < $num * 100) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '抱歉，您的余额不足', 'url' => u('mcenter/money/index')));
            }
            if (FALSE !== $obj->cloud($goods_id, $this->uid, $num)) {
                $details = d('Cloudgoods')->find($goods_id);
                if ($details['price'] <= $details['join']) {
                    $obj->lottery($goods_id);
                }
                $this->ajaxReturn(array('status' => 'success', 'msg' => '云购成功'));
            } else {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '云购失败'));
            }
        }
    }
    public function detail($goods_id)
    {
        if ($goods_id = (int) $goods_id) {
            $obj = d('Cloudgoods');
            if (!($detail = $obj->find($goods_id))) {
                $this->error('没有该商品');
            }
            if ($detail['closed'] != 0 || $detail['audit'] != 1) {
                $this->error('没有该商品');
            }
            $thumb = unserialize($detail['thumb']);
            $this->assign('thumb', $thumb);
            $count = d('Cloudlogs')->where(array('goods_id' => $goods_id, 'user_id' => $this->uid))->sum('num');
            $left = $detail['max'] - $count;
            $cloudlogs = d('Cloudlogs');
            $map = array('goods_id' => $goods_id);
            $list = $cloudlogs->where($map)->order(array('log_id' => 'desc'))->select();
            $user_ids = array();
            foreach ($list as $k => $val) {
                $user_ids[$val['user_id']] = $val['user_id'];
            }
            $this->assign('users', d('Users')->itemsByIds($user_ids));
            $this->assign('list', $list);
            $total = $cloudlogs->where(array('goods_id' => $goods_id, 'user_id' => $detail['win_user_id']))->sum('num');
            $data_all = $obj->get_datas($list);
            $return = $obj->get_last50_time($list);
            $zhongjiang = fmod($return['total'], $detail['price']) + 10000001;
            $zhong = $data_all[$zhongjiang];
            $this->assign('zhong', $zhong);
            $this->assign('total', $total);
            $this->assign('left', $left);
            $this->assign('detail', $detail);
            $this->display();
        } else {
            $this->error('没有该商品');
        }
    }
}