<?php



/*

 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！

 * 作者：baocms团队

 * 官网：www.baocms.com

 * 邮件: youge@baocms.com  QQ 800026911

 */



class SettingAction extends CommonAction {



    public function site() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'site', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/site'));

        } else {

            $this->assign('citys',D('City')->fetchAll());

			$this->assign('ranks',D('Userrank')->fetchAll());//增加分销

            $this->display();

        }

    }

    

     public function quanming() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'quanming', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/quanming'));

        } else {

            $this->display();

        }

    }



    public function attachs() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'attachs', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/attachs'));

        } else {

            $this->display();

        }

    }

    

    public function mall() {

  

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'mall', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/mall'));

        } else {

            $this->display();

        }

    }



    public function ucenter() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'ucenter', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/ucenter'));

        } else {

            $this->display();

        }

    }



    public function sms() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'sms', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/sms'));

        } else {

            $this->display();

        }

    }



    public function weixin() {



        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'weixin', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/weixin'));

        } else {



            $this->display();

        }

    }

    //信鸽

    public function jpush(){

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'jpush', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/jpush'));

        } else {

            $this->display();

        }

    }





    public function weixinmenu() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);



            D('Weixin')->weixinmenu($data);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'weixinmenu', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/weixinmenu'));

        } else {

            $this->display();

        }

    }



    public function connect() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'connect', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/connect'));

        } else {

            $this->display();

        }

    }



    public function integral() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'integral', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/integral'));

        } else {

            $this->display();

        }

    }

    

    public function weidian(){

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'weidian', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/weidian'));

        } else {

            $this->display();

        }

        

    }



    public function prestige() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'prestige', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/prestige'));

        } else {

            $this->display();

        }

    }



    public function mail() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'mail', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/mail'));

        } else {

            $this->display();

        }

    }



    



    public function mobile() {

        if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'mobile', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/mobile'));

        } else {

            $this->display();

        }

    }





    

    public function housework(){

        

         if ($this->isPost()) {

            $data = $this->_post('data', false);

            $data = serialize($data);

            D('Setting')->save(array('k' => 'housework', 'v' => $data));

            D('Setting')->cleanCache();

            $this->baoSuccess('设置成功', U('setting/housework'));

        } else {

            $this->display();

        }

        

        

    }





    public function updateapp(){

        if ($this->isPost()) {
            $data = $this->_post('data', false);
            $data['time'] = time();
            $data = serialize($data);
            D('Setting')->save(array('k' => 'updateapp', 'v' => $data));
            D('Setting')->cleanCache();
            $this->baoSuccess('设置成功', U('setting/updateapp'));
        } else {
            $this->display();

        }

    }

    



	

	

    public function other() {
        if ($this->isPost()) {
            $data = $this->_post('data', false);
            $data = serialize($data);
            D('Setting')->save(array('k' => 'other', 'v' => $data));
            D('Setting')->cleanCache();
            $this->baoSuccess('设置成功', U('setting/other'));
        } else {
            //$this->assign('citys',D('City')->fetchAll());
            $this->display();

        }

    }

    
	 public function operation() {
        if ($this->isPost()) {
            $data = $this->_post('data', false);
            $data = serialize($data);
            D('Setting')->save(array('k' => 'operation', 'v' => $data));
            D('Setting')->cleanCache();
            $this->baoSuccess('设置成功', U('setting/operation'));
        } else {
            $this->display();
        }
    }
}

