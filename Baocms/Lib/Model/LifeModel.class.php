<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class LifeModel extends CommonModel{
    protected $pk   = 'life_id';
    protected $tableName =  'life';
    
    protected $_validate = array(
        array( ),
        array( ),
        array( )
    );

    public function randTop(){
        $lifes = $this->where(array('audit'=>1,'top_date'=>array('EGT',TODAY)))->order(array('last_time'=>'desc'))->limit(0,45)->select();
       //print_r($this->getLastSql());
        shuffle($lifes);
        if(empty($lifes)) return array();
        $num = count($lifes) > 9 ? 9: count($lifes) ;
        $keys = array_rand($lifes,$num);

        $return  = array();
        foreach($lifes as $k=>$val){
            if(in_array($k,$keys)){
                $return[] = $val;
            }
        }
        return $return;
    }
    
}