<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class OrderModel extends CommonModel {

    protected $pk = 'order_id';
    protected $tableName = 'order';
    protected $types = array(
        0 => '等待付款',
        1 => '等待发货',
        2 => '仓库已捡货',
        3 => '客户已收货',
        8 => '已完成配送',
    );

    public function getType() {
        return $this->types;
    }
    
    //可以使用积分 根据订单使用积分的情况 返回支付记录需要实际支付的金额！
    public function useIntegral($uid,$order_ids){
        $orders = $this->where(array('order_id'=>array('IN',$order_ids)))->select();
        $users = D('Users');
        $member = $users->find($uid); 
        $useint = $fan = $total = 0;
        foreach($orders as $k=>$order){
            if($order['use_integral']>$order['can_use_integral']){ //需要返回积分给客户
                $member['integral'] += $order['use_integral']-$order['can_use_integral'];
               
                $this->save($order); //保存ORDER
                $users->addIntegral($uid,$order['use_integral']-$order['can_use_integral'],'商城购物使用积分退还');//积分退还
                $orders[$k]['use_integral'] = $order['use_integral'] = $order['can_use_integral'];
            }else{ //否则就是 使用积分
                if($member['integral'] > $order['can_use_integral']){//账户余额大于可使用积分时
                    $member['integral'] -=$order['can_use_integral'];
                    $orders[$k]['use_integral'] = $order['use_integral'] = $order['can_use_integral'];
                    $this->save($order); //保存ORDER
                    $users->addIntegral($uid,-$order['can_use_integral'],'商城购物使用积分');
                }elseif($member['integral']>0){//账户余额小于积分时
                     $orders[$k]['use_integral'] = $order['use_integral'] = $member['integral'];
                     $this->save($order); //保存ORDER
                     $users->addIntegral($uid,-$member['integral'],'商城购物使用积分'); //小于等于0 就不执行了
                     $member['integral'] = 0;
                }
            }
            $useint+= $order['use_integral'];
            $fan += $order['mobile_fan'];
            $total+= $order['total_price'];
        }
        
        return  $total - $fan - $useint;
    }
    
    
    public function overOrder($order_id) { //后台管理员可以直接确认2的
        $order = $this->find($order_id);
        if (empty($order))
            return false;
        if ($order['status'] != 2 && $order['status'] != 3) //  添加一个客户确认步骤
            return false;
        if ($this->save(array('status' => 8, 'order_id' => $order_id))) {
            $userobj = D('Users');
            $goods = D('Ordergoods')->where(array('order_id' => $order_id))->select();
            $shop = D('Shop')->find($order['shop_id']);
            if (!empty($goods)) {
                D('Ordergoods')->save(array('status' => 8), array('where' => array('order_id' => $order_id)));
                if ($order['is_daofu'] == 0) {
                    $ip = get_client_ip();
                    $info = '购物结算';
                    foreach ($goods as $val) {
                        $money = $val['js_price'];
						//$money = $val['total_price'];//商城取消结算价格吧，哎，草泥马的！
                        //全民经纪人 后期更改 暂时去除了
                        if ($money > 0) {
                            $money =  D('Quanming')->quanming($order['user_id'],$money,'mall'); //扣去全民营销
                            D('Shopmoney')->add(array(
                                'shop_id' => $order['shop_id'],
                                'money' => $money,
                                'create_time' => NOW_TIME,
                                'create_ip' => $ip,
                                'type' => 'goods',
                                'order_id' => $order_id,
                                'intro' => $info,
                            ));
                            D('Users')->addMoney($shop['user_id'], $money, $info);
                        }
                    }
//                  购物积分奖励给买的人，这个开关在后台
                    D('Users')->gouwu($order['user_id'],$order['total_price'],'购物积分奖励');
                }
            }
            return true;
        }
        return false;
    }

    public function money($bg_time, $end_time, $shop_id) {
        $bg_time = (int) $bg_time;
        $end_time = (int) $end_time;
        $shop_id = (int) $shop_id;
        if (!empty($shop_id)) {
            $data = $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%m%d') as d from  " . $this->getTableName() . "   where status=8 AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}' AND shop_id = '{$shop_id}'  group by  FROM_UNIXTIME(create_time,'%m%d')");
        } else {
            $data = $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%m%d') as d from  " . $this->getTableName() . "   where status=8 AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}'  group by  FROM_UNIXTIME(create_time,'%m%d')");
        }
        $showdata = array();
        $days = array();

        for ($i = $bg_time; $i <= $end_time; $i+=86400) {
            $days[date('md', $i)] = '\'' . date('m月d日', $i) . '\'';
        }
        $price = array();
        foreach ($days as $k => $v) {
            $price[$k] = 0;
            foreach ($data as $val) {
                if ($val['d'] == $k) {
                    $price[$k] = $val['price'];
                }
            }
        }
        $showdata['d'] = join(',', $days);
        $showdata['price'] = join(',', $price);
        return $showdata;
    }

}
