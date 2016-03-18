<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PassportAction extends CommonAction {

    private $create_fields = array('account', 'password', 'nickname');

    public function bind() {
        $this->display();
    }

    public function login() {
        if ($this->isPost()) {
        
            $account = $this->_post('account');
            if (empty($account)) {
            
                $this->error('请输入用户名!');
            }

            $password = $this->_post('password');
            if (empty($password)) {
             
                $this->error('请输入登录密码!');
            }
            $backurl = $this->_post('backurl', 'htmlspecialchars');
            if (empty($backurl))
                $backurl = U('index/index');
            if (true == D('Passport')->login($account, $password)) {
                $this->success('恭喜您登录成功！', $backurl);
            }
            $this->error(D('Passport')->getError());
        } else {
            if (!empty($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) && !strstr($_SERVER['HTTP_REFERER'], 'passport')) {
                $backurl = $_SERVER['HTTP_REFERER'];
            } else {
                $backurl = U('index/index');
            }
            $this->assign('backurl', $backurl);
            $this->display();
        }
    }
    

    public function wblogin() {
        $login_url = 'https://api.weibo.com/oauth2/authorize?client_id=' . $this->_CONFIG['connect']['wb_app_id'] . '&response_type=code&redirect_uri=' . urlencode(__HOST__ . U('passport/wbcallback'));
        header("Location:$login_url");
        die;
    }

    public function qqlogin() {
        $state = md5(uniqid(rand(), TRUE));

        session('state', $state);
        $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
                . $this->_CONFIG['connect']['qq_app_id'] . "&redirect_uri=" . urlencode(__HOST__ . U('passport/qqcallback'))
                . "&state=" . $state
                . "&scope=";
        header("Location:$login_url");
        die;
    }

    public function wxlogin() {
        $state = md5(uniqid(rand(), TRUE));

        session('state', $state);
        $login_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->_CONFIG['weixin']['appid']
                . '&redirect_uri=' . urlencode(__HOST__ . U('passport/wxcallback')) . '&response_type=code&scope=snsapi_base&state=' . $state . '#wechat_redirect';
        header("Location:$login_url");
        die;
    }

    public function wbcallback() {

        import("@/Net.Curl");
        $curl = new Curl();

        $params = array(
            'grant_type' => 'authorization_code',
            'code' => $_REQUEST["code"],
            'client_id' => $this->_CONFIG['connect']['wb_app_id'],
            'client_secret' => $this->_CONFIG['connect']['wb_app_key'],
            'redirect_uri' => __HOST__ . U('passport/qqcallback')
        );
        $url = 'https://api.weibo.com/oauth2/access_token';
        $response = $curl->post($url, http_build_query($params));
        $params = json_decode($response, true);
        if (isset($params['error'])) {
            echo "<h3>error:</h3>" . $params['error'];
            echo "<h3>msg  :</h3>" . $params['error_code'];
            exit;
        }
        $url = 'https://api.weibo.com/2/account/get_uid.json?source=' . $this->_CONFIG['connect']['wb_app_key'] . '&access_token=' . $params['access_token'];
        $result = $curl->get($url);
        $user = json_decode($result, true);
        if (isset($user['error'])) {
            echo "<h3>error:</h3>" . $user['error'];
            echo "<h3>msg  :</h3>" . $user['error_code'];
            exit;
        }
        $data = array(
            'type' => 'weibo',
            'open_id' => $user['uid'],
            'token' => $params['access_token']
        );
        $this->thirdlogin($data);
    }

    public function wxcallback() {
        if ($_REQUEST['state'] == session('state')) {
            import("@/Net.Curl");
            $curl = new Curl();
            if (empty($_REQUEST["code"])) {
                $this->error('授权后才能登陆！', U('passport/login'));
            }
            $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->_CONFIG['weixin']["appid"] .
                    '&secret=' . $this->_CONFIG['weixin']["appsecret"] .
                    '&code=' . $_REQUEST["code"] . '&grant_type=authorization_code';
            $str = $curl->get($token_url);

            $params = json_decode($str, true);
            if (!empty($params['errcode'])) {
                echo "<h3>error:</h3>" . $params['errcode'];
                echo "<h3>msg  :</h3>" . $params['errmsg'];
                exit;
            }
            if (empty($params['openid'])) {
                $this->error('登录失败', U('passport/login'));
            }
            $data = array(
                'type' => 'weixin',
                'open_id' => $params['openid'],
                'token' => $params['refresh_token']
            );
            $this->thirdlogin($data);
        }
    }

    public function qqcallback() {
        import("@/Net.Curl");
        $curl = new Curl();
        if ($_REQUEST['state'] == session('state')) {
            $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
                    . "client_id=" . $this->_CONFIG['connect']["qq_app_id"] . "&redirect_uri=" . urlencode(__HOST__ . U('passport/qqcallback'))
                    . "&client_secret=" . $this->_CONFIG['connect']["qq_app_key"] . "&code=" . $_REQUEST["code"];
            $response = $curl->get($token_url);

            if (strpos($response, "callback") !== false) {
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
                $msg = json_decode($response);
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
            $params = array();
            parse_str($response, $params);
            if (empty($params))
                die;
            $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
                    . $params['access_token'];

            $str = $curl->get($graph_url);
            if (strpos($str, "callback") !== false) {
                $lpos = strpos($str, "(");
                $rpos = strrpos($str, ")");
                $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
            }

            $user = json_decode($str, true);
            if (isset($user['error'])) {
                echo "<h3>error:</h3>" . $user['error'];
                echo "<h3>msg  :</h3>" . $user['error_description'];
                exit;
            }
            if (empty($user['openid']))
                die;
            $data = array(
                'type' => 'qq',
                'open_id' => $user['openid'],
                'token' => $params['access_token']
            );
            $this->thirdlogin($data);
        }
    }

    private function thirdlogin($data) {
        if ($this->_CONFIG['connect']['debug']) { //调试状态下 可以直接就登录 不是调试状态就要走绑定用户名的流程
            $data['type'] = 'test'; //DEBUG状态是直接登录
            $connect = D('Connect')->getConnectByOpenid($data['type'], $data['open_id']);
            if (empty($connect)) {
                $connect = $data;
                $connect['connect_id'] = D('Connect')->add($data);
            } else {
                D('Connect')->save(array('connect_id' => $connect['connect_id'], 'token' => $data['token']));
            }
            if (empty($connect['uid'])) {
                $account = $data['type'] . rand(100000, 999999) . '@qq.com';
                $user = array(
                    'account' => $account,
                    'password' => rand(100000, 999999),
                    'nickname' => $data['type'] . $connect['connect_id'],
                    'ext0' => $account,
                    'create_time' => NOW_TIME,
                    'create_ip' => get_client_ip(),
                );
                $connect['uid'] = D('Passport')->register($user);
                D('Connect')->save(array('connect_id' => $connect['connect_id'], 'uid' => $connect['uid']));
            }

            session('uid', $connect['uid']);
            session('access', $connect['connect_id']);
            header("Location:" . U('member/index'));
            die;
        } else {
            $connect = D('Connect')->getConnectByOpenid($data['type'], $data['open_id']);
            if (empty($connect)) {
                $connect = $data;
                $connect['connect_id'] = D('Connect')->add($data);
            } else {
                D('Connect')->save(array('connect_id' => $connect['connect_id'], 'token' => $data['token']));
            }
            if (empty($connect['uid'])) {
                session('connect', $connect['connect_id']);
                header("Location: " . U('passport/bind'));
            } else {
                session('uid', $connect['uid']);
                session('access', $connect['connect_id']);
                header("Location:" . U('member/index'));
            }
            die;
        }
    }

    public function logout() {

        D('Passport')->logout();
        $this->success('退出登录成功！', U('passport/login'));
    }

}
