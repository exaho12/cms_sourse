<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifeservicedianpingModel extends CommonModel {

    protected $pk = 'activity_id';
    protected $tableName = 'lifeservice_dianping';

    public function check($id, $user_id) {
        $data = $this->find(array('where' => array('id' => (int) $id, 'user_id' => (int) $user_id)));
        return $this->_format($data);
    }

    public function CallDataForMat($items) { //专门针对CALLDATA 标签处理的
        if (empty($items))
            return array();
        $obj = D('Users');
        $user_ids = array();
        foreach ($items as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $users = $obj->itemsByIds($user_ids);
        foreach ($items as $k => $val) {
            $val['user'] = $users[$val['user_id']];
            $items[$k] = $val;
        }
        return $items;
    }
    
}