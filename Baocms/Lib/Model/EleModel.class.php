<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class EleModel extends CommonModel {

    protected $pk = 'shop_id';
    protected $tableName = 'ele';

    public function updateMonth($shop_id) {
        $shop_id = (int) $shop_id;
        $month = date('Ym', NOW_TIME);
        $num = (int) (D('Eleorder')->where(array('shop_id' => $shop_id, 'month' => $month))->count());
        return $this->execute("update " . $this->getTableName() . " set  month_num={$num} where shop_id={$shop_id}");
    }

    public function getEleCate() {
        return array(
            '1' => '快餐简餐',
            '2' => '正餐',
            '3' => '馋嘴小吃',
            '4' => '甜点饮料',
            '5' => '生活超市',
            '6' => '水果蔬菜',
        );
    }
    
    
    public function CallDataForMat($items) { //专门针对CALLDATA 标签处理的
        if (empty($items))
            return array();
        $obj = D('Shop');
        $shop_ids = array();
        foreach ($items as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $shops = $obj->itemsByIds($shop_ids);
        foreach ($items as $k => $val) {
            $val['shop'] = $shops[$val['shop_id']];
            $items[$k] = $val;
        }
        return $items;
    }

}
