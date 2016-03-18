<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class  TongjiAction extends CommonAction{
    
    
    public function  index(){
        
        $showdata =  D('Tuanorder')->source();
        $weeks = D('Tuanorder')->weeks();
        $this->assign('weeks',$weeks);
        $this->assign('showdata',$showdata);
        $this->display();
    }
    
    
    public function quanming(){
         if(($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))){
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date)+86400;
           
        }else{
            $bg_date = date('Y-m-d',NOW_TIME-86400*30);
            $bg_time = strtotime($bg_date);
            $end_time = NOW_TIME;
            $end_date = TODAY;
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);

        $this->assign('tongji1',D('Quanming')->tongjiComm($bg_time,$end_time));
        $this->assign('tongji2',D('Quanming')->tongjiNum($bg_time,$end_time));
        $this->display();
    }
    
    public function money(){
        if(($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))){
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date)+86400;
           
        }else{
            $bg_date = date('Y-m-d',NOW_TIME-86400*30);
            $bg_time = strtotime($bg_date);
            $end_time = NOW_TIME;
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);
        $this->assign('money',D('Tuanorder')->money($bg_time,$end_time));
        $this->assign('money_yue',D('Tuanorder')->money_yue());
        $this->display();
    }
    
    
    public function  laiyuan(){
        $bg_date = $this->_param('bg_date', 'htmlspecialchars');
        $end_date = $this->_param('end_date', 'htmlspecialchars');
        if(empty($bg_date) || empty($end_date)){
            $bg_date = date('Y-m-d',NOW_TIME-86400*30);
            $end_date = TODAY;
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);
        
        $this->assign('laiyuan',D('Tongji')->laiyuan($bg_date,$end_date));
        
        $this->display();
    }
    
    public function lmoney(){
        
        $bg_date = $this->_param('bg_date', 'htmlspecialchars');
        $end_date = $this->_param('end_date', 'htmlspecialchars');
        if(empty($bg_date) || empty($end_date)){
            $bg_date = date('Y-m-d',NOW_TIME-86400*30);
            $end_date = TODAY;
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);
        
        $this->assign('laiyuan',D('Tongji')->lmoney($bg_date,$end_date));
        
        $this->display();
        
    }
    
    
    public function tuiguan(){
        
        $bg_date = $this->_param('bg_date', 'htmlspecialchars');
        $end_date = $this->_param('end_date', 'htmlspecialchars');
        if(empty($bg_date) || empty($end_date)){
            $bg_date = date('Y-m-d',NOW_TIME-86400*30);
            $end_date = TODAY;
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);
    
        $this->assign('tuiguan',D('Tongji')->tuiguan($bg_date,$end_date));
        $this->assign('tmoney',D('Tongji')->tmoney($bg_date,$end_date));
        $this->display();
    }
    
    public function keyword(){
        
        $bg_date = $this->_param('bg_date', 'htmlspecialchars');
        $end_date = $this->_param('end_date', 'htmlspecialchars');
        if(empty($bg_date) || empty($end_date)){
            $bg_date = date('Y-m-d',NOW_TIME-86400*30);
            $end_date = TODAY;
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);
    
        $this->assign('keyword',D('Tongji')->keyword($bg_date,$end_date));
        $this->assign('kmoney',D('Tongji')->kmoney($bg_date,$end_date));
        $this->display();
    }
    
    
}