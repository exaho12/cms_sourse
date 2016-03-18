<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanAction extends CommonAction {

    public function ordercode() {
        $Tuancode = D('Tuancode');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid); //这里只显示 实物
        $count = $Tuancode->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuancode->where($map)->order(array('code_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $val) {
            $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
        }
        $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function coderefund($code_id) {
        $code_id = (int) $code_id;
        if ($detail = D('Tuancode')->find($code_id)) {
            if ($detail['user_id'] != $this->uid) {
                $this->baoError('非法操作');
            }
            if ($detail['status'] != 0 || $detail['is_used'] != 0) {
                $this->baoError('该抢购券不能申请退款');
            }
            if (D('Tuancode')->save(array('code_id' => $code_id, 'status' => 1))) {
                $this->baoSuccess('申请成功！等待网站客服处理！', U('member/ordercode'));
            }
        }
        $this->baoError('操作失败');
    }

    
}
