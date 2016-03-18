<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class IndexAction extends CommonAction{
    
     public function index(){
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
                                if ($unset)
                                    unset($menu[$k2]);
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
                        if ($unset)
                            unset($menu[$k1]);
                    }
                }
            }else {
                $menu = array();
            }
        }
         $this->assign('menuList',$menu);
         $this->display();
     }
     
     public function main(){
        $bg_time = strtotime(TODAY);
        $counts['totay_order'] = (int)D('Order')->where(array(
            'type' => 'goods',
            'create_time'=> array(
                array('ELT',NOW_TIME),
                array('EGT',$bg_time),
            ),'status' => array(
                'EGT',0
            ),
        ))->count();
		//今日外卖订单
		 $counts['totay_order_waimai'] = (int)D('Eleorder')->where(array(
            'create_time'=> array(
                array('ELT',NOW_TIME),
                array('EGT',$bg_time),
            ),'status' => array(
                'EGT',0
            ),
        ))->count();
		//今日团购订单
		 $counts['totay_order_tuan'] = (int)D('Tuanorder')->where(array(
            'create_time'=> array(
                array('ELT',NOW_TIME),
                array('EGT',$bg_time),
            ),'status' => array(
                'EGT',0
            ),
        ))->count();
		
		
        $counts['order'] = (int)D('Order')->where(array(
            'type' => 'goods',
           'status' => array(
                'EGT',0
            ),
        ))->count();
        
        $counts['totay_gold'] = (int)D('Order')->where(array(
            'type' => 'gold',
            'create_time'=> array(
                array('ELT',NOW_TIME),
                array('EGT',$bg_time),
            ),'status' => array(
                'EGT',0
            ),
        ))->count();
        $counts['gold'] = (int)D('Order')->where(array(
            'type' => 'gold',
           'status' => array(
                'EGT',0
            ),
        ))->count();
        
        $counts['today_yuyue'] = (int)D('Shopyuyue')->where(array(
            'create_time'=> array(
                array('ELT',NOW_TIME),
                array('EGT',$bg_time),
            )))->count();
         
         $counts['today_coupon'] = (int)D('Coupondownload')->where(array(
                 'create_time'=> array(
                array('ELT',NOW_TIME),
                array('EGT',$bg_time),
            )))->count();
		//查询今日会员
		$counts['totay_user'] = (int) D('Users')->where(array(
						//'user_id' => $this->user_id,
						'reg_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();		
					
		//查询今日商家
		$counts['totay_shop'] = (int) D('Shop')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();	
				
		//查询今日贴吧
		$counts['totay_post'] = (int) D('Post')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
				
		//查询今日商家
		$counts['totay_dianping'] = (int) D('Shopdianping')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
				
		//查询今日商城点评
		$counts['totay_dianping_goods'] = (int) D('Goodsdianping')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
		//查询今日团购点评
		$counts['totay_dianping_tuan'] = (int) D('Tuandianping')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
		//查询今日外卖点评
		$counts['totay_dianping_waimai'] = (int) D('Eledianping')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
				
				
				
		//查询今日分类信息
		$counts['totay_life'] = (int) D('Life')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
		//查询今日分类信息
		$counts['totay_bill'] = (int) D('Billorder')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
		//查询家政
		$counts['totay_jiazheng'] = (int) D('Housework')->where(array(
						//'user_id' => $this->user_id,
						'create_time' => array(
							array('ELT', NOW_TIME),
							array('EGT', $bg_time),
				)))->count();
		//查询商家待认领
		$counts['shoprecognition'] = (int) D('Shop')->where(array('recognition' => 0))->count();
				
		//查询今日分类信息待审核
		$counts['totay_jiazheng_queren'] = (int) D('Housework')->where(array('is_real' => 0))->count();
		$counts['totay_bill_audit'] = (int) D('Billorder')->where(array('status' => 0))->count();
		$counts['totay_life_audit'] = (int) D('Life')->where(array('audit' => 0))->count();
		$counts['totay_post_audit'] = (int) D('Post')->where(array('audit' => 0))->count();
		$counts['totay_shop_audit'] = (int) D('Shop')->where(array('audit' => 0))->count();
		
		 $counts['order_waimai'] = (int)D('Eleorder')->count();
		 $counts['order_tuan'] = (int)D('Tuanorder')->count();
		 
		 $counts['order_tui'] = (int)D('Ordergoods')->where(array('status' => 2))->count();
		 $counts['order_waimai_tui'] = (int)D('Eleorder')->where(array('status' => 3))->count();
		 $counts['order_tuan_tui'] = (int)D('Tuanorder')->where(array('audit' => 0))->count();
		 
		 
		 $counts['yuyue'] = (int)D('Shopyuyue')->count();
         $counts['coupon'] = (int)D('Coupondownload')->count();
		 $counts['bill'] = (int)D('Billorder')->count();
		 $counts['jiazheng'] = (int)D('Housework')->count();//家政
         $counts['dianping'] = (int)D('Shopdianping')->count();
		 $counts['dianping_waimai'] = (int)D('Eledianping')->count();//外卖点评
		 $counts['dianping_tuan'] = (int)D('Tuandianping')->count();//团购点评
		 $counts['dianping_goods'] = (int)D('Goodsdianping')->count();//商城点评
         $counts['users'] = (int)D('Users')->count();
         $counts['shops'] = (int)D('Shop')->count();
		 $counts['life'] = (int)D('Life')->count();
         $counts['post'] = (int)D('Post')->count();
		 //增加IP通知
		 $ad['last_ip'] = $this->ipToArea($admin['last_ip']);
		 $this->assign('ad', $ad);

         $v = require BASE_PATH.'/version.php';//
         $this->assign('v',$v);
         $this->assign('counts',$counts);
         $this->display();
     }
     
     public function check(){ //后期获得通知使用！
         die('1');
     }
     
}