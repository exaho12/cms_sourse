<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CommonModel extends Model{
    protected $pk   = '';
    protected $tableName =  '';
    protected $token = '';
    protected $cacheTime = 86400;
    protected $orderby = array(); //针对全部查询出的数据的排序


    public function updateCount($id,$col,$num = 1){
        $id = (int)$id;
        
        return $this->execute(" update ".$this->getTableName()." set {$col} = ({$col} + '{$num}') where ".$this->pk." = '{$id}' ");
    }
	//多属性开始
	 public function updCount($id,$col,$num = 1){
       return $this->execute("update ".C('DB_PREFIX')."goods_attr set {$col} = ({$col} + '{$num}') where attr_id = '{$id}' ");
    }
	//多属性结束
    
    public  function  itemsByIds($ids = array()){
        if(empty($ids)) return array();
        $data = $this->where(array($this->pk=>array('IN',$ids)))->select();
        $return = array();
        foreach($data as $val){
            $return[$val[$this->pk]] = $val;
        }
        return $return;
    }
   
    public function fetchAll($field = '*', $where = array()){
        $cache = cache(array('type'=>'File','expire'=>  $this->cacheTime));
        if(!$data = $cache->get($this->token)){
            $result = $this->field($field);
            if ( ! empty($where))
            {
                $result = $result->where($where);
            }
            $result = $result->order($this->orderby)->select();
            $data = array();
            foreach($result  as $row){
                $data[$row[$this->pk]] = $this->_format($row);
            }
            $cache->set($this->token,$data);
        }   
        return $data;
     }
     
     public function cleanCache(){
         $cache = cache(array('type'=>'File','expire'=>$this->cacheTime));
         $cache->rm($this->token);
     }
    
    public  function _format($data){
        return $data;
    }
}