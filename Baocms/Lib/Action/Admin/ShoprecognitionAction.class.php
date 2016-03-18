<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShoprecognitionAction extends CommonAction {

    private $create_fields = array('user_id', 'shop_id', 'name', 'mobile','content','reply','recognition', 'create_time', 'create_ip');
    private $edit_fields = array('user_id', 'shop_id', 'name', 'mobile','content','reply','recognition',);

    public function index() {
        $Shoprecognition = D('Shoprecognition');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if($keyword = $this->_param('keyword','htmlspecialchars')){
            $map['name|mobile'] = array('LIKE','%'.$keyword.'%');
            $this->assign('keyword',$keyword);
        }
        if ($shop_id = (int) $this->_param('shop_id')) {
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }
        if ($user_id = (int) $this->_param('user_id')) {
            $map['user_id'] = $user_id;
            $user = D('Users')->find($user_id);
            $this->assign('nickname', $user['nickname']);
            $this->assign('user_id', $user_id);
        }
        $count = $Shoprecognition->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shoprecognition->where($map)->order(array('recognition_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $shop_ids  = array();
        foreach($list  as $k=>$val){            
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;          
            $user_ids[$val['user_id']] = $val['user_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        if(!empty($user_ids)){
            $this->assign('users',D('Users')->itemsByIds($user_ids));
        }
        if(!empty($shop_ids)){
            $this->assign('shops',D('Shop')->itemsByIds($shop_ids));
        }
        
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

   

    public function edit($recognition_id = 0) {
        if ($recognition_id = (int) $recognition_id) {
            $obj = D('Shoprecognition');
            if (!$detail = $obj->find($recognition_id)) {
                $this->baoError('请选择要编辑的商家认领');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['recognition_id'] = $recognition_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('Shoprecognition/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->assign('user', D('Users')->find($detail['user_id']));
                $this->assign('shop', D('Shop')->find($detail['shop_id']));
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的商家预约');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['user_id'] = (int) $data['user_id'];
        $data['shop_id'] = (int) $data['shop_id'];
        if (empty($data['shop_id'])) {
            $this->baoError('商家不能为空');
        } $data['name'] = htmlspecialchars($data['name']);
        if (empty($data['name'])) {
            $this->baoError('称呼不能为空');
        } $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
            $this->baoError('手机不能为空');
        }
        if (!isMobile($data['mobile'])) {
            $this->baoError('手机格式不正确');
        }        
        $data['content'] = htmlspecialchars($data['content']);
        $data['reply'] = htmlspecialchars($data['reply']);
        if(empty($data['reply']) || empty($data['reply'])){
            $this->baoError('还是说拒绝理由吧！');           
        }        
        return $data;
    }

    public function delete($recognition_id = 0) {
        if (is_numeric($recognition_id) && ($recognition_id = (int) $recognition_id)) {
            $obj = D('Shoprecognition');
            $obj->delete($recognition_id);
            $this->baoSuccess('删除成功！', U('Shoprecognition/index'));
        } else {
            $yuyue_id = $this->_post('yuyue_id', false);
            if (is_array($recognition_id)) {
                $obj = D('Shopyuyue');
                foreach ($recognition_id as $id) {
                    $obj->delete($id);
                }
                $this->baoSuccess('删除成功！', U('Shoprecognition/index'));
            }
            $this->baoError('请选择要删除的认领');
        }
    }
	
	//审核商家
	public function audit($recognition_id = 0) {
        if (is_numeric($recognition_id) && ($recognition_id = (int) $recognition_id)) {
            $obj = D('Shoprecognition');
			$obj_shop = D('Shop');
			//这里应该邮件通知会员的id，后面做！
		    $user = $obj->find($r['user_id']);
			$user_id = $user['user_id'];
			$user_ids = D('Users')->find($user_id);//获得申请人id
			$user_email = $user_ids['email'];//获得申请人的邮件
			$user_mobile = $user_ids['mobile'];//获得申请人的手机
			
			$shop_id = $user['shop_id'];//商家id
			$shops = D('Shop')->find($shop_id);
			$shop_name = $shops['shop_name'];//商家名字
			
			$name = $user['name'];//姓名
			$mobile = $user['mobile'];//姓名
			$content = $user['content'];//申请理由
			$reply = $user['reply'];//管理员回复
		
			
			if(!empty($user_mobile)){
                D('Sms')->sendSms('sms_recognition',$user_mobile,array(
				    'shop_name'=>$shop_name,
					'name'=>$name,
					'mobile'=>$mobile,
					'content'=>$content,
					'reply'=>$reply
				));
		   }

		    if(!empty($user_email)){
                 D('Email')->sendMail('email_recognition', $user_email, '商家认领成功的通知', array(
				    'shop_name'=>$shop_name,
					'name'=>$name,
					'mobile'=>$mobile,
					'content'=>$content,
					'reply'=>$reply
				 ));
             }
			//$obj_shop->save(array('shop_id' => $shop_id, 'user_id' => $user_id));//这一步暂时关闭，怕自动化影响突发情况影响不好，虽然可以自动修改！
			$obj_shop->save(array('shop_id' => $shop_id, 'recognition' => 1));//更新商家认领情况recognitio
			
			$obj->save(array('recognition_id' => $recognition_id, 'audit' => 1));
            $this->baoSuccess('审核认领成功！', U('Shoprecognition/index'));
        } else {
            $recognition_id = $this->_post('recognition_id', false);
            if (is_array($recognition_id)) {
                $obj = D('Activity');
                foreach ($recognition_id as $id) {
                    $obj->save(array('recognition_id' => $id, 'audit' => 1));
                }
                $this->baoSuccess('审核成功！', U('Shoprecognition/index'));
            }
            $this->baoError('请选择要审核的认领');
        }
    }

}
