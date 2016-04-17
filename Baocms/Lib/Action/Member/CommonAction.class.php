<?php

class CommonAction extends Action {
    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $citys = array();
    protected $areas = array();
    protected $bizs = array();
    protected $template_setting = array();
    protected $city_id = 0;
    protected $city = array();

	

   protected function _initialize() {
        //global $domains, $city;
        define('__HOST__', 'http://' . $_SERVER['HTTP_HOST']);    
        $this->_CONFIG = D('Setting')->fetchAll();
        $this->citys = D('City')->fetchAll();
        $this->assign('citys', $this->citys);
        $this->city_id = cookie('city_id');
        if(empty($this->city_id)){
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
        }  else{
            $city = $this->citys[$this->city_id];
        }



        $this->uid =  getUid();
        if (empty($this->uid)) {
            header("Location: " . U('pchome/passport/login'));
            die;
        }

        if (!empty($this->uid)) {
            $this->member = D('Users')->find($this->uid);
        }



        $this->_CONFIG = D('Setting')->fetchAll();
        $this->assign('CONFIG', $this->_CONFIG);
        $this->assign('MEMBER', $this->member);
		$this->assign('ranks',D('Userrank')->fetchAll());//增加分销
        $this->assign('today', TODAY); //兼容模版的其他写法
         $this->assign('city',$city);
        $this->areas = D('Area')->fetchAll();
        $this->assign('areas', $this->areas);
        $this->bizs = D('Business')->fetchAll();
        $this->assign('bizs', $this->bizs);
        $this->assign('tuancates',D('Tuancate')->fetchAll());
        $this->assign('ctl', strtolower(MODULE_NAME)); //主要方便调用
        $this->assign('act', ACTION_NAME);
        $this->assign('nowtime', NOW_TIME); // 主要标签短
        $this->assign('bao_city', BAO_CITY ? 1 : 0); //是否切换城市的开关
        $this->assign('domains', $domains); //城市列表加域名
        $this->assign('city_name', $city['name']); //您当前可能在的城市
        $this->assign('city_id', $this->city_id);

	

		//城市循环全局开始
		$citylists = array();
        foreach($this->citys as $val){
			 if($val['is_open'] == 1){
            $a = strtoupper($val['first_letter']);
            $citylists[$a][] = $val;
		}
        }
        ksort($citylists);//重新整理排序
        $this->assign('citylists',$citylists);
		//城市循环结束
		//购物车开始
		$goods = cookie('goods');
        $this->assign('cartnum', (int) array_sum($goods));

		//购物车结束
		
		$mapssss = array('status' => 4,'closed'=>0);
		$this->assign('navigations',$navigations = D('Navigation') ->where($mapssss)->order(array('orderby' => 'asc'))->select());
        //模版的选择

        $this->getTemplateTheme();
        $this->template_setting = D('Templatesetting')->detail($this->theme);
		
		$this->assign('distribution',$distribution = $this->_CONFIG['operation']['distribution']); //赋值分销开关
		$this->assign('open_lifeservice',$open_lifeservice = $this->_CONFIG['operation']['lifeservice']); //赋值家政
		$this->assign('open_tieba',$open_tieba = $this->_CONFIG['operation']['tieba']); //赋值贴吧
		$this->assign('open_news',$open_news = $this->_CONFIG['operation']['news']); //赋值新闻
		$this->assign('open_life',$open_life = $this->_CONFIG['operation']['life']); //分类信息
		$this->assign('open_jifen',$open_jifen = $this->_CONFIG['operation']['jifen']); //积分
		$this->assign('open_billboard',$open_billboard = $this->_CONFIG['operation']['billboard']); //榜单
		$this->assign('open_market',$open_market = $this->_CONFIG['operation']['market']); //卖场
		$this->assign('open_express',$open_express = $this->_CONFIG['operation']['express']); //快递
		$this->assign('color',$color = $this->_CONFIG['other']['color']);
		
		$this->assign('shop_gold',$shop_gold = D('Shop')-> where(array('user_id' => $this->uid))-> find());//查询此会员是否是商家

		$web_close = $this->_CONFIG['site']['web_close'];
		$web_close_title = $this->_CONFIG['site']['web_close_title'];
		if($web_close==0) {
		$this->display("public:web_close"); 
        die;
        }

    }


    private function tmplToStr($str, $datas) {
        return tmplToStr($str, $datas);
    }

    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content = '', $prefix = '');
    }

    private function parseTemplate($template = '') {

        $depr = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        // 获取当前主题名称
        $theme = $this->getTemplateTheme();
        define('NOW_PATH',BASE_PATH.'/themes/'.$theme.'Member/');
        // 获取当前主题的模版路径

        define('THEME_PATH', BASE_PATH . '/themes/default/Member/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Member/');

        // 分析模板文件规则

        if ('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = strtolower(MODULE_NAME) . $depr . strtolower(ACTION_NAME);
        } elseif (false === strpos($template, '/')) {
            $template = strtolower(MODULE_NAME) . $depr . strtolower($template);

        }  

        $file = NOW_PATH . $template . C('TMPL_TEMPLATE_SUFFIX');
        if(file_exists($file)) return $file;
        return THEME_PATH . $template . C('TMPL_TEMPLATE_SUFFIX');



    }







    private function getTemplateTheme() {
        define('THEME_NAME','default');
        if ($this->theme) { // 指定模板主题
         $theme = $this->theme;
        } else {
        /* 获取模板主题名称 */
       $theme = D('Template')->getDefaultTheme();
            if (C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
                $t = C('VAR_TEMPLATE');
                if (isset($_GET[$t])) {
                    $theme = $_GET[$t];
                } elseif (cookie('think_template')) {
                    $theme = cookie('think_template');
                }
                if (!in_array($theme, explode(',', C('THEME_LIST')))) {
                    $theme = C('DEFAULT_THEME');
                }
                cookie('think_template', $theme, 864000);
            }
            $this->theme = $theme;
        }
       // 当前模板主题名称
        return $theme ? $theme . '/' : '';
    }

   protected function baoMsg($message, $jumpUrl = '', $time = 3000,$callback = '',$parent=true) {
        $parents = $parent ? 'parent.':'';
        $str = '<script>';
        $str .=$parents.'bmsg("' . $message . '","' . $jumpUrl .'","'.$time. '","'.$callback.'");';
        $str.='</script>';
        exit($str);
    }

    protected function baoOpen($message, $close = true, $style) {
        $str = '<script>';
        $str .='parent.bopen("' . $message . '","' . $close .'","'.$style. '");';
        $str.='</script>';
        exit($str);
    }

    protected function baoSuccess($message, $jumpUrl = '', $time = 3000, $parent = true) {
        $this->baoMsg($message,$jumpUrl,$time,'',$parent);
    }

    protected function baoJump($jumpUrl) {
        $str = '<script>';
        $str .='parent.jumpUrl("' . $jumpUrl . '");';
        $str.='</script>';
        exit($str);
    }
    protected function baoErrorJump($message, $jumpUrl = '', $time = 3000) {
        $this->baoMsg($message,$jumpUrl,$time);
    }
    protected function baoError($message, $time = 3000, $yzm = false, $parent = true) {
        $parent = $parent ? 'parent.' : '';
        $str = '<script>';
        if ($yzm) {
            $str .= $parent . 'bmsg("' . $message . '","",' . $time . ',"yzmCode()");';
        } else {
            $str .= $parent . 'bmsg("' . $message . '","",' . $time . ');';
        }
        $str.='</script>';
        exit($str);
    }


    protected function baoLoginSuccess() { //异步登录
        $str = '<script>';
        $str .='parent.parent.LoginSuccess();';
        $str.='</script>';
        exit($str);
    }

    protected function ajaxLogin() {
        if ($mini = $this->_get('mini')) { //如果是迷你的弹出层操作就输出0即可
	    die('0');
        }
        $str = '<script>';
        $str .='parent.ajaxLogin();';
        $str.='</script>';
        exit($str);
    }

    protected function checkFields($data = array(), $fields = array()) {
        foreach ($data as $k => $val) {
            if (!in_array($k, $fields)) {
                unset($data[$k]);
            }
        }
        return $data;
    }
    protected function ipToArea($_ip) {
        return IpToArea($_ip);
    }

}



