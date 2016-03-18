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
        if (IS_POST) {
            $money = abs((int) $_POST['money']);
            if ($money == 0) {
                $this->niuMsg('提现金额不能为0');
            }
            $money *= 100;
            if ($money > $this->member['money'] || $this->member['money'] == 0) {
                $this->niuMsg('余额不足，无法提现');
            }
             if(!$data['bank_name'] = htmlspecialchars($_POST['bank_name'])){
                $this->niuMsg('开户行不能为空'); 
            }
            if(!$data['bank_num'] = htmlspecialchars($_POST['bank_num'])){
                $this->niuMsg('银行账号不能为空'); 
            }
            
            if(!$data['bank_realname'] = htmlspecialchars($_POST['bank_realname'])){
                $this->niuMsg('开户姓名不能为空'); 
            }
            $data['bank_branch'] = htmlspecialchars($_POST['bank_branch']);
            $data['user_id'] = $this->uid;
            $arr = array();
            $arr['user_id'] = $this->uid;
            $arr['money'] = $money;
            $arr['addtime'] = NOW_TIME;
            $arr['account'] = $this->member['account'];
            $arr['bank_name'] = $data['bank_name'];
            $arr['bank_num'] = $data['bank_num'];
            $arr['bank_realname'] = $data['bank_realname'];
            $arr['bank_branch'] = $data['bank_branch'];
            D('Userscash')->add($arr);
             D('Usersex')->save($data);
            //扣除余额
            $Users->addMoney($this->member['user_id'], -$money, '申请提现，扣款');
            $this->niuMsg('申请成功', U('cash/cashlog'));
        } else {
            $this->assign('info',D('Usersex')->getUserex($this->uid));
            $this->assign('money', $this->member['money'] / 100);
            $this->display();
        }
    }

    public function cashlog() {
        $this->display();
    }

    public function cashlogloaddata() {
        $Userscash = D('Userscash');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

}