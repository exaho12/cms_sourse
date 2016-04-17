<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CloudgoodsModel extends CommonModel {

    protected $pk = 'goods_id';
    protected $tableName = 'cloud_goods';

    public function getType() {
        return array(
            '1' => array(
                'type_name' => '1元云购',
                'num' => 1,
            ),
            '2' => array(
                'type_name' => '5元云购',
                'num' => 5,
            ),
            '3' => array(
                'type_name' => '10元云购',
                'num' => 10,
            ),
        );
    }

    public function cloud($goods_id, $user_id, $num) {
        $obj = D('Cloudlogs');
        $detail = $this->find($goods_id);
        $lefts = $detail['price'] - $detail['join'];
        if ($num > $lefts) {
            return false;
        }
        $member = D('Users')->find($user_id);
        if ($num * 100 > $member['money']) {
            return false;
        }
        $count = $obj->where(array('goods_id' => $goods_id, 'user_id' => $user_id))->count();
        $left = $detail['max'] - $count;
        if ($num > $left) {
            return false;
        }
        $t = microtime(false);
        $tt = substr($t, 0, 5);
        $microtime = round($tt, 3) * 1000;
        if (!$microtime || $microtime == NULL || empty($microtime)) {
            $microtime = 000;
        }if (strlen($microtime) == 0) {
            $microtime = 000;
        } elseif (strlen($microtime) == 1) {
            $microtime = '00' . $microtime;
        } elseif (strlen($microtime) == 2) {
            $microtime = '0' . $microtime;
        }
        if (false !== D('Users')->addMoney($user_id, -$num * 100, '云购商品' . $detail['title'] . '购买，扣费')) {
            $obj->add(array('goods_id' => $goods_id, 'user_id' => $user_id, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip(), 'num' => $num, 'microtime' => $microtime));
            $new_num = $detail['join'] + $num;
            $this->where('goods_id=' . $goods_id)->setField('join', $new_num);
            return TRUE;
        } else {
            return false;
        }
    }

    public function get_datas($datas) {
        $return = array();
        $i = 0;
        foreach ($datas as $val) {
            $data = $val;
            for ($a = 0; $a < $val['num']; $a++) {
                $num = 10000001 + $i;
                $data['number'] = $num;
                $return[$num] = $data;
                $i++;
            }
        }
        krsort($return);
        return $return;
    }

    public function get_last50_time($data) {
        $return = array('total' => 0, 'datas' => array());
        $i = 0;
        //krsort($data);
        foreach ($data as $val) {
            for ($a = 0; $a < $val['num']; $a++) {
                $user_time = intval(date('His',$val['create_time']).$val['microtime']);
                if ($i < 50) {
                    $return['total']+= $user_time;
                    $return['datas'][] = $val;
                } else
                    break;
                $i++;
            }
        }
        krsort($return['datas']);
        return $return;
    }
    
    public function lottery($goods_id){
        $goods_id = (int) $goods_id;
        $detail = $this->find($goods_id);
        
         $res = D('Cloudlogs')->where(array('goods_id'=>$goods_id))->order(array('log_id' => 'asc'))->select();
         $list = $this->get_datas($res);
        
        $return = $this->get_last50_time($res);
        $zhongjiang = fmod($return['total'],$detail['price'])  + 10000001;
        if(false !== $this->save(array('goods_id'=>$goods_id,'win_user_id'=>$list[$zhongjiang]['user_id'],'win_number'=>$list[$zhongjiang]['number'],'status'=>1,'lottery_time'=>NOW_TIME))){
            if(!empty($detail['shop_id'])){
                $shops = D('Shop')->find($detail['shop_id']);
                D('Users')->addMoney($shops['user_id'],$detail['settlement_price'],'商品'.$detail['title'].'成功卖出，收款！');
            }
            return true;
        } 
    }
    

}
