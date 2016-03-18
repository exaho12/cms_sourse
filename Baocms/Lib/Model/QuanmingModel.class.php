<?php

//全民营销推广插件

class QuanmingModel extends CommonModel {
    
    protected $pk = 'tid';
    protected $tableName = 'quanming';
    
    
    //发放冻结金
    public function fzmoney($uid){
        $CONFIG = D('Setting')->fetchAll();
         $user = D('Users')->find($uid);
        if( empty($user['invite6'])
            &&empty($user['invite5'])
            &&empty($user['invite4'])
            &&empty($user['invite3'])
            &&empty($user['invite2'])
            &&empty($user['invite1'])){
            return false;
        }
        if(empty($CONFIG['quanming']['is_money'])){
            return false;
        }
        $year  = date('Y',NOW_TIME);
        $month = date('m',NOW_TIME);
        $day   = date('d',NOW_TIME);
        
        if(!empty($user['invite6'])&&!empty($CONFIG['quanming']['money6'])){ // 如果都不为空发放奖励
            $money = (int)($CONFIG['quanming']['money6']*100);
            D('Users')->addMoney($user['invite6'],$money,'全民推广(冻结金)佣金');
            $this->add(array(
                'uid' => $user['invite6'],
                'buy_uid'=>$uid,
                'rank' => 1,
                'price' => (int)($CONFIG['quanming']['money']*100),
                'commission' => $money,
                'create_time'=>NOW_TIME,
                'year' => $year,
                'month'=>$month,
                'day'  =>$day,
            ));
        }
        if(!empty($user['invite5'])&&!empty($CONFIG['quanming']['money5'])){ // 如果都不为空发放奖励
            $money = (int)($CONFIG['quanming']['money5']*100);
            D('Users')->addMoney($user['invite5'],$money,'全民推广(冻结金)佣金');
            $this->add(array(
                'uid' => $user['invite5'],
                'buy_uid'=>$uid,
                'rank' => 1,
                'price' => (int)($CONFIG['quanming']['money']*100),
                'commission' => $money,
                'create_time'=>NOW_TIME,
                'year' => $year,
                'month'=>$month,
                'day'  =>$day,
            ));
        }
        if(!empty($user['invite4'])&&!empty($CONFIG['quanming']['money4'])){ // 如果都不为空发放奖励
            $money = (int)($CONFIG['quanming']['money4']*100);
            D('Users')->addMoney($user['invite4'],$money,'全民推广(冻结金)佣金');
            $this->add(array(
                'uid' => $user['invite4'],
                'buy_uid'=>$uid,
                'rank' => 1,
                'price' => (int)($CONFIG['quanming']['money']*100),
                'commission' => $money,
                'create_time'=>NOW_TIME,
                'year' => $year,
                'month'=>$month,
                'day'  =>$day,
            ));
        }
        if(!empty($user['invite3'])&&!empty($CONFIG['quanming']['money3'])){ // 如果都不为空发放奖励
            $money = (int)($CONFIG['quanming']['money6']*100);
            D('Users')->addMoney($user['invite3'],$money,'全民推广(冻结金)佣金');
            $this->add(array(
                'uid' => $user['invite3'],
                'buy_uid'=>$uid,
                'rank' => 1,
                'price' => (int)($CONFIG['quanming']['money']*100),
                'commission' => $money,
                'create_time'=>NOW_TIME,
                'year' => $year,
                'month'=>$month,
                'day'  =>$day,
            ));
        }
        if(!empty($user['invite2'])&&!empty($CONFIG['quanming']['money2'])){ // 如果都不为空发放奖励
            $money = (int)($CONFIG['quanming']['money2']*100);
            D('Users')->addMoney($user['invite2'],$money,'全民推广(冻结金)佣金');
            $this->add(array(
                'uid' => $user['invite2'],
                'buy_uid'=>$uid,
                'rank' => 1,
                'price' => (int)($CONFIG['quanming']['money']*100),
                'commission' => $money,
                'create_time'=>NOW_TIME,
                'year' => $year,
                'month'=>$month,
                'day'  =>$day,
            ));
        }
        if(!empty($user['invite1'])&&!empty($CONFIG['quanming']['money1'])){ // 如果都不为空发放奖励
            $money = (int)($CONFIG['quanming']['money1']*100);
            D('Users')->addMoney($user['invite1'],$money,'全民推广(冻结金)佣金');
            $this->add(array(
                'uid' => $user['invite1'],
                'buy_uid'=>$uid,
                'rank' => 1,
                'price' => (int)($CONFIG['quanming']['money']*100),
                'commission' => $money,
                'create_time'=>NOW_TIME,
                'year' => $year,
                'month'=>$month,
                'day'  =>$day,
            ));
        }
        return true;
    }
    
    //全民推广的接口
    public function quanming($uid,$price,$type){
        //echo 111;
        $CONFIG = D('Setting')->fetchAll();
       
        switch ($type){
            case 'tuan':
                if(empty($CONFIG['quanming']['is_tuan'])) return $price;
                break;
            case 'mall':
                if(empty($CONFIG['quanming']['is_mall'])) return $price;
                break;
            case 'ele':
                if(empty($CONFIG['quanming']['is_ele'])) return $price;
                break;
            default:
                return $price;
                break;
        }
        $user = D('Users')->find($uid);
        if(       empty($user['invite6'])
                &&empty($user['invite5'])
                &&empty($user['invite4'])
                &&empty($user['invite3'])
                &&empty($user['invite2'])
                &&empty($user['invite1'])){
            return  $price;
        }
        $quanming = (int)($price * $CONFIG['quanming']['rate']/100);
        if(empty($quanming)) return $price;
        $year  = date('Y',NOW_TIME);
        $month = date('m',NOW_TIME);
        $day   = date('d',NOW_TIME);
        if(!empty($user['invite6'])){
            $money = (int)($CONFIG['quanming']['rate6'] * $quanming/100);
            if(!empty($money)){
                $price-=$money;
                D('Users')->addMoney($user['invite6'],$money,'全民推广佣金');
                $this->add(array(
                      'uid' => $user['invite6'],
                    'buy_uid'=>$uid,
                    'rank' => 1,
                    'price' => $price,
                    'commission' => $money,
                    'create_time'=>NOW_TIME,
                    'year' => $year,
                    'month'=>$month,
                    'day'  =>$day,
                ));
            } 
        }
        if(!empty($user['invite5'])){
            $money = (int)($CONFIG['quanming']['rate5'] * $quanming/100);
            if(!empty($money)){
                $price-=$money;
                D('Users')->addMoney($user['invite5'],$money,'全民推广佣金');
                $this->add(array(
                    'uid' => $user['invite5'],
                    'buy_uid'=>$uid,
                    'rank' => 2,
                    'price' => $price,
                    'commission' => $money,
                    'create_time'=>NOW_TIME,
                    'year' => $year,
                    'month'=>$month,
                    'day'  =>$day,
                ));
            } 
        }
        if(!empty($user['invite4'])){
            $money = (int)($CONFIG['quanming']['rate4'] * $quanming/100);
            if(!empty($money)){
                $price-=$money;
                D('Users')->addMoney($user['invite4'],$money,'全民推广佣金');
                $this->add(array(
                    'uid' => $user['invite4'],
                    'buy_uid'=>$uid,
                    'rank' => 3,
                    'price' => $price,
                    'commission' => $money,
                    'create_time'=>NOW_TIME,
                    'year' => $year,
                    'month'=>$month,
                    'day'  =>$day,
                ));
            } 
        }
        if(!empty($user['invite3'])){
            $money = (int)($CONFIG['quanming']['rate3'] * $quanming/100);
            if(!empty($money)){
                $price-=$money;
                D('Users')->addMoney($user['invite3'],$money,'全民推广佣金');
                $this->add(array(
                    'uid' => $user['invite3'],
                    'buy_uid'=>$uid,
                    'rank' => 4,
                    'price' => $price,
                    'commission' => $money,
                    'create_time'=>NOW_TIME,
                    'year' => $year,
                    'month'=>$month,
                    'day'  =>$day,
                ));
            } 
        }
        if(!empty($user['invite2'])){
            $money = (int)($CONFIG['quanming']['rate2'] * $quanming/100);
            if(!empty($money)){
                $price-=$money;
                D('Users')->addMoney($user['invite2'],$money,'全民推广佣金');
                $this->add(array(
                    'uid' => $user['invite2'],
                    'buy_uid'=>$uid,
                    'rank' => 5,
                    'price' => $price,
                    'commission' => $money,
                    'create_time'=>NOW_TIME,
                    'year' => $year,
                    'month'=>$month,
                    'day'  =>$day,
                ));
            } 
        }
        if(!empty($user['invite1'])){
            $money = (int)($CONFIG['quanming']['rate1'] * $quanming/100);
            if(!empty($money)){
                $price-=$money;
                D('Users')->addMoney($user['invite1'],$money,'全民推广佣金');
                $this->add(array(
                    'uid' => $user['invite1'],
                    'buy_uid'=>$uid,
                    'rank' => 6,
                    'price' => $price,
                    'commission' => $money,
                    'create_time'=>NOW_TIME,
                    'year' => $year,
                    'month'=>$month,
                    'day'  =>$day,
                ));
            } 
        }
        return $price;
    }
    
    public function tongjiByUid($uid,$bg_date,$end_date){
        $bg_time = strtotime($bg_date);
        $end_time= strtotime($end_date);
        $uid = (int)$uid;
        $data = $this->query(" select count(1) as num,sum(commission) as money,`year`,`month`,`day` FROM ".$this->getTableName()." where uid='{$uid}' group by `year`,`month`,`day` limit 0,100");
        return $data;
    }
    
    
    public function  tongjiComm($bg_time,$end_time,$uid=0){
         $bg_time   = (int)$bg_time;
        $end_time  = (int)$end_time;
        $uid = (int) $uid;
        if(!empty($uid)){
            $data = $this->query("select  sum(commission) as money ,`month`,`day` FROM  ".$this->getTableName()." where uid ='{$uid}' AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}' group by `month`,`day` ");
        }else{
            $data = $this->query("select  sum(commission) as money ,`month`,`day` FROM  ".$this->getTableName()." where create_time >= '{$bg_time}' AND create_time <= '{$end_time}' group by `month`,`day` ");
        }
        
        $showdata = array();
        $days = array();
        
        for($i=$bg_time;$i<=$end_time;$i+=86400){
            $days[date('md',$i)] = '\''.date('m月d日',$i).'\''; 
        }
        $money = array();
        foreach($days  as $k=>$v){
            $money[$k] = 0;
            foreach($data as $val){
                if($val['month'].$val['day'] == $k){
                    $money[$k] = round($val['money']/100,2);
                }
            }
        }
       $showdata['d'] = join(',',$days);
       $showdata['money'] = join(',',$money);
       return $showdata;
    }
    
    public function  tongjiNum($bg_time,$end_time,$uid=0){
         $bg_time   = (int)$bg_time;
        $end_time  = (int)$end_time;
        $uid = (int) $uid;
        if(!empty($uid)){
            $data = $this->query("select count(1) as num ,`month`,`day` FROM  ".$this->getTableName()." where uid ='{$uid}' AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}' group by `month`,`day` ");
        }else{
            $data = $this->query("select  count(1) as num ,`month`,`day` FROM  ".$this->getTableName()." where create_time >= '{$bg_time}' AND create_time <= '{$end_time}' group by `month`,`day` ");
        }
        
        $showdata = array();
        $days = array();
        
        for($i=$bg_time;$i<=$end_time;$i+=86400){
            $days[date('md',$i)] = '\''.date('m月d日',$i).'\''; 
        }
        $num = array();
        foreach($days  as $k=>$v){
            $num[$k] = 0;
            foreach($data as $val){
                if($val['month'].$val['day'] == $k){
                    $num[$k] = $val['num'];
                }
            }
        }
       $showdata['d'] = join(',',$days);
       $showdata['num'] = join(',',$num);
       return $showdata;
    }
    
}