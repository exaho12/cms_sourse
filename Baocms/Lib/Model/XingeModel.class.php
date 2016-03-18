<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class XingeModel {
    /**
     * 信鸽推送过来的数据或响应数据
     * @var array
     */
    private $data = array();
    private $config = array();
    /**
     * 构造方法，用于实例化信鸽SDK
     * @param string $token 信鸽开放平台设置的TOKEN
     */
    public function __construct() {
        import("@/Net.XingeApp");
    }
    
    public function mass($data){
        $token = $this->getToken('android');
        //TODO
        $PushService = new PushService($token['appid'],$token['appsecret']);
        $ret= $PushService->PushAllAndroid($data['title'],$data['contents'],$data['url']);
        if($ret['err_msg']){
            return $ret['err_msg'];
        }


        $token = $this->getToken('ios');
        $PushService = new PushService($token['appid'],$token['appsecret']);
        $ret = $PushService->PushAllIos($data['title'],$data['contents'],$data['url']); 
        
        //群发信息
        $data['sendtype'] = '1';
        D('XingeHistory')->save($data);
        return true;
    }
    
    public function single(){
        $token = $this->getToken('android');
        $PushService = new PushService($token['appid'],$token['appsecret']);
        $ret= $PushService->PushSingleAndroid($data['title'],$data['contents'],$data['uid'],$data['url']);

        if($ret['err_msg']){
            $token = $this->getToken('ios');
            $PushService = new PushService($token['appid'],$token['appsecret']);
            $ret = $PushService->PushAllIos($data['title'],$data['contents'],$data['uid']); 
            if($ret['err_msg']){
                return false;
            }
        }
       // $data['sendtype'] = '0';
       //TODO D('XingeHistory')->save($data);
        return true;
    } 


    public function history(){
        exit;
        //TODO
        $Coupon = D('XingeHistory');
        import('ORG.Util.Page'); // 导入分页类
        $news= (int) $this->_get('order');
        if ($news == 1) {
        $orderby = array('id' => 'desc');
        }
        if($this->_param('type')){
            $data['type'] = $this->_get['type'];
        }
       
    }


    public function getToken($type) {
        $this->config = D('Setting')->fetchAll();
         switch ($type) {
            case 'ios':
                $data['appid'] = $this->config['xinge']['iosappid'];
                $data['appsecret'] = $this->config['xinge']['iosappsecret'];
                break;
            default:
                $data['appid'] = $this->config['xinge']['appid'];
                $data['appsecret'] = $this->config['xinge']['appsecret'];
                break;
        }
        return $data;
    }
    



    /**
     * 数据XML编码
     * @param  object $xml  XML对象
     * @param  mixed  $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string
     */
    private function data2xml($xml, $data, $item = 'item') {
        foreach ($data as $key => $value) {
            /* 指定默认的数字key */
            is_numeric($key) && $key = $item;

            /* 添加子元素 */
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

    /**
     * 对数据进行签名认证，确保是信鸽发送的数据
     * @param  string $token 信鸽开放平台设置的TOKEN
     * @return boolean       true-签名正确，false-签名错误
     */
    private function auth($token) {
        /* 获取数据 */
        $data = array($_GET['timestamp'], $_GET['nonce'], $token);
        $sign = $_GET['signature'];

        /* 对数据进行字典排序 */
        sort($data);

        /* 生成签名 */
        $signature = sha1(implode($data));
       // file_put_contents('/www/web/bao_baocms_cn/public_html/Baocms/Lib/Action/Weixin/bb.txt',$signature);
        return $signature === $sign;
    }


}

