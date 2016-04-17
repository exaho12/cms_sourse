<?php
class CloudModel extends CommonModel{
	
	 private $curl = null;
	
    public function __construct() {
        import("@/Net.Curl");
        $this->curl = new Curl();
    }
	

    public function GetLocation($lat,$lng) { //通过云端校准坐标
		if(!empty($lat) && !empty($lng)){
			$url = 'http://www.niubest.cn/api-local-local-key-'.C('NIU_KEY').'-lat-'.$lat.'-lng-'.$lng.'.html';
		}
        return $str;
    }
	
	
    public function GetAddress($lat,$lng) { //通过云端校准以坐标获取详细地址
		$str = file_get_contents($url);
        return $str;
    }
	
	
	
    public function NearData($lat,$lng,$key,$p) { //通过云端获取POIS数据
		if(!empty($lat) && !empty($lng) && !empty($key)){
			$url = 'http://www.niubest.cn/api-local-neardata-key-'.C('NIU_KEY').'-lat-'.$lat.'-lng-'.$lng.'-kw-'.$key.'-p-'.$p.'.html';
		}
		return $arr;
    }
		
}