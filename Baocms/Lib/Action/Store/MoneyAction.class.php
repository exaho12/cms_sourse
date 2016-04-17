<?php

class MoneyAction extends CommonAction{
	
	 public function index(){
		$bg_time = strtotime(TODAY);
		$counts = array();
			//财务管理
		$counts['money'] = (int) D('Shopmoney')->where(array('shop_id' => $this->shop_id))->sum('money');

		$counts['money_goods'] = (int) D('Shopmoney')->where(array('type'=>goods,'shop_id' => $this->shop_id))->sum('money');
		$counts['money_tuan'] = (int) D('Shopmoney')->where(array('type'=>tuan,'shop_id' => $this->shop_id))->sum('money');
		$counts['money_ele'] = (int) D('Shopmoney')->where(array('type'=>ele,'shop_id' => $this->shop_id))->sum('money');
		$counts['money_ding'] = (int) D('Shopmoney')->where(array('type'=>ding,'shop_id' => $this->shop_id))->sum('money');
		
		$counts['money_day'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'shop_id' => $this->shop_id,
			))->sum('money');
			
		$counts['money_day_goods'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'shop_id' => $this->shop_id,
				'type'=>goods,
			))->sum('money');
			
		$counts['money_day_tuan'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'shop_id' => $this->shop_id,
				'type'=>tuan,
			))->sum('money');
		$counts['money_day_ele'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'shop_id' => $this->shop_id,
				'type'=>ele,
			))->sum('money');
		$counts['money_day_ding'] = (int) D('Shopmoney')->where(array(
				'create_time' => array(array('ELT', NOW_TIME), array('EGT', $bg_time)),
				'shop_id' => $this->shop_id,
				'type'=>ding,
			))->sum('money');
		$this->assign('counts', $counts);
		$this->display();

   }
   
	
    public function detail(){
        $map = array('shop_id' => $this->shop_id);
        if (($bg_date = $this->_param('bg_date', 'htmlspecialchars')) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date);
            $map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
            $this->assign('bg_date', $bg_date);
            $this->assign('end_date', $end_date);
        } else {
            if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
                $bg_time = strtotime($bg_date);
                $this->assign('bg_date', $bg_date);
                $map['create_time'] = array('EGT', $bg_time);
            }
            if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
                $end_time = strtotime($end_date);
                $this->assign('end_date', $end_date);
                $map['create_time'] = array('ELT', $end_time);
            }
        }
        $shopmoney = D('Shopmoney');
        import('ORG.Util.Page');
 
        $count = $shopmoney->where($map)->count();
        $Page = new Page($count, 16);
        $show = $Page->show();
        $list = $shopmoney->where($map)->order(array('money_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
		
		$sum = $shopmoney->where($map)->sum('money');
        $this->assign('sum',$sum);
		
		
        $this->display();
   }
   
   
    public function cashlogs(){
		$map = array('shop_id' => $this->shop_id,'type'=>shop);
        if (($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date);
            $map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
            $this->assign('bg_date', $bg_date);
            $this->assign('end_date', $end_date);
        } else {
            if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
                $bg_time = strtotime($bg_date);
                $this->assign('bg_date', $bg_date);
                $map['create_time'] = array('EGT', $bg_time);
            }
            if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
                $end_time = strtotime($end_date);
                $this->assign('end_date', $end_date);
                $map['create_time'] = array('ELT', $end_time);
            }
        }
        $Userscash = D('Userscash');
        import('ORG.Util.Page'); // 导入分页类
        $count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();


   }
   
   
   public function cash(){
           $user_ids = D('Shop')->where(array('shop_id' => $this->shop_id))->find();
		   $user_id = $user_ids['user_id'];
           $shop = D('Shop')->where(array('user_id' => $user_id))->find();
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
            $gold = (int) ($_POST['gold'] * 100);
            if ($gold <= 0){
                $this->niuMsg('提现金额不合法');
            }
			if ($gold < $cash_money*100){
                $this->niuMsg('提现金额小于最低提现额度');
            }
			if ($gold > $cash_money_big*100){
                $this->niuMsg('您单笔最多能提现'.$cash_money_big.'元');
            }
			
            if ($gold > $this->member['gold'] || $this->member['gold'] == 0){
                $this->niuMsg('商户资金不足，无法提现');
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
				$arr['shop_id'] = $this->shop_id;//提现商家
				$arr['city_id'] = $shop['city_id'];
				$arr['area_id'] = $shop['area_id'];
				$arr['money'] = $gold;
				$arr['type'] = shop;
				$arr['addtime'] = NOW_TIME;
				$arr['account'] = $this->member['account'];
				$arr['bank_name'] = $data['bank_name'];
				$arr['bank_num'] = $data['bank_num'];
				$arr['bank_realname'] = $data['bank_realname'];
				$arr['bank_branch'] = $data['bank_branch'];
				
            D("Userscash")->add($arr);
			
            D('Usersex')->save($data);
            D('Users')->Money($this->uid, -$gold, '商户申请提现，扣款');
            $this->niuMsg('申请提现成功', U('money/index'));
        }
        else
        {
            $this->assign('info',D('Usersex')->getUserex($this->uid));
            $this->assign('gold', $this->member['gold']);
			$this->assign('cash_money', $cash_money);
			$this->assign('cash_money_big', $cash_money_big);
            $this->display();
        }
    }
}