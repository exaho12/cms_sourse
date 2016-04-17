<?php
class AllAction extends CommonAction {
    public function index(){
        import('ORG.Util.Page');

        $where_shop = array('closed'=>0,'audit' =>1,'city_id'=>$this->city_id);
		$where_tuan = array('closed'=>0,'audit' =>1,'end_date'=>array('EGT',TODAY));
		$where_huodong = array('audit'=>1,'closed' =>0,'end_date'=>array('EGT',TODAY));
		$where_goods = array('closed' => 0, 'audit' => 1, 'end_date' => array('EGT', TODAY));
		$where_ele = array('audit'=>1,'closed' => 0);
		$where_ding = array('closed' => 0);
		$where_life = array('audit' => 1);
		$where_coupon = array('audit' => 1,'closed' => 0, 'expire_date' => array('EGT', TODAY));
		$where_post = array('audit' => 1);
		
		$where_tieba = array('audit'=>1,'closed' => 0);
		$where_news = array('audit'=>1,'closed' => 0);
		$where_xiaoqu = array('audit'=>1,'closed' => 0);

        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $where_shop['shop_name|tags'] = array('LIKE','%'.$keyword.'%');
			$where_tuan['title'] = array('LIKE', '%' . $keyword . '%');
			$where_huodong['title'] = array('LIKE', '%' . $keyword . '%');
			$where_goods['title'] = array('LIKE', '%' . $keyword . '%');
			$where_ele['product_name'] = array('LIKE', '%' . $keyword . '%');
			$where_ding['menu_name'] = array('LIKE', '%' . $keyword . '%');
			$where_life['title'] = array('LIKE', '%' . $keyword . '%');
			$where_coupon['title'] = array('LIKE', '%' . $keyword . '%');
			$where_post['title'] = array('LIKE', '%' . $keyword . '%');
			
			$where_tieba['title'] = array('LIKE', '%' . $keyword . '%');//贴吧
			$where_news['title|keywords'] = array('LIKE', '%' . $keyword . '%');//新闻
			$where_xiaoqu['name|property'] = array('LIKE', '%' . $keyword . '%');//小区
			
            $this->assign('keyword', $keyword);
        }

		$Shop=D('Shop');
		$Tuan=D('Tuan');
		$Activity=D('Activity');
		$Goods=D('Goods');
		$Eleproduct = D('Eleproduct');
		$Dingmenu = D('Shopdingmenu');
		$Life = D('Life');
		$Coupon = D('Coupon');
		$Post = D('Post');
		
		$Tieba = D('Post');
		$News = D('Article');//新闻
		$Xiaoqu = D('Community');//小区
		
		$list_shop=$Shop->Field('"商家" as t_name,"shop/detail" as t_url,"shop_id" as t_param,shop_id as t_id,shop_name as t_title,photo as t_photo,tel as t_note')->order($orderby)->where($where_shop)->select();
		$list_tuan=$Tuan->Field('"抢购" as t_name,"tuan/detail" as t_url,"tuan_id" as t_param,tuan_id as t_id,title as t_title,photo as t_photo,concat("￥",round(tuan_price/100,2)) as t_note')->order($orderby)->where($where_tuan)->select();
		$list_goods=$Goods->Field('"商城" as t_name,"mall/detail" as t_url,"goods_id" as t_param,goods_id as t_id,title as t_title,photo as t_photo,concat("￥",round(mall_price/100,2)) as t_note')->order($orderby)->where($where_goods)->select();
		$list_ele=$Eleproduct->Field('"外卖" as t_name,"ele/shop" as t_url,"shop_id" as t_param,shop_id as t_id,product_name as t_title,photo as t_photo,concat("￥",round(settlement_price/100,2)) as t_note')->order($orderby)->where($where_ele)->select();
		$list_ding=$Dingmenu->Field('"订座" as t_name,"ding/detail" as t_url,"shop_id" as t_param,shop_id as t_id,menu_name as t_title,photo as t_photo,concat("￥",round(ding_price/100,2)) as t_note')->order($orderby)->where($where_ding)->select();
		$list_life=$Life->Field('"分类" as t_name,"life/detail" as t_url,"life_id" as t_param,life_id as t_id,title as t_title,photo as t_photo,FROM_UNIXTIME(create_time,"%Y-%m-%d") as t_note')->order($orderby)->where($where_life)->select();
		$list_coupon=$Coupon->Field('"优惠券" as t_name,"coupon/detail" as t_url,"coupon_id" as t_param,coupon_id as t_id,title as t_title,photo as t_photo,FROM_UNIXTIME(create_time,"%Y-%m-%d") as t_note')->order($orderby)->where($where_coupon)->select();
		$list_post=$Post->Field('"贴吧" as t_name,"tieba/detail" as t_url,"post_id" as t_param,post_id as t_id,title as t_title,pic as t_photo,FROM_UNIXTIME(create_time,"%Y-%m-%d") as t_note')->order($orderby)->where($where_post)->select();
		$list_huodong=$Activity->Field('"活动" as t_name,"huodong/detail" as t_url,"activity_id" as t_param,activity_id as t_id,title as t_title,photo as t_photo,concat("￥",round(price/100,2)) as t_note')->order($orderby)->where($where_huodong)->select();
		
		
		
		$list_news=$News->Field('"新闻" as t_name,"news/detail" as t_url,"article_id" as t_param,article_id as t_id,title as t_title,photo as t_photo,FROM_UNIXTIME(create_time,"%Y-%m-%d") as t_note')->order($orderby)->where($where_news)->select();
		
		$list_xiaoqu=$Xiaoqu->Field('"小区" as t_name,"xiaoqu/detail" as t_url,"community_id" as t_param,community_id as t_id,name as t_title,pic as t_photo,FROM_UNIXTIME(create_time,"%Y-%m-%d") as t_note')->order($orderby)->where($where_xiaoqu)->select();
		
		
		
		$list=array();
		if(!empty($list_tuan)){
			$list=array_merge($list,$list_tuan);
		}
		if(!empty($list_huodong)){
			$list=array_merge($list,$list_huodong);
		}
		if(!empty($list_shop)){
			$list=array_merge($list,$list_shop);
		}
		if(!empty($list_goods)){
			$list=array_merge($list,$list_goods);
		}
		if(!empty($list_ele)){
			$list=array_merge($list,$list_ele);
		}
		if(!empty($list_ding)){
			$list=array_merge($list,$list_ding);
		}
		
		if(!empty($list_coupon)){
			$list=array_merge($list,$list_coupon);
		}
		//分类关闭后无法搜索
		$life= (int)$this->_CONFIG['operation']['life'];
		if ($life == 0){
		}else{
			if(!empty($list_life)){
			$list=array_merge($list,$list_life);
			}
			
		}
		
		//贴吧关闭后无法搜索
		$tieba= (int)$this->_CONFIG['operation']['tieba'];
		if ($tieba == 0){
		}else{
			if(!empty($list_post)){
			$list=array_merge($list,$list_post);
			}
			
		}
		//新闻
		$news= (int)$this->_CONFIG['operation']['news'];
		if ($news == 0){
		}else{
			if(!empty($list_news)){
			$list=array_merge($list,$list_news);
			}
			
		}
		
		
		
		if(!empty($list_xiaoqu)){
			$list=array_merge($list,$list_xiaoqu);
		}
		
		$Page=new Page(count($list),10);
		
		
		$list=array_slice($list,$Page->firstRow,$Page->listRows);
		$show=$Page->show();

		$this->assign('searchindex',0);
        $this->assign('total_num',count($list));
        $this->assign('list',$list);

        $this->assign('page', $show);
        $this->display();
    }
}