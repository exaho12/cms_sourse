<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class TuancodeAction extends CommonAction{
    
    
    public function overdue(){
       $Tuancode = D('Tuancode');
       import('ORG.Util.Page');// 导入分页类
       $map = array('is_used'=>0,'status'=>array('IN',array(0,1)),'fail_date'=>array('ELT',TODAY));
       if(($bg_date = $this->_param('bg_date',  'htmlspecialchars') )&& ($end_date=$this->_param('end_date','htmlspecialchars'))){
           $bg_time = strtotime($bg_date);
           $end_time = strtotime($end_date);
           $map['create_time'] = array(array('ELT',$end_time),array('EGT',$bg_time));
           $this->assign('bg_date',$bg_date);
           $this->assign('end_date',$end_date);
       }else{
           if($bg_date = $this->_param('bg_date',  'htmlspecialchars')){
               $bg_time = strtotime($bg_date);
               $this->assign('bg_date',$bg_date);
               $map['create_time'] = array('EGT',$bg_time);
           }
           if($end_date = $this->_param('end_date',  'htmlspecialchars')){
               $end_time = strtotime($end_date);
               $this->assign('end_date',$end_date);
               $map['create_time'] = array('ELT',$end_time);
           }
       }
       if($user_id = (int)  $this->_param('user_id')){
           $users = D('Users')->find($user_id);
           $this->assign('nickname',$users['nickname']);
           $this->assign('user_id',$user_id);
           $map['user_id'] = $user_id;
       }
       if($keyword = $this->_param('keyword','htmlspecialchars')){
           $map['code'] = array('LIKE', '%'.$keyword.'%');
           $this->assign('keyword',$keyword);
       } 
       if($shop_id = (int)$this->_param('shop_id')){
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name',$shop['shop_name']);
            $this->assign('shop_id',$shop_id);
       }

       $count      = $Tuancode->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Tuancode->where($map)->order(array('code_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $shop_ids = $user_ids  = array();
       foreach($list as $k=>$val){
           if(!empty($val['shop_id'])) $shop_ids[$val['shop_id']] = $val['shop_id'];
           $user_ids[$val['user_id']] = $val['user_id'];
           $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
       }
       
       $this->assign('list',$list);// 赋值数据集
       $this->assign('page',$show);// 赋值分页输出
       $this->assign('users',D('Users')->itemsByIds($user_ids));
       $this->assign('shops',D('Shop')->itemsByIds($shop_ids));
       $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
       $this->display(); // 输出模板
        
    }
    
    
    public function  refund(){
       $Tuancode = D('Tuancode');
       import('ORG.Util.Page');// 导入分页类
       $map = array('status'=>1);
       if(($bg_date = $this->_param('bg_date',  'htmlspecialchars') )&& ($end_date=$this->_param('end_date','htmlspecialchars'))){
           $bg_time = strtotime($bg_date);
           $end_time = strtotime($end_date);
           $map['create_time'] = array(array('ELT',$end_time),array('EGT',$bg_time));
           $this->assign('bg_date',$bg_date);
           $this->assign('end_date',$end_date);
       }else{
           if($bg_date = $this->_param('bg_date',  'htmlspecialchars')){
               $bg_time = strtotime($bg_date);
               $this->assign('bg_date',$bg_date);
               $map['create_time'] = array('EGT',$bg_time);
           }
           if($end_date = $this->_param('end_date',  'htmlspecialchars')){
               $end_time = strtotime($end_date);
               $this->assign('end_date',$end_date);
               $map['create_time'] = array('ELT',$end_time);
           }
       }
       if($user_id = (int)  $this->_param('user_id')){
           $users = D('Users')->find($user_id);
           $this->assign('nickname',$users['nickname']);
           $this->assign('user_id',$user_id);
           $map['user_id'] = $user_id;
       }
       if($keyword = $this->_param('keyword','htmlspecialchars')){
           $map['code'] = array('LIKE', '%'.$keyword.'%');
           $this->assign('keyword',$keyword);
       } 
       if($shop_id = (int)$this->_param('shop_id')){
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name',$shop['shop_name']);
            $this->assign('shop_id',$shop_id);
       }

       $count      = $Tuancode->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Tuancode->where($map)->order(array('code_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $shop_ids = $user_ids  = array();
       foreach($list as $k=>$val){
           if(!empty($val['shop_id'])) $shop_ids[$val['shop_id']] = $val['shop_id'];
           $user_ids[$val['user_id']] = $val['user_id'];
           $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
       }
       
       $this->assign('list',$list);// 赋值数据集
       $this->assign('page',$show);// 赋值分页输出
       $this->assign('users',D('Users')->itemsByIds($user_ids));
       $this->assign('shops',D('Shop')->itemsByIds($shop_ids));
       $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
       $this->display(); // 输出模板
    }

    
    public  function index(){
       $Tuancode = D('Tuancode');
       import('ORG.Util.Page');// 导入分页类
       $map = array();
       if(($bg_date = $this->_param('bg_date',  'htmlspecialchars') )&& ($end_date=$this->_param('end_date','htmlspecialchars'))){
           $bg_time = strtotime($bg_date);
           $end_time = strtotime($end_date);
           $map['create_time'] = array(array('ELT',$end_time),array('EGT',$bg_time));
           $this->assign('bg_date',$bg_date);
           $this->assign('end_date',$end_date);
       }else{
           if($bg_date = $this->_param('bg_date',  'htmlspecialchars')){
               $bg_time = strtotime($bg_date);
               $this->assign('bg_date',$bg_date);
               $map['create_time'] = array('EGT',$bg_time);
           }
           if($end_date = $this->_param('end_date',  'htmlspecialchars')){
               $end_time = strtotime($end_date);
               $this->assign('end_date',$end_date);
               $map['create_time'] = array('ELT',$end_time);
           }
       }
       if($user_id = (int)  $this->_param('user_id')){
           $users = D('Users')->find($user_id);
           $this->assign('nickname',$users['nickname']);
           $this->assign('user_id',$user_id);
           $map['user_id'] = $user_id;
       }
       if($keyword = $this->_param('keyword','htmlspecialchars')){
           $map['code'] = array('LIKE', '%'.$keyword.'%');
           $this->assign('keyword',$keyword);
       } 
       if($shop_id = (int)$this->_param('shop_id')){
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name',$shop['shop_name']);
            $this->assign('shop_id',$shop_id);
       }
       if ($is_used = (int) $this->_param('is_used')) {
            $map['is_used'] = ($is_used === 1 ? 1 : 0);
            $this->assign('is_used', $is_used);
       }
       if ($status = (int) $this->_param('status')) {
            $map['status'] = $status === 999 ? 0 :$status;
            $this->assign('status', $status);
       }
       $count      = $Tuancode->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Tuancode->where($map)->order(array('code_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $shop_ids = $user_ids  = array();
       foreach($list as $k=>$val){
           if(!empty($val['shop_id'])) $shop_ids[$val['shop_id']] = $val['shop_id'];
           $user_ids[$val['user_id']] = $val['user_id'];
             $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
       }
       
       $this->assign('list',$list);// 赋值数据集
       $this->assign('page',$show);// 赋值分页输出
       $this->assign('users',D('Users')->itemsByIds($user_ids));
       $this->assign('shops',D('Shop')->itemsByIds($shop_ids));
       $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
       $this->display(); // 输出模板
    }




    public function delete($code_id = 0){
         if(is_numeric($code_id) && ($code_id = (int)$code_id)){
             $obj =D('Tuancode');
             $obj->delete($code_id);
             $this->baoSuccess('删除成功！',U('Tuancode/index'));
         }else{
            $code_id = $this->_post('code_id',false);
            if(is_array($code_id)){     
                $obj = D('Tuancode');
                foreach($code_id as $id){
                    $obj->delete($id);
                }                
                $this->baoSuccess('删除成功！', U('Tuancode/index'));
            }
            $this->baoError('请选择要删除的抢购券');
         }
         
    }
    
    public function overdueing($code_id = 0){
        if(is_numeric($code_id) && ($code_id = (int)$code_id)){
            $detail = D('Tuancode')->find($code_id);
            if(($detail['status'] == 1 ||$detail['status'] == 0) && (int)$detail['is_used'] === 0 ){
                if(D('Tuancode')->save(array('code_id'=>$code_id,'status'=>2))){ //将内容变成
                    $obj = D('Users');
                    if($detail['real_money'] >0){
                        $obj->addMoney($detail['user_id'],$detail['real_money'],'抢购券退款:'.$detail['code']);
                    }
                    if($detail['real_integral'] >0){
                        $obj->addIntegral($detail['user_id'],$detail['real_integral'],'抢购券退款:'.$detail['code']);
                    }
                }                
            }
            
        }else{
           $code_id = $this->_post('code_id',false); 
           if(is_array($code_id)){     
                $obj = D('Users');
                $tuancode = D('Tuancode');
               foreach($code_id as $id){
                   $detail = $tuancode->find($id);
                    if(($detail['status'] == 1 ||$detail['status'] == 0)&& (int)$detail['is_used'] === 0 ){
                        if(D('Tuancode')->save(array('code_id'=>$id,'status'=>2))){ //将内容变成

                            if($detail['real_money'] >0){
                                $obj->addMoney($detail['user_id'],$detail['real_money'],'抢购券退款:'.$detail['code']);
                            }
                            if($detail['real_integral'] >0){
                                $obj->addIntegral($detail['user_id'],$detail['real_integral'],'抢购券退款:'.$detail['code']);
                            }
                        }                
                    }
               }
           }
            
        }
         $this->baoSuccess('退款成功！', U('Tuancode/refund'));
    }
    
    
    public function refunding($code_id = 0){
        if(is_numeric($code_id) && ($code_id = (int)$code_id)){
            $detail = D('Tuancode')->find($code_id);
            if($detail['status'] == 1 && (int)$detail['is_used'] === 0 ){
                if(D('Tuancode')->save(array('code_id'=>$code_id,'status'=>2))){ //将内容变成
                    $obj = D('Users');
                    if($detail['real_money'] >0){
                        $obj->addMoney($detail['user_id'],$detail['real_money'],'抢购券退款:'.$detail['code']);
                    }
                    if($detail['real_integral'] >0){
                        $obj->addIntegral($detail['user_id'],$detail['real_integral'],'抢购券退款:'.$detail['code']);
                    }
                }                
            }
            
        }else{
           $code_id = $this->_post('code_id',false); 
           if(is_array($code_id)){     
                $obj = D('Users');
                $tuancode = D('Tuancode');
               foreach($code_id as $id){
                   $detail = $tuancode->find($id);
                    if($detail['status'] == 1 && (int)$detail['is_used'] === 0 ){
                        if(D('Tuancode')->save(array('code_id'=>$id,'status'=>2))){ //将内容变成

                            if($detail['real_money'] >0){
                                $obj->addMoney($detail['user_id'],$detail['real_money'],'抢购券退款:'.$detail['code']);
                            }
                            if($detail['real_integral'] >0){
                                $obj->addIntegral($detail['user_id'],$detail['real_integral'],'抢购券退款:'.$detail['code']);
                            }
                        }                
                    }
               }
           }
            
        }
		
		 $where['tuan_id'] = $detail['tuan_id'];
       $tuan_num = D("Tuanorder")->where($where)->getField("num");
       D("Tuan")->where($where)->setInc("num",$tuan_num);  // 修复退款后增加库存
         $this->baoSuccess('退款成功！', U('Tuancode/refund'));
    }
    
   
}
