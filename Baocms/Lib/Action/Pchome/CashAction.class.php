<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CashAction extends CommonAction{
      public function cashlog(){
        $Userscash = D('Userscash');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id'=>$this->uid);
        $count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }
    
    public function cash(){
        $Users = D('Users');
        $data = $Users->find($this->uid);
        if (IS_POST)
        {
            $money = (int)$_POST['money'];
            if ($money == 0)
            {
                $this->error('提现金额不能为0');
            }
            $money *= 100;
            if ($money > $data['money'] || $data['money'] == 0)
            {
                $this->error('余额不足，无法提现');
            }
            $arr = array();
            $arr['user_id'] = $this->uid;
            $arr['money']   = $money;
            $arr['addtime'] = NOW_TIME;
            $arr['account'] = $data['account'];
            $arr['bank_name'] = $data['bank_name'];
            $arr['bank_num'] = $data['bank_num'];
            $arr['bank_realname'] = $data['bank_realname'];
            $arr['bank_branch'] = $data['bank_branch'];
            D('Userscash')->add($arr);
            //扣除余额
            $Users->addMoney($data['user_id'], -$money, '申请提现，扣款');
            $this->success('申请成功', U('cash/cash'));
        }
        else
        {
            $this->assign('money', $data['money'] / 100);
            $this->display();
        }
    }


}