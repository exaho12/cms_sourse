<?php
class  DingAction extends  CommonAction{
    
    public  function _initialize() {
        parent::_initialize();
        if(empty($this->shop['is_ding'])){
            $this->error('订座功能要和网站洽谈，由网站开通！');
        }
    }
    //订座配置
    public function setting(){
        $obj = D('Shopdingsetting');
        if(IS_POST){
            $data['shop_id'] = $this->shop_id;
            $data['mobile'] = htmlspecialchars($_POST['data']['mobile']);
            if(!isMobile($data['mobile'])){
                $this->error('请填写正确的手机号码！');
            }
            $data['money'] = (int)($_POST['data']['money']* 100);
			if(empty($data['money'])){
				$this->baoError('定金不能为空或者为0');
			}
            $data['bao_time'] = (int)$_POST['data']['bao_time'];
            $data['start_time'] = (int)$_POST['data']['start_time'];
	
            $data['end_time'] = (int)$_POST['data']['end_time'];
			
            $data['is_bao'] = (int)$_POST['data']['is_bao'];
            $data['is_ting'] = (int)$_POST['data']['is_ting'];
            $obj->save($data);
            $this->baoSuccess('设置成功！',U('ding/setting'));
        }  else {
             $this->assign('cfg',$obj->getCfg());
            $this->assign('detail',$obj->detail($this->shop_id));
            $this->display();
        }
    }
    
    //
    public function room(){
        $obj = D('Shopdingroom');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id'=>  $this->shop_id);
        $keyword = trim($this->_param('keyword', 'htmlspecialchars'));
        if ($keyword) {
            $map['name'] = array('LIKE', '%'.$keyword.'%');
        }
        $this->assign('keyword',$keyword);
        if($type_id = (int)$this->_param('type_id')){
            $map['type_id'] = $type_id;
            $this->assign('type_id',$type_id);
        }        
        $count = $obj->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $obj->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('types',$obj->getType());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
       
        $this->display();
    }
    
    
    public function roomcreate(){
         $obj = D('Shopdingroom');
         if(IS_POST){
             $data['name'] = htmlspecialchars($_POST['data']['name']);
             if(empty($data['name'])){
                 $this->baoError('包厢名称不能为空');
             }
             $data['type_id'] = (int)($_POST['data']['type_id']);
             if(empty($data['type_id'])){
                 $this->baoError('请选择房间大小');
             }
             $data['photo'] = htmlspecialchars($_POST['data']['photo']);
             if(empty($data['photo'])){
                 $this->baoError('请上传图片');
             }
             $data['intro'] = htmlspecialchars($_POST['data']['intro']);
             $data['money'] = (int)($_POST['data']['money']*100);
             $data['closed'] = (int)($_POST['data']['closed']);
             
             $data['shop_id'] = $this->shop_id;
             if($obj->add($data)){
                 $this->baoSuccess('恭喜你操作成功',U('ding/roomcreate'));
             }
             $this->baoError('操作失败');
         }else{             
             $this->assign('types',$obj->getType());
             $this->display();
         }
    }
    
    public function roomedit($room_id){
        $obj = D('Shopdingroom');
        if(!$detail = $obj->find($room_id)){
            $this->error('参数错误');
        }
        if($detail['shop_id']!= $this->shop_id){
            $this->error('参数错误');
        }
        $obj = D('Shopdingroom');
         if(IS_POST){
             $data['name'] = htmlspecialchars($_POST['data']['name']);
             if(empty($data['name'])){
                 $this->baoError('包厢名称不能为空');
             }
             $data['type_id'] = (int)($_POST['data']['type_id']);
             if(empty($data['type_id'])){
                 $this->baoError('请选择房间大小');
             }
             $data['photo'] = htmlspecialchars($_POST['data']['photo']);
             if(empty($data['photo'])){
                 $this->baoError('请上传图片');
             }
             $data['intro'] = htmlspecialchars($_POST['data']['intro']);
             $data['money'] = (int)($_POST['data']['money']*100);
             $data['closed'] = (int)($_POST['data']['closed']);
             $data['room_id'] = $room_id;
             $data['shop_id'] = $this->shop_id;
             if(false !== $obj->save($data)){
                 $this->baoSuccess('恭喜你操作成功',U('ding/roomedit',array('room_id'=>$room_id)));
             }
             $this->baoError('操作失败');
         }else{             
             $this->assign('types',$obj->getType());
             $this->assign('detail',$detail);
             $this->display();
         }
    }
    
    public function roomdelete($room_id){
         $obj = D('Shopdingroom');
        if($room_id = (int)$room_id){
            if(!$detail = $obj->find($room_id)){
                $this->baoError('参数错误');
            }
            if($detail['shop_id']!= $this->shop_id){
                $this->baoError('参数错误');
            }
            $data['closed'] = $detail['closed'] ? 0 : 1;
            $data['room_id'] = $room_id;
            if(false != $obj->save($data)){
                $this->baoSuccess('操作成功',U('ding/room'));
            }
            $this->baoError('操作失败');
        }else{
            $this->baoError('参数错误');
        }        
    }
    
    public function index(){
        
        
        
    }
    
    
    
}