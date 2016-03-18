<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class WeixintmplAction extends CommonAction {

	public function index()
	{
		if($data = $this->_post('data',false)){
			$on = true;
			$tmpl = D('Weixintmpl');
			foreach($data as $item){
				$is = isset($item['tmpl_id']);
				if($is){
					$item['update_time'] = time();
				}else{
					$item['create_time'] = time();
				}
				if(!$tmpl->create($item)){
					$this->baoError($tmpl->getError());
					continue;
				}else{
					if($is){
						if(!$tmpl->save()){
							$on = false;
							$this->baoError('编辑失败！');
							continue;
						}
					}else{
						if(!$tmpl->add()){
							$on = false;
							$this->baoError('添加失败！');
							continue;
						}
					}
				}
			}		
			if($on){
				$this->baoSuccess('操作成功！',U('Weixintmpl/index'));
			}	
		}else{

            D('Weixin')->tmplmesg($tmpl_data);

			$list = D('Weixintmpl')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}
}
