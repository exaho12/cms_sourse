<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class LoginAction extends CommonAction{
    
    public function index(){
        $this->display();
    }
    
    public function loging(){
        $yzm = $this->_post('yzm');
        if(strtolower($yzm) != strtolower(session('verify'))){
            session('verify',null);
            $this->baoError('验证码不正确!',2000,true);
        }
        $username = $this->_post('username','trim');
        $password = $this->_post('password','trim,md5');
        $adminObj = D('Admin');
        $admin = $adminObj->getAdminByUsername($username);
        if(empty($admin) || $admin['password'] != $password){
            session('verify',null);
            $this->baoError('用户名或密码不正确!',2000,true);
        }
		
        if($admin['closed'] == 1){
           session('verify',null);
           $this->baoError('该账户已经被禁用!',2000,true); 
        }
		
		if($admin['role_id'] == 2) {
			session('verify',null);
			$this->baoError('分站管理员请登录分站后台',2000,true); 
		}

		
		$ip = $admin['last_ip']; //旧的IP
		
        $admin['last_time'] = NOW_TIME;
        $admin['last_ip']  = get_client_ip();
		if(!empty($ip)){//首先判断是否等于空
			if($ip != $admin['last_ip']){
				$adminObj->where("admin_id=%d",$admin['admin_id'])->save(array('is_ip'=>1));//对比IP不对更更新is_ip值不一样
			}
		}
		
        $adminObj->where("admin_id=%d",$admin['admin_id'])->save(array('last_time'=>$admin['last_time'],'last_ip'=>$admin['last_ip']));
        
        session('admin',$admin);
        $this->baoSuccess('登录成功！',U('index/index'));
    }
    
    public function logout(){
		
		$admin_ids = $this->_admin = session('admin');
		$adminObj = D('Admin');
	    $adminObj->where("admin_id=%d",$admin_ids['admin_id'])->save(array('is_ip'=>0));//不论怎么样退出的时候值修改为0，只有登录时候IP不一样才会修改
        session('admin',null);
        $this->success('退出成功',U('login/index'));
    }
    
    public function verify(){
        import('ORG.Util.Image');
        Image::buildImageVerify(5,2,'png',60,30);
    }
    
}
