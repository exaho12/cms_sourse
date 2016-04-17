<?php

class IndexAction extends  CommonAction{
    
    public function index(){
       // $this->city_id = 1;
        if(empty($this->city_id)) $this->showmsg ('没有数据',1002);
//$this->logs($this->city_id);
        $today = TODAY;
        $datas['ad'] = D('Ad')->where(" closed=0 AND site_id=39 AND city_id IN (0,'{$this->city_id}')  and bg_date <= '{$today}' AND end_date >= '{$today}' " )
                      -> field('title,link_url,photo') ->order(array('orderby'=>'asc'))->limit(0,5)->select();
        //if(empty($datas['ad'])){
           // $datas['ad'] = array();
       // }
      //$this->logs(D('Ad')->getLastSql());
        $datas['cloud'] = D('Cloudgoods')->where("closed=0 AND city_id = '{$this->city_id}' AND audit=1 AND status=0")->field('goods_id,title,join,photo')
                        ->order(array('goods_id'=>'desc'))->limit(0,3)->select(); 
       // if(empty($datas['cloud'])){
          //  $datas['cloud'] = array();
        //}
      //$this->logs(D('Cloudgoods')->getLastSql());
      //$this->logs(var_export($datas['cloud'],true));
        $datas['tuan']  = D('Tuan')->where("audit =1 AND closed=0 AND city_id='{$this->city_id}' AND end_date >= '{$today}'")
                ->field('tuan_id,title,num,photo,end_date,sold_num,price,tuan_price')
                ->order("orderby asc,sold_num desc")->limit(0,3)->select(); 
        //if(empty($datas['tuan'])){
          //  $datas['tuan'] = array();
      //  }
       //  $this->logs(D('Tuan')->getLastSql());
       // $this->logs(var_export($datas['tuan'],true));
        $datas['shop']  = D('Shop')->where("audit =1 AND closed=0 AND city_id='{$this->city_id}' ")
                ->field('shop_id,shop_name,logo,photo,addr,score,score_num,tags')
                ->order("orderby asc,fans_num desc,view desc")->limit(0,3)->select(); 
        //if(empty($datas['shop'])){
            //$datas['shop'] = array();
       // }
       // $this->logs(D('Shop')->getLastSql());
       // $this->logs(var_export($datas['shop'],true));
        $datas['ele']   = D('Ele')->where("audit =1 AND city_id='{$this->city_id}' ")
                ->field('shop_id,shop_name,is_open,is_pay,is_new,is_fan,month_num,sold_num,logistics,since_money,full_money,new_money')
                ->order("orderby asc,month_num desc")->limit(0,3)->select(); 
        //if(empty($datas['ele'])){
           // $datas['ele'] = array();
      //  }
       // $this->logs(D('Ele')->getLastSql());
       // $this->logs(var_export($datas['ele'],true));
        $datas['function'] = D('Function')->where('is_index = 1')->field('photo,title,url')->order('orderby asc')->select();
        //if(empty($datas['function'])){
          //  $datas['function'] = array();
       // }
        $shop_ids =array();
        foreach($datas['ele'] as $k=>$v){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }
        $shops = D('Shop')->itemsByIds($shop_ids);
        foreach($datas['ele'] as $k=>$v){
             $datas['ele'][$k]['photo']=$shops[$v['shop_id']]['photo'];     
             $datas['ele'][$k]['logo']=$shops[$v['shop_id']]['logo']; 
        }
       
        //print_r($datas['shop']);
        $this->showmsg($datas);
     }
     
     private  function logs($log){
        $fp = fopen(BASE_PATH.'/logs.php', 'a+');
        fwrite($fp,$log.";\n");
        fclose($fp);
     }
    
    public function changecity(){
        $city_id = $this->_get('city_id','htmlspecialchars');
         if(empty($city_id)){
             $this->showmsg('参数不正确',1001);
         }
         if(isset($this->citys[$city_id])){            
            cookie('city_id',$city_id,86400*30);
            $this->showmsg($this->citys[$city_id]);
         }else{
             $this->showmsg('没有该城市',2001);
         }
    }
    
    public function city(){
        $citys = array();
        foreach($this->citys as $val){
            $citys[] = $val;
        }
        $this->showmsg($citys);
    }
    
    public function dingwei(){
        $lat = $this->_get('lat', 'htmlspecialchars');
        $lng = $this->_get('lng', 'htmlspecialchars');
        cookie('lat', $lat);
        cookie('lng', $lng);
        import('ORG/Net/IpLocation');
        $IpLocation = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
        $result = $IpLocation->getlocation($_SERVER['REMOTE_ADDR']);
       
        foreach ($this->citys as $val) {
            if (strstr($result['country'], $val['name'])) {
                $city = $val;
                $this->city_id = $val['city_id'];
                break;
            }
        }
        if(empty($city)){
            $this->city_id = $this->_CONFIG['site']['city_id'];
            $city = $this->citys[$this->_CONFIG['site']['city_id']];
        } 
        cookie('city_id',  $this->city_id);
        $this->showmsg($city);
    }
    
    public function register() {
        if(!$account = htmlspecialchars($this->_param('account'))){
            $this->showmsg('请输入手机号',2001);
        }elseif(!isMobile(htmlspecialchars($account))){
            $this->showmsg('只允许手机注册',2002);
        }elseif(!$scode = trim($this->_param('scode'))){
            $this->showmsg('请输入短信验证码',2003);
        }elseif(!$scode2 = session('scode')){
            $this->showmsg('请获取短信验证码',2004);
        }elseif($scode != $scode2){
            $this->showmsg('请输入正确的短信验证码',2005);
        }elseif(!$password = htmlspecialchars($this->_param('password'))){
            $this->showmsg('密码不能为空',2006);
        }elseif(strlen($password) < 6){
            $this->showmsg('密码长度必须要在6个字符以上',2007);
        }elseif($member = D('Users')->getUserByAccount($account)){
            $this->showmsg('手机号码已存在',2009);
        }else{
            $data['account'] = $account;
            $data['nickname'] = $account;
            $data['ext0'] = $account; //兼容UCENTER
            $data['mobile'] = $account;
            $data['reg_ip'] = get_client_ip();
            $data['reg_time'] = NOW_TIME;
            $data['password'] = $password;
            $invite_id = (int)cookie('invite_id');
            if(!empty($invite_id)){
                $userinvite = D('Users')->find($invite_id);
                if(!empty($userinvite)){ //讲新的 推广员身份给创建账号的
                    $data['invite6'] = $invite_id;
                    $data['invite5'] = $userinvite['invite6'];
                    $data['invite4'] = $userinvite['invite5'];
                    $data['invite3'] = $userinvite['invite4'];
                    $data['invite2'] = $userinvite['invite3'];
                    $data['invite1'] = $userinvite['invite2'];
                }
            }
            if($user_id = D('Users')->add($data)){
                setUid($user_id);
                $this->showmsg('注册成功',0);
            }else{
                $this->showmsg('注册失败',2010);
            }
        }
    }
    
    
    
    public function login() {
        if(!$account = htmlspecialchars($this->_param('account'))){
            $this->showmsg('请输入用户名',2001);
        }elseif(!$password = htmlspecialchars($this->_param('password'))){
            $this->showmsg('请输入登录密码',2002);
        }elseif(!$member = D('Users')->getUserByAccount($account)){
            $this->showmsg('该用户不存在',2003);
        }elseif($member['closed']==1){
            $this->showmsg('用户不存在或被删除',2004);
        }elseif($member['password'] != $password){
            $this->showmsg('账号或密码不正确',2005);
        }else{
            if (date('Y-m-d', $member['last_time']) < TODAY) {
                D('Users')->prestige($member['user_id'], 'login');
            }
            $data = array(
                'last_time' => NOW_TIME,
                'last_ip' => get_client_ip(),
                'user_id' => $member['user_id'],
                'token' => $this->token['token'],
            );
            if(false !== D('Users')->save($data)){
                setUid($member['user_id']);
                //file_put_contents('/aaa.txt', $member['user_id']);
                $this->showmsg('登录成功',0);
            }else{
                $this->showmsg('登录失败',2006);
            }
        }
    }
    
    public function info(){ //个人中心订单数目等信息
        $token = $this->_param('BAOCMS_TOKEN');
        $token = urldecode($token);
        $uid = (int)getUid($token);
        $datas = array();
        $datas['order'] = D('Tuanorder')->where(array('user_id' => $uid))->count();
        $datas['code'] = D('Tuancode')->where(array('user_id' => $uid, 'is_used' => 0))->count();
        $datas['goods_order'] = D('Order')->where(array('user_id'=>$uid))->count();
        $datas['ele_order'] = D('Eleorder')->where(array('user_id'=>$uid))->count();
        $datas['coupon'] = D('Coupondownload')->where(array('user_id'=>$uid,'is_used'=>0))->count();
        $datas['huodong'] = D('Huodong')->where(array('user_id'=>$uid,'closed'=>0,'audit'=>1))->count();
        $datas['favorites'] = D('ShopFavorites')->where(array('user_id'=>$uid))->count();
        $datas['breaks'] = D('Breaksorder')->where(array('user_id'=>$uid))->count();
        $user_id = array($uid,0);
        $res =  D('Msg')->where(array('user_id'=>array('IN',$user_id)))->order(array('msg_id'=>'desc'))->find();
        if(!$result = D('Msgread')->find($res['msg_id'])){
            $datas['msg'] = 1;
        }else{
            $datas['msg'] = 0;
        }
        $member = D('Users')->find($uid);
        unset($member['password']);
        $datas['member'] =  $member;
        $this->showmsg($datas,0);
    }
    
    public function msg(){ //消息
        $token = $this->_param('BAOCMS_TOKEN');
        $token = urldecode($token);
        $uid = (int)getUid($token);
        $user_id = array($uid,0);
        $res =  D('Msg')->where(array('user_id'=>array('IN',$user_id)))->order(array('msg_id'=>'desc'))->find();
        if(!$result = D('Msgread')->find($res['msg_id'])){
            $data = 1;
        }else{
            $data = 0;
        }
        $this->showmsg($data,0);
    }
    
    public function clearCookie(){
        cookie('community_id',null);
    }

        public function logout(){
        clearUid();
        $this->showmsg('退出登录成功',0);
    }
    
    
    public function sendsms() {
        if (!$mobile = htmlspecialchars($this->_param('account'))) {
            $this->showmsg('请输入正确的手机号码',2001);
        }
        if (!isMobile($mobile)) {
            $this->showmsg('请输入正确的手机号码',2002);
        }
        session_start();
        //$randstring = session('scode');
        //if (empty($randstring)) {
        $randstring = rand_string(6, 1);
        session('scode', $randstring);
        //}
        D('Sms')->sendSms('sms_code', $mobile, array('code' => $randstring));
        $this->showmsg('短信发送成功',0);
    }
        
        
    public function updatepwd(){ //重置密码
        if(!$account = htmlspecialchars($this->_param('account'))){
            $this->showmsg('请输入手机号',2001);
        }elseif(!isMobile(htmlspecialchars($account))){
            $this->showmsg('手机号码格式不正确',2002);
        }elseif(!$scode = trim($this->_param('scode'))){
            $this->showmsg('请输入短信验证码',2003);
        }elseif(!$scode2 = session('scode')){
            $this->showmsg('请获取短信验证码',2004);
        }elseif($scode != $scode2){
            $this->showmsg('请输入正确的短信验证码',2005);
        }elseif(!$password = htmlspecialchars($this->_param('password'))){
            $this->showmsg('密码不能为空',2006);
        }elseif(strlen($password) < 6){
            $this->showmsg('密码长度必须要在6个字符以上',2007);
        }elseif(!$member = D('Users')->getUserByAccount($account)){
            $this->showmsg('帐号不存在',2009);
        }else{
            if(false !== D('Users')->save(array('user_id'=>$member['user_id'],'password'=>$password))){
               clearUid();
               $this->showmsg('重置密码成功',0);
            }else{
               $this->showmsg('重置密码失败',2010);
            }
        }
    }
    
    public function keywords(){ 
        $data = D('Keyword')->field('keyword,type')->select();
        $this->showmsg($data,0);
    }
    
    public function basedata(){
        $tuancates = D('Tuancate')->fetchAll();
        $cates = array();
        foreach($tuancates as $k=>$val){
            if($val['parent_id'] == 0){
                $cates[$k] = $val;
            }
        }
        foreach ($cates as $k=>$val){
            foreach($tuancates as $kk=>$v){
                if($v['parent_id'] == $val['cate_id']){
                    $cates[$k]['son'][] = $v;
                }
            }
        }
        $areas = D('Area')->fetchAll();
        foreach($areas as $key=>$v){
            if($v['city_id'] == $this->city_id){
                $areass[$v['area_id']] = $areas[$key];
            }
        }
        
        $bizs = D('Business')->fetchAll();
        $cbd = array();
        foreach($areass as $k=>$val){
            $cbd[$k] = $val;
            foreach($bizs as $kk=>$v){
                if($val['area_id'] == $v['area_id']){
                    $cbd[$k]['biz'][] = $v;
                }
            }
        }
        $data['tuancates'] = array_values($cates);
        $data['areas'] = array_values($cbd);
        $this->showmsg($data,0);
    }

    public function tuan(){ //抢购
        $Tuan = D('Tuan');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('audit' => 1, 'closed' => 0, 'city_id' => $this->city_id, 'end_date' => array('EGT', TODAY));
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Tuancate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        }
        $bussiness = (int) $this->_param('bussiness');
        if ($bussiness) {
            $map['bussiness_id'] = $bussiness;
        }
        $order = $this->_param('order', 'htmlspecialchars');
        $lat = $this->_param('lat', 'htmlspecialchars');
        $lng = $this->_param('lng', 'htmlspecialchars');
        

        $orderby = '';
        switch ($order) {
            case 2:
                $orderby = array('orderby' => 'asc', 'tuan_id' => 'desc');
                break;
            default:
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
                break;
        }
        $count = $Tuan->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }

        $list = $Tuan->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val['end_time'] = strtotime($val['end_date']) - NOW_TIME + 86400;
            $list[$k] = $val;
        }
        if ($shop_ids) {
            $shops = D('Shop')->itemsByIds($shop_ids);
            $ids = array();
            foreach ($shops as $k => $val) {
                $shops[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
                $d = getDistanceNone($lat, $lng, $val['lat'], $val['lng']);
                $ids[$d][] = $k; //防止同样的距离出现 
            }
            ksort($ids);
            $showshops = array();
            foreach ($ids as $arr1) {
                foreach ($arr1 as $val) {
                    $showshops[$val] = $shops[$val];
                }
            }
            $items = array();
            foreach($showshops as $k=>$val){
                $items[$k] = $val;
                foreach($list as $kk=>$v){
                    if($val['shop_id'] == $v['shop_id']){
                        $items[$k]['tuan'][] = $v;
                    }
                }
            }
            if(empty($items)){
                $this->showmsg ('没有数据',1002);
            }else{
                $this->showmsg(array_values($items),0);
            }
        }
    }
    
    
}