<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class TuiAction extends CommonAction{
    private $create_fields = array('tui_name','tui_link');
    private $edit_fields = array('tui_name','tui_link');
    
    public  function index(){
       $Tui = D('Tui');
       import('ORG.Util.Page');// 导入分页类
       $map = array();
       if($keyword = $this->_param('keyword',  'htmlspecialchars')){
           $map['tui_name'] = array('LIKE', '%'.$keyword.'%');
       }    
        $this->assign('keyword',$keyword);
         
       $count      = $Tui->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Tui->where($map)->order(array('tui_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $this->assign('list',$list);// 赋值数据集
       $this->assign('page',$show);// 赋值分页输出
       $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Tui');
        if($obj->add($data)){
            $this->baoSuccess('添加成功',U('tui/index'));
        }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['tui_name'] = htmlspecialchars($data['tui_name']);
        if(empty($data['tui_name'])){
            $this->baoError('推广名称不能为空');
        }        $data['tui_link'] = htmlspecialchars($data['tui_link']);
        if(empty($data['tui_link'])){
            $this->baoError('推广连接不能为空');
        }
        return $data;
    }
    public function edit($tui_id = 0){
        if($tui_id =(int) $tui_id){
            $obj = D('Tui');
            if(!$detail = $obj->find($tui_id)){
                $this->baoError('请选择要编辑的推广配置');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['tui_id'] = $tui_id;
                if(false!==$obj->save($data)){
                    $this->baoSuccess('操作成功',U('tui/index'));
                }
                $this->baoError('操作失败');
                
            }else{
                $this->assign('detail',$detail);         
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的推广配置');
        }
    }
     private function editCheck(){
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['tui_name'] = htmlspecialchars($data['tui_name']);
        if(empty($data['tui_name'])){
            $this->baoError('推广名称不能为空');
        }        $data['tui_link'] = htmlspecialchars($data['tui_link']);
        if(empty($data['tui_link'])){
            $this->baoError('推广连接不能为空');
        }
        return $data;  
    }

    public function delete($tui_id = 0){
         if(is_numeric($tui_id) && ($tui_id = (int)$tui_id)){
             $obj =D('Tui');
             $obj->delete($tui_id);
             $this->baoSuccess('删除成功！',U('tui/index'));
         }else{
            $tui_id = $this->_post('tui_id',false);
            if(is_array($tui_id)){     
                $obj = D('Tui');
                foreach($tui_id as $id){
                    $obj->delete($id);
                }                
                $this->baoSuccess('删除成功！', U('tui/index'));
            }
            $this->baoError('请选择要删除的推广配置');
         }
         
    }

    
   
}
