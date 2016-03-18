<?php

function array_comparison($v1, $v2) { //比较数组
    if ($v1 === $v2) {
        return 0;
    }
    if ($v1 > $v2) {
        return 1;
    } else {
        return -1;
    }
}



function setUid($uid){
    import("ORG/Crypt/Base64");
    $uid = 'USER_'.$uid.'_'.NOW_TIME;
    $uid = Base64::encrypt($uid, C('AUTH_KEY'));
    cookie('BAOCMS_TOKEN',$uid,86400*365); //存一年
    return true;
}
function clearUid(){
    cookie('BAOCMS_TOKEN',null); //存一年
    return true;
}
//不清楚是什么东西
function getUid(){
    import("ORG/Crypt/Base64");
    $token = cookie('BAOCMS_TOKEN');
    $token = Base64::decrypt($token, C('AUTH_KEY'));
    $token = explode('_', $token);
    if($token[0]!= 'USER') return 0;
    return (int)$token[1];
}

function export_csv($filename,$data)   
{   
    header("Content-type:text/csv");   
    header("Content-Disposition:attachment;filename=".$filename);   
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
    header('Expires:0');   
    header('Pragma:public');   
    echo $data;die;   
} 


function searchWordFrom() { //主要方便存入COOKIE（跟踪一个月）
    
    if(!empty($_GET['tuiyitui'])){//全局的推广连接可以 主要是投放广告等监控使用
        $keyword = htmlspecialchars($_GET['tuiyitui']);
        $from = 'tui';//推广
        cookie('tui_from',$keyword,30*86400);//存放在COOKIE 一个月
    }
    
    $referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
    if(strstr( $referer, 'baidu.com')){ //百度
        preg_match( "|baidu.+wo?r?d=([^\&]*)|is", $referer, $tmp );
        $keyword = htmlspecialchars(urldecode( $tmp[1] ));
        $from = 'baidu';
    }elseif(strstr( $referer, 'google.com') or strstr( $referer, 'google.cn')){ //谷歌
        preg_match( "|google.+q=([^\&]*)|is", $referer, $tmp );
        $keyword = htmlspecialchars(urldecode( $tmp[1] ));
        $from = 'google';
    }elseif(strstr( $referer, 'soso.com')){ //搜搜
        preg_match( "|soso.com.+w=([^\&]*)|is", $referer, $tmp );
        $keyword = htmlspecialchars(urldecode( $tmp[1] ));
        $from = 'soso';
    }elseif(strstr( $referer, 'so.com')){ //360搜索
        preg_match( "|so.+q=([^\&]*)|is", $referer, $tmp );
        $keyword = htmlspecialchars(urldecode( $tmp[1] ));
        $from = '360';  
    }elseif(strstr( $referer, 'sogou.com')){ //搜狗
        preg_match( "|sogou.com.+query=([^\&]*)|is", $referer, $tmp );
        $keyword = htmlspecialchars(urldecode( $tmp[1] ));
        $from = 'sogou';    
    }else{
        return false;
    }
    cookie('search_word_from',json_encode(array('keyword'=>$keyword,'from'=>$from)),30*86400);//存放在COOKIE 一个月
    return true;
}


require_cache(APP_PATH.'Lib/phpqrcode/phpqrcode.php'); //引入二维码生成图片
//baoQrCode
function baoQrCode($token,$url,$size = 8){ //生成网址的二维码 返回图片地址
    $md5 = md5($token);
    $dir = substr($md5,0,3).'/'.substr($md5,3,3).'/'.substr($md5,6,3).'/';
    $patch =BASE_PATH.'/attachs/'. 'weixin/'.$dir;
    if(!file_exists($patch)){
        mkdir($patch,0755,true);
    }
    $file = 'weixin/'.$dir.$md5.'.png';
    $fileName  =BASE_PATH.'/attachs/'.$file;
    if(!file_exists($fileName)){
        $level = 'L';
        if(strstr($url,__HOST__)){
            $data = $url;
        }else{
            $data =__HOST__. $url;
        }
        QRcode::png($data, $fileName, $level, $size,2,true);
    }
    return $file;
}
function is_mobile() { 
    $user_agent = $_SERVER['HTTP_USER_AGENT']; 
    $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi", 
    "android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio", 
    "au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu", 
    "cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ", 
    "fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi", 
    "htc","huawei","hutchison","inno","ipad","ipaq","iphone","ipod","jbrowser","kddi", 
    "kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo", 
    "mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-", 
    "moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia", 
    "nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-", 
    "playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo", 
    "samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank", 
    "sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit", 
    "tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin", 
    "vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce", 
    "wireless","xda","xde","zte"); 
    $is_mobile = false; 
    foreach ($mobile_agents as $device) { 
        if (stristr($user_agent, $device)) { 
            $is_mobile = true; 
            break; 
        } 
    } 
    return $is_mobile; 
} 

function is_weixin() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
}
function isWx() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
}

function isMail($email) {
	$pattern = "/^[a-zA-Z][a-zA-z0-9-]*[@]([a-zA-Z0-9]*[.]){1,3}[a-zA-Z]*/";
	if(preg_match($pattern,$email)!= 1){
		return false;
	}else{
		return true;
	}
}

function isIos(){
	$is_iphone = (strpos($agent, 'iphone')) ? true : false; 
	$is_ipad = (strpos($agent, 'ipad')) ? true : false;  
	if($is_iphone==true || $is_ipad == true){
		return true;
	}else{
		return false;
	}
}
   


//专门给含有HTML的字段
function niuMsubstr($str,$start,$length,$suffix){
    
   $str = preg_replace( "@<(.*?)>@is", "", $str);
   return   msubstr($str, $start, $length, 'utf-8', $suffix);
}



//专门给含有HTML的字段
function bao_msubstr($str,$start,$length,$suffix){
    
   $str = preg_replace( "@<(.*?)>@is", "", $str);
   return   msubstr($str, $start, $length, 'utf-8', $suffix);
}


function isSecondDomain($domain){
    return (boolean) preg_match('/^[a-z0-9]{4,10}$/i', $domain);
}

function getDomain($url) {
    $host = strtolower($url);
    if (strpos($host, '/') !== false) {
        $parse = @parse_url($host);
        $host = $parse ['host'];
    }
    $topleveldomaindb = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me');
    $str = '';
    foreach ($topleveldomaindb as $v) {
        $str .= ($str ? '|' : '') . $v;
    }

    $matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
    if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
        $domain = $matchs ['0'];
    } else {
        $domain = $host;
    }
    return $domain;
}



//时间格式化2
function formatTime($time) {

    $t = NOW_TIME - $time;
    $mon = (int) ($t / (86400 * 30));
    if ($mon >= 1) {
        return '一个月前';
    }
    $day = (int) ($t / 86400);
    if ($day >= 1) {
        return $day . '天前';
    }
    $h = (int) ($t / 3600);
    if ($h >= 1) {
        return $h . '小时前';
    }
    $min = (int) ($t / 60);
    if ($min >= 1) {
        return $min . '分前';
    }
    return '刚刚';
}

/*
 * 经度纬度 转换成距离
 * $lat1 $lng1 是 数据的经度纬度
 * $lat2,$lng2 是获取定位的经度纬度
 */

function rad($d) {
    return $d * 3.1415926535898 / 180.0;
}

function getDistanceNone($lat1, $lng1, $lat2, $lng2) {
    $EARTH_RADIUS = 6378.137;
    $radLat1 = rad($lat1);
    //echo $radLat1;  
    $radLat2 = rad($lat2);
    $a = $radLat1 - $radLat2;
    $b = rad($lng1) - rad($lng2);
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $s = $s * $EARTH_RADIUS;
    $s = round($s * 10000);
    return $s;
}

function getDistance($lat1, $lng1, $lat2, $lng2) {
    $s = getDistanceNone($lat1, $lng1, $lat2, $lng2);
    $s = $s / 10000;
    if ($s < 1) {
        $s = round($s * 1000);
        $s.='m';
    } else {
        $s = round($s, 2);
        $s.='km';
    }
    return $s;
}

function getDistanceCN($lat1, $lng1, $lat2, $lng2) {
    $s = getDistanceNone($lat1, $lng1, $lat2, $lng2);
    $s = $s / 10000;
    if ($s < 1) {
        $s = round($s * 1000);
        $s.='米';
    } else {
        $s = round($s, 2);
        $s.='千米';
    }
    return $s;
}


//空白区域插件
function block($id) {
    if (!defined('THEME_NAME'))
        return '';
    $token = 'bao_' . THEME_NAME . '_' . $id;
    $cache = cache(array('type' => 'File', 'expire' => 600)); //10分钟缓存
    if (!$content = $cache->get($token)) {
        $settings = D('Templatesetting')->getWidgetsByThemeBlock(THEME_NAME, $id);
        if (empty($settings))
            return '';
        $content = '';
        foreach ($settings as $data) {
            $cfg = $data['setting'];
            $cfg['setting_id'] = $data['setting_id'];
            $content.= W($data['widget'], $cfg, true);
        }
        $cache->set($token, $content);
    }
    return $content;
}

function tmplToStr($str, $datas) {
    preg_match_all('/{(.*?)}/', $str, $arr);
    foreach ($arr[1] as $k => $val) {
        $v = isset($datas[$val]) ? $datas[$val] : '';
        $str = str_replace($arr[0][$k], $v, $str);
    }
    return $str;
}

function arrayToObject($e) {
    if (gettype($e) != 'array')
        return;
    foreach ($e as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object')
            $e[$k] = (object) arrayToObject($v);
    }
    return (object) $e;
}

function objectToArray($e) {
    $e = (array) $e;
    foreach ($e as $k => $v) {
        if (gettype($v) == 'resource')
            return;
        if (gettype($v) == 'object' || gettype($v) == 'array')
            $e[$k] = (array) objectToArray($v);
    }
    return $e;
}

function delFileByDir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
           
            $fullpath = $dir . "/" . $file;
            if(is_dir($fullpath)) {
                delFileByDir($fullpath);
            }else{
                unlink($fullpath);
            }
        }
    }
    closedir($dh);
}

function getDirName($dir) {
    $dh = opendir($dir);
    $return = array();
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (is_dir($fullpath)) {
                $return[$file] = $file;
            }
        }
    }
    closedir($dh);
    return $return;
}



function LinkTo($ctl, $vars = array(),$var2=array()) {
    $vars = array_merge($vars,$var2);
    foreach ($vars as $k => $v) {
        if (empty($v))
            unset($vars[$k]);
    }
    return U($ctl, $vars);
}
//获取IP返回地址的函数
function IpToArea($_ip) {
    static $IpLocation;
    if(empty($IpLocation)){
         import('ORG.Net.IpLocation'); // 
         $IpLocation = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
    }
    $arr = $IpLocation->getlocation($_ip);
 
    return $arr['country'] . $arr['area'];
}

/**
 * 后台模版带权限的URL校验
 * @param string $url URL表达式，格式：'[分组/模块/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string $title  标题
 * @param string $mini  是否异步加载
 * @param string $class A标签样式
 * @param string|array  $vars 传入的参数，支持数组和字符串
 * @return string
 */
function BA($url = '', $vars = '', $title = '', $mini = "", $class = "", $width = '', $height = '') {
	static $admin;

    if(empty($admin)){
	
		$admin = session('admin');
		$admin['menu_list'] = D('RoleMaps')->getMenuIdsByRoleId($admin['role_id']);
    }
	if ($admin['role_id'] != 1) {
        $menu = D('Menu')->fetchAll();
		//print_r($menu);exit;
        $menu_id = 0;
        foreach ($menu as $k => $v) {
            if ($v['menu_action'] == $url) {
                $menu_id = (int) $k;
            }
        }
        if (empty($menu_id) || !isset($admin['menu_list'][$menu_id])) {
			
            $url = 'javascript:void(0);';
            $title = '未授权';
            $mini = '';
        } else {
            $url = U($url, $vars);
        }
    } else {
        $url = U($url, $vars);
    }

    //权限判断 暂时忽略，后面补充
    $m = $c = $h = $w = '';
    if (!empty($mini)) {
        $m = ' mini="' . $mini . '"  ';
    }
    if (!empty($class)) {
        $c = ' class="' . $class . ' " ';
    }
    if (!empty($width)) {
        $w = ' w="' . $width . ' " ';
    }
    if (!empty($width)) {
        $h = ' h="' . $height . ' " ';
    }

    return '<a href="' . $url . '" ' . $m . $c . $w . $h . ' >' . $title . '</a>';
}

/**
 * 过滤不安全的HTML代码
 */
function SecurityEditorHtml($str) {
    $farr = array(
        "/\s+/", //过滤多余的空白 
        "/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU"
    );
    $tarr = array(
        " ",
        "＜\\1\\2\\3＞",
        "\\1\\2",
    );
    $str = preg_replace($farr, $tarr, $str);
    return $str;
}

/**
 * 判断一个字符串是否是一个Email地址
 *
 * @param string $string
 * @return boolean
 */
function isEmail($string) {
    return (boolean) preg_match('/^[a-z0-9.\-_]{2,64}@[a-z0-9]{2,32}(\.[a-z0-9]{2,5})+$/i', $string);
}

/**
 * 检查是否为一个合法的时间格式
 *
 * @access  public
 * @param   string  $time
 * @return  void
 */
function isTime($time) {
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

    return preg_match($pattern, $time);
}

/**
 * 判断一个字符串是否是一个合法时间
 *
 * @param string $string
 * @return boolean
 */
function isDate($string) {
    if (preg_match('/^\d{4}-[0-9][0-9]-[0-9][0-9]$/', $string)) {
        $date_info = explode('-', $string);
        return checkdate(ltrim($date_info[1], '0'), ltrim($date_info[2], '0'), $date_info[0]);
    }
    if (preg_match('/^\d{8}$/', $string)) {
        return checkdate(ltrim(substr($string, 4, 2), '0'), ltrim(substr($string, 6, 2), '0'), substr($string, 0, 4));
    }
    return false;
}

/**
 * 判断输入的字符串是否是一个合法的电话号码（仅限中国大陆）
 *
 * @param string $string
 * @return boolean
 */
function isPhone($string) {
    if (preg_match('/^[0,4]\d{2,3}-\d{7,8}$/', $string))
        return true;
    return false;
}

/**
 * 判断输入的字符串是否是一个合法的手机号(仅限中国大陆)
 *
 * @param string $string
 * @return boolean
 */
function isMobile($string) {
    if(preg_match('/^[1]+[3,4,5,7,8]+\d{9}$/', $string))
            return true;
        return false;
    //return ctype_digit($string) && (11 == strlen($string)) && ($string[0] == 1);
}

/**
 * 判断输入的字符串是否是一个合法的QQ
 *
 * @param string $string
 * @return boolean
 */
function isQQ($string) {
    if (ctype_digit($string)) {
        $len = strlen($string);
        if ($len < 5 || $len > 13)
            return false;
        return true;
    }
    return isEmail($string);
}

/**
 *
 * @param string $fileName
 * @return boolean
 */
function isImage($fileName) {
    $ext = explode('.', $fileName);
    $ext_seg_num = count($ext);
    if ($ext_seg_num <= 1)
        return false;

    $ext = strtolower($ext[$ext_seg_num - 1]);
    return in_array($ext, array('jpeg', 'jpg', 'png', 'gif'));
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {//位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str.= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

/**
 * 获取登录验证码 默认为4位数字
 * @param string $fmode 文件名
 * @return string
 */
function build_verify($length = 4, $mode = 1) {
    return rand_string($length, $mode);
}

/**
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * @return string
 */
function byte_format($size, $dec = 2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}

/**
 * 检查字符串是否是UTF8编码
 * @param string $string 字符串
 * @return Boolean
 */
function is_utf8($string) {
    return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
}

/**
 * 代码加亮
 * @param String  $str 要高亮显示的字符串 或者 文件名
 * @param Boolean $show 是否输出
 * @return String
 */
function highlight_code($str, $show = false) {
    if (file_exists($str)) {
        $str = file_get_contents($str);
    }
    $str = stripslashes(trim($str));
    // The highlight string function encodes and highlights
    // brackets so we need them to start raw
    $str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);

    // Replace any existing PHP tags to temporary markers so they don't accidentally
    // break the string out of PHP, and thus, thwart the highlighting.

    $str = str_replace(array('&lt;?php', '?&gt;', '\\'), array('phptagopen', 'phptagclose', 'backslashtmp'), $str);

    // The highlight_string function requires that the text be surrounded
    // by PHP tags.  Since we don't know if A) the submitted text has PHP tags,
    // or B) whether the PHP tags enclose the entire string, we will add our
    // own PHP tags around the string along with some markers to make replacement easier later

    $str = '<?php //tempstart' . "\n" . $str . '//tempend ?>'; // <?
    // All the magic happens here, baby!
    $str = highlight_string($str, TRUE);

    // Prior to PHP 5, the highlight function used icky font tags
    // so we'll replace them with span tags.
    if (abs(phpversion()) < 5) {
        $str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
        $str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
    }

    // Remove our artificially added PHP
    $str = preg_replace("#\<code\>.+?//tempstart\<br />\</span\>#is", "<code>\n", $str);
    $str = preg_replace("#\<code\>.+?//tempstart\<br />#is", "<code>\n", $str);
    $str = preg_replace("#//tempend.+#is", "</span>\n</code>", $str);

    // Replace our markers back to PHP tags.
    $str = str_replace(array('phptagopen', 'phptagclose', 'backslashtmp'), array('&lt;?php', '?&gt;', '\\'), $str); //<?
    $line = explode("<br />", rtrim(ltrim($str, '<code>'), '</code>'));
    $result = '<div class="code"><ol>';
    foreach ($line as $key => $val) {
        $result .= '<li>' . $val . '</li>';
    }
    $result .= '</ol></div>';
    $result = str_replace("\n", "", $result);
    if ($show !== false) {
        echo($result);
    } else {
        return $result;
    }
}

//输出安全的html
function h($text, $tags = null) {
    $text = trim($text);
    //完全过滤注释
    $text = preg_replace('/<!--?.*-->/', '', $text);
    //完全过滤动态代码
    $text = preg_replace('/<\?|\?' . '>/', '', $text);
    //完全过滤js
    $text = preg_replace('/<script?.*\/script>/', '', $text);

    $text = str_replace('[', '&#091;', $text);
    $text = str_replace(']', '&#093;', $text);
    $text = str_replace('|', '&#124;', $text);
    //过滤换行符
    $text = preg_replace('/\r?\n/', '', $text);
    //br
    $text = preg_replace('/<br(\s\/)?' . '>/i', '[br]', $text);
    $text = preg_replace('/<p(\s\/)?' . '>/i', '[br]', $text);
    $text = preg_replace('/(\[br\]\s*){10,}/i', '[br]', $text);
    //过滤危险的属性，如：过滤on事件lang js
    while (preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i', $text, $mat)) {
        $text = str_replace($mat[0], $mat[1], $text);
    }
    while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
        $text = str_replace($mat[0], $mat[1] . $mat[3], $text);
    }
    if (empty($tags)) {
        $tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
    }
    //允许的HTML标签
    $text = preg_replace('/<(' . $tags . ')( [^><\[\]]*)>/i', '[\1\2]', $text);
    $text = preg_replace('/<\/(' . $tags . ')>/Ui', '[/\1]', $text);
    //过滤多余html
    $text = preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i', '', $text);
    //过滤合法的html标签
    while (preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i', $text, $mat)) {
        $text = str_replace($mat[0], str_replace('>', ']', str_replace('<', '[', $mat[0])), $text);
    }
    //转换引号
    while (preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i', $text, $mat)) {
        $text = str_replace($mat[0], $mat[1] . '|' . $mat[3] . '|' . $mat[4], $text);
    }
    //过滤错误的单个引号
    while (preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i', $text, $mat)) {
        $text = str_replace($mat[0], str_replace($mat[1], '', $mat[0]), $text);
    }
    //转换其它所有不合法的 < >
    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);
    $text = str_replace('"', '&quot;', $text);
    //反转换
    $text = str_replace('[', '<', $text);
    $text = str_replace(']', '>', $text);
    $text = str_replace('|', '"', $text);
    //过滤多余空格
    $text = str_replace('  ', ' ', $text);
    return $text;
}

function ubb($Text) {
    $Text = trim($Text);
    //$Text=htmlspecialchars($Text);
    $Text = preg_replace("/\\t/is", "  ", $Text);
    $Text = preg_replace("/\[h1\](.+?)\[\/h1\]/is", "<h1>\\1</h1>", $Text);
    $Text = preg_replace("/\[h2\](.+?)\[\/h2\]/is", "<h2>\\1</h2>", $Text);
    $Text = preg_replace("/\[h3\](.+?)\[\/h3\]/is", "<h3>\\1</h3>", $Text);
    $Text = preg_replace("/\[h4\](.+?)\[\/h4\]/is", "<h4>\\1</h4>", $Text);
    $Text = preg_replace("/\[h5\](.+?)\[\/h5\]/is", "<h5>\\1</h5>", $Text);
    $Text = preg_replace("/\[h6\](.+?)\[\/h6\]/is", "<h6>\\1</h6>", $Text);
    $Text = preg_replace("/\[separator\]/is", "", $Text);
    $Text = preg_replace("/\[center\](.+?)\[\/center\]/is", "<center>\\1</center>", $Text);
    $Text = preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\2</a>", $Text);
    $Text = preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\2</a>", $Text);
    $Text = preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\1</a>", $Text);
    $Text = preg_replace("/\[url\]([^\[]*)\[\/url\]/is", "<a href=\"\\1\" target=_blank>\\1</a>", $Text);
    $Text = preg_replace("/\[img\](.+?)\[\/img\]/is", "<img src=\\1>", $Text);
    $Text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color=\\1>\\2</font>", $Text);
    $Text = preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is", "<font size=\\1>\\2</font>", $Text);
    $Text = preg_replace("/\[sup\](.+?)\[\/sup\]/is", "<sup>\\1</sup>", $Text);
    $Text = preg_replace("/\[sub\](.+?)\[\/sub\]/is", "<sub>\\1</sub>", $Text);
    $Text = preg_replace("/\[pre\](.+?)\[\/pre\]/is", "<pre>\\1</pre>", $Text);
    $Text = preg_replace("/\[email\](.+?)\[\/email\]/is", "<a href='mailto:\\1'>\\1</a>", $Text);
    $Text = preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis", "color_txt('\\1')", $Text);
    $Text = preg_replace("/\[emot\](.+?)\[\/emot\]/eis", "emot('\\1')", $Text);
    $Text = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $Text);
    $Text = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $Text);
    $Text = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $Text);
    $Text = preg_replace("/\[quote\](.+?)\[\/quote\]/is", " <div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $Text);
    $Text = preg_replace("/\[code\](.+?)\[\/code\]/eis", "highlight_code('\\1')", $Text);
    $Text = preg_replace("/\[php\](.+?)\[\/php\]/eis", "highlight_code('\\1')", $Text);
    $Text = preg_replace("/\[sig\](.+?)\[\/sig\]/is", "<div class='sign'>\\1</div>", $Text);
    $Text = preg_replace("/\\n/is", "<br/>", $Text);
    return $Text;
}

function cleanhtml($str, $length = 0, $suffix = true) {
    $str = preg_replace("@<(.*?)>@is", "", $str);
    if ($length > 0) {
        $str = msubstr($str, 0, $length, 'utf-8', $suffix);
    }
    return $str;
}

// 随机生成一组字符串
function build_count_rand($number, $length = 4, $mode = 1) {
    if ($mode == 1 && $length < strlen($number)) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand = array();
    for ($i = 0; $i < $number; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $unqiue = array_unique($rand);
    if (count($unqiue) == count($rand)) {
        return $rand;
    }
    $count = count($rand) - count($unqiue);
    for ($i = 0; $i < $count * 3; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $rand = array_slice(array_unique($rand), 0, $number);
    return $rand;
}

function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $parent[$child][] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 在数据列表中搜索
 * @access public
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list, $condition) {
    if (is_string($condition))
        parse_str($condition, $condition);
    // 返回的结果集合
    $resultSet = array();
    foreach ($list as $key => $data) {
        $find = false;
        foreach ($condition as $field => $value) {
            if (isset($data[$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } elseif ($data[$field] == $value) {
                    $find = true;
                }
            }
        }
        if ($find)
            $resultSet[] = &$list[$key];
    }
    return $resultSet;
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from = 'gbk', $to = 'utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}

/* 提取所有图片 */
function getImgs($content,$order='all'){
	$pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
	preg_match_all($pattern,$content,$match);
	if(isset($match[1])&&!empty($match[1])){
		if($order==='all'){
			return $match[1];
		}
		if(is_numeric($order)&&isset($match[1][$order])){
			return $match[1][$order];
		}
	}
	return '';
}

//格式化打印函数
function p($array) {
	dump ( $array, 1, '<pre style=font-size:14px;color:#00ae19;>', 0 );
}
/*对象转换为数组*/
function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
     } if(is_array($array)) {  
         foreach($array as $key=>$value) {  
             $array[$key] = object_array($value);  
             }  
     }  
     return $array;  
}

//重复数组
function a_array_unique($array){
   $out = array();
   foreach ($array as $key=>$value){
       if (!in_array($value, $out)){
           $out[$key] = $value;
       }
   }
   return $out;
}

//坐标范围
function returnSquarePoint($lng, $lat,$distance){
    $dlng =  2 * asin(sin($distance / (2 * 6378.2)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);
    $dlat = $distance/6378.2;
    $dlat = rad2deg($dlat);
    return array(
		'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
		'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
		'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
		'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
	);
}

//偏移换算
function placeToBaidu($lng,$lat){
	$p = 3.14159265358979324 * 6378.2 / 360.0;
	$x = $lng;
	$y = $lat;
	$z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $p); 
	$theta = atan2($y, $x) + 0.000003 * cos($x * $p); 
	$bd_lng = $z * cos($theta) + 0.0065;
	$bd_lat = $z * sin($theta) + 0.006;
	return array('lng' => $bd_lng ,'lat' => $bd_lat);
}
///////丶carrot添加全局归递找父级///////
function get_all_parent ($array, $cate_id) {
	$arr = array();
	foreach ($array as $v) {
		if ($v['cate_id'] == $cate_id) {
			$arr[] = $v;
			$arr = array_merge($arr, get_all_parent($array, $v['parent_id']));
		}
	}
	return $arr;
}

///////丶carrot添加结束///////

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
