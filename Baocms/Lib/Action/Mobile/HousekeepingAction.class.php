<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HousekeepingAction extends CommonAction {

    public function main() {
        $this->display();
    }

    public function index() {
        $this->display();
    }

    public function appointment($svc_id) {
        if(!$svc_id=(int)$svc_id){
            $this->error('服务类型不能为空');
        }
        $workTypes = D('Housework')->getCfg();
        if(!isset($workTypes[$svc_id])){
            $this->error('暂时没有该服务类型');
        }
        $this->assign('workTypes',$workTypes);
        $this->assign('svc_id',$svc_id);
        $this->display();
    }
    
    
    public function create($svc_id){
         if(!$svc_id=(int)$svc_id){
            $this->error('服务类型不能为空');
        }
        $workTypes = D('Housework')->getCfg();
        if(!isset($workTypes[$svc_id])){
            $this->error('暂时没有该服务类型');
        }
        $data['svc_id'] = $svc_id;
        if(!$data['svctime'] = $this->_post('svctime',  'htmlspecialchars')){
             $this->error('服务时间不能为空');
        }
        if(!$data['addr'] = $this->_post('addr',  'htmlspecialchars')){
             $this->error('服务地址不能为空');
        }
        if(!$data['name'] = $this->_post('name',  'htmlspecialchars')){
             $this->error('联系人不能为空');
        }
        if(!$data['tel'] = $this->_post('tel',  'htmlspecialchars')){
             $this->error('联系电话不能为空');
        }
        if(!isMobile($data['tel']) && !isPhone($data['tel'])){
            $this->error('电话号码不正确');
        }
        $data['contents'] = $this->_post('contents','htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        if(D('Housework')->add($data)){
            $this->success('恭喜您预约家政服务成功！网站会推荐给您最优秀的阿姨帮忙！');
        }
        $this->error('服务器繁忙');

    }

}
