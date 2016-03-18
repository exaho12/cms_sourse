<?php
class NearModel extends CommonModel{
    protected $pk   = 'pois_id';
    protected $tableName =  'pois_content';

	
	//通过云端获取纠偏后的坐标
    public function GetLocation($latt=0,$lngg=0) {
		if($latt!=0 && $lngg!=0){
			$json = D('Cloud')->GetLocation($latt,$lngg);
			if(!empty($json)){
				$arr =json_decode($json);
				$lat = $arr->Lat;
				$lng = $arr->Lng;
			}
		}else{
			if(isWx() == true){
				if($this->member['lat']!='' && $this->member['lng']!=''){
					$lat = $this->member['lat'];
					$lng = $this->member['lng'];
				}else{
					$lat = cookie('lat');
					$lng = cookie('lng');
				}
			}else{
				$lat = cookie('lat');
				$lng = cookie('lng');
			}
	
		}

		$local=array('lat'=>$lat,'lng'=>$lng);
        return $local;
    }
	
	
	//通过云端获取地址
	public function GetAddress($lat,$lng) {
		if(!empty($lat) && !empty($lng)){
			$json = D('Cloud')->GetAddress($lat,$lng);
			if(!empty($json)){
				$arr =json_decode($json);
				$addr = $arr->formatted_address;
			}
		}
		
		return $addr;
	}
	
}