<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class  RoleMapsModel extends CommonModel{
    
    protected $tableName =  'role_maps';
    
    public function getMenuIdsByRoleId($role_id){
        $role_id = (int) $role_id;
        $datas = $this->where(" role_id = '{$role_id}' ")->select();
        $return = array();
        foreach($datas as $val){
            $return[$val['menu_id']] = $val['menu_id'];
        }
        return $return;
    }
    
}
