<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class XingeAction extends CommonAction{


    //推送单发
    public  function single(){
         if ($this->isPost()) {
            $data = $this->checkFields($this->_post('data', false), array('plat','title', 'contents'));
            $data['title'] = htmlspecialchars($data['title']);
            $data['contents'] = htmlspecialchars($data['contents']);
            $data['type'] = htmlspecialchars($data['plat']);
            if(!empty($data['url'])){
                $data['url'] = htmlspecialchars($data['url']);
            }
            $result =  D('Xinge')->single($data);
            if(true !==$result){
                $this->baoError($result);
            }    
             $this->baoSuccess('发送成功！',U('xinge/mass'));
          } else {
              $this->display();
          }
    }
    

    //推送群发
    public function mass() {
        if ($this->isPost()) {
            $data = $this->checkFields($this->_post('data', false), array('plat','title', 'contents', 'url'));
            $data['title'] = htmlspecialchars($data['title']);
            $data['contents'] = htmlspecialchars($data['contents']);
            if(!empty($data['url'])){
                $data['url'] = htmlspecialchars($data['url']);
            }
            $data['url'] = htmlspecialchars($data['url']);
            $data['type'] = htmlspecialchars($data['plat']);
            $result =  D('Xinge')->mass($data);
            if(true !==$result){
                $this->baoError($result);
            }    
             $this->baoSuccess('发送成功！',U('xinge/mass'));
          } else {
              $this->display();
          }
    }


    
   
}
