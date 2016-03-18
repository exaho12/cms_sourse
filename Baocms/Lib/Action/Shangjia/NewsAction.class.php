<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class NewsAction extends CommonAction {

    private $edit_fields = array('title', 'photo', 'details');

    public function index() {
        $Shopnews = D('Shopnews');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id' => $this->shop_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Shopnews->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Shopnews->where($map)->order(array('news_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', D('Shopcate')->fetchAll());
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->editCheck(); //这里和 编辑的字段差不多
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $obj = D('Shopnews');
            if ($news_id = $obj->add($data)) {
                D('Shopfavorites')->save(array('last_news_id'=>$news_id),array('where'=>array( //更新粉丝表里面的动态
                    'shop_id' => $this->shop_id,
                )));
                $this->baoSuccess('添加成功', U('news/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    public function edit($news_id = 0) {
        if (empty($news_id)) {
            $this->error('请选择需要编辑的内容操作');
        }
        $news_id = (int) $news_id;
        $obj = D('Shopnews');
        $detail = $obj->find($news_id);
        if (empty($detail) || $detail['shop_id'] != $this->shop_id) {
            $this->error('请选择需要编辑的内容操作');
        }
        if ($this->isPost()) {

            $data = $this->editCheck();
            $data['news_id'] = $news_id;
            if (false !== $obj->save($data)) {
                $this->baoSuccess('操作成功', U('news/edit', array('news_id' => $news_id)));
            }
            $this->baoError('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['shop_id'] = $this->shop_id;
        $data['cate_id'] = $this->shop['cate_id'];
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
        } $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        } $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('详细内容含有敏感词：' . $words);
        }
        return $data;
    }

}
