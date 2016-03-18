<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HuodongModel extends CommonModel {

    protected $pk = 'huodong_id';
    protected $tableName = 'huodong';

    public function getHuoCate() {
        return array(
            '1' => '约吃饭',
            '2' => '约看电影',
            '3' => '约K歌',
            '4' => '约游玩',
        );
    }

    public function getPeopleCate() {
        return array(
            '1' => '女生',
            '2' => '男生',
            '3' => '不限',
        );
    }
    
     public function getTraffic() {
        return array(
            '1' => '公共交通',
            '2' => '自行车',
            '3' => '电动车',
            '4' => '汽车',
            '5' => '大巴',
            '6' => '火车',
            '7' => '飞机',
            '8' => '轮船',
            '9'=> '不限',
        );
    }
    

}
