<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ConvenientphonevillagesModel extends CommonModel {

    protected $pk = 'phone_id';
    protected $tableName = 'convenient_phone_villages';

    public function getCommunity($phone_id) {
        $phone_id = (int) $phone_id;
        return $this->where(array('phone_id' => $phone_id))->select();
    }

}
