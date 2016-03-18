
<?php
//傻逼破解的

class ExpressAction extends CommonAction{
	
	protected function _initialize() {
        parent::_initialize();
		$express = (int)$this->_CONFIG['operation']['express'];
		if ($express == 0) {
				$this->error('此功能已关闭');
				die;
			}
     }

    public function index(){
        $status = ( integer )$this->_param( "status" );
        $this->assign( "status", $status );
        $this->display();
    }

    public function load(){
        $express = D('Express');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid, 'closed' => 0);

        $status = (int) $this->_param('status');

        if ($status == 2) {
            $map['status'] = 2;
        } elseif ($status == 1) {
            $map['status'] = array(0, 1, -1);
        } else {
            $status == null;
        }
        $count = $express->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $express->where($map)->order('express_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function detail( $express_id ){
        $express_id = ( integer )$express_id;
        if ( empty( $express_id ) || !( $detail = d( "Express" )->find( $express_id ) ) ){
            $this->error( "该快递不存在" );
        }
        if ( $detail['user_id'] != $this->uid ){
            $this->error( "请不要操作他人的快递" );
        }
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function create( ){
        if ( $this->isPost( ) ){
            $data = $this->createCheck( );
            if ( $express_id = d( "Express" )->add( $data ) ){
                $this->niuMsg( "发布成功", u( "express/index" ) );
            }
            $this->niuMsg( "发布失败" );
        }
        else{
			$this->assign('useraddr', D('Useraddr')->where(array('user_id' => $this->uid,'is_default' => 1))->limit(0,1)->select());
            $this->display( );
        }
    }

    public function createCheck( ){
        $data = $this->_post( "data", FALSE );
        $data['title'] = htmlspecialchars( $data['title'] );
        if ( empty( $data['title'] ) ){
            $this->niuMsg( "标题不能为空" );
        }
        $data['from_name'] = htmlspecialchars( $data['from_name'] );
        if ( empty( $data['from_name'] ) ){
            $this->niuMsg( "寄件人姓名不能为空" );
        }
        $data['from_addr'] = htmlspecialchars( $data['from_addr'] );
        if ( empty( $data['from_addr'] ) ){
            $this->niuMsg( "寄件人地址不能为空" );
        }
        $data['from_mobile'] = htmlspecialchars( $data['from_mobile'] );
        if ( empty( $data['from_mobile'] ) ){
            $this->niuMsg( "寄件人手机不能为空" );
        }
        if ( !ismobile( $data['from_mobile'] ) ){
            $this->niuMsg( "寄件人手机格式不正确" );
        }
        $data['to_name'] = htmlspecialchars( $data['to_name'] );
        if ( empty( $data['to_name'] ) ){
            $this->niuMsg( "收件人姓名不能为空" );
        }
        $data['to_addr'] = htmlspecialchars( $data['to_addr'] );
        if ( empty( $data['to_addr'] ) ){
            $this->niuMsg( "收件人地址不能为空" );
        }
        $data['to_mobile'] = htmlspecialchars( $data['to_mobile'] );
        if ( empty( $data['to_mobile'] ) ){
            $this->niuMsg( "收件人手机不能为空" );
        }
        if ( !ismobile( $data['to_mobile'] ) ){
            $this->niuMsg( "收件人手机格式不正确" );
        }
        $data['city_id'] = $this->city_id;
        $data['area_id'] = $data['area_id'];
        $data['business_id'] = $data['business_id'];
        $data['user_id'] = $this->uid;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip( );
        return $data;
    }

    public function edit( $express_id ){
        if ( $express_id = ( integer )$express_id ){
            $obj = d( "Express" );
            if ( !( $detail = $obj->find( $express_id ) ) ){
                $this->error( "请选择要编辑的快递" );
            }
            if ( $detail['status'] != 0 ){
                $this->error( "该快递状态不允许被编辑" );
            }
            if ( $detail['closed'] == 1 ){
                $this->error( "该快递已被删除" );
            }
            if ( $this->isPost( ) ){
                $data = $this->editCheck( );
                $data['express_id'] = $express_id;
                if ( FALSE !== $obj->save( $data ) ){
                    $this->niuMsg( "操作成功", u( "express/index" ) );
                }
                $this->niuMsg( "操作失败" );
            }
            else{
                $this->assign( "detail", $detail );
                $this->display( );
            }
        }
        else{
            $this->error( "请选择要编辑的快递信息" );
        }
    }

    public function editCheck( ){
        $data = $this->_post( "data", FALSE );
        $data['title'] = htmlspecialchars( $data['title'] );
        if ( empty( $data['title'] ) ){
            $this->niuMsg( "标题不能为空" );
        }
        $data['from_name'] = htmlspecialchars( $data['from_name'] );
        if ( empty( $data['from_name'] ) ){
            $this->niuMsg( "寄件人姓名不能为空" );
        }
        $data['from_addr'] = htmlspecialchars( $data['from_addr'] );
        if ( empty( $data['from_addr'] ) ){
            $this->niuMsg( "寄件人地址不能为空" );
        }
        $data['from_mobile'] = htmlspecialchars( $data['from_mobile'] );
        if ( empty( $data['from_mobile'] ) ){
            $this->niuMsg( "寄件人手机不能为空" );
        }
        if ( !ismobile( $data['from_mobile'] ) ){
            $this->niuMsg( "寄件人手机格式不正确" );
        }
        $data['to_name'] = htmlspecialchars( $data['to_name'] );
        if ( empty( $data['to_name'] ) ){
            $this->niuMsg( "收件人姓名不能为空" );
        }
        $data['to_addr'] = htmlspecialchars( $data['to_addr'] );
        if ( empty( $data['to_addr'] ) ){
            $this->niuMsg( "收件人地址不能为空" );
        }
        $data['to_mobile'] = htmlspecialchars( $data['to_mobile'] );
        if ( empty( $data['to_mobile'] ) ){
            $this->niuMsg( "收件人手机不能为空" );
        }
        if ( !ismobile( $data['to_mobile'] ) ){
            $this->niuMsg( "收件人手机格式不正确" );
        }
        $data['city_id'] = $this->city_id;
        $data['area_id'] = $data['area_id'];
        $data['business_id'] = $data['business_id'];
        return $data;
    }

    public function delete( $express_id ){
        if ( is_numeric( $express_id ) && ( $express_id = ( integer )$express_id ) ){
            $obj = d( "Express" );
            if ( !( $detail = $obj->find( $express_id ) ) ){
                $this->error( "快递不存在" );
            }
            if ( $detail['closed'] == 1 || $detail['status'] != 0 && $detail['status'] != 2 ){
                $this->error( "该快递状态不允许被删除" );
            }
            $obj->save( array(
                "express_id" => $express_id,
                "closed" => 1
            ) );
            $this->success( "删除成功！", u( "express/index" ) );
        }
    }

}


