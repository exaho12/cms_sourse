<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ConvenientphoneAction extends CommonAction {

	public function index() {
		$this->assign('nextpage', LinkTo('Convenientphone/loaddata', array('t' => NOW_TIME, 'community_id' => $this->community_id, 'p' => '0000')));
		$map=array('community_id' => $this->community_id);
		$community=D('community')->where($map)->find();
		$this->assign('list',$community);
		$this->display(); // 输出模板 
	}

	public function loaddata() {
		$convenientphone = D('Convenientphone');
		$convenientphonemaps = D('Convenientphonemaps');
		import('ORG.Util.Page'); // 导入分页类
		$map = array('community_id' => $this->community_id);
		//$map2 = $convenientphonemaps->where($map)->select();
		$count = $convenientphonemaps->where($map)->count(); // 查询满足要求的总记录数 
		$Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $convenientphonemaps->order(array('phone_id' => 'desc'))->where($map)->select();//小区所有的电话
		$phone_ids = array();
		foreach($list as $k=>$val){
			$phone_ids[$val['phone_id']] = $val['phone_id'];
		}
		if(!empty($phone_ids)){
			$this->assign('phones',$convenientphone->itemsByIds($phone_ids));
		}//逐条释放对应所有信息
		$this->assign('list', $list); // 赋值数据集
		$this->assign('page', $show); // 赋值分页输出
		$this->display(); // 输出模板   
	}

	public function delete() {
		$id=(int)($_GET['phone_id']);
		$res=D('Convenientphonemaps')->delete($id);
		if($res){
			$this->success("删除成功!");
			
		}
	}
	
	 public function create() {
        if ($this->isPost()) {
            $data = $this->checkCreate();
            $convenientphone=D('Convenientphone');
		    $convenientphonemaps=D('Convenientphonemaps');
            if ($phone_id = $convenientphone->add($data)){
				$data = $this->checkCreate();  
				$data['phone_id']=$phone_id;
			    if($convenientphonemaps->add($data)){
				$this->success('添加成功', U('convenientphone/index'));}
				}
            $this->error('操作失败！');
        }else{
            $this->display();
        }
    }

    public function checkCreate() {
        $data = $this->checkFields($this->_post('data', false), array('name', 'phone', 'expiry_date'));
        $data['community_id']=(int)$this->community_id;
        $data['name']=$data['name'];
		$data['phone']=htmlspecialchars($data['phone']);
        if (empty($data['name'])){
            $this->error('名称不能为空');
        }
        if (empty($data['phone'])) {
            $this->error('电话不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['name'])) {
            $this->error('名称含有敏感词：' . $words);
        }
        return $data;
    }
	
		}