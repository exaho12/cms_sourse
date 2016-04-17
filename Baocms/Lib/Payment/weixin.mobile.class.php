<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class weixin {

    public function init($payment) {
       // print_r($payment);die;
        define('WEIXIN_APPID', $payment['appid']);
        define('WEIXIN_MCHID', $payment['mchid']);
        define('WEIXIN_APPSECRET', $payment['appsecret']);
        define('WEIXIN_KEY',$payment['appkey']);
        //=======【证书路径设置】=====================================
        /**
         * TODO：设置商户证书路径
         * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
         * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
         * @var path
         */
        define('WEIXIN_SSLCERT_PATH', '../cert/apiclient_cert.pem');
        define('WEIXIN_SSLKEY_PATH', '../cert/apiclient_key.pem');

        //=======【curl代理设置】===================================
        /**
         * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
         * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
         * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
         * @var unknown_type
         */
        define('WEIXIN_CURL_PROXY_HOST', "0.0.0.0"); //"10.152.18.220";
        define('WEIXIN_CURL_PROXY_PORT', 0); //8080;
        //=======【上报信息配置】===================================
        /**
         * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
         * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
         * 开启错误上报。
         * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
         * @var int
         */
        define('WEIXIN_REPORT_LEVENL', 1);

        require_once "weixin/WxPay.Api.php";
        
        require_once "weixin/WxPay.JsApiPay.php";
        
        //require_once "weixin/WxPay.Notify.php";
        
    }

    public function getCode($logs, $payment) {
       
        $this->init($payment);
        //①、获取用户openid
        $tools = new JsApiPay();
     
        $openId = $tools->GetOpenid($logs);
       //echo $openId;die;
        $input = new WxPayUnifiedOrder();
        $input->SetBody($logs['subject']);
        $input->SetAttach($logs['subject']);
        $input->SetOut_trade_no($logs['logs_id']);
        $logs['logs_amount'] = $logs['logs_amount'] *100;
        $input->SetTotal_fee("{$logs['logs_amount']}");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($logs['subject']);
        $input->SetNotify_url(__HOST__ . U( 'mobile/payment/respond', array('code' => 'weixin')));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        //var_dump($order);die;
        //   echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';

        $jsApiParameters = $tools->GetJsApiParameters($order);
		
        $str = '<script>function jsApiCall()
	{
		WeixinJSBridge.invoke(
			\'getBrandWCPayRequest\',
			'.$jsApiParameters.',
			function(res){
                            if(res.err_msg ==\'get_brand_wcpay_request:ok\'){ 
                                location.href="'.U('mobile/payment/yes',array('log_id'=>$logs['logs_id'])).'";
                            }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener(\'WeixinJSBridgeReady\', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent(\'WeixinJSBridgeReady\', jsApiCall); 
		        document.attachEvent(\'onWeixinJSBridgeReady\', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}</script>
        
<button   class="button button-block bg-dot button-big" type="button" onclick="callpay()" >立即支付</button>


        ';
        
        
        return $str;
    }

    public function respond() {
        
       // file_put_contents('aaa.txt', var_export($_POST,true));
       // file_put_contents('bbb.txt', var_export($_GET,true));
        $xml = file_get_contents("php://input");
        if (empty($xml))
            return false;
        $xml = new SimpleXMLElement($xml);
        if (!$xml)
            return false;
        $data = array();
        foreach ($xml as $key => $value) {
            $data[$key] = strval($value);
        }
       // file_put_contents('ccc.txt', var_export($data,true));
        if (empty($data['return_code']) || $data['return_code'] != 'SUCCESS') {
            //file_put_contents('/www/web/baocms_cn/public_html/Baocms/Lib/Payment/aaa.txt', '1');
            return false;
        }
        if (empty($data['result_code']) || $data['result_code'] != 'SUCCESS') {
            //file_put_contents('/www/web/baocms_cn/public_html/Baocms/Lib/Payment/aaa.txt', '2');

            return false;
        }
        if (empty($data['out_trade_no'])){
           // file_put_contents('/www/web/baocms_cn/public_html/Baocms/Lib/Payment/aaa.txt', '3');

            return false;
        }
        ksort($data);
        reset($data);
        $payment = D('Payment')->getPayment('weixin');
        /* 检查支付的金额是否相符 */
        if (!D('Payment')->checkMoney($data['out_trade_no'], $data['total_fee'])) {
            return false;
        }

        $sign = array();
        foreach ($data as $key => $val) {
            if ($key != 'sign') {
                $sign[] = $key . '=' . $val;
            }
        }
        $sign[] = 'key=' . $payment['appkey'];
        $signstr = strtoupper(md5(join('&', $sign)));
        if ($signstr != $data['sign']){
           
           
            return false;
        }    
        D('Payment')->logsPaid($data['out_trade_no']);

        return true;
    }

}
