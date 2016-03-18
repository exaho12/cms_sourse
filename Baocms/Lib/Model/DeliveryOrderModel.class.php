<?php

class DeliveryOrderModel extends RelationModel {
    
        protected $pk   = 'order_id';
        protected $tableName =  'delivery_order';
    
	protected $_link = array(

        'Delivery' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'Delivery',
            'foreign_key' => 'delivery_id',
            'mapping_fields' =>'name,mobile',
            'as_fields'=>'name,mobile', 
        ),
        
    );

}

?>