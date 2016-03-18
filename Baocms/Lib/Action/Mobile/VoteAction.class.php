<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class VoteAction extends CommonAction {

    public function index($vote_id) {
        $vote_id = (int) $vote_id;
        if (!$detail = D('Vote')->find($vote_id)) {
            die('该投票不存在！');
        }
        if (!$detail['is_work']) {
            die('该投票不存在！');
        }
        $options = D('Voteoption')->where(array('vote_id' => $vote_id))->select();
        if (empty($options)) {
            die('该投票还没有设置好');
        }

        $this->assign('detail', $detail);
        $options = D('Voteoption')->where(array('vote_id' => $vote_id))->select();
        $this->assign('options', $options);

        $ip = get_client_ip();
        $works = 0;
        $res = D('Voteresult')->where(array('vote_id' => $vote_id, 'create_ip' => $ip))->select();
        if ($detail['is_select'] == 0) {
            if (!empty($res)) {
                $works = 1;
            }
        } else {
            if ($detail['is_pic'] == 0) {
                if (!empty($res)) {
                    $works = 1;
                }
            }
        }
        $shopdetails = D('Shopdetails')->find($detail['shop_id']);
        $this->assign('shopdetails', $shopdetail);
        $this->assign('works', $works);
        if ($detail['is_pic']) {
            $this->display('pic');
        } else {
            $this->display();
        }
    }

    public function vote($vote_id) {
        $vote_id = (int) $vote_id;
        $detail = D('Vote')->find($vote_id);
        if ($this->isPost()) {
            $options = $this->_post('data', false);
            if (empty($options)) {
                $this->error('请选择您要投的选项');
            }
            $data = array();
            $data['vote_id'] = $vote_id;
            $data['user_id'] = $this->uid;
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            if ($detail['is_select'] == 1) {
                $option = array_flip($options);
                $res = implode(',', $option);
                $data['vote_option'] = $res;
                foreach ($options as $k => $v) {
                    $option_id[] = $k;
                }
                if ($result_id = D('Voteresult')->add($data)) {
                    D('Vote')->updateCount($vote_id, 'num');
                    foreach ($option_id as $key => $val) {
                        D('Voteoption')->updateCount($val, 'number');
                    }
                    $this->success('投票成功', U('vote/index', array('vote_id' => $vote_id)));
                }
                $this->error("投票失败");
            } else {
                $data['vote_option'] = $options;
                if ($result_id = D('Voteresult')->add($data)) {
                    D('Vote')->updateCount($vote_id, 'num');
                    D('Voteoption')->updateCount($options, 'number');
                    $this->success('投票成功', U('vote/index', array('vote_id' => $vote_id)));
                }
                $this->error("投票失败");
            }
        }
    }

    public function picvote($vote_id, $option_id) {
        $vote_id = (int) $vote_id;
        $detail = D('Vote')->find($vote_id);
        $ip = get_client_ip();
        $result = D('Voteresult')->where(array('vote_id' => $vote_id, 'create_ip' => $ip))->select();
        if ($this->_param()) {
            $data = array();
            $data['vote_id'] = $vote_id;
            $data['user_id'] = $this->uid;
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();

            if ($detail['is_select'] == 1) {
                if (!empty($result)) {
                    $res = explode(',', $result[0]['vote_option']);
                    if (in_array($option_id, $res)) {
                        $this->error('您已经投过该选项了');
                    } else {
                        $res[] = $option_id;
                        $data['vote_option'] = join(',', $res);
                        $data['result_id'] = $result[0]['result_id'];

                        if (false !== D('Voteresult')->save($data)) {
                            D('Voteoption')->updateCount($option_id, 'number');
                            $this->success('投票成功', U('vote/index', array('vote_id' => $vote_id)));
                        }
                        $this->error("投票失败");
                    }
                }
                $data['vote_option'] = $option_id;
                if ($result_id = D('Voteresult')->add($data)) {
                    D('Vote')->updateCount($vote_id, 'num');
                    D('Voteoption')->updateCount($option_id, 'number');
                    $this->success('投票成功', U('vote/index', array('vote_id' => $vote_id)));
                }
                $this->error("投票失败");
            } else {
                if (!empty($result)) {
                    $this->error('您已经投过票了');
                }
                if ($result_id = D('Voteresult')->add($data)) {
                    D('Vote')->updateCount($vote_id, 'num');
                    D('Voteoption')->updateCount($options, 'number');
                    $this->success('投票成功', U('vote/index', array('vote_id' => $vote_id)));
                }
                $this->error("投票失败");
            }
        }
    }

    public function result($vote_id) {
        if (!$detail = D('Vote')->find($vote_id)) {
            $this->error('请选择正确的投票');
        }

        $total = D('Voteoption')->where(array('vote_id' => $vote_id))->sum('number');
        $this->assign('total', $total);
        $this->assign('options', D('Voteoption')->order(array('orderby' => 'asc'))->where(array('vote_id' => $vote_id))->select());
        $this->assign('detail', $detail);
        $this->display();
    }

}
