<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CommonAction extends Action {

    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $citys = array();
    protected $areas = array();
    protected $bizs = array();
    protected $city_id = 0;
    protected $city  = array();

    protected function _initialize() {
        define( "__HOST__", "http://".$_SERVER['HTTP_HOST'] );
        define( "IN_MOBILE", TRUE );
        $is_weixin = is_weixin( );
        $is_weixin = !$is_weixin ? FALSE : TRUE;
        define( "IS_WEIXIN", $is_weixin );
        searchwordfrom( );
        $this->uid = ( integer )getuid( );
        if ( empty( $this->uid ) )
        {
            header( "Location: ".u( "mobile/passport/login" ) );
            exit( );
        }
        $this->member = d( "Users" )->find( $this->uid );
        if ( empty( $this->member ) )
        {
            setuid( 0 );
            header( "Location: ".u( "mobile/passport/login" ) );
            exit( );
        }
        $this->ex = d( "Usersex" )->find( $this->uid );
        $this->checkFzmoney( );
        $this->_CONFIG = d( "Setting" )->fetchAll( );
        $this->citys = d( "City" )->fetchAll( );
        $this->city_id = cookie( "city_id" );
        if ( empty( $this->city_id ) )
        {
            import( "ORG/Net/IpLocation" );
            ( "UTFWry.dat" );
            $IpLocation = new IpLocation( );
            $result = $IpLocation->getlocation( $_SERVER['REMOTE_ADDR'] );
            foreach ( $this->citys as $val )
            {
                if ( !strstr( $result['country'], $val['name'] ) )
                {
                    continue;
                }
                $city = $val;
                $this->city_id = $val['city_id'];
                break;
            }
            if ( empty( $city ) )
            {
                $this->city_id = $this->_CONFIG['site']['city_id'];
                $city = $this->citys[$this->_CONFIG['site']['city_id']];
            }
        }
        else
        {
            $city = $this->citys[$this->city_id];
        }
        $this->assign( "city", $city );
        $this->assign( "citys", $this->citys );
        $this->areas = d( "Area" )->fetchAll( );
        $this->assign( "areas", $this->areas );
        $this->bizs = d( "Business" )->fetchAll( );
        $this->assign( "bizs", $this->bizs );
        $invite = ( integer )$this->_get( "invite" );
        if ( !empty( $invite ) )
        {
            cookie( "invite_id", $invite );
        }
        $tui_uid = ( integer )$this->_get( "tui_uid" );
        if ( $tui_uid && ( $goods_id = ( integer )$this->_get( "goods_id" ) ) )
        {
            cookie( "tui", $tui_uid."_".$goods_id, 2592000 );
        }
        $this->assign( "CONFIG", $this->_CONFIG );
        $this->assign( "MEMBER", $this->member );
        $this->assign( "MEMBER_EX", $this->ex );
        $this->assign( "shopcates", d( "Shopcate" )->fetchAll( ) );
        $this->assign( "tuancates", d( "Tuancate" )->fetchAll( ) );
        $this->assign( "goodscates", d( "Goodscate" )->fetchAll( ) );
        $this->assign( "today", TODAY );
        $this->assign( "nowtime", NOW_TIME );
        $this->assign( "ctl", strtolower( MODULE_NAME ) );
        $this->assign( "act", ACTION_NAME );
        $this->assign( "is_weixin", IS_WEIXIN );
        $this->msg( );
		 
		$this->assign('distribution',$distribution = $this->_CONFIG['operation']['distribution']); //赋值分销开关
		$this->assign('open_lifeservice',$open_lifeservice = $this->_CONFIG['operation']['lifeservice']); //赋值家政
		$this->assign('open_tieba',$open_tieba = $this->_CONFIG['operation']['tieba']); //赋值贴吧
		$this->assign('open_news',$open_news = $this->_CONFIG['operation']['news']); //赋值新闻
		$this->assign('open_life',$open_life = $this->_CONFIG['operation']['life']); //分类信息
		$this->assign('open_jifen',$open_jifen = $this->_CONFIG['operation']['jifen']); //积分
		$this->assign('open_billboard',$open_billboard = $this->_CONFIG['operation']['billboard']); //榜单
		$this->assign('open_market',$open_market = $this->_CONFIG['operation']['market']); //卖场
		$this->assign('open_express',$open_express = $this->_CONFIG['operation']['express']); //快递

		$web_close = $this->_CONFIG['site']['web_close'];
		$web_close_title = $this->_CONFIG['site']['web_close_title'];
		
		if($web_close==0) {
		$this->display("public:web_close"); 
        die;
        }
		
    }
	
	private function checkFzmoney( ){
        if ( $this->ex['frozen_money'] && $this->ex['is_no_frozen'] != 1 && $this->ex['frozen_date'] < NOW_TIME ){
            $this->ex['is_no_frozen'] = 1;
            if ( d( "Usersex" )->save( array(
                "user_id" => $this->uid,
                "is_no_frozen" => 1
            ) ) ){
                d( "Users" )->addMoney( $this->uid, $this->ex['frozen_money'], "冻结金额系统自动解冻了" );
            }
        }
        return TRUE;
    }

    protected function msg() {
        $msgs = D('Msg')->where(array('user_id' => array('IN', array(0, $this->uid))))->limit(0, 20)->select();
        $this->assign('msgs', $msgs);
        $msg_ids = array();
        foreach ($msgs as $val) {
            $msg_ids[] = $val['msg_id'];
        }
        if (!empty($this->uid)) {
            $reads = D('Msgread')->where(array('user_id' => $this->uid, 'msg_id' => array('IN', $msg_ids)))->select();
            $messagenum = count($msgs) - count($reads);
            $messagenum = $messagenum > 9 ? 9 : $messagenum;
            $readids = array();
            foreach ($reads as $val) {
                $readids[$val['msg_id']] = $val['msg_id'];
            }
            $this->assign('readids', $readids);
            $this->assign('messagenum', $messagenum);
        } else {
            $this->assign('messagenum', 0);
        }
    }

    private function seo() {
        $seo = D('Seo')->fetchAll();
        $this->assign('seo_title', $this->_CONFIG['site']['title']);
        $this->assign('seo_keywords', $this->_CONFIG['site']['keyword']);
        $this->assign('seo_description', $this->_CONFIG['site']['description']);
    }

    private function tmplToStr($str, $datas) {
        preg_match_all('/{(.*?)}/', $str, $arr);
        foreach ($arr[1] as $k => $val) {
            $v = isset($datas[$val]) ? $datas[$val] : '';
            $str = str_replace($arr[0][$k], $v, $str);
        }
        return $str;
    }

    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        $this->seo();
        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content = '', $prefix = '');
    }

    private function parseTemplate($template = '') {

        $depr = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        // 获取当前主题名称
        $theme = $this->getTemplateTheme();
        define('NOW_PATH',BASE_PATH.'/themes/'.$theme.'Mcenter/');
       
        // 获取当前主题的模版路径
        define('THEME_PATH', BASE_PATH . '/themes/default/Mcenter/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Mcenter/');

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
        }
        return $theme ? $theme . '/' : '';
    }

    protected function baoSuccess($message, $jumpUrl = '', $time = 3000) {
        $str = '<script>';
        $str .='parent.success("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str.='</script>';
        exit($str);
    }
    
    
     protected function baoMsg($message, $jumpUrl = '', $time = 3000) {
        $str = '<script>';
        $str .='parent.bmsg("' . $message . '","' . $jumpUrl .'","'.$time. '");';
        $str.='</script>';
        exit($str);
    }

    protected function baoErrorJump($message, $jumpUrl = '', $time = 3000) {
        $str = '<script>';
        $str .='parent.error("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str.='</script>';
        exit($str);
    }

    protected function baoError($message, $time = 3000, $yzm = false) {
        $str = '<script>';
        if ($yzm) {
            $str .='parent.error("' . $message . '",' . $time . ',"yzmCode()");';
        } else {
            $str .='parent.error("' . $message . '",' . $time . ');';
        }
        $str.='</script>';
        exit($str);
    }

    protected function baoAlert($message, $url = '') {
        $str = '<script>';
        $str.='parent.alert("' . $message . '");';
        if (!empty($url)) {
            $str.='parent.location.href="' . $url . '";';
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
	 protected function niuSuccess($message, $jumpUrl = '', $time = 3000) {
        $str = '<script>';
        $str .='parent.success("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str.='</script>';
        exit($str);
    }
    
    
     protected function niuMsg($message, $jumpUrl = '', $time = 3000) {
        $str = '<script>';
        $str .='parent.boxmsg("' . $message . '","' . $jumpUrl .'","'.$time. '");';
        $str.='</script>';
        exit($str);
    }

    protected function niuErrorJump($message, $jumpUrl = '', $time = 3000) {
        $str = '<script>';
        $str .='parent.error("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str.='</script>';
        exit($str);
    }

    protected function niuError($message, $time = 3000, $yzm = false) {
        $str = '<script>';
        if ($yzm) {
            $str .='parent.error("' . $message . '",' . $time . ',"yzmCode()");';
        } else {
            $str .='parent.error("' . $message . '",' . $time . ');';
        }
        $str.='</script>';
        exit($str);
    }

    protected function niuAlert($message, $url = '') {
        $str = '<script>';
        $str.='parent.alert("' . $message . '");';
        if (!empty($url)) {
            $str.='parent.location.href="' . $url . '";';
        }
        $str.='</script>';
        exit($str);
    }

    protected function niuLoginSuccess() { //异步登录
        $str = '<script>';
        $str .='parent.parent.LoginSuccess();';
        $str.='</script>';
        exit($str);
    }

    protected function ajaxLogin() {
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
