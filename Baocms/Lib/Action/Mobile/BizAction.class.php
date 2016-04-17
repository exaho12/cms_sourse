<?php
class BizAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
		$Biz = D('Biz');
		$counts = $Biz->count(); 
		$this->assign('counts', $counts);
    }


    public function index() {
		$keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $order = (int) $this->_param('order');
        $this->assign('nextpage', LinkTo('biz/loaddata', array('t' => NOW_TIME, 'area_id' => $area_id, 'order' => $order,  'keyword' => $keyword, 'p' => '0000')));
        $this->display(); // 输出模板
	
    }
	
	 public function loaddata() {
        $Biz = D('Biz');
        import('ORG.Util.Page'); // 导入分页类
		$keyword = $this->_param('keyword');
		
		//查询置顶
		if(!empty($keyword)){
			$word = D('Nearword')->where(array('text' => $keyword))->find();
			$word_pois = $word['pois_id'];
			if($word_pois){
				$ding = $Biz->find($word_pois);
			}
			//查询列表条件
			$map['name|tag'] = array('LIKE',array('%'.$keyword.'%','%'.$keyword,$keyword.'%','OR'));
		}
			
		$map['status'] = array('egt',0);
		if(!empty($ding)){
			$map['pois_id'] = array('neq',$ding['pois_id']);
		}
		//距离排序
		$lat = $this->_param('lat');
		$lng = $this->_param('lng');
		$orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
		
        $count = $Biz->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
        $list = $Biz->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
		foreach ($list as $k => $val) {
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
		
		
		$this->assign('keyword', $keyword); 
		$this->assign('ding', $ding); 
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	public function load() {
        $Biz = D('Biz');
		$keyword = $this->_param('keyword');
        $map['name|tag'] = array('LIKE',array('%'.$keyword.'%','%'.$keyword,$keyword.'%','OR'));
		$lat = $this->_param('lat');
		$lng = $this->_param('lng');
		$orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
        $list = $Biz->where($map)->order($orderby)->limit(1,10)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->display(); // 输出模板
    }
	
	
	
   
	
	

    public function detail($pois_id = 0) {

        if ($pois_id = (int) $pois_id) {
            $obj = D('Biz');
            if (!$detail = $obj->find($pois_id)) {
                $this->error('没有该商家信息');
            }

			$lat =$detail['lat'];
			$lng =$detail['lng'];
			$orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
			$list = $obj->order($orderby)->limit(0,10)->select();
			
			$this->assign('list', $list);
            $this->assign('detail', $detail);
            $this->seodatas['title'] = $detail['name'];
            $this->seodatas['keywords'] = $detail['tag'];
            $this->display();
        } else {
            $this->error('没有该商家信息');
        }
    }



}