<?php
class WorkerAction extends CommonAction {

	private $edit_fields = array('user_id', 'name', 'tel', 'mobile', 'qq', 'weixin', 'work', 'addr', 'tuan', 'coupon', 'yuyue');
	
    public function index() {
        $Shopworker = D('Shopworker');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id' => $this->shop_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Shopworker->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shopworker->where($map)->order(array('worker_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
		$this->assign('detail', $detail);
		$this->display();

    }
	
    public function create() {
        if ($this->isPost()) {
            $data = $this->editCheck(); //这里和 编辑的字段差不多
			$user_id = intval($data['user_id']);
			$user = D('Users')->find($user_id);
			if(empty($user)){
				$this->baoError('没有找到该用户信息'.$data['user_id']);
			}
			$worker = $user = D('Shopworker')->where(array('user_id'=>$user['user_id']))->find();
			if(!empty($worker) && $worker['status'] !=0 ){
				$this->baoError('该人员已经属于其他公司了！');
			}
			$worker = array();
			$worker = $user = D('Shopworker')->where(array('user_id'=>$user['user_id'],'shop_id'=>$this->shop_id))->find();
			if(!empty($worker)){
				$this->baoError('你已经添加了该员工，不能重复添加！');
			}
		
			
			
            $data['status'] = 0;
            $obj = D('Shopworker');
			$result = $obj->add($data);
            if ($result) {
				$url = U('mcenter/information/worker',array('worker_id'=>$result));
				$arr = array();
				$arr['send_id'] = 0;
				$arr['user_id'] = $data['user_id'];
				$arr['parent_id'] = 0;
				$arr['content'] = $this->shop['shop_name'].'希望你们成为他的员工，点击链接同意：<a href="'.$url.'">查看详情</a>';
				$arr['create_time'] = time();
				$msg_id = D('Message')->add($arr);
				
				if($msg_id){
					$this->baoSuccess('添加成功，已经为该用户发送系统信息，等待他确认！', U('worker/index'));
				}else{
					$this->baoError('操作失败！');
				}
				
				 
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }
	
	
    public function edit($worker_id = 0) {
        if (empty($worker_id)) {
            $this->error('请选择需要编辑的内容操作');
        }
        $worker_id = (int) $worker_id;
        $obj = D('Shopworker');
        $detail = $obj->find($worker_id);
        if (empty($detail) || $detail['shop_id'] != $this->shop_id) {
            $this->error('请选择需要编辑的内容操作');
        }
        if ($this->isPost()) {
            $data = $this->editCheck();
            $data['worker_id'] = $worker_id;
            if (false !== $obj->save($data)) {
                $this->baoSuccess('操作成功', U('worker/edit', array('worker_id' => $worker_id)));
            }
            $this->baoError('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }
	
	
	public function delete($worker_id = 0){
        if (empty($worker_id)) {
            $this->error('访问错误！');
        }
		$worker = D('Shopworker')->find($worker_id);
        if (empty($worker)) {
            $this->error('访问错误！');
        }
        if ($worker['shop_id'] != $this->shop_id ) {
            $this->error('没有权限访问错误！');
        }
		D('Shopworker')->where(array('worker_id'=>$worker['worker_id']))->delete();
		$this->success('员工信息删除成功！', U('worker/index'));
	}
	
	
	
	
	
    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['shop_id'] = $this->shop_id;
        if (empty($data['user_id'])) {
            $this->baoError('用户编号不能为空');
        }

        if (empty($data['name'])) {
            $this->baoError('姓名不能为空');
        }
        if (empty($data['mobile'])) {
            $this->baoError('手机号码不能为空');
        }
        if (empty($data['work'])) {
            $this->baoError('员工职务不能为空');
        }
        if (empty($data['addr'])) {
            $this->baoError('联系地址不能为空');
        }

        return $data;
    }
	

}
