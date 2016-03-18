<?php

class InfoAction extends CommonAction {

    public function face() {
		 $this->display();
	}
	
    public function nickname() {
		if($this->isPost()){
			$nickname = $this->_post('nickname');
			$user = D('Users')->where(array('nickname'=>$nickname))->find();
			if(!empty($user)){
				$this->niuMsg('该昵称已被使用');
			}
			D('Users')->save(array('nickname'=>$nickname,'user_id'=>$this->uid));
			$this->niuMsg('昵称已经更新', U('info/nickname'));
		}
		
		
		 $this->display();
	}
	
    public function nickcheck() {
		$nickname = $this->_get('nickname');
		$user = D('Users')->where(array('nickname'=>$nickname))->find();
		if(empty($user)){
			echo '1';
		}else{
			echo '0';
		}
	}
	
	
	
    public function sendsms() {
        $mobile = $this->_post('mobile');
        if (isMobile($mobile)) {
            session('mobile', $mobile);
            $randstring = session('code');
            if (empty($randstring)) {
                $randstring = rand_string(6, 1);
                session('code', $randstring);
            }
            D('Sms')->sendSms('sms_code', $mobile, array('code' => $randstring));
        }
    }

    public function password() {
        if ($this->isPost()) {
            $newpwd = $this->_post('newpwd', 'htmlspecialchars');
            if (empty($newpwd)) {
                $this->niuMsg('请输入新密码');
            }
            $pwd2 = $this->_post('pwd2', 'htmlspecialchars');
            if (empty($pwd2) || $newpwd != $pwd2) {
                $this->niuMsg('两次密码输入不一致！');
            }
            if (D('Users')->save(array('user_id' => $this->uid, 'password' => md5($newpwd)))) {
                $this->niuMsg('更改密码成功！', U('index/index'));
            }
            $this->niuMsg('修改密码失败！');
        } else {
            $this->display();
        }
    }
	
	
    public function account() {
		if($this->isPost()){
			$mobile = $this->_post('mobile');
            $yzm = $this->_post('yzm');
            if (empty($mobile) || empty($yzm)){
				$this->niuMsg('请填写正确的手机及手机收到的验证码！');
			}
            $s_mobile = session('mobile');
            $s_code = session('code');
            if ($mobile != $s_mobile){
				$this->niuMsg('手机号码和收取验证码的手机号不一致！');
			}
            if ($yzm != $s_code){
				$this->niuMsg('验证码不正确');
			}
			$user_id = D('Users')->where(array('mobile'=>$mobile))->getField('user_id'); //获取用户输入的认证手机所在的user_id
			$uids = D('Users')->where(array('user_id'=>$this->uid))->getField('user_id'); //获取当前自动登录用户的user_id
			
			$connect = M('Connect'); //连接connect表
			$open_id = $connect->where(array('uid'=>$uids))->getField('open_id'); //获取当前用户的open_id
			
			$result = $connect -> where(array('open_id'=>$open_id))->setField('uid',$user_id); //根据open_id查询在connect表中的uid并进行替换
			
			//$result = $connect->where(array('open_id'=>$open_id))->setField('uid',$user_id);
			
			//$domain_arr = explode('.',$this->_CONFIG['site']['host']);
			//$domain_str = $domain_arr[1].'.'.$domain_arr[2];
			//if(!strpos($this->member['account'],$domain_str)){
			//	$this->niuMsg('您的帐号不符合修改昵称的条件！');
			//}
			//$user = D('Users')->where(array('account'=>$mobile))->find();
			//if(!empty($user)){
			//	$this->niuMsg('该手机号已经被其他用户绑定！');
			//}
			//$uid = D('Connect')->where(array('uid'=>$uid))->find();
			//$uid = D('Users')->where(array('user_id'=>$this->uid))->find();
			//$opneid= D('Connect') ->where(array('opne_id'=>));
			//D('Connect')->save(array('uid'=>$uid));
			//D('Users')->save(array('account'=>$mobile,'ext0'=>$mobile,'user_id'=>$this->uid));
			
			 D('Passport')->logout();
			
			$this->niuMsg('您的帐号已经更新！', U('mobile/index/index'));
		}
		$this->display();
	}
	
}
