<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CommonAction extends Action {

    protected $citys = array( );
    protected $city_id = 0;
    protected $city = array( );
	
	
    protected function _initialize() {
        
        
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
        $cityss = $city['city_id'];
	    $this->assign('cityss', $cityss);
	
		
		$this->assign('open_express',$open_express = $this->_CONFIG['operation']['express']); //快递
        $this->assign( "ctl", strtolower( MODULE_NAME ) );
        $this->assign( "act", ACTION_NAME );
        $this->assign( "nowtime", NOW_TIME );
		
        
        
    }

    public function check_login(){   //检测登录状态

            $rs = D('Delivery');
            $where = array();
            $where['id'] = array('eq',cookie('delivery'));
            $result = $rs->where($where)->find();

            if (!$result) {
                cookie('delivery',null);
                return false;
            }else{
                return $result;
            }

    }
    
    

     protected function cookid( $uid ){
        import( "ORG/Crypt/Base64" );
        $uid = "Delivery_".$uid."_".NOW_TIME;
        $uid = Base64::encrypt( $uid, c( "AUTH_KEY" ) );
        cookie( "DL", $uid, 31536000 );
        return TRUE;
    }

    protected function reid( ){
        import( "ORG/Crypt/Base64" );
        $token = cookie( "DL" );
        $token = Base64::decrypt( $token, c( "AUTH_KEY" ) );
        $token = explode( "_", $token );
        if ( $token[0] != "Delivery" )
        {
            return 0;
        }
        return ( integer )$token[1];
    }
   

    protected function baoMsg($message, $jumpUrl = '', $time = 3000, $callback = '', $parent = true) {
        $parents = $parent ? 'parent.' : '';
        $str = '<script>';
        $str .=$parents . 'bmsg("' . $message . '","' . $jumpUrl . '","' . $time . '","' . $callback . '");';
        $str.='</script>';
        exit($str);
    }

    protected function baoOpen($message, $close = true, $style) {
        $str = '<script>';
        $str .='parent.bopen("' . $message . '","' . $close . '","' . $style . '");';
        $str.='</script>';
        exit($str);
    }

    protected function baoSuccess($message, $jumpUrl = '', $time = 3000, $parent = true) {
        $this->baoMsg($message, $jumpUrl, $time, '', $parent);
    }

    protected function baoErrorJump($message, $jumpUrl = '', $time = 3000) {
        $this->baoMsg($message, $jumpUrl, $time);
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
	
	private function seo() {
        $seo = D('Seo')->fetchAll();
        $this->assign('seo_title', $this->_CONFIG['site']['title']);
        $this->assign('seo_keywords', $this->_CONFIG['site']['keyword']);
        $this->assign('seo_description', $this->_CONFIG['site']['description']);
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
        
        define('NOW_PATH',BASE_PATH.'/themes/'.$theme.'Delivery/');
       
        // 获取当前主题的模版路径
        define('THEME_PATH', BASE_PATH . '/themes/default/Delivery/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Delivery/');

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
	
	//开始
	
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

   ///小灰灰添加结束
	
	
	

}
