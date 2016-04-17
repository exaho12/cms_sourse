
<?php

class CloudAction extends CommonAction
{

    protected $types = array( );

    public function _initialize( )
    {
        parent::_initialize( );
        $this->types = d( "Cloudgoods" )->getType( );
        $this->assign( "types", $this->types );
    }

    public function index( )
    {
        $goods = d( "Cloudgoods" );
        import( "ORG.Util.Page" );
        $map = array( "audit" => 1, "closed" => 0 );
        $type = ( integer )$this->_param( "type" );
        if ( !empty( $type ) )
        {
            $map['type'] = $type;
            $this->assign( "type", $type );
        }
        if ( $area_id = ( integer )$this->_param( "area_id" ) )
        {
            $map['area_id'] = $area_id;
            $this->assign( "area_id", $area_id );
        }
        if ( $keyword = $this->_param( "keyword", "htmlspecialchars" ) )
        {
            $map['title|intro'] = array(
                "LIKE",
                "%".$keyword."%"
            );
            $this->assign( "keyword", $keyword );
        }
        $count = $goods->where( $map )->count( );
        
        $Page = new Page ( $count, 25 );
        $show = $Page->show( );
        $list = $goods->where( $map )->order( array( "goods_id" => "desc" ) )->limit( $Page->firstRow.",".$Page->listRows )->select( );
        $this->assign( "list", $list );
        $this->assign( "page", $show );
        $this->display( );
    }

    public function cloudbuy( )
    {
        if ( empty( $this->uid ) )
        {
            $this->ajaxReturn( array( "status" => "login" ) );
        }
        $goods_id = ( integer )$_POST['goods_id'];
        $detail = d( "Cloudgoods" )->find( $goods_id );
        if ( empty( $detail ) )
        {
            $this->ajaxReturn( array( "status" => "error", "msg" => "该云购商品不存在" ) );
        }
        $obj = d( "Cloudgoods" );
        $logs = d( "Cloudlogs" );
        if ( IS_AJAX )
        {
            $num = ( integer )$_POST['num'];
            if ( empty( $num ) )
            {
                $this->ajaxReturn( array( "status" => "error", "msg" => "数量不能为空" ) );
            }
            if ( $num < $this->types[$detail['type']]['num'] || $num % $this->types[$detail['type']]['num'] != 0 )
            {
                $this->ajaxReturn( array( "status" => "error", "msg" => "数量不正确" ) );
            }
            $count = $logs->where( array(
                "goods_id" => $goods_id,
                "user_id" => $this->uid
            ) )->sum( "num" );
            $left = $detail['max'] - $count;
            $lefts = $detail['price'] - $detail['join'];
            $left <= $lefts ? ( $limit = $left ) : ( $limit = $lefts );
            if ( $limit < $num )
            {
                $this->ajaxReturn( array(
                    "status" => "error",
                    "msg" => "您最多能购买".$limit."人次"
                ) );
            }
            if ( $this->member['money'] < $num * 100 )
            {
                $this->ajaxReturn( array(
                    "status" => "error",
                    "msg" => "抱歉，您的余额不足",
                    "url" => u( "member/money/money" )
                ) );
            }
            if ( FALSE !== $obj->cloud( $goods_id, $this->uid, $num ) )
            {
                $details = d( "Cloudgoods" )->find( $goods_id );
                if ( $details['price'] <= $details['join'] )
                {
                    $obj->lottery( $goods_id );
                }
                $this->ajaxReturn( array( "status" => "success", "msg" => "云购成功，请等待结果或者继续加注" ) );
            }
            else
            {
                $this->ajaxReturn( array( "status" => "error", "msg" => "云购失败" ) );
            }
        }
    }

    public function detail( $goods_id = 0 )
    {
        if ( $goods_id = ( integer )$goods_id )
        {
            $obj = d( "Cloudgoods" );
            if ( !( $detail = $obj->find( $goods_id ) ) )
            {
                $this->error( "没有该商品" );
            }
            $thumb = unserialize( $detail['thumb'] );
            $count = d( "Cloudlogs" )->where( array(
                "goods_id" => $goods_id,
                "user_id" => $this->uid
            ) )->sum( "num" );
            $left = $detail['max'] - $count;
            $cloudlogs = d( "Cloudlogs" );
            $map = array(
                "goods_id" => $goods_id
            );
            $list = $cloudlogs->where( $map )->order( array( "log_id" => "asc" ) )->select( );
            $lists = $obj->get_datas( $list );
            $listss = $user_ids = array( );
            foreach ( $lists as $k => $val )
            {
                $user_ids[$val['user_id']] = $val['user_id'];
                $listss[date( "Y-m-d", $val['create_time'] )][date( "H:i:s", $val['create_time'] ).".".$val['microtime']][] = $val;
            }
            krsort( &$listss );
            foreach ( $listss as $k => $val )
            {
                krsort( &$listss[$k] );
            }
            $this->assign( "users", d( "Users" )->itemsByIds( $user_ids ) );
            $this->assign( "list", $listss );
            $this->assign( "left", $left );
            $this->assign( "thumb", $thumb );
            $this->assign( "detail", $detail );
            if ( $detail['status'] == 1 )
            {
                redirect( u( "cloud/zhong", array(
                    "goods_id" => $goods_id
                ) ) );
            }
            else
            {
                $this->display( );
            }
        }
        else
        {
            $this->error( "没有该商品" );
        }
    }

    public function zhong( $goods_id )
    {
        if ( $goods_id = ( integer )$goods_id )
        {
            $obj = d( "Cloudgoods" );
            if ( !( $detail = $obj->find( $goods_id ) ) )
            {
                $this->error( "没有该商品" );
            }
            if ( $detail['status'] != 1 || empty( $detail['win_number'] ) || empty( $detail['win_user_id'] ) )
            {
                $this->error( "该商品还未开奖" );
            }
            $cloudlogs = d( "Cloudlogs" );
            $map = array(
                "goods_id" => $goods_id
            );
            $list = $cloudlogs->where( $map )->order( array( "log_id" => "asc" ) )->select( );
            $lists = $obj->get_datas( $list );
            $listss = $user_ids = array( );
            foreach ( $lists as $k => $val )
            {
                $user_ids[$val['user_id']] = $val['user_id'];
                $listss[date( "Y-m-d", $val['create_time'] )][date( "H:i:s", $val['create_time'] ).".".$val['microtime']][] = $val;
            }
            krsort( &$listss );
            foreach ( $listss as $k => $val )
            {
                krsort( &$listss[$k] );
            }
            $this->assign( "users", d( "Users" )->itemsByIds( $user_ids ) );
            $this->assign( "list", $listss );
            $win_list = $cloudlogs->where( $map )->order( array( "log_id" => "asc" ) )->select( );
            $win_lists = $obj->get_datas( $win_list );
            $win_listss = array( );
            foreach ( $win_lists as $k => $val )
            {
                if ( $val['user_id'] == $detail['win_user_id'] )
                {
                    $win_listss[date( "Y-m-d H:i:s", $val['create_time'] ).".".$val['microtime']][] = $val;
                }
            }
            $this->assign( "lists", $win_listss );
            $total = $cloudlogs->where( array(
                "goods_id" => $goods_id,
                "user_id" => $detail['win_user_id']
            ) )->sum( "num" );
            $return = $obj->get_last50_time( $list );
            $this->assign( "return", $return );
            $this->assign( "total", $total );
            $u_list = array( );
            foreach ( $win_lists as $k => $val )
            {
                if ( $val['user_id'] == $detail['win_user_id'] )
                {
                    $u_list[] = $val;
                }
            }
            $this->assign( "u_list", $u_list );
            $this->assign( "detail", $detail );
            $this->display( );
        }
        else
        {
            $this->error( "没有该商品" );
        }
    }

}

?>
