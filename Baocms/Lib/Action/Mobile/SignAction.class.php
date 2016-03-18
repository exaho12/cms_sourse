<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class SignAction extends CommonAction {

    public function signed() {
		
        if(!empty($this->uid)){
            $integral = $this->_CONFIG['integral']['sign'];
            $data = D('Usersign')->getSign($this->uid,$integral);
           
            $this->assign('sign',D('Usersign')->getSign($this->uid,$integral));
		}else{
			$this->error('请登录后进行签到！',U("passport/login"));
		}
        $this->display();
    }
    public function signing(){
        if(empty($this->uid)){
            $this->error('请登录后进行签到！');              
        }
        $integal = D('Usersign')->sign($this->uid,$this->_CONFIG['integral']['sign'],$this->_CONFIG['integral']['firstsign']);
        if($integal !== false){
            $this->success('恭喜您签到成功！系统赠送了您'.$integal.'积分',U('sign/signed'));
        }else{
            $this->error('很抱歉您已经签到过了！',U('sign/signed'));
        }
    }
    
    
    
}

