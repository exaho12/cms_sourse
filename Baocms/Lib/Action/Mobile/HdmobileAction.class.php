<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HdmobileAction extends CommonAction {

    protected function _initialize() {
        parent::_initialize();
        $getHuoCate = D('Huodong')->getHuoCate();
        $this->assign('getHuoCate', $getHuoCate);
        $getPeopleCate = D('Huodong')->getPeopleCate();
        $this->assign('getPeopleCate', $getPeopleCate);
        $this->assign('traffic', D('Huodong')->getTraffic());
    }

    public function index() {
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $cate_id = (int) $this->_param('cat');
        $this->assign('cat', $cate_id);
        $this->assign('nextpage', LinkTo('hdmobile/loaddata', array('cat' => $cate_id, 't' => NOW_TIME, 'keyword' => $keyword, 'p' => '0000')));
        $user_id = (int) $this->$user_id;
        $this->display(); // 输出模板
    }

    public function loaddata() {
        $huodong = D('Huodong');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('audit' => 1, 'closed' => 0, 'city_id' => $this->city_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['cate_id'] = $cat;
        }
        $count = $huodong->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
        $list = $huodong->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $signs = D('Huodongsign')->select();
        $user_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val['user_id'])) {
                $user_ids[$val['user_id']] = $val['user_id'];
            }
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
            if ($val['limit_num'] > 0 && $val['limit_num'] <= $val['sign_num']) {
                $list[$k]['sign'] = 2;   //已报名人数超限
            } else {
                foreach ($signs as $kk => $v) {
                    if ($val['huodong_id'] == $v['huodong_id'] && $v['user_id'] == $this->uid) {
                        $list[$k]['sign'] = 1; //已报过
                    } else {
                        $list[$k]['sign'] = 0;
                    }
                }
            }
        }
        $this->assign('y', date('Y', NOW_TIME));
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('ex', D('Usersex')->itemsByIds($user_ids));
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function sign($huodong_id) {
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }
        $huodong_id = (int) $huodong_id;
        $detail = D('Huodong')->find($huodong_id);
        if (empty($detail)) {
            $this->error('报名的活动不存在');
        }
        if ($detail['audit'] != 1 || $detail['closed'] != 0) {
            $this->error('活动不存在');
        }
        if ($this->isPost()) {
            $data = $this->checkSign();
            $data['huodong_id'] = $huodong_id;
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $obj = D('Huodongsign');
            if ($obj->add($data)) {
                D('Huodong')->updateCount($huodong_id, 'sign_num');
                $this->niuMsg('恭喜您报名成功', U('hdmobile/index'));
            }
            $this->niuMsg('操作失败！');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function checkSign() {
        $data = $this->checkFields($this->_post('data', false), array('name', 'mobile', 'num'));
        $data['user_id'] = (int) $this->uid;
        $data['name'] = $data['name'];
        if (empty($data['name'])) {
            $this->niuMsg('联系人不能为空');
        }
        $data['mobile'] = htmlspecialchars($data['mobile']);
        if (empty($data['mobile'])) {
            $this->niuMsg('联系电话不能为空');
        }
        if (!isPhone($data['mobile']) && !isMobile($data['mobile'])) {
            $this->niuMsg('联系电话格式不正确');
        }
        $data['num'] = (int) $data['num'];
        if (empty($data['num'])) {
            $this->niuMsg('活动人数不能为空');
        }
        return $data;
    }

    public function detail() {
        $huodong_id = (int) $this->_get('huodong_id');
        if (empty($huodong_id)) {
            $this->error('该活动信息不存在！');
            die;
        }
        if (!$detail = D('Huodong')->find($huodong_id)) {
            $this->error('该活动信息不存在！');
            die;
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('该活动信息不存在！');
            die;
        }
        if (!empty($detail['limit_num'])) {
            if ($detail['limit_num'] <= $detail['sign_num']) {
                $detail['sign'] = 2;
            }
        }
        $sign = D('Huodongsign')->where(array('user_id' => $this->uid, 'huodong_id' => $huodong_id))->find();
        if (!empty($sign)) {
            $detail['sign'] = 1;
        } else {
            $detail['sign'] = 0;
        }
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $detail['d'] = getDistance($lat, $lng, $detail['lat'], $detail['lng']);
        $list = D('Huodongdianping')->where(array('huodong_id' => $huodong_id))->select();
        $user_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val['user_id'])) {
                $user_ids[$val['user_id']] = $val['user_id'];
            }
        }
        $this->assign('users', D('Users')->itemsByIds($user_ids));

        $td = strtotime(date('Y-m-d', time()));
        $tmd = strtotime(date('Y-m-d', time() + 86400));
        $looks = D('Huodonglooks')->where(array('create_time' => array('between', $td . ',' . $tmd), 'create_ip' => get_client_ip(), 'huodong_id' => $huodong_id))->find();
        if (empty($looks)) {
            if (!empty($this->uid)) {
                D('Huodonglooks')->add(array('user_id' => $this->uid, 'huodong_id' => $huodong_id, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
                D('Huodong')->updateCount($huodong_id, 'views');
            }
        }
        $huodonglooks = D('Huodonglooks')->where(array('huodong_id' => $huodong_id))->select();
        $user_idss = array();
        foreach ($huodonglooks as $k => $val) {
            if (!empty($val['user_id'])) {
                $user_idss[$val['user_id']] = $val['user_id'];
            }
        }
        $this->assign('lookusers', D('Users')->itemsByIds($user_idss));
        $this->assign('ex', D('Usersex')->itemsByIds($user_idss));
        $this->assign('y', date('Y', NOW_TIME));
        $huser = D("Users")->find($detail['user_id']);
        $hex = D("Usersex")->find($detail['user_id']);
        $this->assign('hex', $hex);
        $this->assign('huser', $huser);
        $this->assign('detail', $detail);
        $this->assign('traffic', D('Huodong')->getTraffic());
        $this->assign('stars', D('Usersex')->getStar());
        $this->assign('list', $list);
        $this->assign('huodonglooks', $huodonglooks);
        $this->display();
    }

    public function dianping() {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login', 'message' => '请先登录'));
        }
        $huodong_id = (int) $_POST['huodong_id'];
        if (empty($huodong_id)) {
            $this->ajaxReturn(array('status' => 'error', 'message' => '该活动信息不存在!'));
        }
        if (!$detail = D('Huodong')->find($huodong_id)) {
            $this->ajaxReturn(array('status' => 'error', 'message' => '该活动信息不存在!'));
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->ajaxReturn(array('status' => 'error', 'message' => '该活动信息不存在!'));
        }
        $dd = D('Huodongdianping')->where(array('huodong_id' => $huodong_id, 'user_id' => $this->uid))->find();
        if (!empty($dd)) {
            $this->ajaxReturn(array('status' => 'error', 'message' => '您已经评论过了!'));
        }
        if (IS_AJAX) {
            $contents = htmlspecialchars($_POST['contents']);
            if (empty($contents)) {
                $this->ajaxReturn(array('status' => 'error', 'message' => '评论内容不能为空!'));
            }
            if ($words = D('Sensitive')->checkWords($contents)) {
                $this->ajaxReturn(array('status' => 'error', 'message' => '评论内容含有敏感词：!' . $words));
            }
            $obj = D('Huodongdianping');

            if ($obj->add(array('huodong_id' => $huodong_id, 'user_id' => $this->uid, 'contents' => $contents, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()))) {
                D('Huodong')->updateCount($huodong_id, 'ping_num');
                $this->ajaxReturn(array('status' => 'success', 'message' => '评论成功!'));
            }
            $this->ajaxReturn(array('status' => 'error', 'message' => '操作失败!'));
        }
    }

    public function send_message() {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login', 'message' => '请先登录'));
        }
        $user_id = (int) $this->_param('user_id');
        if (empty($user_id)) {
            $this->ajaxReturn(array('status' => 'error', 'message' => '接收人不能为空'));
        }
        if ($user_id == $this->uid) {
            $this->ajaxReturn(array('status' => 'error', 'message' => '不能给自己发消息'));
        }
        if (IS_AJAX) {
            $content = $this->_post('content', 'htmlspecialchars');
            if (empty($content)) {
                $this->ajaxReturn(array('status' => 'error', 'message' => '发送内容不能为空'));
            }
            if ($words = D('Sensitive')->checkWords($content)) {
                $this->ajaxReturn(array('status' => 'error', 'message' => '发送内容含有敏感词:' . $words));
            }
            $obj = D('Usermessage');
            if ($obj->add(array('from_id' => $this->uid, 'user_id' => $user_id, 'content' => $content, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()))) {
                $this->ajaxReturn(array('status' => 'success', 'message' => '发送成功'));
            }
            $this->ajaxReturn(array('status' => 'error', 'message' => '发送失败!'));
        }
    }

    public function hdfabu() {
        //dump($this->uid);
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }
        if ($this->isPost()) {
            $data = $this->fabuCheck();
            $obj = D('Huodong');
            if ($obj->add($data)) {
                $this->niuMsg('恭喜您发布成功，审核过后即可显示！', U('mobile/hdmobile/index'));
            }
            $this->niuMsg('操作失败！');
        } else {
            $lat = addslashes(cookie('lat'));
            $lng = addslashes(cookie('lng'));
            if (empty($lat) || empty($lng)) {
                $lat = $this->city['lat'];
                $lng = $this->city['lng'];
            }
            $this->assign('lng', $lng);
            $this->assign('lat', $lat);
            $getHuoCate = D('Huodong')->getHuoCate();
            $this->assign('getHuoCate', $getHuoCate);
            $getPeopleCate = D('Huodong')->getPeopleCate();
            $this->assign('getPeopleCate', $getPeopleCate);
            $this->display();
        }
    }

    public function fabuCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'addr', 'intro', 'sex', 'city_id', 'lng', 'lat', 'photo', 'cate_id', 'traffic', 'limit_num', 'time'));
        $data['user_id'] = $this->uid;
        $data['city_id'] = $this->city_id;
        $data['lng'] = $data['lng'];
        $data['lat'] = $data['lat'];
        $data['cate_id'] = (int) $data['cate_id'];
        $data['sex'] = (int) $data['sex'];
        $data['traffic'] = (int) $data['traffic'];
        $data['limit_num'] = (int) $data['limit_num'];
        $data['title'] = trim(htmlspecialchars($data['title']));
        if (empty($data['title'])) {
            $this->niuMsg('活动标题不能为空！');
        }
        $data['intro'] = trim(htmlspecialchars($data['intro']));
        if (empty($data['intro'])) {
            $this->niuMsg('详情不能为空！');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->niuMsg('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->niuMsg('缩略图格式不正确');
        }
        $data['audit'] = $this->_CONFIG['site']['hdmobile_hdfabu_audit'];//发布是免审核
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

}
