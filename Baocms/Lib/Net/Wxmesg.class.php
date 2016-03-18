<?php 
/*+=================================================
  +==============微信模板消息数据格式封装===========
  +=================================================
  */
class Wxmesg{
	/**
	 * 网络发送数据
	 * @param string $uid,用户的openid
	 * @param string $serial,模板编号
	 * @param array  $data ,填充模板数据
	 */
	static public function net($uid,$serial=null,$data=null)
	{
		if(!$uid) throw new Exception("Uid参数不正确！");

		$openid = D('Connect')->where("type='weixin'")->getFieldByUid($uid,'open_id'); 
		if($openid){
			if(!$serial)     throw new Exception("模板编号参数不正确！", 1000);
			if(empty($data)) throw new Exception("没有数据可供发送！");
            $data['template_id'] = D('Weixintmpl')->getFieldBySerial($serial,'template_id');//支付成功模板
            $data['touser']  = $openid;
            return D('Weixin')->tmplmesg($data);
		}
		return false;
	}
	/**
	 * 下单成功模板
	 */
	static public function order($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！",1001);
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=>	$data['first'],    'color'=>'#000000'),
				'keyword1'=>array('value'=> $data['orderNum'], 'color'=>'#000000'), //订单号
				'keyword2'=>array('value'=> $data['goodsName'],'color'=>'#000000'), //商品名称
				'keyword3'=>array('value'=> $data['buyNum'],   'color'=>'#000000'), //订购数量
				'keyword4'=>array('value'=> $data['money'],    'color'=>'#000000'), //订单金额
				'keyword5'=>array('value'=> $data['payType'],  'color'=>'#000000'), //付款方式
				'remark'  =>array('value'=> $data['remark'],   'color'=>'#000000')
			)
		);
	}
	//支付成功
	static public function pay($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！",1002);
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=>$data['first'],   'color'=>'#000000'),
				'keyword1'=>array('value'=>$data['money'],   'color'=>'#000000'), //订单金额
				'keyword2'=>array('value'=>$data['orderInfo'],'color'=>'#000000'), //订单详情
				'keyword3'=>array('value'=>$data['addr'],    'color'=>'#000000'), //收货信息
				'keyword4'=>array('value'=>$data['orderNum'],'color'=>'#000000'), //订单编号
				'remark'  =>array('value'=>$data['remark'],  'color'=>'#000000')
			)
		);
	}
	//订单取消
	static public function cancle($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！");
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=>$data['first'],              'color'=>'#000000'),
				'orderProductPrice'=>array('value'=>$data['money'],     'color'=>'#000000'),  //订单金额
				'orderProductName' =>array('value'=>$data['orderInfo'], 'color'=>'#000000'), //商品详情
				'orderAddress'     =>array('value'=>$data['addr'],      'color'=>'#000000'), //收货地址
				'orderName'        =>array('value'=>$data['orderNum'],  'color'=>'#000000'), //订单编号
				'remark'           =>array('value'=>$data['remark'],    'color'=>'#000000')
			)
		);
	}
	//商家确认
	static public function sure($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！");
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=> $data['first'],     'color'=>'#000000'),
				'keyword1'=>array('value'=> $data['orderNum'],  'color'=>'#000000'), //订单编号
				'keyword2'=>array('value'=> $data['money'],     'color'=>'#000000'), //订单金额
				'keyword3'=>array('value'=> $data['orderDate'], 'color'=>'#000000'), //订单时间
				'remark'  =>array('value'=> $data['remark'],    'color'=>'#000000')
			)
		);
	}
	//已发货
	static public function deliver($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！");
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=> $data['first'],     'color'=>'#000000'),
				'keyword1'=>array('value'=> $data['orderInfo'], 'color'=>'#000000'), //订单内容
				'keyword2'=>array('value'=> $data['wuliu'],     'color'=>'#000000'), //物流服务
				'keyword3'=>array('value'=> $data['wuliuNum'],  'color'=>'#000000'), //快递单号
				'keyword4'=>array('value'=> $data['addr'],      'color'=>'#000000'), //收货信息
				'remark'  =>array('value'=> $data['remark'],    'color'=>'#000000')
			)
		);
	}
	//确认收货
	static public function take($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！");
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=> $data['first'],    'color'=>'#000000'),
				'keyword1'=>array('value'=> $data['orderNum'], 'color'=>'#000000'), //订单号
				'keyword2'=>array('value'=> $data['goodsName'],'color'=>'#000000'), //商品名称
				'keyword3'=>array('value'=> $data['orderDate'],'color'=>'#000000'), //下单时间
				'keyword4'=>array('value'=> $data['sendDate'], 'color'=>'#000000'), //发货时间
				'keyword5'=>array('value'=> $data['sureDate'], 'color'=>'#000000'), //收货时间
				'remark'  =>array('value'=> $data['remark'],   'color'=>'#000000')
			)
		);
	}
	//余额变动
	static public function balance($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！");
		return array(
			'touser'       => '',
			'url'          => $data['url'],
			'template_id'  => '',
			'topcolor'     => $data['topcolor'],
			'data'		   => array(
				'first'   =>array('value'=> $data['first'],       'color'=>'#000000'),
				'keyword1'=>array('value'=> $data['accountType'], 'color'=>'#000000'), //账户类型
				'keyword2'=>array('value'=> $data['operateType'], 'color'=>'#000000'), //操作类型
				'keyword3'=>array('value'=> $data['operateInfo'], 'color'=>'#000000'), //操作内容
				'keyword4'=>array('value'=> $data['limit'],       'color'=>'#000000'), //变动额度
				'keyword5'=>array('value'=> $data['balance'],     'color'=>'#000000'), //账户余额
				'remark'  =>array('value'=> $data['remark'],      'color'=>'#000000')
			)
		);
	}

	/*订单通知模板*/
	static public function notice($data=null)
	{
		if(empty($data)) throw new Exception("微信模板消息没有数据！");
		return array(
			'touser'         => '',
			'url'            => $data['url'],
			'template_id'    => '',
			'topcolor'       => '#000000',
			'data'           => array(
				'first'   => array( 'value'=> $data['first'],  'color'=>'#000000' ),
				'keyword1'=> array( 'value'=> $data['order'],  'color'=>'#000000' ),//订单号
				'keyword2'=> array( 'value'=> $data['amount'], 'color'=>'#000000' ),//订单金额
				'keyword3'=> array( 'value'=> $data['info'],   'color'=>'#000000' ),//商品信息
				'remark'  => array( 'value'=> $data['remark'], 'color'=>'#000000' )
			)
		);
	}
}