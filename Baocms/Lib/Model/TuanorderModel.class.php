<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanorderModel extends CommonModel{
    protected $pk   = 'order_id';
    protected $tableName =  'tuan_order';
    
    public function source(){
        $y = date('Y',NOW_TIME);
        $data= $this->query(" SELECT count(1) as num,is_mobile,FROM_UNIXTIME(create_time,'%c') as m from  ".$this->getTableName()."  where status=1 AND FROM_UNIXTIME(create_time,'%Y') ='{$y}'  group by  is_mobile,FROM_UNIXTIME(create_time,'%c')");
        $showdata = array();
        $mobile = array();
        $pc = array();
        for($i=1;$i<=12;$i++){
            $mobile[$i] = 0;
            $pc[$i] = 0;
            foreach($data as $val){
                if($val['m'] == $i){
                    if($val['is_mobile']){
                       $mobile[$i] =$val['num'];
                    }else{
                        $pc[$i] =$val['num'];
                    }                    
                }
            }  
        }     
        ksort($mobile);
        ksort($pc);
        $showdata['mobile'] = join(',',$mobile);
        $showdata['pc'] = join(',',$pc);
        return $showdata;
    }
    
    
    public function money_yue(){
        $y = date('Y',NOW_TIME);
        $data= $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%c') as m from  ".$this->getTableName()."  where status=1 AND FROM_UNIXTIME(create_time,'%Y') ='{$y}'  group by  FROM_UNIXTIME(create_time,'%c')");
        $showdata = array();
        for($i=1;$i<=12;$i++){
            $showdata[$i] = 0;
            foreach($data as $val){
                if($val['m'] == $i){
                   $showdata[$i] = $val['price'];                 
                }
            }  
        }     
        ksort($showdata);
        return join(',',$showdata);
    }
    
    public function money($bg_time,$end_time,$shop_id){      
        $bg_time   = (int)$bg_time;
        $end_time  = (int)$end_time;
        $shop_id = (int) $shop_id;
        if(!empty($shop_id)){
            $data = $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%m%d') as d from  ".$this->getTableName()."   where status=1 AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}' AND shop_id = '{$shop_id}'  group by  FROM_UNIXTIME(create_time,'%m%d')");   
        }else{
            $data = $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%m%d') as d from  ".$this->getTableName()."   where status=1 AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}'  group by  FROM_UNIXTIME(create_time,'%m%d')");      
        }
        $showdata = array();
        $days = array();
        
        for($i=$bg_time;$i<=$end_time;$i+=86400){
            $days[date('md',$i)] = '\''.date('m月d日',$i).'\''; 
        }
        $price = array();
        foreach($days  as $k=>$v){
            $price[$k] = 0;
            foreach($data as $val){
                if($val['d'] == $k){
                    $price[$k] = $val['price'];
                }
            }
        }
       $showdata['d'] = join(',',$days);
       $showdata['price'] = join(',',$price);
        return $showdata;
    }
    
    public function weeks(){
        $y =NOW_TIME - 86400 * 6;
  
        $data= $this->query(" 
            SELECT count(1) as num,is_mobile,FROM_UNIXTIME(create_time,'%d') as d from  __TABLE__ 
            where status=1 AND create_time >= '{$y}'  group by  
                is_mobile,FROM_UNIXTIME(create_time,'%d')"
            );
        $showdata = array();
        $mobile = array();
        $pc = array();
        $days= array();
        for($i=0;$i<=6;$i++){
            $d = date('d',$y+$i*86400);
            $mobile[$i] = 0;
            $pc[$i] = 0;
            $days[] = '\''.$d.'号\'';
            foreach($data as $val){
                if($val['d'] == $d){
                    if($val['is_mobile']){
                       $mobile[$i] =$val['num'];
                    }else{
                        $pc[$i] =$val['num'];
                    }                    
                }
            }  
        }   
        ksort($mobile);
        ksort($pc);
        $showdata['mobile'] = join(',',$mobile);
        $showdata['pc'] = join(',',$pc);
        $showdata['days'] = join(',',$days);
        return $showdata;    
            
    }
    
}