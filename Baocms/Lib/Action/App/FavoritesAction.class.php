<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class FavoritesAction extends CommonAction {
	
	/*
		关注 首页
		参数UID(是) like(否) page(默认1)	
	*/
	public function index() {
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else{
			$like = trim(htmlspecialchars($this->_param('like')));
			$Shopfavorites = D('Shopfavorites');
			$map = array('user_id' => $uid);
			$count = $Shopfavorites->where($map)->count();
			$limit = 10;
			$pages = ceil($count/$limit);
			$page = $this->_param('page')?$this->_param('page'):1;
			if($page>$pages){
				die($count);
			}
			$start = ($page-1)*$limit;
			$list = $Shopfavorites->where($map)->order('last_news_id desc')->limit($start.','.$limit)->select();
			$shop_ids = array();
			$last_news_ids= $read_ids = array();
			foreach ($list as $k => $val) {
				$shop_ids[$val['shop_id']] = $val['shop_id'];
				if(!empty($val['last_news_id'])){
					$last_news_ids[$val['last_news_id']] = $val['last_news_id'];
					$read_ids[$val['read_id']] = $val['read_id'];
				}
			}
			$shops = array();
			if(!empty($shop_ids)){
				if ($like) {
					$shops = D('Shop')->where(array('shop_id'=>array('IN',$shop_ids),'shop_name'=>array('like','%'.$like.'%')))->select();
				} else {
					$shops = D('Shop')->itemsByIds($shop_ids);
				}
			}
			
			if(!empty($last_news_ids)){
				$news = D('Shopnews')->itemsByIds($last_news_ids);
				$newsdata = array();
				foreach($news as $val){
					$newsdata[$val['shop_id']] = $val;
				}
			}
			$data = array('status'=>self::BAO_REQUEST_SUCCESS,'news'=>$newsdata,'news'=>$newsdata,'read_ids'=>$read_ids,'shops'=>$shops,'list'=>$list,'page'=>$page+1);
		}
		$this->stringify($data);
    }
	
	/*
		关注 列表
		参数UID(是) like(否) page(默认1)	
	*/

	public function lists()
	{
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else{
			$like = trim(htmlspecialchars($this->_param('like')));
			$shopObj = D('Shop');
			$map = array('audit'=>1,'closed'=>0);
			 if($like){
				$map['shop_name|tags'] = array('like','%'.$like.'%');
			}else{
				$map['city_id'] = $this->city_id;//搜索的时候 不限制城市
			}
			$count = $shopObj->where($map)->count();
			$limit = 10;
			$pages = ceil($count/$limit);
			$page = $this->_param('page')?$this->_param('page'):1;
			if($page>$pages){
				die($count);
			}
			$start = ($page-1)*$limit;
			$lat = $this->lat;
			$lng = $this->lng;

			$list = $shopObj->where($map)->order('orderby asc,fans_num desc')->limit($start.','.$limit)->select();
			
			$shop_ids = array();
			foreach ($list as $k => $val) {
				$shop_ids[$val['shop_id']] = $val['shop_id'];
				$list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
			}
			$datas = D('Shopfavorites')->where(array('user_id'=>$uid,'shop_id'=>array('IN',$shop_ids)))->select();
			$guanzhu = array();
			foreach($datas as $val){
				$guanzhu[$val['shop_id']] = $val;
			}
			$data = array('status'=>self::BAO_REQUEST_SUCCESS,'guanzhu'=>$guanzhu,'list'=>$list,'like'=>$like,'page'=>$page+1);
		}
		$this->stringify($data);
	}

	/*
		关注 
		参数UID(是) shop_id(是)
	*/


	public function add()
	{
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else if(!$shop_id = $this->_param('shop_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if(!$detail = D('Shop')->find($shop_id)){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"商家不存在！");
		}else if($detail['closed']){
			$data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"该商家已经被删除");
		}else{
			if(D('Shopfavorites')->check($shop_id, $uid)){
				$data = array('status' => self::BAO_FAVOR_IS_TRUE, 'msg'=>"您已经关注过该商家了！");
			}else{
				$datas = array(
					'shop_id' => $shop_id,
					'user_id' => $this->uid,
					'create_time' => NOW_TIME,
					'create_ip' => get_client_ip()
				);
				if (D('Shopfavorites')->add($datas)) {
					D('Shop')->updateCount($shop_id,'fans_num');
					$data = array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>'恭喜您关注成功！','url'=>U('favorites/index'));
				}else{
					$data = array('status' => self::BAO_ADD_FALSE, 'msg'=>"关注失败");
				}
			}
		}
		$this->stringify($data);
	}

	/*
		关注 
		参数UID(是) shop_id(是)
	*/

	public function detail()
	{
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else if(!$shop_id = $this->_param('shop_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if(!$detail = D('Shop')->find($shop_id)){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"商家不存在！");
		}else if($detail['closed']){
			$data = array('status' => self::BAO_DETAIL_NO_EXSITS, 'msg'=>"该商家已经被删除");
		}else if(!$fans = D('Shopfavorites')->check($shop_id,$uid)){
			$data = array('status' => self::BAO_FAVOR_IS_FALSE, 'msg'=>"您还未关注该商家");
		}else{
			$news = D('Shopnews')->where(array(
				'shop_id' => $shop_id,
				'audit' => 1,
				'news_id' => array(
					'EGT',$fans['read_id']
				)
			))->order(array('news_id'=>'desc'))->limit(0,1)->select();
			$details = D('Shopdetails')->find($shop_id);
			$datas =  unserialize($details['menus']);

			$data = array('status'=>self::BAO_REQUEST_SUCCESS,'news'=>$news,'detail'=>$detail,'weixin'=>$datas);
		}
		$this->stringify($data);
	}
   
    /*
		关注 
		参数UID(是) news_id(是)
	*/

    public function read()
	{
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else if(!$news_id = $this->_param('news_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if(!$detail = D('Shopnews')->find($news_id)){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"您所查看的内容不存在");
		}else if($detail['audit'] != 1){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"您所查看的内容暂未通过审核");
		}else{
			$fans = D('Shopfavorites')->check($detail['shop_id'],$uid);
            if($fans && $fans['read_id']< $new_id){
                $fans['read_id'] = $new_id;
                D('Shopfavorites')->save($fans);
            }
			D('Shopnews')->updateCount($new_id,'views');
			$shop = D('Shop')->find($detail['shop_id']);
			$data = array('status'=>self::BAO_REQUEST_SUCCESS,'detail'=>$detail,'shop'=>$shop);
		}

		$this->stringify($data);
	}

	/*
		关注 
		参数UID(是) shop_id(是) word(是)
	*/

	public function send()
	{
		if(!$uid = $this->_param('uid')){
			$data = array('status' => self::BAO_LOGIN_NO_REG, 'msg'=>"未登录或登录状态不正确！");
		}else if(!$shop_id = $this->_param('shop_id')){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if(!$word = htmlspecialchars($this->_param('word'))){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"参数不正确！");
		}else if(!$detail = D('Shop')->find($shop_id)){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该商家不存在");
		}else if($detail['audit']!=1 || $detail['closed']!=0){
			$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该商家不存在");
		}else if(!$fans = D('Shopfavorites')->check($shop_id,$uid)){
			$data = array('status' => self::BAO_FAVOR_IS_FALSE, 'msg'=>"您还未关注该商家");
		}else{
			$keyword = D('Shopweixinkeyword')->checkKeyword($shop_id,$word);
			//echo D('Shopweixinkeyword')->getLastSql();echo "File:", __FILE__, ',Line:',__LINE__;exit;
			if($keyword){
				 switch ($keyword['type']) {
					 case 'text':
						 $datas = array(
							 'ret' => 1,
							 'type' => 'text',
							 'contents' => $keyword['contents'],
							 'face' => __ROOT__.'/attachs/' .$detail['logo'],
						 );
						 $data = array('status'=>self::BAO_REQUEST_SUCCESS,'datas'=>$datas);
					 case 'news':
						 $datas = array(
							 'ret'   => 1,
							 'type'  => 'news',
							 'title' =>$keyword['title'],
							 'intro' => msubstr($keyword['contents'],0,60),
							 'photo' => __ROOT__.'/attachs/' .$keyword['photo'],
							 'url'   => $keyword['url'],
							 'face' => __ROOT__.'/attachs/' .$detail['logo'],
						 );
						 $data = array('status'=>self::BAO_REQUEST_SUCCESS,'datas'=>$datas);
						 
				 }
				
			}else{
				$data = array('status' => self::BAO_PAGE_NO_EXSITS, 'msg'=>"该关键字不存在");
			}       
		}

		$this->stringify($data);
	}
   
}
