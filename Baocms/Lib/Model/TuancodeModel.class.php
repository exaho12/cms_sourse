<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuancodeModel extends CommonModel{
    protected $pk   = 'code_id';
    protected $tableName =  'tuan_code';
    public function getCode(){       
        $i=0;
        while(true){
            $i++;
            $code = rand_string(8,1);
            $data = $this->find(array('where'=>array('code'=>$code)));
            if(empty($data)) return $code;
            if($i > 20) return $code;//CODE 做了唯一索引，如果大于20 我们也跳出循环以免更多资源消耗
        }
    }
}