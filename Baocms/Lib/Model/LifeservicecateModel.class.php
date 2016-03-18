<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifeservicecateModel extends CommonModel {

	protected $pk = 'id';
    protected $tableName = 'life_service_cate';

    protected $channel_list = array(array('id'=>'jiazheng', 'name'=>"家政" ), array('id'=>'zhuangxiu', 'name'=>"装修" ),array('id'=>'hunqing', 'name'=>"婚庆" ));
	
	public function channel_list(){
        return $this->channel_list;
    }
	
	
	 protected $channel = array(
        'jiazheng'  => 1,
        'zhuangxiu'     => 2,
        'hunqing'  => 3,
    );
    protected $channelMeans = array(
         1 => '家政',
         2 => '装修',
         3 => '婚庆'
    );

    public function getChannel(){
        return $this->channel;
    }
    
    public function getChannelMeans(){
        return $this->channelMeans;
    }
    

}