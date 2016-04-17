<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class SmsModel extends CommonModel{
    protected $pk   = 'sms_id';
    protected $tableName =  'sms';
    protected $token  = 'bao_sms';
	
    
    public function sendSms($code,$mobile,$data){
        $tmpl = $this->fetchAll();
        if(!empty($tmpl[$code]['is_open'])){
            $content = $tmpl[$code]['sms_tmpl'];
            $config = D('Setting')->fetchAll();
            $data['sitename'] = $config['site']['sitename'];
            $data['tel']      = $config['site']['tel'];
            foreach($data as $k=>$val){
                 $val = str_replace('【', '', $val);
                $val = str_replace('】', '', $val);
               $content =  str_replace('{'.$k.'}', $val, $content);
            }
            if(is_array($mobile)){
                $mobile = join(',',$mobile);
            }
          
            if($config['sms']['charset']){
                $content = auto_charset($content,'UTF8','gbk');
            }
            $local = array(
                'mobile'    => $mobile,
                'content'   => $content
            );
            $http = tmplToStr($config['sms']['url'], $local);
            $res = file_get_contents($http);
            if($res == $config['sms']['code']) return true;
        }
        return false;
    }
    
	  /**
     * 大鱼短信发送
     * 下面是调用示例：
     * D('Sms')->DySms('登录验证', '模板ID', '手机号码', array('模板变量' => '值'));
     */
    public function DySms($sign, $code, $mobile, $data){
		$config = D('Setting')->fetchAll();
		$dycode = D('Dayu')->where(array("dayu_local='{$code}'"))->find();
        if(!empty($dycode['is_open'])){
        import('ORG.Util.Dayu');
        $obj = new AliSms($config['sms']['dykey'], $config['sms']['dysecret']);
        if($obj->sign($sign)->data($data)->code($dycode['dayu_tag'])->send($mobile)){
            return true;
        	}
		}
        return false;
    }
	
    public function mallTZshop($order_id){
        if(is_numeric($order_id) &&  ($order_id = (int)$order_id)){
           $order_id = array($order_id); 
        }
        $config = D('Setting')->fetchAll();
        $orders = D('Order')->itemsByIds($order_id);
        $shop = array();
        foreach($orders as $val){
            $shop[$val['shop_id']] =$val['shop_id'];             
        }
        $shops = D('Shop')->itemsByIds($shop);
        foreach($shops as $val){
            if($config['sms']['dxapi'] == 'dy'){
                $this->DySms($config['site']['sitename'],'sms_sctzsj', $shop['mobile'], array('sitename'=>$config['site']['sitename']));
            }else{
                $this->sendSms('sms_shop_mall', $val['mobile'], array());
            }
        }
        return true;
    }
    
    public function eleTZshop($order_id){
        if(is_numeric($order_id) &&  ($order_id = (int)$order_id)){
          $order = D('Eleorder')->find($order_id); 
          $config = D('Setting')->fetchAll();
          $shop = D('Shop')->find($order['shop_id']);
          if($config['sms']['dxapi'] == 'dy'){
            $this->DySms($config['site']['sitename'],'sms_sctzsj', $shop['mobile'], array('sitename'=>$config['site']['sitename']));
          }else{
            $this->sendSms('sms_shop_ele', $shop['mobile'], array());
          }		  
        }
        return true;
    }

	public function dingTZshop($order_id){
        if(is_numeric($order_id) &&  ($order_id = (int)$order_id)){
          $config = D('Setting')->fetchAll();
          $order = D('Shopdingorder')->find($order_id); 
          $shop = D('Shop')->find($order['shop_id']);
          if($config['sms']['dxapi'] == 'dy'){
            $this->DySms($config['site']['sitename'],'sms_sctzsj', $shop['mobile'], array('sitename'=>$config['site']['sitename']));
          }else{
            $this->sendSms('sms_shop_ele', $shop['mobile'], array());
          }
        }
        return true;
    }
    
     public function tuanTZshop($shop_id){
        $shop_id = (int)$shop_id;
        $shop = D('Shop')->find($shop_id);
        $config = D('Setting')->fetchAll();
        //file_put_contents('/www/web/bao_baocms_cn/public_html/Baocms/Lib/Model/aaa.txt', var_export($shop, true));
        if($config['sms']['dxapi'] == 'dy'){
            $this->DySms($config['site']['sitename'],'sms_tgtzsj', $shop['mobile'], array('sitename'=>$config['site']['sitename']));
        }else{
            $this->sendSms('sms_shop_tuan', $shop['mobile'], array());
        }        
        return true;
    }
  
    
     public function fetchAll(){
        $cache = cache(array('type'=>'File','expire'=>  $this->cacheTime));
        if(!$data = $cache->get($this->token)){
            $result = $this->order($this->orderby)->select();
            $data = array();
            foreach($result  as $row){
                $data[$row['sms_key']] = $row;
            }
            $cache->set($this->token,$data);
        }   
        return $data;
     }
  
}