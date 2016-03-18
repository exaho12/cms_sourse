<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MessageAction extends CommonAction {

	public function index() {


		$this->display();
	}

	public function msgshow($msg_id) {
		$msg_id = (int) $msg_id;
		if (!$detail = D('Msg')->find($msg_id)) {
			$this->error('消息不存在');
		}
		if ($detail['user_id'] != $this->uid && $detail['user_id'] != 0) {
			$this->error('您没有权限查看该消息');
		}
		if (!D('Msgread')->find(array('user_id' => $this->uid, 'msg_id' => $msg_id))) {
			D('Msgread')->add(array(
				'user_id' => $this->uid,
				'msg_id' => $msg_id,
				'create_time' => NOW_TIME,
				'create_ip' => get_client_ip()
			));
		}
		if ($detail['link_url']) {
			header("Location:" . $detail['link_url']);
			die;
		}
		$this->assign('detail', $detail);
		$this->display();
	}

  
}