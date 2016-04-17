<?php

    //微信NATIVE--原生扫码支付
    require_once "weixin/WxPay.Api.php";

    require_once "weixin/WxPay.NativePay.php";

    require_once 'weixin/WxPay.Notify.php';

    require_once 'weixin/notify.php';

    import( 'WxPay' , APP_PATH . 'Lib/Payment/weixin' , '.Api.php' );

    class native
    {
//    （1）商户后台系统根据用户选购的商品生成订单。
//    （2）用户确认支付后调用微信支付【统一下单API】生成预支付交易；
//    （3）微信支付系统收到请求后生成预支付交易单，并返回交易会话的二维码链接code_url。
//    （4）商户后台系统根据返回的code_url生成二维码。
//    （5）用户打开微信“扫一扫”扫描二维码，微信客户端将扫码内容发送到微信支付系统。
//    （6）微信支付系统收到客户端请求，验证链接有效性后发起用户支付，要求用户授权。
//    （7）用户在微信客户端输入密码，确认支付后，微信客户端提交授权。
//    （8）微信支付系统根据用户授权完成支付交易。
//    （9）微信支付系统完成支付交易后给微信客户端返回交易结果，并将交易结果通过短信、微信消息提示用户。微信客户端展示支付交易结果页面。
//    （10）微信支付系统通过发送异步消息通知商户后台系统支付结果。商户后台系统需回复接收情况，通知微信后台系统不再发送该单的支付通知。
//    （11）未收到支付通知的情况，商户后台系统调用【查询订单API】。
//    （12）商户确认订单已支付后给用户发货。
        public function getCode( $logs , $payment )
        {
            //模式一
            /**
             * 流程：
             * 1、组装包含支付信息的url，生成二维码
             * 2、用户扫描二维码，进行支付
             * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
             * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
             * 5、支付完成之后，微信服务器会通知支付成功
             * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
             */
            $notify = new NativePay();
            $url1 = $notify->GetPrePayUrl( $logs['logs_id'] );
            $url1 = urlencode( $url1 );
            //模式二
            /**
             * 流程：
             * 1、调用统一下单，取得code_url，生成二维码
             * 2、用户扫描二维码，进行支付
             * 3、支付完成之后，微信服务器会通知支付成功
             * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
             */

            $input = new WxPayUnifiedOrder();
            $input->SetBody( $logs['subject'] );//是 商品或支付单简要描述
            $input->SetAttach( $logs['subject'] );//否 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
            $input->SetDetail( $logs['subject'] );//否 商品名称明细列表
            $input->SetOut_trade_no( $logs['logs_id'] );//商户系统内部的订单号,32个字符内、可包含字母,
            $logs['logs_amount'] = $logs['logs_amount'] * 100;
            $input->SetTotal_fee( $logs['logs_amount'] );//订单总金额，单位为分
            $input->SetTime_start( date( "YmdHis" ) );//否 订单生成时间
            $input->SetTime_expire( date( "YmdHis" , time() + 600 ) );// 否 注意：最短失效时间间隔必须大于5分钟
            $input->SetGoods_tag( $logs['subject'] ); // 否 商品标记，代金券或立减优惠功能的参数
            $input->SetNotify_url( __HOST__ . U( 'pchome/payment/respond' , array( 'code' => 'native' ) ) );
//            $input->SetNotify_url( __HOST__ . '/WxPay/notify.php' );//支付回调一般有个不成文的规定：传递给支付服务商的回调地址不允许带有任何参数
            $input->SetTrade_type( "NATIVE" );
            $input->SetProduct_id( $logs['logs_id'] );//trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义
            $result = $notify->GetPayUrl( $input );//weixin://wxpay/bizpayurl?pr=OINmxCp
            $url2 = $result["code_url"];
            $url2 = urlencode( $url2 );
            $questurl = 'http://paysdk.weixin.qq.com/example/qrcode.php?data=';
            $img = '<img src=' . '\'' . $questurl . $url2 . '\'' . ' style="width:260px;height:260px;"/>';


//            $token = 'native_' . $logs['logs_id'];
//            $imgUrl = __ROOT__ . DIRECTORY_SEPARATOR . 'attachs' . DIRECTORY_SEPARATOR . baoQrCodes($token, $url2);//DIRECTORY_SEPARATOR win "\"  linux "/"
//            $img = '<img src=' . '\'' . $imgUrl . '\'' . ' style="width:260px;height:260px;"/>';

            return $img;
        }
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。

        public function respond()
        {
            WxLog::DEBUG( "native 异步通知begin" );
//            $notify = new PayNotifyCallBack();
//            $data = $notify->Handle(true);//true 带sign输出
//            WxLog::DEBUG(dump($data));
//
//            if (!empty($data)) {//支付成功
//                if($data['return_code'] == 'SUCCESS'&& $data['result_code'] == 'SUCCESS' ){
//                    //以下字段在return_code 和result_code都为SUCCESS的时候有返回
//                    //商户自行增加处理流程,
//                    //例如：更新订单状态
//                    //例如：数据库操作
//                    $map['log_id'] = $data['out_trade_no'];
//                    $logs = D( 'Paymentlogs' )->where($map)->find();
//                    if( empty( $logs ) || !$logs['is_paid']){
//                        if ( !D( 'Payment' )->checkMoney( $data['out_trade_no'] , $data['total_fee'] ) ) {
//                            D('Payment')->logsPaid($data['out_trade_no']);
//                            WxLog::DEBUG("native成功写入支付日志！");
//                            echo 'SUCCESS';
//                            return true;
//                        }
//                    }
//                }
//            }
//            return false;
            // 存储微信的回调
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            if ( !empty( $xml ) ) {
                $res = new WxPayResults();
                $data = $res->FromXml( $xml );
                if ( $res->CheckSign() ) {
                    WxLog::DEBUG( "native verify right!" );
                    if ( $data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS' ) {
                        //以下字段在return_code 和result_code都为SUCCESS的时候有返回
                        //商户自行增加处理流程,
                        //例如：更新订单状态
                        //例如：数据库操作
                        WxLog::DEBUG( "native return_code  result_code SUCCESS!" );
                        $map['log_id'] = $data['out_trade_no'];
                        $logs = D( 'Paymentlogs' )->where( $map )->find();
                        if ( !$logs ) {
                            D( 'Payment' )->logsPaid( $data['out_trade_no'] );
                            WxLog::DEBUG( "native成功写入支付日志！" );

                            return true;
                        } else {
                            if ( !$logs['is_paid'] ) {
                                WxLog::DEBUG( "native更新未支付日志！" );
                                $result = D( 'Payment' )->checkMoney( $data['out_trade_no'] , $data['total_fee'] );
                                if ( $result ) {
                                    WxLog::DEBUG( "native money right!" );
                                    D( 'Payment' )->logsPaid( $data['out_trade_no'] );
                                    WxLog::DEBUG( "native成功写入支付日志！" );

                                    return true;
                                }
                            }
                        }
                    }
                }
            }

            return false;
        }

        public function b2cQuery( $logs , $payment )
        {

            $input = new WxPayOrderQuery();

            $out_trade_no = $logs["log_id"];
            $input->SetOut_trade_no( $out_trade_no );
            $output = WxPayApi::orderQuery( $input );

            return $output;

        }

        public function b2cRefund($logs , $payment){
            $out_trade_no = $logs["log_id"]; //商户订单号：商户侧传给微信的订单号
            $total_fee = $logs["need_pay"];  //订单总金额(分)订单总金额，单位为分，只能为整数，Int
            $refund_fee = $logs["need_pay"]; //退款金额(分)：退款总金额，订单总金额，单位为分，只能为整数，详见
            $input = new WxPayRefund();
            $input->SetOut_trade_no($out_trade_no);
            $input->SetTotal_fee($total_fee);
            $input->SetRefund_fee($refund_fee);
            $input->SetOut_refund_no(WxPayConfig::MCHID . date("YmdHis"));
            $input->SetOp_user_id(WxPayConfig::MCHID);
            $output = WxPayApi::refund($input);
            return $output;
        }
    }
