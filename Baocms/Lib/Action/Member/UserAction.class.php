<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class UserAction extends CommonAction {

    
    

    public function myactivity() {
        $Activity = D('Activity');
        $Activitysign = D('Activitysign');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Activitysign->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Activitysign->where($map)->order(array('sign_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $activitys_ids = array();
        foreach ($list as $k => $val) {
            $activitys_ids[$val['activity_id']] = $val['activity_id'];
        }
        $this->assign('activity', $Activity->itemsByIds($activitys_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $this->display(); // 输出模板
    }

    

    

    public function usercard() {
        $Usercard = D('Usercard');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid); //这里只显示 实物
        $count = $Usercard->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Usercard->where($map)->order(array('card_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $cards_ids = array();
        $shop_ids = $user_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $Usercard->_format($val);
            $cards_ids[$val['card_id']] = $val['card_id'];

            if (!empty($val['shop_id'])) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
                $user_ids[$val['user_id']] = $val['user_id'];
            }
        }
        if ($shop_id = (int) $this->_param('shop_id')) {
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('shopdetails', D('Shopdetails')->itemsByIds($shop_ids));
        $this->display(); // 输出模板
    }

    
}
