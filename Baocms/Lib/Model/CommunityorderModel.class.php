<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CommunityorderModel extends CommonModel {

    protected $pk = 'order_id';
    protected $tableName = 'community_order';

    public function getType() {
        return array(
            '1' => '水费',
            '2' => '电费',
            '3' => '燃气费',
            '4' => '停车费',
            '5' => '物业费',
        );
    }

    public function orderpay($order_id, $user_id, $type, $total) {
        $order_id = (int) $order_id;
        $detail = $this->find($order_id);
        $user_id = (int) $user_id;
        $products = D('Communityorderproducts')->where(array('order_id' => $order_id))->select();
        $needs = array();
        foreach ($products as $k => $val) {
            foreach ($type as $kk => $v) {
                if ($kk == $val['type']) {
                    $needs[$k] = $val;
                }
            }
        }
        $member = D('Users')->find($user_id);
        D('Users')->addMoney($user_id, -$total, '生活缴费，扣费');
        foreach ($needs as $k => $val) {
            D('Communityorderproducts')->save(array('id' => $val['id'], 'is_pay' => 1));
            D('Communityorderlogs')->add(array(
                'user_id' => $user_id,
                'community_id' => $detail['community_id'],
                'money' => $val['money'],
                'type' => $val['type'],
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip()
            ));
        }
        return true;
    }

}
