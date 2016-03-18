<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifecateModel extends CommonModel {

    protected $pk = 'cate_id';
    protected $tableName = 'life_cate';
    protected $token = 'life_cate';
    protected $orderby = array(
        'is_hot'    => 'desc',
        'orderby'   => 'asc',
    );
    protected $channel = array(
        'ershou'  => 1,
        'car'     => 2,
        'qiuzhi'  => 3,
        'love'    => 4,
        'house'   => 5,
        'peixun'  => 6,
        'jobs'    => 7,
        'service' => 8,
        'jianzhi' => 9,
        'chongwu' => 10  
    );
    protected $channelMeans = array(
         1 => '二手',
         2 => '车辆',
         3 => '求职',
         4 => '交友',
         5 => '房屋',
         6 => '培训',
         7 => '招聘',
         8 => '服务',
         9 => '兼职',
        10 => '宠物'
    );

    public function getChannel(){
        return $this->channel;
    }
    
    public function getChannelMeans(){
        return $this->channelMeans;
    }
    
    
}