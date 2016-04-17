<?php
class UsersModel extends CommonModel
{
    protected $pk = 'user_id';
    protected $tableName = 'users';
    protected $_integral_type = array('share' => '发帖分享', 'reply' => '回复帖子', 'mobile' => '手机认证', 'email' => '邮件认证');
    public function checkInvite($uid, $money)
    {
        //推广员功能
        $uid = (int) $uid;
        $money = (int) $money;
        if ($money <= 0) {
            return false;
        }
        $user = $this->find($uid);
        $invite_t = NOW_TIME - 30 * 86400;
        if (!empty($user['invite_id'])) {
            if ($invite_t < $user['reg_time']) {
                //30天推广有效果
                $user2 = $this->find($user['invite_id']);
                //邀请人
                $ranks = D('Userrank')->fetchAll();
                //查出所有的Userrank
                $integral = (int) ($money * $ranks[$user2['rank_id']]['rebate'] / 100);
                if ($money <= 0) {
                    return false;
                }
                return array('uid' => $user2['user_id'], 'integral' => $integral);
            }
        }
        return false;
    }
    public function getUserByAccount($account)
    {
        $data = $this->find(array('where' => array('account' => $account)));
        return $this->_format($data);
    }
    public function getUserByMobile($mobile)
    {
        $data = $this->find(array('where' => array('mobile' => $mobile)));
        return $this->_format($data);
    }
    //邮件登录暂时不处理
    public function getUserByEmail($email)
    {
        $data = $this->find(array('where' => array('email' => $email)));
        return $this->_format($data);
    }
    public function getUserByUcId($uc_id)
    {
        $data = $this->find(array('where' => array('uc_id' => (int) $uc_id)));
        return $this->_format($data);
    }
    //声望不记录日志了
    public function prestige($user_id, $mdl)
    {
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        $user = $this->find($user_id);
        if (!empty($user) && $CONFIG['prestige'][$mdl]) {
            $data = array('user_id' => $user_id, 'prestige' => $user['prestige'] + $CONFIG['prestige'][$mdl]);
            $userrank = D('Userrank')->fetchAll();
            foreach ($userrank as $val) {
                if ($val['prestige'] <= $data['prestige']) {
                    $data['rank_id'] = $val['rank_id'];
                }
            }
            return $this->save($data);
        }
        return false;
    }
    public function integral($user_id, $mdl)
    {
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        if (!isset($this->_integral_type[$mdl])) {
            return false;
        }
        if ($CONFIG['integral'][$mdl]) {
            return $this->addIntegral($user_id, $CONFIG['integral'][$mdl], $this->_integral_type[$mdl]);
        }
        return false;
    }
    //购物返积分
    public function gouwu($user_id, $price, $intro)
    {
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        if (empty($CONFIG['integral']['gouwu'])) {
            return false;
        }
        $price = (int) ($price / 100);
        $integral = (int) ($price * $CONFIG['integral']['gouwu']);
        if ($integral <= 0) {
            return false;
        }
        return $this->addIntegral($user_id, $integral, $intro);
    }
    public function addGold($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'gold', $num)) {
            return D('Usergoldlogs')->add(array('user_id' => $user_id, 'gold' => $num, 'intro' => $intro, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
        }
        return false;
    }
	
	//写入商户金块
	  public function Money($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'gold', $num)) {
            return D('Usergoldlogs')->add(array(
				'user_id' => $user_id, 
				'gold' => $num, 
				'intro' => $intro, 
				'create_time' => NOW_TIME, 
				'create_ip' => get_client_ip()
			));
        }
        return false;
    }
	//写入用户余额
    public function addMoney($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'money', $num)) {
            return D('Usermoneylogs')->add(array('user_id' => $user_id, 'money' => $num, 'intro' => $intro, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
        }
        return false;
    }
	
	
    public function addIntegral($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'integral', $num)) {
            return D('Userintegrallogs')->add(array('user_id' => $user_id, 'integral' => $num, 'intro' => $intro, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
        }
	
        return false;
    }
    public function CallDataForMat($items){
        //专门针对CALLDATA 标签处理的
        if (empty($items)) {
            return array();
        }
        $obj = D('Userrank');
        $rank_ids = array();
        foreach ($items as $k => $val) {
            $rank_ids[$val['rank_id']] = $val['rank_id'];
        }
        $userranks = $obj->itemsByIds($rank_ids);
        foreach ($items as $k => $val) {
            $val['rank'] = $userranks[$val['rank_id']];
            $items[$k] = $val;
        }
        return $items;
    }
    public function addProfit($user_id, $orderType = 0, $orderId, $num, $is_separate)
    {
        return D('Userprofitlogs')->add(array('user_id' => $user_id, 'money' => $num, 'order_id' => $orderId, 'order_type' => $orderType, 'create_time' => NOW_TIME, 'is_separate' => $is_separate));
    }
}