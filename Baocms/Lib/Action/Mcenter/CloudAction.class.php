<?php 
class CloudAction extends CommonAction{
    public function index(){
        $cloudlogs = d('Cloudlogs');
        $cloudgoods = d('Cloudgoods');
        import('ORG.Util.Page');
        $goods_ids = $cloudlogs->where(array('user_id' => $this->uid))->getField('goods_id', TRUE);
        array_unique($goods_ids);
        $map = array('closed' => 0, 'audit' => 1);
        $map['goods_id'] = array('IN', $goods_ids);
        if (isset($_GET['st']) || isset($_POST['st'])) {
            $st = (int) $this->_param('st');
            if ($st != 999) {
                $map['status'] = $st;
            }
            $this->assign('st', $st);
        } else {
            $this->assign('st', 999);
        }
        $count = $cloudgoods->where($map)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $list = $cloudgoods->where($map)->order(array('goods_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $shop_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['win_user_id']] = $val['win_user_id'];
			$shop_ids[$val['shop_id']] = $val['shop_id'];
            $sum = $cloudlogs->where(array('goods_id' => $val['goods_id'], 'user_id' => $this->uid))->sum('num');
            $list[$k]['sum'] = $sum;
            if (!empty($val['win_user_id'])) {
                $sum2 = $cloudlogs->where(array('goods_id' => $val['goods_id'], 'user_id' => $val['win_user_id']))->sum('num');
            }
            $list[$k]['sum2'] = $sum2;
            $res = $cloudlogs->where(array('goods_id' => $val['goods_id']))->order(array('log_id' => 'asc'))->select();
            $rlist = $cloudgoods->get_datas($res);
            foreach ($rlist as $kk => $v) {
                if ($v['user_id'] == $this->uid) {
                    $list[$k]['mlist'][] = $rlist[$kk];
                }
            }
        }
		$this->assign('shops', D('Shop')->itemsByIds($shop_ids));//增加的
        $this->assign('users', d('Users')->itemsByIds($user_ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
	
	
	
	
	
}