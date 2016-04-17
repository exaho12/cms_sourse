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
		
		$shop = D('Shop')->where(array('user_id' => $this->uid))->find();
		
		    if($shop == ''){
				$cash_money = $this->_CONFIG['cash']['user'];
				$cash_money_big = $this->_CONFIG['cash']['user_big'];
			}elseif($shop['is_renzheng'] == 0){
				$cash_money = $this->_CONFIG['cash']['shop'];
				$cash_money_big = $this->_CONFIG['cash']['shop_big'];
			}elseif($shop['is_renzheng'] == 1){
				$cash_money = $this->_CONFIG['cash']['renzheng_shop'];
				$cash_money_big = $this->_CONFIG['cash']['renzheng_shop_big'];
			}else{
				$cash_money = $this->_CONFIG['cash']['user'];	
				$cash_money_big = $this->_CONFIG['cash']['user_big'];
			}
		
		
        if (IS_POST){
            $money = abs((int)($_POST['money']*100));
            if ($money == 0){
                $this->baoError('提现金额不合法');
            }
			
			if ($money < $cash_money*100){
                $this->baoError('提现金额小于最低提现额度');
            }
			
			if ($money > $cash_money_big*100){
                $this->baoError('您单笔最多能提现'.$cash_money_big.'元');
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
			$arr['type'] = user;
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
            $this->assign('cash_money', $cash_money);
			$this->assign('cash_money_big', $cash_money_big);
			$this->assign('money', $data['money'] / 100);
            $this->assign('info',D('Usersex')->getUserex($this->uid));
            $this->display();
        }
    }

}
