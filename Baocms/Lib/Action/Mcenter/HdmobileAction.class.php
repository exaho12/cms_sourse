<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HdmobileAction extends CommonAction {

    public function index() {
        $this->display(); // 输出模板;
    }

    public function fabuloaddata() {
        $huodong = D('Huodong');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'user_id' => $this->uid);
        $count = $huodong->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $huodong->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $getHuoCate = D('Huodong')->getHuoCate();

        $this->assign('getHuoCate', $getHuoCate);
        $getPeopleCate = D('Huodong')->getPeopleCate();
        $this->assign('getPeopleCate', $getPeopleCate);


        $this->assign('pages', $shows); // 赋值分页输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    public function bm(){
        
         $this->display(); // 输出模板;
    }
    
    public function bmloaddata() {
        
        import('ORG.Util.Page'); // 导入分页类
        $huodongsign = D('Huodongsign');
        $maps = array('user_id' => $this->uid);
        $counts = $huodongsign->where($maps)->count(); // 查询满足要求的总记录数 
        $Pages = new Page($counts, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $shows = $Pages->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Pages->totalPages < $p) {
            die('0');
        }
        $lists = $huodongsign->where($maps)->limit($Pages->firstRow . ',' . $Pages->listRows)->select();
        $huodong_ids = array();
        foreach ($lists as $k => $val) {
            if ($val['huodong_id']) {
                $huodong_ids[$val['huodong_id']] = $val['huodong_id'];
            }
        }
        $this->assign('huodong', D('Huodong')->itemsByIds($huodong_ids));
        $getHuoCate = D('Huodong')->getHuoCate();

        $this->assign('getHuoCate', $getHuoCate);
        $getPeopleCate = D('Huodong')->getPeopleCate();
        $this->assign('getPeopleCate', $getPeopleCate);


        $this->assign('lists', $lists); // 赋值数据集
        $this->assign('pages', $shows); // 赋值分页输出
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	 public function post() {
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }

        if ($this->isPost()) {
            $data = $this->fabuCheck();
            $obj = D('Huodong');
            if ($obj->add($data)) {
                $this->niuMsg('添加成功', U('member/index'));
            }
            $this->niuMsg('操作失败！');
        } else {
            $getHuoCate = D('Huodong')->getHuoCate();
            $this->assign('getHuoCate', $getHuoCate);
            $getPeopleCate = D('Huodong')->getPeopleCate();
            $this->assign('getPeopleCate', $getPeopleCate);
            $this->display();
        }
    }
	
	

   

    public function fabuCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title', 'addr', 'intro', 'sex', 'photo', 'cate_id', 'time'));
        $data['user_id'] = $this->uid;
        $data['cate_id'] = (int) $data['cate_id'];
        $data['sex'] = (int) $data['sex'];

        $data['title'] = trim(htmlspecialchars($data['title']));
        if (empty($data['title'])) {
            $this->error('活动标题不能为空！');
        }
        $data['intro'] = trim(htmlspecialchars($data['intro']));
        if (empty($data['intro'])) {
            $this->error('详情不能为空！');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->error('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->error('缩略图格式不正确');
        }
        $data['audit'] = 1;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }

    public function join_people() {
        $huodong_id = (int) $this->_param('huodong_id');
        $this->assign('nextpage', LinkTo('mcenter/hdmobile/join_loaddata', array('t' => NOW_TIME, 'huodong_id' => $huodong_id, 'p' => '0000')));
        $this->display(); // 输出模板
    }

    public function join_loaddata() {

        $huodong_id = (int) $this->_param('huodong_id');
        if (!$detail = D('Huodong')->find($huodong_id)) {
            $this->error('活动不存在');
        }
        if ($detail['audit'] != 1 || $detail['closed'] != 0) {
            $this->error('活动不存在');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->error('请不要查看别人的活动报名');
        }
        $huodongsign = D('Huodongsign');

        import('ORG.Util.Page'); // 导入分页类
        $count = $huodongsign->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $huodongsign->where(array('huodong_id' => $huodong_id))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

}
