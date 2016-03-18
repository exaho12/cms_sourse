<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class NavigationAction extends CommonAction {


    private $create_fields = array('nav_name','ioc','url', 'title','photo','status','closed','colour','target','orderby');
    private $edit_fields = array('nav_name','ioc','url', 'title','photo','status','closed','colour','target','orderby');

	
	
	
	public function main() {
		$this->display(); // 输出模板
		
	}

    public function index() {
		
		$Navigation = D('Navigation');//定义模型
		$map = array(); //这里只显示 实物
		$aready = (int) $this->_param('aready');
        if ($aready == 0) {
            $map['status'] = 4;
        } elseif($aready == 2) {
            $map['status'] = 2;
        } elseif($aready == 3) {
            $map['status'] = 3;
        } elseif($aready == 4) {
            $map['status'] = 4;
        } elseif($aready == 5) {
            $map['status'] = 5;
        } elseif($aready == 6) {
            $map['status'] = 6;
        }else{
			$map['status'] = 4;
		}
			
		$list = $Navigation->where($map)->order(array('orderby' => 'asc'))->select();//查出数据		
        $this->assign('list', $list); // 赋值数据集

		$this->assign('aready', $aready);
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

   public function create($parent_id=0) {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Navigation');
            $data['parent_id'] = $parent_id;
            if ($obj->add($data)) {
                $obj->cleanCache();
                $this->baoSuccess('添加成功', U('Navigation/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('parent_id',$parent_id);
			

            $this->display();
        }
    }
	public function child($parent_id=0){
        $datas = D('Navigation')->fetchAll();
        $str = '';

        foreach($datas as $var){
            if($var['parent_id'] == 0 && $var['nav_id'] == $parent_id){
         
                foreach($datas as $var2){

                    if($var2['parent_id'] == $var['nav_id']){
                        $str.='<option value="'.$var2['nav_id'].'">'.$var2['nav_name'].'</option>'."\n\r";
           
                        foreach($datas as $var3){
                            if($var3['parent_id'] == $var2['nav_id']){
                                
                               $str.='<option value="'.$var3['nav_id'].'">&nbsp;&nbsp;--'.$var3['nav_name'].'</option>'."\n\r"; 
                                
                            }
                            
                        }
                    }  
                }
                             
              
            }           
        }
        echo $str;die;
    }
	
   private function createCheck() {
			$data = $this->checkFields($this->_post('data', false), $this->create_fields);
			$data['nav_name'] = htmlspecialchars($data['nav_name']);
			if (empty($data['nav_name'])) {
				$this->baoError('导航名字不能为空');
			}
			$data['title'] = htmlspecialchars($data['title']);
			if (empty($data['title'])) {
				$this->baoError('别名不能为空');
			}
			$data['ioc'] = htmlspecialchars($data['ioc']);
			$data['url'] = htmlspecialchars($data['url']);
			$data['photo'] = htmlspecialchars($data['photo']);
			if (!empty($data['photo']) && !isImage($data['photo'])) {
				$this->baoError('ioc图标不正确');
			}		
			$data['status'] = (int)$data['status'];//小灰灰添加
			if (empty($data['status'])) {
				$this->baoError('类型不能为空');
			}
			$data['closed'] = (int)$data['closed'];//小灰灰添加
			$data['colour'] = htmlspecialchars($data['colour']);
			$data['target'] = (int)$data['target'];//小灰灰添加
			$data['orderby'] = (int) $data['orderby'];
			return $data;
    }
	
    public function edit($nav_id = 0) {
        if ($nav_id = (int) $nav_id) {
            $obj = D('Navigation');
            if (!$detail = $obj->find($nav_id)) {
                $this->baoError('请选择要编辑的手机底部导航');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['nav_id'] = $nav_id;
                if (false !== $obj->save($data)) {
                    $obj->cleanCache();
                    $this->baoSuccess('操作成功', U('Navigation/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的商家分类');
        }
    }
	

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['nav_name'] = htmlspecialchars($data['nav_name']);
        if (empty($data['nav_name'])) {
            $this->baoError('导航名字不能为空');
        }
		$data['title'] = htmlspecialchars($data['title']);
		if (empty($data['title'])) {
				$this->baoError('别名不能为空');
			}
			
			
			$data['status'] = (int)$data['status'];//小灰灰添加
			if (empty($data['status'])) {
				$this->baoError('类型不能为空');
			}
			
			
		$data['ioc'] = htmlspecialchars($data['ioc']);
		$data['url'] = htmlspecialchars($data['url']);
		$data['photo'] = htmlspecialchars($data['photo']);
		if (!empty($data['photo']) && !isImage($data['photo'])) {
		$this->baoError('ioc图标不正确');
		}	
		$data['closed'] = (int)$data['closed'];//小灰灰添加
		$data['colour'] = htmlspecialchars($data['colour']);
		$data['target'] = (int)$data['target'];//小灰灰添加
        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }
	

	

    public function delete($nav_id = 0) {
        if (is_numeric($nav_id) && ($nav_id = (int) $nav_id)) {
            $obj = D('Navigation');
			
			$navigation = $obj->fetchAll();
            foreach ($navigation as $val) {
                if ($val['parent_id'] == $nav_id)
                    $this->baoError('该菜单下还有其他子菜单');
            }
			
            $obj->delete($nav_id);
            $obj->cleanCache();
            $this->baoSuccess('删除成功！', U('Navigation/index'));
        } else {
            $cate_id = $this->_post('nav_id', false);
            if (is_array($nav_id)) {
                $obj = D('Navigation');
                foreach ($nav_id as $id) {
                    $obj->delete($id);
                }
                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('Navigation/index'));
            }
            $this->baoError('请选择要删除的商家分类');
        }
    }
	
    
    public function update() {
        $orderby = $this->_post('orderby', false);
        $obj = D('Navigation');
        foreach ($orderby as $key => $val) {
            $data = array(
                'nav_id' => (int) $key,
                'orderby' => (int) $val
            );
            $obj->save($data);
        }
        $obj->cleanCache();
        $this->baoSuccess('更新成功', U('Navigation/index'));
    }

}
