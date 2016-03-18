<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CommunityModel extends CommonModel {

    protected $pk = 'community_id';
    protected $tableName = 'community';
    protected $token = 'community';
    protected $orderby = array('orderby' => 'asc');

    public function _format($data) {
        static $area = null;
        if ($area == null) {
            $area = D('Area')->fetchAll();
        }
        $data['area_name'] = $area[$data['area_id']]['area_name'];
        return $data;
    }

    
}
