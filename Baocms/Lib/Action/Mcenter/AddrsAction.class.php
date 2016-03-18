<?php
class AddrsAction extends CommonAction {

	public function index() {
		$u = D('Users');
		$ud = D('UserAddr');
                $addr = $ud -> where('user_id='.$this->uid) -> select();
                $this->assign('addr',$addr);
		$this->display(); // 输出模板
	}
  
}