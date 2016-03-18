<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.taobao.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class SeoAction extends CommonAction {

    public function index() {
        $shop_id = (int) $this->shop_id;
        if ($this->isPost()) {

            $data = $this->_post('data',true);
            
            $update['seo_title'] = htmlspecialchars($data['seo_title']);
            
            $update['seo_keywords'] = htmlspecialchars($data['seo_keywords']);
            
            $update['seo_description'] = htmlspecialchars($data['seo_description']);
            $update['icp'] = htmlspecialchars($data['icp']);
            $update['sitelogo'] = htmlspecialchars($data['sitelogo']);
            if (!isImage($update['sitelogo'])) {
                $this->baoError('网站logo格式不正确');
            }
            if (false !== D('Shopdetails')->upDetails($this->shop_id, $update)) {
                $this->baoSuccess('操作成功', U('seo/index'));
            }
        } else {
            $detail = D('Shopdetails')->find($shop_id);
            $this->assign('detail', $detail);
            $this->display();
        }
    }

}
