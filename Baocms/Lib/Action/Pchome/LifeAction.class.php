<?php
class LifeAction extends CommonAction {

    protected $cates = array();
    private $create_fields = array('title','city_id', 'area_id', 'business_id', 'text1', 'text2', 'text3', 'num1', 'num2', 'select1', 'select2', 'select3', 'select4', 'select5', 'photo', 'contact', 'mobile', 'qq', 'addr', 'lng', 'lat');

    public function _initialize() {
        parent::_initialize();
		$life = (int)$this->_CONFIG['operation']['life'];
		if ($life == 0) {
				$this->error('此功能已关闭');
				die;
		}
        $this->cates = D('Lifecate')->fetchAll();
        $this->assign('cates', $this->cates);
		$this->assign('channelmeans', D('Lifecate')->getChannelMeans());
    }
// 解决niucms搜索问题开始
    public function index() {
      $Life = D('Life');
        import('ORG.Util.Pageabc'); // 导入分页类
        $map = $linkArr = array('city_id' => $this->city_id,'audit'=>1);
        $keyword = $this->_param('keyword');
        if ($keyword) {
            $map['qq|mobile|contact|title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
            $linkArr['keyword'] = $keyword;
        }
        $areas = D('Area')->fetchAll();
        if ($area_id = (int) $this->_param('area')) {
            $map['area_id'] = $area_id;
            $this->assign('area', $area_id);
            $this->seodatas['area_name'] = $areas[$area_id]['area_name'];
            $linkArr['area'] = $area_id;
        }

		$chl = D('Lifecate')->getChannelMeans();
		
        if ($channel = (int) $this->_param('channel')) {
            $cates_ids = array();
            foreach ($this->cates as $val) {
                if ($val['channel_id'] == $channel) {
                    $cates_ids[] = $val['cate_id'];
                }
            }
            if (!empty($cates_ids))
                $map['cate_id'] = array('IN', $cates_ids); //这个保留 因为下面有 cate_id 的时候 会覆盖条件

            $this->assign('channel', $channel);
            $linkArr['channel'] = $channel;
			
			$this->seodatas['channel_name'] = $chl[$channel];
        }
		
        if ($cate_id = (int) $this->_param('cat')) {
            if ($cate = $this->cates[$cate_id]) {
                $map['cate_id'] = $cate_id;
				$this->seodatas['cate_name'] = $cate['cate_name'];
                $this->assign('cat', $cate_id);
                $linkArr['cat'] = $cate_id;
                $this->assign('cate', $cate);
                $this->assign('channel', $cate['channel_id']);
                $attrs = D('Lifecateattr')->getAttrs($cate_id);
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($cate['select' . $i])) {
                        $s{$i} = (int) $this->_param('s' . $i);
                        if ($attrs['select' . $i][$s{$i}]) {
							$map['select' . $i] = $s{$i};//解决分类信息搜索bug
                            $this->assign('s' . $i, $s{$i});
                            $linkArr['s' . $i] = $s{$i};
                        }
                    }
                }
                $this->assign('attrs', $attrs);
            }
        }
        if ($business_id = (int) $this->_param('business')) {
            $map['business_id'] = $business_id;
            $this->assign('business', $business_id);
            $this->seodatas['business_name'] = $this->bizs[$business_id]['business_name'];
            $linkArr['business'] = $business_id;
        }

        $count = $Life->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Life->where($map)->order(array('top_date' => 'desc', 'last_time' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();


		
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('channelmeans',$chl );
        $this->assign('linkArr', $linkArr);
        $this->assign('tops', D('Life')->randTop());
        $this->display();
    }
// 解决niucms搜索问题结束


    public function detail($life_id) {
        $life_id = (int) $life_id;
        if (empty($life_id)) {
            $this->error('很抱歉该信息不存在');
        }
        if (!$detail = D('Life')->find($life_id)) {
            $this->error('很抱歉该信息不存在');
        }
        if ($detail['audit'] != 1) {
            $this->error('很抱歉该信息不存在');
        }
		$chl = D('Lifecate')->getChannelMeans();
		$this->assign('chl', $chl);
		$ex = D('Lifedetails')->find($detail['life_id']);
        $this->assign('user', D('Users')->find($detail['user_id']));
        $this->assign('cate', $this->cates[$detail['cate_id']]);
        $this->assign('detail', $detail);
        $this->assign('pics', D('Lifephoto')->getPics($detail['life_id']));
        $this->assign('ex', $ex);
        $attrs = D('Lifecateattr')->getAttrs($detail['cate_id']);
        D('Life')->updateCount($life_id, 'views');
        $this->assign('channel', $this->cates[$detail['cate_id']]['channel_id']);
        $this->assign('attrs', $attrs);
        $this->seodatas['title'] = $detail['title'];
		$this->seodatas['channel'] = $chl[$this->cates[$detail['cate_id']]['channel_id']];
		$this->seodatas['cate'] = $this->cates[$detail['cate_id']]['cate_name'];
		if(!empty($ex[details])){
			$this->seodatas['desc'] =  bao_Msubstr($ex['details'],0,200,false);
		}else{
			$this->seodatas['desc'] =  $detail['title'];
		}
		
		
        $this->assign('list', D('Life')->where(array('user_id' => $detail['user_id'], 'audit' => 1, 'life_id' => array('NEQ', $life_id)))->limit(0, 5)->order('last_time desc')->select());
        $this->display();
    }

    public function love($life_id) {
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        $life_id = (int) $life_id;
        if (!$detail = D('Life')->find($life_id)) {
            $this->niuError('该信息不存在');
        }
        if (D('Lifelove')->check($life_id, $this->uid)) {
            $this->niuError('您已经收藏过了！');
        }
        $arr = array(
            'user_id' => $this->uid,
            'life_id' => $life_id,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip()
        );
        D('Lifelove')->add($arr);
        $this->niuSuccess('收藏成功', U('life/detail', array('life_id' => $life_id)));
    }

    public function report($life_id) {
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        $life_id = (int) $life_id;
        if (!$detail = D('Life')->find($life_id)) {
            $this->niuError('该信息不存在');
        }
        if (D('Lifereport')->check($life_id, $this->uid)) {
            $this->niuError('您已经举报过了！');
        }
        $arr = array(
            'user_id' => $this->uid,
            'life_id' => $life_id,
            'create_time' => NOW_TIME,
            'create_ip' => get_client_ip()
        );
        D('Lifereport')->add($arr);
        $this->niuSuccess('举报成功', U('life/detail', array('life_id' => $life_id)));
    }

    public function main() {
        $Life = D('Life');
        import('ORG.Util.Pageabc'); // 导入分页类
        $map = $linkArr = array('city_id' => $this->city_id,'audit'=>1);
        $keyword = $this->_param('keyword');
        if ($keyword) {
            $map['qq|mobile|contact|title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
            $linkArr['keyword'] = $keyword;
        }
        $areas = D('Area')->fetchAll();
        if ($area_id = (int) $this->_param('area')) {
            $map['area_id'] = $area_id;
            $this->assign('area', $area_id);
            $this->seodatas['area_name'] = $areas[$area_id]['area_name'];
            $linkArr['area'] = $area_id;
        }

		$chl = D('Lifecate')->getChannelMeans();
		
        if ($channel = (int) $this->_param('channel')) {
            $cates_ids = array();
            foreach ($this->cates as $val) {
                if ($val['channel_id'] == $channel) {
                    $cates_ids[] = $val['cate_id'];
                }
            }
            if (!empty($cates_ids))
                $map['cate_id'] = array('IN', $cates_ids); //这个保留 因为下面有 cate_id 的时候 会覆盖条件

            $this->assign('channel', $channel);
            $linkArr['channel'] = $channel;
			
			$this->seodatas['channel_name'] = $chl[$channel];
        }
		
        if ($cate_id = (int) $this->_param('cat')) {
            if ($cate = $this->cates[$cate_id]) {
                $map['cate_id'] = $cate_id;
				$this->seodatas['cate_name'] = $cate['cate_name'];
                $this->assign('cat', $cate_id);
                $linkArr['cat'] = $cate_id;
                $this->assign('cate', $cate);
                $this->assign('channel', $cate['channel_id']);
                $attrs = D('Lifecateattr')->getAttrs($cate_id);
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($cate['select' . $i])) {
                        $s{$i} = (int) $this->_param('s' . $i);
                        if ($attrs['select' . $i][$s{$i}]) {
							$map['select' . $i] = $s{$i};//解决搜索问题
                            $this->assign('s' . $i, $s{$i});
                            $linkArr['s' . $i] = $s{$i};
                        }
                    }
                }
                $this->assign('attrs', $attrs);
            }
        }
        if ($business_id = (int) $this->_param('business')) {
            $map['business_id'] = $business_id;
            $this->assign('business', $business_id);
            $this->seodatas['business_name'] = $this->bizs[$business_id]['business_name'];
            $linkArr['business'] = $business_id;
        }

        $count = $Life->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Life->where($map)->order(array('top_date' => 'desc', 'last_time' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();


		
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('channelmeans',$chl );
        $this->assign('linkArr', $linkArr);
        $this->assign('tops', D('Life')->randTop());
        $this->display();
    }

    public function rand() {
        $this->assign('tops', D('Life')->randTop());
        $this->display();
    }

    public function fabu() {
        $this->assign('channelmeans', D('Lifecate')->getChannelMeans());
        $this->display();
    }

    public function create($cat) {
        $cat = (int) $cat;
        if (empty($cat)) {
            $this->error('请选择分类');
        }
        if (!$cate = $this->cates[$cat]) {
            $this->error('请选择正确分类');
        }

        if ($this->isPost()) {
            if (empty($this->uid)) {
                $this->ajaxLogin();
            }

            $data = $this->createCheck();
            $shop = D('Shop')->find(array("where" => array('user_id' => $this->uid, 'closed' => 0, 'audit' => 1)));
            if ($shop) {
                $data['is_shop'] = 1;
            }
			$data['city_id'] = $this->city_id;//小灰灰自己添加的
            $data['user_id'] = $this->uid;
            $data['cate_id'] = $cat;
            $details = $this->_post('details', 'SecurityEditorHtml');
            if ($words = D('Sensitive')->checkWords($details)) {
                $this->niuAlert('商家介绍含有敏感词：' . $words);
            }
            if ($life_id = D('Life')->add($data)) {
                $photos = $this->_post('photos', false);
                if (!empty($photos)) {
                    D('Lifephoto')->upload($life_id, $photos);
                }
                if ($details) {
                    D('Lifedetails')->updateDetails($life_id, $details);
                }
                $this->niuSuccess('发布信息成功，通过审核后将会显示！', U('member/life'));
            }
            $this->niuError('发布信息失败！');
        } else {
            $this->assign('channelmeans', D('Lifecate')->getChannelMeans());
            $this->assign('cate', $cate);
            $this->assign('attrs', D('Lifecateattr')->getAttrs($cat));
            $lat = $this->_get('lat', 'htmlspecialchars');
            $lng = $this->_get('lng', 'htmlspecialchars');

            $this->assign('lat', $lat ? $lat : $this->_CONFIG['site']['lat']);
            $this->assign('lng', $lng ? $lng : $this->_CONFIG['site']['lng']);
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->niuError('标题不能为空');
        }

        $data['area_id'] = (int) $data['area_id'];
        if (empty($data['area_id'])) {
            $this->niuError('地区不能为空');
        }
        $data['business_id'] = (int) $data['business_id'];
        if (empty($data['business_id'])) {
            $this->niuError('商圈不能为空');
        }
        $data['text1'] = htmlspecialchars($data['text1']);
        $data['text2'] = htmlspecialchars($data['text2']);
        $data['text3'] = htmlspecialchars($data['text3']);
        $data['num1'] = (int) $data['num1'];
        $data['num2'] = (int) $data['num2'];
        $data['select1'] = (int) $data['select1'];
        $data['select2'] = (int) $data['select2'];
        $data['select3'] = (int) $data['select3'];
        $data['select4'] = (int) $data['select4'];
        $data['select5'] = (int) $data['select5'];
        $data['urgent_date'] = TODAY;
        $data['top_date'] = TODAY;
        $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->niuError('缩略图格式不正确');
        }
        $data['contact'] = htmlspecialchars($data['contact']);
        if (empty($data['contact'])) {
            $this->niuError('联系人不能为空');
        } $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
            $this->niuError('电话不能为空');
        }
        if (!isMobile($data['mobile']) && !isPhone($data['mobile'])) {
            $this->niuError('电话格式不正确');
        }
        $data['qq'] = htmlspecialchars($data['qq']);
        $data['addr'] = htmlspecialchars($data['addr']);
        $data['lng'] = htmlspecialchars(trim($data['lng']));
        $data['lat'] = htmlspecialchars(trim($data['lat']));
        $data['views'] = (int) $data['views'];
        $data['create_time'] = NOW_TIME;
        $data['last_time'] = NOW_TIME + 86400 * 30;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

}
