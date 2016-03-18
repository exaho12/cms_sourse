<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class MenuModel extends CommonModel {

    protected $pk = 'menu_id';
    protected $tableName = 'menu';
    protected $token = 'bao_menu';
    protected $orderby = array('orderby'=>'asc');
   
    public function checkAuth($auth) {
        $data = $this->fetchAll();
        foreach ($data as $row) {
            if ($auth == $row['menu_action']) {
                return true;
            }
        }
        return false;
    }

    

}