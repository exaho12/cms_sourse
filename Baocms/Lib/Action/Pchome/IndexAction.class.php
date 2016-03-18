<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class IndexAction extends CommonAction {
    
     public function _initialize() {
        parent::_initialize();
        $this->type = D('Keyword')->fetchAll();
        $this->assign('types', $this->type);
		$this->goodscates = D('Goodscate')->fetchAll();
        $this->assign('goodscates', $this->goodscates);
    }

    public function index() {
        
        if (is_mobile()) {
            header("Location:" . U('mobile/index/index'));
            die;
        }
		$linkArr = array();
		
		
		$cache = cache(array('type' => 'File', 'expire' => 600));
        if (!$counts = $cache->get('index_count')) {
            $counts['shop'] = D('Shop')->count();
            $counts['coupon'] = D('Coupon')->count();
            $counts['users'] = D('Users')->count();
            $counts['life'] = D('Life')->count();
            $counts['post'] = D('Post')->count();
            $cache->set('index_count', $counts);
        }

		
		$this->assign('counts', $counts);
		$this->assign('host',__HOST__);
		$this->assign('linkArr', $linkArr);
        $this->display();
  
    }
    
    
    public function get_arr(){
        
         if(IS_AJAX){
            
            $cate_id = I('val',0,'intval,trim');
            
            $today = date('Y-m-d');

            $t = D('Tuan');
            $map = array(
                'cate_id'=>$cate_id,
                'city_id'=>$this->city_id,
                'closed'=>0,
                'audit'=>1,
                'bg_date' => array('elt',$today),
                'end_date'=>array('egt',$today)
                
            );
            $r = $t->where($map)->limit(8)->select();
            
            if($r){
                $this->ajaxReturn(array('status'=>'success','arr'=>$r));
            }else{
                $this->ajaxReturn(array('status'=>'error'));
            }
            
        }
        
    }
    

    public function test() {

        //$data = D('Email')->sendMail('email_newpwd', '1442211217@qq.com', '重置密码', array('newpwd' => 123456));
       // var_dump($data);
        //var_dump(D('Email')->getEorrer());
       
    }

}
