<?php
class CoupondownloadModel extends CommonModel{
    protected $pk   = 'download_id';
    protected $tableName =  'coupon_download';
    
    public function getCode(){       
        $i=0;
        while(true){
            $i++;
            $code = rand_string(8,1);
            $data = $this->find(array('where'=>array('code'=>$code)));
            if(empty($data)) return $code;
            if($i > 10) return $code;//CODE 做了唯一索引，如果大于10 我们也跳出循环以免更多资源消耗
        }
        
    }
    
    public function CallDataForMat($items){ //专门针对CALLDATA 标签处理的
        if(empty($items)) return array();
        $obj = D('Coupon');        
        $coupon_ids = array();
        foreach($items as $k=>$val){
            $coupon_ids[$val['coupon_id']] = $val['coupon_id'];
        }       
        $coupons = $obj->itemsByIds($coupon_ids);
        foreach($items as $k=>$val)
        {
            $val['coupon'] = $coupons[$val['coupon_id']];
            $items[$k] = $val;
        }
        return $items;
    }
}