<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class GoodsAction extends CommonAction {
	 private $create_fields = array('title', 'photo', 'cate_id', 'price', 'shopcate_id', 'mall_price','use_integral', 'instructions', 'details', 'end_date');
    private $edit_fields = array('title', 'photo', 'cate_id', 'price', 'shopcate_id', 'mall_price','use_integral','instructions', 'details', 'end_date');
	  public function _initialize() {
        parent::_initialize();
        $this->autocates = D('Goodsshopcate')->where(array('shop_id' => $this->shop_id))->select();
        $this->assign('autocates', $this->autocates);
    }
 public function index() {
        $Goods = D('Goods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'shop_id' => $this->shop_id, 'is_mall' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = array('IN', D('Goodscate')->getChildren($cate_id));
            $this->assign('cate_id', $cate_id);
        }

        if ($audit = (int) $this->_param('audit')) {
            $map['audit'] = ($audit === 1 ? 1 : 0);
            $this->assign('audit', $audit);
        }
        $count = $Goods->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Goods->where($map)->order(array('goods_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val = $Goods->_format($val);
            $list[$k] = $val;
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('cates', D('Goodscate')->fetchAll());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板   
}
public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Goods');
            if ($obj->add($data)) {
                $this->success('添加成功', U('goods/index'));
            }
            $this->error('操作失败！');
        } else {
            $this->assign('cates', D('Goodscate')->fetchAll());
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->error('产品名称不能为空');
        }
        $data['shop_id'] = $this->shop_id;
        $admin = D('Admin')->where(array('role_id' => 1))->find();
        $shopdetail = D('Shop')->find($this->shop_id);
        if ($shopdetail['is_mall'] == 0) {
            $this->error('您还没有入驻商城，请联系管理员开通，电话' . $admin['mobile']);
        }
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->error('请选择分类');
        }
        $data['shopcate_id'] = (int) $data['shopcate_id'];
        $data['area_id'] = $this->shop['area_id'];
        $data['business_id'] = $this->shop['business_id'];
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->error('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->error('缩略图格式不正确');
        } 
        $data['price'] = (int) ($data['price'] * 100);
        if (empty($data['price'])) {
            $this->error('市场价格不能为空');
        } $data['mall_price'] = (int) ($data['mall_price'] * 100);
        if (empty($data['mall_price'])) {
            $this->error('商城价格不能为空');
        }$data['commission'] = (int) ($data['commission'] * 100);
        if ($data['commission'] < 0) {
            $this->error('佣金不能为负数');
        } $data['instructions'] = SecurityEditorHtml($data['instructions']);
        if (empty($data['instructions'])) {
            $this->error('购买须知不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['instructions'])) {
            $this->error('购买须知含有敏感词：' . $words);
        } $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->error('商品详情不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->error('商品详情含有敏感词：' . $words);
        } $data['end_date'] = htmlspecialchars($data['end_date']);
        if (empty($data['end_date'])) {
            $this->error('过期时间不能为空');
        }
        if (!isDate($data['end_date'])) {
            $this->error('过期时间格式不正确');
        }
        $data['use_integral'] = (int)$data['use_integral'];
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['sold_num'] = 0;
        $data['view'] = 0;
        $data['is_mall'] = 1;
        return $data;
    }

    public function edit($goods_id = 0) {
        if ($goods_id = (int) $goods_id) {
            $obj = D('Goods');
            if (!$detail = $obj->find($goods_id)) {
                $this->error('请选择要编辑的商品');
            }
            if ($detail['shop_id'] != $this->shop_id) {
                $this->error('请不要试图越权操作其他人的内容');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['goods_id'] = $goods_id;
                if (false !== $obj->save($data)) {
                    $this->success('操作成功', U('goods/index'));
                }
                $this->error('操作失败');
            } else {
                $this->assign('detail', $obj->_format($detail));
                $this->assign('cates', D('Goodscate')->fetchAll());
                $this->display();
            }
        } else {
            $this->error('请选择要编辑的商品');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['title'] = htmlspecialchars($data['title']);
		
        if (empty($data['title'])) {
            $this->error('产品名称不能为空');
        } $data['shop_id'] = (int) $this->shop_id;
        if (empty($data['shop_id'])) {
            $this->error('商家不能为空');
        }
        $admin = D('Admin')->where(array('role_id' => 1))->find();
        $shopdetail = D('Shop')->find($this->shop_id);
        if ($shopdetail['is_mall'] == 0) {
            $this->error('您还没有入驻商城，请联系管理员开通，电话' . $admin['mobile']);
        }
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->error('请选择分类');
        }
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->error('请选择分类');
        }
        $data['shopcate_id'] = (int) $data['shopcate_id'];
        $data['area_id'] = $this->shop['area_id'];
        $data['business_id'] = $this->shop['business_id'];
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->error('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->error('缩略图格式不正确');
        } $data['price'] = (int) ($data['price'] * 100);
        if (empty($data['price'])) {
            $this->error('市场价格不能为空');
        } $data['mall_price'] = (int) ($data['mall_price'] * 100);
        if (empty($data['mall_price'])) {
            $this->error('商城价格不能为空');
        }$data['commission'] = (int) ($data['commission'] * 100);
        if ($data['commission'] < 0) {
            $this->error('佣金不能为负数');
        }$data['instructions'] = SecurityEditorHtml($data['instructions']);
        if (empty($data['instructions'])) {
            $this->error('购买须知不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['instructions'])) {
            $this->error('购买须知含有敏感词：' . $words);
        } $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->error('商品详情不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->error('商品详情含有敏感词：' . $words);
        } $data['end_date'] = htmlspecialchars($data['end_date']);
        if (empty($data['end_date'])) {
            $this->error('过期时间不能为空');
        }
        if (!isDate($data['end_date'])) {
            $this->error('过期时间格式不正确');
        }
        $data['use_integral'] = (int)$data['use_integral'];
        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }
	public function delete($goods_id=0) {
		  if (is_numeric($goods_id) && ($goods_id = (int) $goods_id)) {
            $obj = D('Goods');
            $obj->delete($goods_id);
            $this->success('删除成功！', U('goods/index'));
        } else {
            $goods_id = $this->_post('goods_id', false);
            if (is_array($goods_id)) {
                $obj = D('Goods');
                foreach ($goods_id as $id) {
                    $obj->delete($id);
                }
                $this->success('删除成功！', U('goods/index'));
            }
            $this->success('请选择要删除的文章');
        }
    }
	}


