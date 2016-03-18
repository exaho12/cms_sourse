<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CheckAction extends CommonAction {

    protected $shop_id = 0;
    protected $branch_id = 0;

    public function _initialize() {
        parent::_initialize();
        $this->shop_id = (int) $this->_get('shop_id');
        $this->branch_id = (int) $this->_get('branch_id');
        if(empty($this->shop_id) || empty($this->branch_id)){
            $this->error('您不能访问该页面');
        }
        if(ACTION_NAME != 'password'){
            $this->pwd = session('pwd');
            if (empty($this->pwd)) {
                header("Location: " . U('pchome/check/password',array('shop_id'=>  $this->shop_id,'branch_id'=>  $this->branch_id)));
                die;
            }
            $result = D('Shopbranch')->where(array('shop_id' => $this->shop_id, 'branch_id' => $this->branch_id))->find();
            if (empty($result)) {
                $this->error('参数错误');
            }
            if (empty($result['password'])) {
                $this->error('您的店没设置口令，暂不支持！');
            }
            if(md5($result['password'].C('AUTH_KEY')) != $this->pwd){
                header("Location: " . U('pchome/check/password',array('shop_id'=>  $this->shop_id,'branch_id'=>  $this->branch_id)));
                die;
            }
        }    
        $this->assign('shop_id', $this->shop_id);
        $this->assign('branch_id', $this->branch_id);
    }

    public function index() {        
        if ($this->isPost()) {
            $code = $this->_post('code', false);
            $res = array();
            foreach ($code as $k => $val) {
                if (!empty($val)) {
                    $res[$k] = $val;
                }
            }
            if (empty($res)) {
                $this->baoMsg('请输入抢购券！');
            }
            $obj = D('Tuancode');
            $shopmoney = D('Shopmoney');
            $return = array();
            $ip = get_client_ip();
            if (count($code) > 10) {
                $this->baoMsg("一次最多验证10条抢购券!");
            }
            $userobj = D('Users');
            foreach ($code as $key => $var) {
                $var = trim(htmlspecialchars($var));
                if (!empty($var)) {
                    $data = $obj->find(array('where' => array('code' => $var)));
                    if (!empty($data) && $data['shop_id'] == $this->shop_id && ($data['branch_id'] == $this->branch_id || empty($data['branch_id']) )&& (int) $data['is_used'] == 0 && (int) $data['status'] == 0) {
                        if ($obj->save(array('code_id' => $data['code_id'], 'is_used' => 1))) { //这次更新保证了更新的结果集              
                            //增加MONEY 的过程 稍后补充
                            if (!empty($data['price'])) {
                                $data['intro'] = '抢购消费' . $data['order_id'];
                                $data['settlement_price'] =  D('Quanming')->quanming($data['user_id'],$data['settlement_price'],'tuan'); //扣去全民营销
                                $shopmoney->add(array(
                                    'shop_id' => $data['shop_id'],
                                    'money' => $data['settlement_price'],
                                    'create_ip' => $ip,
                                    'create_time' => NOW_TIME,
                                    'order_id' => $data['order_id'],
                                    'intro' => $data['intro'],
                                ));
                                $return[$var] = $var;
                                $obj->save(array('code_id' =>$data['code_id'],'branch_id'=> $this->branch_id,'used_time' => NOW_TIME, 'used_ip' => $ip)); //拆分2次更新是保障并发情况下安全问题
                                $userobj->gouwu($data['user_id'],$data['price'],'团购消费');//购物积分
                                echo '<script>parent.used(' . $key . ',"√验证成功",1);</script>';
                            } else {
                                echo '<script>parent.used(' . $key . ',"√到店付抢购券验证成功",2);</script>';
                            }
                        }
                    } else {
                        //$this->baoError('该抢购券无效');
                        echo '<script>parent.used(' . $key . ',"X该抢购券无效",3);</script>';
                    }
                }
            }
        } else {
            $this->display();
        }
    }

    public function password() {
        if ($this->isPost()) {
            $password = htmlspecialchars($_REQUEST['password']);
            if (empty($password)) {
                $this->error('口令不能为空');
            }
            $result = D('Shopbranch')->where(array('shop_id' => $this->shop_id, 'branch_id' => $this->branch_id))->find();
            if ($password != $result['password']) {
                $this->error('口令不正确');
            }
            session('pwd', md5($password.C('AUTH_KEY')));
            $this->success('验证通过，您现在可以进行其他操作了', U('check/index', array('shop_id' => $this->shop_id, 'branch_id' => $this->branch_id)));
        } else {
            $this->display();
        }
    }

    public function coupon() {
        if ($this->isPost()) {
            $code = $this->_post('code', false);
            $res = array();
            foreach ($code as $k => $val) {
                if (!empty($val)) {
                    $res[$k] = $val;
                }
            }
            if (empty($res)) {
                $this->baoMsg('请输入电子优惠券！');
            }
            $obj = D('Coupondownload');
            $return = array();
            $ip = get_client_ip();
            foreach ($code as $var) {
                if (!empty($var)) {
                    $data = $obj->find(array('where' => array('code' => $var)));
                    if (!empty($data) && $data['shop_id'] == $this->shop_id && $data['is_used'] == 0) {
                        $obj->save(array('download_id' => $data['download_id'],'branch_id'=>  $this->branch_id, 'is_used' => 1, 'used_time' => NOW_TIME, 'used_ip' => $ip));
                        $return[$var] = $var;
                    }
                }
            }
            if (empty($return)) {
                $this->baoMsg('没有可消费的电子优惠券！');
            }
            if (NOW_TIME - $this->shop['ranking'] < 86400) { //更新排名
                D('Shop')->save(array('shop_id' => $this->shop_id, 'ranking' => NOW_TIME));
            }
            //exit('<script>parent.used("' . join(',', $return) . '");</script>');
            $message = "恭喜您，您成功消费的优惠券如下：" . join(',', $return);
            $this->baoOpen($message, true, "layui-layer-demo");
        } else {
            $this->display();
        }
    }

    public function tuanlist() {

        
        $tuancode = D('Tuancode');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('status' => 0,'is_used'=>1, 'shop_id' => $this->shop_id, 'branch_id' => $this->branch_id, 'fail_date' => array('EGT', TODAY));
        $count = $tuancode->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $tuancode->where($map)->order(array('code_id' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $tuan_ids = $user_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val)) {
                $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
                $user_ids[$val['user_id']] = $val['user_id'];
            }
        }
        $shop = D('Shop')->find($this->shop_id);
        $branch = D('Shopbranch')->find($this->branch_id);
        $this->assign('shops',$shop);
        $this->assign('branch',$branch);
        $this->assign('tuans',D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('users',D('Users')->itemsByIds($user_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }

    public function couponlist() {
        
        $cd = D('CouponDownload');
        $cp = D('Coupon');
        $u = D('Users');
        $s = D('Shop');
        $map = array();
        $map['is_used'] = 1;
        $map['shop_id'] = $this->shop_id;
        $map['branch_id'] = $this->branch_id;
        import('ORG.Util.Page');// 导入分页类
        $count = $cd->where($map)->count();// 查询满足要求的总记录数
        $Page = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $cd->where($map)->order('download_id')->limit($Page->firstRow.','.$Page->listRows)->select();
        $coupon_ids = $user_ids = array();
        foreach ($list as $k=>$val){
            $coupon_ids[$val['coupon_id']] = $val['coupon_id'];
            $user_ids[$val['user_id']] = $val['user_id'];
            $this->shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $r = $cp->itemsByIds($coupon_ids);
        $ui = $u -> itemsByIds($user_ids);
        $si = $s -> itemsByIds($this->shop_ids);
        $this->assign('r',$r);$this->assign('ui',$ui);$this->assign('si',$si);

        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
        
    }

}
