<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TuanAction extends CommonAction {

    private $create_fields = array('shop_id', 'use_integral', 'cate_id', 'intro', 'title', 'photo', 'price', 'tuan_price', 'settlement_price', 'num', 'sold_num', 'bg_date', 'end_date', 'fail_date', 'is_hot', 'is_new', 'is_chose', 'freebook', 'branch_id');
    private $edit_fields = array('shop_id', 'use_integral', 'cate_id', 'intro', 'title', 'photo', 'price', 'tuan_price', 'settlement_price', 'num', 'sold_num', 'bg_date', 'end_date', 'fail_date', 'is_hot', 'is_new', 'is_chose', 'freebook', 'branch_id');
    protected $tuancates = array();

    public function _initialize() {
        parent::_initialize();
        $this->tuancates = D('Tuancate')->fetchAll();
        $this->assign('tuancates', $this->tuancates);
        $branch = D('Shopbranch')->where(array('shop_id' => $this->shop_id, 'closed' => 0, 'audit' => 1))->select();
        $this->assign('branch', $branch);
    }

    public function index() {
        $Tuan = D('Tuan');

        import('ORG.Util.Page'); // 导入分页类    
        $map = array('shop_id' => $this->shop_id, 'closed' => 0, 'end_date' => array('EGT', TODAY));
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Tuan->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuan->where($map)->order(array('tuan_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            $val = $Tuan->_format($val);
            $list[$k] = $val;
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function history() {
        $Tuan = D('Tuan');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id' => $this->shop_id, 'end_date' => array('LT', TODAY));
        $count = $Tuan->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuan->where($map)->order(array('tuan_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            $val = $Tuan->_format($val);
            $list[$k] = $val;
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function order() {
        $Tuanorder = D('Tuanorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('shop_id' => $this->shop_id);
        if (($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date);
            $map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
            $this->assign('bg_date', $bg_date);
            $this->assign('end_date', $end_date);
        } else {
            if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
                $bg_time = strtotime($bg_date);
                $this->assign('bg_date', $bg_date);
                $map['create_time'] = array('EGT', $bg_time);
            }
            if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
                $end_time = strtotime($end_date);
                $this->assign('end_date', $end_date);
                $map['create_time'] = array('ELT', $end_time);
            }
        }

        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['order_id'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }

        if (isset($_GET['st']) || isset($_POST['st'])) {
            $st = (int) $this->_param('st');
            if ($st != 999) {
                $map['status'] = $st;
            }
            $this->assign('st', $st);
        } else {
            $this->assign('st', 999);
        }
        $count = $Tuanorder->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Tuanorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = $user_ids = $tuan_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val['shop_id'])) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $user_ids[$val['user_id']] = $val['user_id'];
            $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
        }
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('tuan', D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function used() {
        if ($this->isPost()) {
            $code = $this->_post('code', false);
            if (empty($code)) {
                $this->baoError('请输入抢购券！');
            }

            $obj = D('Tuancode');
            $shopmoney = D('Shopmoney');
            $return = array();
            $ip = get_client_ip();
            if (count($code) > 10) {
                $this->baoError('一次最多验证10条抢购券！');
            }
            $userobj = D('Users');
            foreach ($code as $key => $var) {
                $var = trim(htmlspecialchars($var));
				
                if (!empty($var)) {
                    $data = $obj->find(array('where' => array('code' => $var)));
					$shop = D('Shop')->find(array('where' => array('shop_id' => $data['shop_id'])));

                    if (!empty($data) && $data['shop_id'] == $this->shop_id && (int) $data['is_used'] == 0 && (int) $data['status'] == 0) {
                        if ($obj->save(array('code_id' => $data['code_id'], 'is_used' => 1,'used_time' => NOW_TIME,'worker_id' => $this->uid, 'used_ip' => $ip))) { //这次更新保证了更新的结果集              
                            //增加MONEY 的过程 稍后补充
                            if (!empty($data['price'])) {
                                $data['intro'] = '抢购消费' . $data['order_id'];
						
                                $shopmoney->add(array(
                                    'shop_id' => $data['shop_id'],
									'city_id' => $shop['city_id'],
									'area_id' => $shop['area_id'],
									'branch_id' => $data['branch_id'],
                                    'money' => $data['settlement_price'],
                                    'create_ip' => $ip,
                                    'create_time' => NOW_TIME,
                                    'order_id' => $data['order_id'],
                                    'intro' => $data['intro'],
                                ));
                                
								D('Users')->Money($shop['user_id'], $data['settlement_price'], '商户抢购资金结算:' . $data['order_id']);
							
                                $return[$var] = $var;
                                D('Users')->gouwu($data['user_id'],$data['price'],'抢购券消费成功');
                                echo '<script>parent.used(' . $key . ',"√验证成功",1);</script>';
                            } else {
                                echo '<script>parent.used(' . $key . ',"√到店付抢购券验证成功，需要现金付款",2);</script>';
                            }
                  
                        }
                    } else {
                        echo '<script>parent.used(' . $key . ',"X该抢购券无效",3);</script>';
                    }
                }
            }
        } else {
            $this->display();
        }
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Tuan');
            $details = $this->_post('details', 'SecurityEditorHtml');
            if (empty($details)) {
                $this->baoError('抢购详情不能为空');
            }
            if ($words = D('Sensitive')->checkWords($details)) {
                $this->baoError('详细内容含有敏感词：' . $words);
            }
            $instructions = $this->_post('instructions', 'SecurityEditorHtml');
            if (empty($instructions)) {
                $this->baoError('购买须知不能为空');
            }
            if ($words = D('Sensitive')->checkWords($instructions)) {
                $this->baoError('购买须知含有敏感词：' . $words);
            }
            $thumb = $this->_param('thumb', false);
            foreach ($thumb as $k => $val) {
                if (empty($val)) {
                    unset($thumb[$k]);
                }
                if (!isImage($val)) {
                    unset($thumb[$k]);
                }
            }
            $data['thumb'] = serialize($thumb);
            if ($tuan_id = $obj->add($data)) {
                $wei_pic = D('Weixin')->getCode($tuan_id, 2); //抢购类型是2
                $obj->save(array('tuan_id' => $tuan_id, 'wei_pic' => $wei_pic));
                D('Tuandetails')->add(array('tuan_id' => $tuan_id, 'details' => $details, 'instructions' => $instructions));
                $this->baoSuccess('添加成功', U('tuan/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('抢购分类不能为空');
        }
        $data['shop_id'] = $this->shop_id;
        $data['branch_id'] = (int) $data['branch_id'];
        if (!empty($data['branch_id'])) {
            $branch = D('Shopbranch')->where(array('shop_id' => $this->shop_id, 'branch_id' => $data['branch_id'], 'audit' => 1))->find();
            $data['lng'] = $branch['lng'];
            $data['lat'] = $branch['lat'];
            $data['area_id'] = $branch['area_id'];
            $data['business_id'] = $branch['business_id'];
            $data['city_id'] = $branch['city_id'];
        } else {
            $data['lng'] = $this->shop['lng'];
            $data['lat'] = $this->shop['lat'];
            $data['area_id'] = $this->shop['area_id'];
            $data['business_id'] = $this->shop['business_id'];
            $data['city_id'] = $this->shop['city_id'];
        }
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('抢购名称不能为空');
        }
        $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('抢购简介不能为空');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传图片');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('图片格式不正确');
        } $data['price'] = (int) ($data['price'] * 100);
        if (empty($data['price'])) {
            $this->baoError('市场价格不能为空');
        }
        $data['tuan_price'] = (int) ($data['tuan_price'] * 100);
        if (empty($data['tuan_price'])) {
            $this->baoError('抢购价格不能为空');
        }
        
        $data['settlement_price'] = (int)( $data['tuan_price'] - ($data['tuan_price'] *  $this->tuancates[$data['cate_id']]['rate'] /1000) );
        
        $data['use_integral'] = (int) $data['use_integral'];

        $data['num'] = (int) $data['num'];
        if (empty($data['num'])) {
            $this->baoError('库存不能为空');
        } $data['sold_num'] = (int) $data['sold_num'];
        $data['bg_date'] = htmlspecialchars($data['bg_date']);
        if (empty($data['bg_date'])) {
            $this->baoError('开始时间不能为空');
        }
        if (!isDate($data['bg_date'])) {
            $this->baoError('开始时间格式不正确');
        } $data['end_date'] = htmlspecialchars($data['end_date']);
        if (empty($data['end_date'])) {
            $this->baoError('结束时间不能为空');
        }
        if (!isDate($data['end_date'])) {
            $this->baoError('结束时间格式不正确');
        }
        $data['is_hot'] = (int) $data['is_hot'];
        $data['is_new'] = (int) $data['is_new'];
        $data['is_chose'] = (int) $data['is_chose'];
        $data['is_multi'] = (int) $data['is_multi'];
        $data['freebook'] = (int) $data['freebook'];
        $data['is_return_cash'] = (int) $data['is_return_cash'];
        $data['fail_date'] = htmlspecialchars($data['fail_date']);
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

    public function setting($tuan_id = 0) {
        if ($tuan_id = (int) $tuan_id) {
            $obj = D('Tuanmeal');
            if (!$detail = D('Tuan')->find($tuan_id)) {
                $this->baoError('请选择要设置的抢购');
            }
            if ($detail['shop_id'] != $this->shop_id) {
                $this->baoError('请不要操作别人的抢购');
            }
            if ($detail['closed'] != 0) {
                $this->baoError('该抢购已被删除');
            }
            $tuan_details = D('Tuandetails')->getDetail($tuan_id);
            if ($this->isPost()) {
                $name = $this->_post('name', 'htmlspecialchars');
                if (empty($name)) {
                    $this->baoError('主套餐名称不能为空');
                }
                $data = $this->_post('data', false);
                $obj->delete(array("where" => array('tuan_id' => $tuan_id)));
                $obj->add(array('tuan_id' => $tuan_id, 'id' => 0, 'name' => $name));
                foreach ($data as $val) {
                    if (!empty($val['id']) && !empty($val['name'])) {

                        $obj->add(array(
                            'tuan_id' => $tuan_id,
                            'id' => $val['id'],
                            'name' => $val['name'],
                        ));
                    }
                }
                $this->baoSuccess('操作成功！', U('tuan/index'));
            } else {
                $this->assign('detail', $detail);
                $this->assign('meals', $obj->where(array('tuan_id' => $tuan_id, 'id' => array('NEQ', 0)))->select());
                $this->assign('name', $obj->where(array('tuan_id' => $tuan_id, 'id' => 0))->find());
                $this->display();
            }
        } else {
            $this->baoError('请选择要设置的抢购');
        }
    }

    public function edit($tuan_id = 0) {
        if ($tuan_id = (int) $tuan_id) {
            $obj = D('Tuan');
            if (!$detail = $obj->find($tuan_id)) {
                $this->baoError('请选择要编辑的抢购');
            }
            if ($detail['shop_id'] != $this->shop_id) {
                $this->baoError('请不要操作别人的抢购');
            }
            if ($detail['closed'] != 0) {
                $this->baoError('该抢购已被删除');
            }
            $tuan_details = D('Tuandetails')->getDetail($tuan_id);
            if ($this->isPost()) {
                $data = $this->editCheck();
                $details = $this->_post('details', 'SecurityEditorHtml');
                if (empty($details)) {
                    $this->baoError('抢购详情不能为空');
                }
                if ($words = D('Sensitive')->checkWords($details)) {
                    $this->baoError('详细内容含有敏感词：' . $words);
                }
                $instructions = $this->_post('instructions', 'SecurityEditorHtml');
                if (empty($instructions)) {
                    $this->baoError('购买须知不能为空');
                }
                if ($words = D('Sensitive')->checkWords($instructions)) {
                    $this->baoError('购买须知含有敏感词：' . $words);
                }
                $thumb = $this->_param('thumb', false);
                foreach ($thumb as $k => $val) {
                    if (empty($val)) {
                        unset($thumb[$k]);
                    }
                    if (!isImage($val)) {
                        unset($thumb[$k]);
                    }
                }
                $data['thumb'] = serialize($thumb);
                $data['tuan_id'] = $tuan_id;
                if (!empty($detail['wei_pic'])) {
                    if (true !== strpos($detail['wei_pic'], "https://mp.weixin.qq.com/")) {
                        $wei_pic = D('Weixin')->getCode($tuan_id, 2);
                        $data['wei_pic'] = $wei_pic;
                    }
                } else {
                    $wei_pic = D('Weixin')->getCode($tuan_id, 2);
                    $data['wei_pic'] = $wei_pic;
                }
                $data['audit'] = 0;
                if($data['tuan_price']<= $detail['settlement_price']){
                    $this->baoError('售价不能小于或者等于结算价格');
                }
                if (false !== $obj->save($data)) {
                    D('Tuandetails')->save(array('tuan_id' => $tuan_id, 'details' => $details, 'instructions' => $instructions));
                    $this->baoSuccess('操作成功', U('tuan/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $obj->_format($detail));
                $thumb = unserialize($detail['thumb']);
                $this->assign('thumb', $thumb);
                $this->assign('shop', D('Shop')->find($detail['shop_id']));
                $this->assign('tuan_details', $tuan_details);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的抢购');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['cate_id'] = (int) $data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('抢购分类不能为空');
        }
        $data['shop_id'] = $this->shop_id;
        $data['lng'] = $this->shop['lng'];
        $data['lat'] = $this->shop['lat'];
        $data['area_id'] = $this->shop['area_id'];
        $data['business_id'] = $this->shop['business_id'];
        $data['city_id'] = $this->shop['city_id'];
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('商品名称不能为空');
        }
        $data['intro'] = htmlspecialchars($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('副标题不能为空');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传图片');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('图片格式不正确');
        }$data['price'] = (int) ($data['price'] * 100);
        if (empty($data['price'])) {
            $this->baoError('市场价格不能为空');
        } $data['tuan_price'] = (int) ($data['tuan_price'] * 100);
        if (empty($data['tuan_price'])) {
            $this->baoError('抢购价格不能为空');
        }
        $data['settlement_price'] = (int)( $data['tuan_price'] - ($data['tuan_price'] *  $this->tuancates[$data['cate_id']]['rate'] /1000) );
        $data['use_integral'] = (int) $data['use_integral'];
        $data['num'] = (int) $data['num'];
        if (empty($data['num'])) {
            $this->baoError('库存不能为空');
        } $data['sold_num'] = (int) $data['sold_num'];
        $data['bg_date'] = htmlspecialchars($data['bg_date']);
        if (empty($data['bg_date'])) {
            $this->baoError('开始时间不能为空');
        }
        if (!isDate($data['bg_date'])) {
            $this->baoError('开始时间格式不正确');
        } $data['end_date'] = htmlspecialchars($data['end_date']);
        if (empty($data['end_date'])) {
            $this->baoError('结束时间不能为空');
        }
        if (!isDate($data['end_date'])) {
            $this->baoError('结束时间格式不正确');
        }
        $data['branch_id'] = (int) $data['branch_id'];
        $data['is_hot'] = (int) $data['is_hot'];
        $data['is_new'] = (int) $data['is_new'];
        $data['is_chose'] = (int) $data['is_chose'];
        $data['is_multi'] = (int) $data['is_multi'];
        $data['freebook'] = (int) $data['freebook'];
        $data['is_return_cash'] = (int) $data['is_return_cash'];
        $data['fail_date'] = htmlspecialchars($data['fail_date']);
        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }

}
