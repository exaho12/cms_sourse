<?php

class QuanmingAction extends CommonAction {
    
    public function index() {
        
        $Quanming = D('Quanming');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('uid' => $this->uid);
        $count = $Quanming->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Quanming->where($map)->order(array('tid' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        
        $this->display();
    }
    
    public function info(){
        $Users = D('Users');
        import('ORG.Util.Page'); // 导入分页类
        $uid = (int)  $this->uid;
        $map = " invite6 = '{$uid}' or invite5 = '{$uid}' or invite4 = '{$uid}' or invite3 = '{$uid}' or invite2 = '{$uid}' or invite1 = '{$uid}' ";
        $count = $Users->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Users->where($map)->order(array('user_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }
    
    public function tongji(){
        
        $Quanming = D('Quanming');
        if((!$bg_date = $this->_param('bg_date','htmlspecialchars') )||(!$end_date=$this->_param('end_date','htmlspecialchars'))){
            $bg_date = date('Y-m-d',NOW_TIME-30*86400);
            $end_date = date('Y-m-d',NOW_TIME);
        }else{
            if(strtotime($end_date) - strtotime($bg_date) > 86400 * 90){
                $this->error('只能查询90天区间的数据！');
            }
        }
        $this->assign('bg_date',$bg_date);
        $this->assign('end_date',$end_date);
        $data = $Quanming->tongjiByUid($this->uid,$bg_date,$end_date);
        $this->assign('datas',$data);
        $this->display();
    }
    
    
}