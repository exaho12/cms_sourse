<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MessageAction extends CommonAction {
	
	public function index() {
        $this->display(); // 输出模板
	}

	public function load() {
		$Msg = D('Msg');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('is_fenzhan'=>0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
		
        $count = $Msg->where($map)->count(); // 查询满足要求的总记录数
		$Page = new Page($count, 6); // 实例化分页类 传入总记录数和每页显示的 
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
        $msgs = $Msg->where($map)->order(array('msg_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('msgs', $msgs); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('types', $Msg->getType());
        $this->display(); // 输出模板
	}

	public function msgshow($msg_id) {
		$msg_id = (int) $msg_id;
		D('Msg')->updateCount($msg_id, 'views');
		if (!$detail = D('Msg')->find($msg_id)) {
			$this->error('消息不存在');
		}
		if ($detail['user_id'] != $this->uid && $detail['user_id'] != 0  ) {
			$this->error('您没有权限查看该消息');
		}
		if (!empty($detail['city_id'])) {
			$this->error('消息属于代理商的，您无权查看！');
		}
		if (!D('Msgread')->find(array('user_id' => $this->uid, 'msg_id' => $msg_id))) {
			D('Msgread')->add(array('user_id' => $this->uid,'msg_id' => $msg_id,'create_time' => NOW_TIME,'create_ip' => get_client_ip()));
		}
		if ($detail['link_url']) {
			header("Location:" . $detail['link_url']);
			die;
		}
		$this->assign('detail', $detail);
		$this->display();
	}

  
}