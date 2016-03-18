<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ShopdingmenuModel extends CommonModel{
    protected $pk   = 'menu_id';
    protected $tableName =  'shop_ding_menu';


	public function get_menu($shop_id)
	{
		$cate = D('Shopdingcate');
		$cate_list = $cate->where('shop_id = '.$shop_id)->order(array('orderby'=>'asc'))->select();
		$menu = $this->where('closed=0 and shop_id='.$shop_id)->select();
		$tem= array();
		foreach($cate_list as $k => $v){
			foreach($menu as $kk => $vv){
				if($vv['cate_id'] == $v['cate_id']){
					$tem[$v['cate_id']][] = $vv;
				}
			}
		}
		//var_dump($tem);echo "File:", __FILE__, ',Line:',__LINE__;exit;
		return $tem;
	}

	public function get_cate($shop_id)
	{
		$menu = $this->get_menu($shop_id);
		$tem= array();
		foreach($menu as $k => $v){
			$tem[$k] = $k;
		}
		$cate = D('Shopdingcate');
		$cate_list = $cate->order(array('orderby'=>'asc'))->itemsByIds($tem);
		return $cate_list;
	}

	public function  get_count($shop_id)
	{
		$menu = $this->get_menu($shop_id);
		$cate = $this->get_cate($shop_id);
		$tem= array();
		foreach($cate as $k => $v){
			$tem[$k]['cate_name'] =  $v['cate_name'];
			$tem[$k]['count']  = count($menu[$k]);
 		}
		return $tem;
	}

	public function shop_menu($shop_id)
	{
		$tem = array();
		$menu = $this->where('shop_id = '.$shop_id)->select();
		foreach($menu as $k => $v){
			$tem[$v['menu_id']] = $v;
		}
		return $tem;
	}

}