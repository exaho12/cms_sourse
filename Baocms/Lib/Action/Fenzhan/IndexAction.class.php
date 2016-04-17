<?php
class IndexAction extends CommonAction
{
    public function index()
    {
        $menu = D('Menu')->fetchAll();
        if ($this->_admin['role_id'] != 1) {
            if ($this->_admin['menu_list']) {
                foreach ($menu as $k => $val) {
                    if (!empty($val['menu_action']) && !in_array($k, $this->_admin['menu_list'])) {
                        unset($menu[$k]);
                    }
                }
                foreach ($menu as $k1 => $v1) {
                    if ($v1['parent_id'] == 0) {
                        foreach ($menu as $k2 => $v2) {
                            if ($v2['parent_id'] == $v1['menu_id']) {
                                $unset = true;
                                foreach ($menu as $k3 => $v3) {
                                    if ($v3['parent_id'] == $v2['menu_id']) {
                                        $unset = false;
                                    }
                                }
                                if ($unset) {
                                    unset($menu[$k2]);
                                }
                            }
                        }
                    }
                }
                foreach ($menu as $k1 => $v1) {
                    if ($v1['parent_id'] == 0) {
                        $unset = true;
                        foreach ($menu as $k2 => $v2) {
                            if ($v2['parent_id'] == $v1['menu_id']) {
                                $unset = false;
                            }
                        }
                        if ($unset) {
                            unset($menu[$k1]);
                        }
                    }
                }
            } else {
                $menu = array();
            }
        }
        $this->assign('menuList', $menu);
        $this->display();
    }
    public function main(){
        $counts['users'] = (int) D('Users')->where(array('closed' => 0))->count();
        $counts['shops'] = (int) D('Shop')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['article'] = (int) D('Article')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['community'] = (int) D('Community')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['coupon'] = (int) D('Coupon')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['ele'] = (int) D('Ele')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['express'] = (int) D('Express')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['goods'] = (int) D('Goods')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['life'] = (int) D('Life')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['tuan'] = (int) D('Tuan')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
        $counts['village'] = (int) D('Village')->where(array('closed' => 0, 'city_id' => $this->city_id))->count();
		
		
		$counts['money'] = (int) D('Shopmoney')->where(array('city_id' => $this->city_id))->sum('money');
		$counts['money_goods'] = (int) D('Shopmoney')->where(array('type'=>goods,'city_id' => $this->city_id))->sum('money');
		$counts['money_tuan'] = (int) D('Shopmoney')->where(array('type'=>tuan,'city_id' => $this->city_id))->sum('money');
		$counts['money_ele'] = (int) D('Shopmoney')->where(array('type'=>ele,'city_id' => $this->city_id))->sum('money');
		$counts['money_ding'] = (int) D('Shopmoney')->where(array('type'=>ding,'city_id' => $this->city_id))->sum('money');
		
		$counts['money_day'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'city_id' => $this->city_id
			))->sum('money');
			
		$counts['money_day_goods'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'city_id' => $this->city_id,
				'type'=>goods,
			))->sum('money');
			
		$counts['money_day_tuan'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'city_id' => $this->city_id,
				'type'=>tuan,
			))->sum('money');
		$counts['money_day_ele'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'city_id' => $this->city_id,
				'type'=>ele,
			))->sum('money');
		$counts['money_day_ding'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'city_id' => $this->city_id,
				'type'=>ding,
			))->sum('money');
	
		
        $v = (require BASE_PATH . '/version.php');
        $this->assign('v', $v);
        $this->assign('counts', $counts);
        $msg = D('Msg');
        import('ORG.Util.Page');
        // 导入分页类
        $map = array('city_id' => $this->city_id, 'is_fenzhan' => 1);
        $list = $msg->where($map)->order(array('msg_id' => 'desc'))->select();
        $count = $msg->where($map)->count();
        $Page = new Page($count, 6);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();
        // 分页显示输出
        $this->assign('list', $list);
        // 赋值数据集
        $this->assign('page', $show);
        // 赋值分页输出
        $this->display();
    }
    public function check() {
        //后期获得通知使用！
        die('1');
    }
}