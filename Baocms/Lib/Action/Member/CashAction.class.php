<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CashAction extends CommonAction {

  
    public function index() {
        $Users = D('Users');
        $data = $Users->find($this->uid);
        if (IS_POST){
            $money = abs((int)($_POST['money']*100));
            if ($money == 0){
                $this->baoError('提现金额不合法');
            }
            if ($money > $data['money'] || $data['money'] == 0) {
                $this->baoError('余额不足，无法提现');
            }
            if(!$data['bank_name'] = htmlspecialchars($_POST['bank_name'])){
                $this->baoError('开户行不能为空'); 
            }
            if(!$data['bank_num'] = htmlspecialchars($_POST['bank_num'])){
                $this->baoError('银行账号不能为空'); 
            }
            
            if(!$data['bank_realname'] = htmlspecialchars($_POST['bank_realname'])){
                $this->baoError('开户姓名不能为空'); 
            }
            $data['bank_branch'] = htmlspecialchars($_POST['bank_branch']);
            $data['user_id'] = $this->uid;
            
            
            $arr = array();
            $arr['user_id'] = $this->uid;
            $arr['money'] = $money;
            $arr['addtime'] = NOW_TIME;
            $arr['account'] = $data['account'];
            $arr['bank_name'] = $data['bank_name'];
            $arr['bank_num'] = $data['bank_num'];
            $arr['bank_realname'] = $data['bank_realname'];
            $arr['bank_branch'] = $data['bank_branch'];
            D('Userscash')->add($arr);
            
            D('Usersex')->save($data);
            
            //扣除余额
            $Users->addMoney($data['user_id'], -$money, '申请提现，扣款');
            $this->baoSuccess('申请成功', U('logs/cashlogs'));
        } else {
            $this->assign('money', $data['money'] / 100);
            $this->assign('info',D('Usersex')->getUserex($this->uid));
            $this->display();
        }
    }

}
