<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class WeixinmsgAction extends CommonAction{


    
    public  function index(){
       $Weixinmsg = D('Weixinmsg');
       import('ORG.Util.Page');// 导入分页类
       $map = array();
       $count      = $Weixinmsg->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Weixinmsg->where($map)->order(array('msg_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $this->assign('list',$list);// 赋值数据集
       $this->assign('page',$show);// 赋值分页输出
       $this->display(); // 输出模板
    }

    

    
   
}
