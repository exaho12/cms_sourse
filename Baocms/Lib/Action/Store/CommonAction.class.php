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
    protected $shop_id = 0;
    protected $shop = array();
    protected $shopcates = array();

    protected function _initialize() {
        $this->uid =  getUid();
        if (!empty($this->uid)) {
            $this->member = D('Users')->find($this->uid);
        }
        if (strtolower(MODULE_NAME) != 'passport' && strtolower(MODULE_NAME) != 'public') { //public 不受权限控制
            if (empty($this->uid)) {
                header("Location: " . U('passport/login'));
                die;
            }
            $this->shop = D('Shop')->find(array("where" => array('user_id' => $this->uid, 'closed' => 0, 'audit' => 1)));
            if (empty($this->shop)) {
                $this->error('该用户没有开通商户', U('passport/login'));
            }
            $this->shop_id = $this->shop['shop_id']; //为了程序调用的时候方便
            $this->assign('SHOP', $this->shop);
        }
        $this->_CONFIG = D('Setting')->fetchAll();
               define('__HOST__', 'http://'.$_SERVER['HTTP_HOST']);
        $this->assign('CONFIG', $this->_CONFIG);
        $this->assign('MEMBER', $this->member);
        $this->shopcates = D('Shopcate')->fetchAll();
        $this->assign('shopcates', $this->shopcates);
        $this->assign('ctl', strtolower(MODULE_NAME)); //主要方便调用
        $this->assign('act', ACTION_NAME);

        $this->assign('today', TODAY); //兼容模版的其他写法
        $this->assign('nowtime', NOW_TIME);
    }

    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {

        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content = '', $prefix = '');
    }

    private function parseTemplate($template = '') {

        $depr = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        // 获取当前主题名称
        // 获取当前主题的模版路径
          $theme = $this->getTemplateTheme();
          
            define('NOW_PATH',BASE_PATH.'/themes/'.$theme.'Store/');
        // 获取当前主题的模版路径
        define('THEME_PATH', BASE_PATH . '/themes/default/Store/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Store/');
   
        
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
            $themes = D('Template')->fetchAll();
            if (C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
                $t = C('VAR_TEMPLATE');
                if (isset($_GET[$t])) {
                    $theme = $_GET[$t];
                } elseif (cookie('think_template')) {
                    $theme = cookie('think_template');
                }
                if (!isset($themes[$theme])) {
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



///开始
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

    protected function niuAlert($message, $url = '') {
        $str = '<script>';
        $str.='parent.alert("' . $message . '");';
        if (!empty($url)) {
            $str.='parent.location.href="' . $url . '";';
        }

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

    protected function niuLoginSuccess() { //异步登录
        $str = '<script>';
        $str .='parent.parent.LoginSuccess();';
        $str.='</script>';
        exit($str);
    }
	
	 protected function baoOpen($message, $close = true, $style) {
        $str = '<script>';
        $str .='parent.bopen("' . $message . '","' . $close . '","' . $style . '");';
        $str.='</script>';
        exit($str);
    }

//结束
   

    

}
