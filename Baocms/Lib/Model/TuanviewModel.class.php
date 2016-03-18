<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanviewModel extends CommonModel{
    protected $pk   = 'view_id';
    protected $tableName =  'tuan_view';
 
    public function getViews($users_id, $tuan_id) {
        if (!empty($users_id)) {
            $result['user_id'] = $users_id;
            $result['tuan_id'] = $tuan_id;
            $result['create_time'] = NOW_TIME;
            $result['create_ip'] = get_client_ip();
            $res = $this->where(array('user_id' => $users_id, 'tuan_id' => $tuan_id))->find();
            if (!$res) {
                $this->add($result);
            } else {
                $result['view_id'] = $res['view_id'];
                $this->save($result);
            }
        }
    }
}