<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LoginAction extends CommonAction {

    public function index() {
        if(cookie('DL') == true){
           header("Location: " . U('index/index'));
        }else{
           $this->display();
        }          
    }
    
    public function handle(){
        
        if(IS_AJAX){
            
            $username = I('username','','trim,htmlspecialchars');
            $password = I('password');
            
            if(!$username){
                $this->ajaxReturn(array('status'=>'error','message'=>'请输入帐号！'));
            }
            if(!$password){
                $this->ajaxReturn(array('status'=>'error','message'=>'请输入密码！'));
            }
            
            $dv = D('Delivery');
            $r = $dv -> where('username ="'.$username.'"')-> find();
            if(!$r){
                $this->ajaxReturn(array('status'=>'error','message'=>'错误的帐号！'));
            }else{
                if($r['password'] != md5($password)){
                    $this->ajaxReturn(array('status'=>'error','message'=>'密码错误！'));
                }else{

                    $this->cookid($r['id']); // 指定cookie保存时间
                    $this->ajaxReturn(array('status'=>'success','message'=>'登录成功！'));
                    
                }
            }
  
        }
        
    }
    
    
    public function logout(){
        
        cookie('DL',null);
        header("Location: " . U('login/index'));
        
    }


}