<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class IndexAction extends CommonAction {

    
    public function index() {
        
        $this->display();
    }
    
        public function dingwei(){
        $lat = $this->_get('lat', 'htmlspecialchars');
        $lng = $this->_get('lng', 'htmlspecialchars');
        cookie('lat',$lat);
        cookie('lng',$lng);
        die(NOW_TIME);
    }
}

   