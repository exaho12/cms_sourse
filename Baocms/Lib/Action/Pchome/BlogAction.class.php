<?php
class  BlogAction extends  CommonAction{
    
    protected $user = array();
    protected $user_id = 0;

    public function _initialize(){
        parent::_initialize();
        $this->user_id = (int)  $this->_get('user_id');
		$act = $this->_get('act');
        $this->user = D('Users')->find($this->user_id);
        if(empty($this->user)){
            $this->error('该用户不存在');
        }
        $userex = D('Usersex')->getUserex($this->user_id);//其实很多字段 是可以放在EX表里面的，不过因为历史原因改动起来费点时间！在以后版本中优化
        if($userex['last_uid'] != $this->uid){
            D('Usersex')->save(array('user_id'=>  $this->user_id, 'last_uid'=>  $this->uid,'views'=>$userex['views']+1)); //更新访客数量  只记录登录用户的来访
        }  
        D('Usersvisitors')->up($this->user_id,  $this->uid);
        //用户中心
        $this->assign('user',  $this->user);
        $this->assign('user_id',  $this->user_id);
        $this->seodatas['nickname'] = $this->user['nickname'];
        //取出 最近的访客
        $this->assign('visitors',D('Usersvisitors')->last($this->user_id,9));
        $this->assign('act',$act);
        $this->assign('ranks', D('Userrank')->fetchAll());
    }
    
    public function index(){
        $shop_ids = array();
        
        $dianping = D('Shopdianping')->where(array('user_id'=>  $this->user_id,'show_date'=>array('ELT',TODAY)))->order(array('dianping_id'=>'desc'))->limit(0,4)->select();
        $this->assign('dianping',$dianping);
        foreach($dianping as $val){
            $shop_ids[$val['shop_id']] = $val['shop_id'];            
        }
        
        $favorites = D('Shopfavorites')->where(array('user_id'=>  $this->user_id))->order(array('favorites_id'=>'desc'))->limit(0,5)->select();
        $this->assign('favorites',$favorites);
        foreach($favorites as $val){
            $shop_ids[$val['shop_id']] = $val['shop_id'];            
        }
        
        $pics = D('Shopdianpingpics')->where(array('user_id'=>  $this->user_id))->order(array('pic_id'=>'desc'))->limit(0,10)->select();
        $this->assign('pics',$pics);
        
		
		
        $tie = D('Post')->where(array('user_id'=>  $this->user_id))->order(array('post_id'=>'desc'))->limit(0,20)->select();
        $this->assign('tie',$tie);


        $this->assign('shops',D('Shop')->itemsByIds($shop_ids));
        $this->display();
        
    }
    
    
    
}
