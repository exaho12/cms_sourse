<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
//分钟级别锁！一分钟只允许过一个用户一条请求 防止用户并发恶意请求
class LockModel extends CommonModel{
    protected $pk   = 'id';
    protected $tableName =  'lock';
    
    protected $id = 0;


    public function  lock($uid){
        $uid = (int)$uid;
        $t = date('mdHi',NOW_TIME);
        $this->id= $this->add(array('uid'=>$uid,'t'=>$t));
        return $this->id;
    }
    
    public function unlock(){
        return $this->delete($this->id);
    }
}