<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PublicAction extends CommonAction {
    //根据后面实际需要 调整缩略图大小
    
    
    
    
    public function maps(){
        $lat = $this->_get('lat',  'htmlspecialchars');
        $lng = $this->_get('lng','htmlspecialchars');
        
        $this->assign('lat' , $lat ? $lat : $this->_CONFIG['site']['lat']);
        $this->assign('lng' , $lng ? $lng : $this->_CONFIG['site']['lng']);
        $this->display();
    }
    

    
}