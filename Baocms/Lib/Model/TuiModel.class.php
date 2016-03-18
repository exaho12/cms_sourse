<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuiModel extends CommonModel{
    protected $pk   = 'tui_id';
    protected $tableName =  'tui';
    protected $token = 'bao_tui';
    
    public function fetchAll(){
        $datas = $this->select();
        $return = array();
        foreach($datas as $k=>$v){
            $return[$v['tui_link']] = $v['tui_name'];
        }
        return $return;
    }
    
}