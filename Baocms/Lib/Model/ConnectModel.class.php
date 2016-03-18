<?php
class  ConnectModel extends Model{
    protected $pk   = 'connect_id';
    protected $tableName =  'connect';
    
    public function getConnectByOpenid($type,$open_id){
        
        return $this->find(array("where"=>array(
            'type' => $type,
            'open_id' => $open_id
        )));     
    }
    
    
}