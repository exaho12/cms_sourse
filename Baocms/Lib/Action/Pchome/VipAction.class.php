<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class VipAction extends CommonAction {

    public function index() {
        $shop = D('Shop');
        import('ORG.Util.Pageabc'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1,'card_date' => array('EGT', TODAY));
        $count = $shop->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 12); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $shop->where($map)->order(array('orderby'=>'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
        }
        if ($shop_ids) {
            $this->assign('shopdetail', D('Shopdetails')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        $this->display();
    }


}
