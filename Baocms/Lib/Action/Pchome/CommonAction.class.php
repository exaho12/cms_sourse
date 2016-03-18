<?php
class CommonAction extends Action{
    protected $uid = 0;
    protected $member = array();
    protected $_CONFIG = array();
    protected $seodatas = array();
    protected $shopcates = array();
    protected $citys = array();
    protected $areas = array();
    protected $bizs = array();
    protected $template_setting = array();
    protected $city_id = 0;
    protected $city = array();
    protected $_admin = array();
    protected function _initialize(){  
        define('__HOST__', 'http://' . $_SERVER['HTTP_HOST']);
        $this->_CONFIG = d('Setting')->fetchAll();
        $this->citys = d('City')->fetchAll();
        $this->assign('citys', $this->citys);
        $this->city_id = cookie('city_id');
        if (empty($this->city_id)) {
            import('ORG/Net/IpLocation');
            $IpLocation = new IpLocation('UTFWry.dat');
            $result = $IpLocation->getlocation($_SERVER['REMOTE_ADDR']);
            foreach ($this->citys as $val) {
                if (strstr($result['country'], $val['name'])) {
                    $city = $val;
                    $this->city_id = $val['city_id'];
                    break;
                }
            }
            if (empty($city)) {
                $this->city_id = $this->_CONFIG['site']['city_id'];
                $city = $this->citys[$this->_CONFIG['site']['city_id']];
            }
        } else {
            $city = $this->citys[$this->city_id];
        }
        $this->city = $city;
        searchwordfrom();
        $this->uid = getuid();
        if (!empty($this->uid)) {
            $this->member = d('Users')->find($this->uid);
        }
        $this->shopcates = d('Shopcate')->fetchAll();
        $this->assign('shopcates', $this->shopcates);
        $this->Tuancates = d('Tuancate')->fetchAll();
        $this->assign('tuancates', $this->Tuancates);
        $this->areas = d('Area')->fetchAll();
        $this->assign('areas', $this->areas);
        $limit_area = array();
        foreach ($this->areas as $k => $val) {
            if ($val['city_id'] == $this->city_id) {
                $limit_area[] = $val['area_id'];
            }
        }
        $this->bizs = d('Business')->fetchAll();
        $this->assign('bizs', $this->bizs);
        $this->assign('limit_area', $limit_area);
        $this->assign('ctl', strtolower(MODULE_NAME));
        $this->assign('contrl', strtolower(CONTROLLER_NAME));
        $this->assign('act', ACTION_NAME);
        $this->assign('nowtime', NOW_TIME);
        $this->assign('city_name', $city['name']);
        $this->assign('city_id', $this->city_id);
        $this->getTemplateTheme();
        $this->template_setting = d('Templatesetting')->detail($this->theme);
        $this->assign('CONFIG', $this->_CONFIG);
        $this->assign('MEMBER', $this->member);
        $this->assign('today', TODAY);
        $city_ids = array('0', $this->city_id);
        $city_ids = join(',', $city_ids);
        $this->assign('city_ids', $city_ids);
		
		$url_jump = $this->_CONFIG['other']['url_jump'];//是否开启PC自动跳转
        if ($url_jump == 1) {
				if (is_mobile()) {
				$url_mobile = 'http://' . $_SERVER['HTTP_HOST'] . '/mobile' . $_SERVER['REQUEST_URI'];
				header('Location:' . $url_mobile);
				die;
        	}
        }
		
		
        //分销开始
        $fuid = (int) $this->_get('fuid');
        if (!empty($fuid)) {
            $profit_expire = (int) $this->_CONFIG['site']['profit_expire'];
            if ($profit_expire) {
                cookie('fuid', $fuid, $profit_expire * 60 * 60);
            } else {
                cookie('fuid', $fuid);
            }
        }
        //分销结束
        //城市循环全局开始
        $citylists = array();
        foreach ($this->citys as $val) {
            if ($val['is_open'] == 1) {
                $a = strtoupper($val['first_letter']);
                $citylists[$a][] = $val;
            }
        }
        ksort($citylists);
        //重新整理排序
        $this->assign('citylists', $citylists);
        //城市循环结束
        //购物车开始
        $goods = cookie('goods');
        $this->assign('cartnum', (int) array_sum($goods));
        //购物车结束
		
		$mapssss = array('status' => 4,'closed'=>0);
		$this->assign('navigations',$navigations = D('Navigation') ->where($mapssss)->order(array('orderby' => 'asc'))->select());
		
        //底部导航
        $web_close = $this->_CONFIG['site']['web_close'];
        $web_close_title = $this->_CONFIG['site']['web_close_title'];
        if ($web_close == 0) {
            $this->display('public:web_close');
            die;
        }
		
		
		
		
    }
    private function seo()
    {
        $seo = d('Seo')->fetchAll();
        $this->seodatas['sitename'] = $this->_CONFIG['site']['sitename'];
        $this->seodatas['tel'] = $this->_CONFIG['site']['tel'];
        $key = strtolower(MODULE_NAME . '_' . ACTION_NAME);
        if (isset($seo[$key])) {
            $this->assign('seo_title', $this->tmplToStr($seo[$key]['seo_title'], $this->seodatas));
            $this->assign('seo_keywords', $this->tmplToStr($seo[$key]['seo_keywords'], $this->seodatas));
            $this->assign('seo_description', $this->tmplToStr($seo[$key]['seo_desc'], $this->seodatas));
        } else {
            $this->assign('seo_title', $this->_CONFIG['site']['title']);
            $this->assign('seo_keywords', $this->_CONFIG['site']['keyword']);
            $this->assign('seo_description', $this->_CONFIG['site']['description']);
        }
    }
    private function tmplToStr($str, $datas)
    {
        return tmpltostr($str, $datas);
    }
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '')
    {
        $this->seo();
        parent::display($this->parseTemplate($templateFile), $charset, $contentType, $content = '', $prefix = '');
    }
    private function parseTemplate($template = '')
    {
        $depr = c('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);
        $theme = $this->getTemplateTheme();
        define('NOW_PATH', BASE_PATH . '/themes/' . $theme . 'Pchome/');
        define('THEME_PATH', BASE_PATH . '/themes/default/Pchome/');
        define('APP_TMPL_PATH', __ROOT__ . '/themes/default/Pchome/');
        if ('' == $template) {
            $template = strtolower(MODULE_NAME) . $depr . strtolower(ACTION_NAME);
        } else {
            if (false === strpos($template, '/')) {
                $template = strtolower(MODULE_NAME) . $depr . strtolower($template);
            }
        }
        $file = NOW_PATH . $template . c('TMPL_TEMPLATE_SUFFIX');
        if (file_exists($file)) {
            return $file;
        }
        return THEME_PATH . $template . c('TMPL_TEMPLATE_SUFFIX');
    }
    private function getTemplateTheme()
    {
        define('THEME_NAME', 'default');
        if ($this->theme) {
            $theme = $this->theme;
        } else {
            $theme = d('Template')->getDefaultTheme();
            if (c('TMPL_DETECT_THEME')) {
                $t = c('VAR_TEMPLATE');
                if (isset($_GET[$t])) {
                    $theme = $_GET[$t];
                } else {
                    if (cookie('think_template')) {
                        $theme = cookie('think_template');
                    }
                }
                if (!in_array($theme, explode(',', c('THEME_LIST')))) {
                    $theme = c('DEFAULT_THEME');
                }
                cookie('think_template', $theme, 864000);
            }
            $this->theme = $theme;
        }
        return $theme ? $theme . '/' : '';
    }
    protected function baoMsg($message, $jumpUrl = '', $time = 3000, $callback = '', $parent = true)
    {
        $parents = $parent ? 'parent.' : '';
        $str = '<script>';
        $str .= $parents . 'bmsg("' . $message . '","' . $jumpUrl . '","' . $time . '","' . $callback . '");';
        $str .= '</script>';
        die($str);
    }
    //开始
    protected function niuSuccess($message, $jumpUrl = '', $time = 3000, $parent = true)
    {
        $parent = $parent ? 'parent.' : '';
        $str = '<script>';
        $str .= $parent . 'success("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str .= '</script>';
        die($str);
    }
    protected function niuSuccess2($message, $jumpUrl = '', $time = 3000, $parent = true)
    {
        $parent = $parent ? 'parent.' : '';
        $str = '<script>';
        $str .= $parent . 'success("' . $message . '",' . $time . ',\'jump("' . $jumpUrl . '")\');';
        $str .= '</script>';
        die($str);
    }
    protected function niuJump($jumpUrl)
    {
        $str = '<script>';
        $str .= 'parent.jumpUrl("' . $jumpUrl . '");';
        $str .= '</script>';
        die($str);
    }
    protected function niuErrorJump($message, $jumpUrl = '', $time = 3000)
    {
        $str = '<script>';
        $str .= 'parent.error("' . $message . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '")\');';
        $str .= '</script>';
        die($str);
    }
    protected function niuError($message, $time = 3000, $yzm = false, $parent = true)
    {
        $parent = $parent ? 'parent.' : '';
        $str = '<script>';
        if ($yzm) {
            $str .= $parent . 'error("' . $message . '",' . $time . ',"yzmCode()");';
        } else {
            $str .= $parent . 'error("' . $message . '",' . $time . ');';
        }
        $str .= '</script>';
        die($str);
    }
    protected function niuLoginSuccess()
    {
        //异步登录
        $str = '<script>';
        $str .= 'parent.parent.LoginSuccess();';
        $str .= '</script>';
        die($str);
    }
    //结束
    protected function baoOpen($message, $close = true, $style)
    {
        $str = '<script>';
        $str .= 'parent.bopen("' . $message . '","' . $close . '","' . $style . '");';
        $str .= '</script>';
        die($str);
    }
    protected function baoSuccess($message, $jumpUrl = '', $time = 3000, $parent = true)
    {
        $this->baoMsg($message, $jumpUrl, $time, '', $parent);
    }
    protected function baoJump($jumpUrl)
    {
        $str = '<script>';
        $str .= 'parent.jumpUrl("' . $jumpUrl . '");';
        $str .= '</script>';
        die($str);
    }
    protected function baoErrorJump($message, $jumpUrl = '', $time = 3000)
    {
        $this->baoMsg($message, $jumpUrl, $time);
    }
    protected function baoError($message, $time = 3000, $yzm = false, $parent = true)
    {
        $parent = $parent ? 'parent.' : '';
        $str = '<script>';
        if ($yzm) {
            $str .= $parent . 'bmsg("' . $message . '","",' . $time . ',"yzmCode()");';
        } else {
            $str .= $parent . 'bmsg("' . $message . '","",' . $time . ');';
        }
        $str .= '</script>';
        die($str);
    }
    protected function baoLoginSuccess()
    {
        $str = '<script>';
        $str .= 'parent.parent.LoginSuccess();';
        $str .= '</script>';
        die($str);
    }
    protected function ajaxLogin()
    {
        if ($mini = $this->_get('mini')) {
            die('0');
        }
        $str = '<script>';
        $str .= 'parent.ajaxLogin();';
        $str .= '</script>';
        die($str);
    }
    protected function ajaxLoginSuccess(){
        //异步登录新版
        $str = '<script>';
        $str .= 'parent.parent.ajaxLoginSuccess();';
        $str .= '</script>';
        die($str);
    }
    protected function checkFields($data = array(), $fields = array()){
        foreach ($data as $k => $val) {
            if (!in_array($k, $fields)) {
                unset($data[$k]);
            }
        }
        return $data;
    }
    protected function ipToArea($_ip){
        return iptoarea($_ip);
    }
    protected function getMenus(){
        $menus = $this->memberMenu();
        return $menus;
    }
}
