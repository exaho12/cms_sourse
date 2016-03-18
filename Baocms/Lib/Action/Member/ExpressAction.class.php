<?php



/*

 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！

 * 作者：baocms团队

 * 官网：www.baocms.com

 * 邮件: youge@baocms.com  QQ 800026911

 */



class ExpressAction extends CommonAction {

	protected function _initialize() {
        parent::_initialize();
		$express = (int)$this->_CONFIG['operation']['express'];
		if ($express == 0) {
				$this->error('此功能已关闭');
				die;
			}
     }

    public function index() {

        $express = D('Express');

        import('ORG.Util.Page'); // 导入分页类

        $map = array('user_id' => $this->uid,'closed'=>0); //分类信息是关联到UID 的 

        $count = $express->where($map)->count(); // 查询满足要求的总记录数

        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数

        $show = $Page->show(); // 分页显示输出

        $list = $express->where($map)->order(array('express_id' => 'desc'))->select();

        $this->assign('list', $list); // 赋值数据集

        $this->assign('page', $show); // 赋值分页输出

        $this->display(); // 输出模板

    }



    public function create() {

        if ($this->isPost()) {

            $data = $this->createCheck();

            if($express_id = D('Express')->add($data)){

                $this->baoSuccess('发布成功',U('express/index'));

            }

            $this->baoError('发布失败');

        } else {
			$this->assign('useraddr', D('Useraddr')->where(array('user_id' => $this->uid,'is_default' => 1))->limit(0,1)->select());
            $this->display();

        }

    }

    

    public function createCheck(){

        $data = $this->_post('data', false);

        $data['title'] = htmlspecialchars($data['title']);

        if (empty($data['title'])) {

            $this->baoError('标题不能为空');

        }

        $data['from_name'] = htmlspecialchars($data['from_name']);

        if (empty($data['from_name'])) {

            $this->baoError('寄件人姓名不能为空');

        }

        $data['from_addr'] = htmlspecialchars($data['from_addr']);

        if (empty($data['from_addr'])) {

            $this->baoError('寄件人地址不能为空');

        }

        $data['from_mobile'] = htmlspecialchars($data['from_mobile']);

        if (empty($data['from_mobile'])) {

            $this->baoError('寄件人手机不能为空');

        }

        if (!isMobile($data['from_mobile'])) {

            $this->baoError('寄件人手机格式不正确');

        }

        $data['to_name'] = htmlspecialchars($data['to_name']);

        if (empty($data['to_name'])) {

            $this->baoError('收件人姓名不能为空');

        }

        $data['to_addr'] = htmlspecialchars($data['to_addr']);

        if (empty($data['to_addr'])) {

            $this->baoError('收件人地址不能为空');

        }

        $data['to_mobile'] = htmlspecialchars($data['to_mobile']);

        if (empty($data['to_mobile'])) {

            $this->baoError('收件人手机不能为空');

        }

        if (!isMobile($data['to_mobile'])) {

            $this->baoError('收件人手机格式不正确');

        }

        $data['city_id'] = $this->city_id;

        $data['area_id'] = $data['area_id'];

        $data['business_id'] = $data['business_id'];

        $data['user_id'] = $this->uid;

        $data['create_time'] = NOW_TIME;

        $data['create_ip'] = get_client_ip();

        return $data;

    }



    public function edit($express_id) {

        if ($express_id = (int) $express_id) {

            $obj = D('Express');

            if (!$detail = $obj->find($express_id)) {

                $this->baoError('请选择要编辑的快递');

            }

            if ($detail['status'] != 0) {

                $this->baoError('该快递状态不允许被编辑');

            }

            if ($detail['closed'] == 1) {

                $this->baoError('该快递已被删除');

            }

            if ($this->isPost()) {

                $data = $this->editCheck();

                $data['express_id'] = $express_id;

                if (false !== $obj->save($data)) {

                    $this->baoSuccess('操作成功',U('express/index'));

                }

                $this->baoError('操作失败');

            } else {

                $this->assign('detail', $detail);

                $this->display();

            }

        } else {

            $this->baoError('请选择要编辑的快递信息');

        }

    }

    

    public function editCheck(){

        $data = $this->_post('data', false);

        $data['title'] = htmlspecialchars($data['title']);

        if (empty($data['title'])) {

            $this->baoError('标题不能为空');

        }

        $data['from_name'] = htmlspecialchars($data['from_name']);

        if (empty($data['from_name'])) {

            $this->baoError('寄件人姓名不能为空');

        }

        $data['from_addr'] = htmlspecialchars($data['from_addr']);

        if (empty($data['from_addr'])) {

            $this->baoError('寄件人地址不能为空');

        }

        $data['from_mobile'] = htmlspecialchars($data['from_mobile']);

        if (empty($data['from_mobile'])) {

            $this->baoError('寄件人手机不能为空');

        }

        if (!isMobile($data['from_mobile'])) {

            $this->baoError('寄件人手机格式不正确');

        }

        $data['to_name'] = htmlspecialchars($data['to_name']);

        if (empty($data['to_name'])) {

            $this->baoError('收件人姓名不能为空');

        }

        $data['to_addr'] = htmlspecialchars($data['to_addr']);

        if (empty($data['to_addr'])) {

            $this->baoError('收件人地址不能为空');

        }

        $data['to_mobile'] = htmlspecialchars($data['to_mobile']);

        if (empty($data['to_mobile'])) {

            $this->baoError('收件人手机不能为空');

        }

        if (!isMobile($data['to_mobile'])) {

            $this->baoError('收件人手机格式不正确');

        }

        $data['city_id'] = $this->city_id;

        $data['area_id'] = $data['area_id'];

        $data['business_id'] = $data['business_id'];

        return $data;

    }

    



    public function delete($express_id) {

        if (is_numeric($express_id) && ($express_id = (int) $express_id)) {

            $obj = D('Express');

            if(!$detail = $obj->find($express_id)){

                $this->baoError('快递不存在');

            }

            if($detail['closed'] == 1 ||($detail['status'] !=0&&$detail['status'] !=2)){

                $this->baoError('该快递状态不允许被删除');

            }

            $obj->save(array('express_id' => $express_id, 'closed' => 1));

            $this->baoSuccess('删除成功！', U('express/index'));

        } 

    }



}

