<?php
/**
 * @Author shaowei
 * @Date   2016-05-23
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\SkuAttrModel;

class SkuAttrController extends AdminController
{
    const ONE_PAGE_SIZE = 20;

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = SkuAttrModel::fetchSkuAttrCount([], [], []);
        $skuAttrList = SkuAttrModel::fetchSomeSkuAttr([], [], [], $page, self::ONE_PAGE_SIZE);
        foreach ($skuAttrList as &$skuAttr) {
            $skuAttr['state'] =  SkuAttrModel::getStateDesc($skuAttr['state']);
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/SkuAttr/listPage',
            $searchParams
        );
        $data = array(
            'skuAttrList' => $skuAttrList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("sku_attr_list", $data);
    }

    public function info()
    {
        $attrId = $this->getParam('attrId', 0);
        $info = array();
        $action = '/admin/SkuAttr/add';
        $title = '新建SKU属性';
        if ($attrId > 0) {
            $title = '编辑SKU属性';
            $action = '/admin/SkuAttr/edit';
            $info = SkuAttrModel::findSkuAttrById($attrId);
        }
        $data = array(
            'title' => $title,
            'attrId' => $attrId,
            'info' => $info,
            'action' => $action,
        );
        $this->display("sku_attr_info", $data);
    }

    public function addPage()
    {
        $title = '新建SKU属性';
        $data = array(
            'attrId' => 0,
            'title' => $title,
            'info' => array(),
            'action' => '/admin/SkuAttr/add',
        );
        $this->display("sku_attr_info", $data);
    }

    public function add()
    {
        $skuAttrInfo = array();
        $error = '';
        if ($this->fetchFormParams($skuAttrInfo, $error) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error);
            return ;
        }
        if ($skuAttrInfo['skuAttr'] == '默认') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '不能编辑默认属性');
            return ;
        }
        $ret = SkuAttrModel::newOne(
            $skuAttrInfo['skuAttr'],
            $skuAttrInfo['state'],
            $this->account
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败(数据可能重复)');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/SkuAttr/listPage');
    }

    public function edit()
    {
        $skuAttrInfo = array();
        $error = '';
        if ($this->fetchFormParams($skuAttrInfo, $error) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error);
            return ;
        }
        $updateData = array(
            'attr' => $skuAttrInfo['skuAttr'],
            'state' => $skuAttrInfo['state'],
            'mtime' => CURRENT_TIME,
            'm_user' => $this->account,
        );
        $ret = SkuAttrModel::update(
            $skuAttrInfo['attrId'],
            $updateData
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/SkuAttr/listPage');
    }

    private function fetchFormParams(&$skuAttrInfo, &$error)
    {
        $skuAttrInfo['attrId'] = $this->postParam('attrId', 0);
        $skuAttrInfo['skuAttr'] = trim($this->postParam('skuAttr', ''));
        $skuAttrInfo['state'] = intval($this->postParam('state', 0));

        if (empty($skuAttrInfo['skuAttr'])) {
            $error = 'sku属性名称不能为空';
            return false;
        }
        if (strlen($skuAttrInfo['skuAttr']) > 60) {
            $error = 'sku属性名不能超过20个字符';
            return false;
        }
        return true;
    }
}
