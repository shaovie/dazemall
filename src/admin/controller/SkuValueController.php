<?php
/**
 * @Author shaowei
 * @Date   2016-05-23
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\SkuAttrModel;
use \src\mall\model\SkuValueModel;

class SkuValueController extends AdminController
{
    const ONE_PAGE_SIZE = 20;

    public function listPage()
    {
        $page = $this->getParam('page', 1);
        $attrId = intval($this->getParam('attrId', 0));
        $skuAttrinfo = SkuAttrModel::findSkuAttrById($attrId);
        if (empty($skuAttrinfo)) {
            echo "无效的sku属性";
            return ;
        }

        $totalNum = SkuValueModel::fetchSkuValueCount(array('attr_id'), array($attrId), []);
        $skuValueList = SkuValueModel::fetchSomeSkuValue(array('attr_id'), array($attrId), [], $page, self::ONE_PAGE_SIZE);
        foreach ($skuValueList as &$skuValue) {
            $skuValue['state'] =  SkuValueModel::getStateDesc($skuValue['state']);
        }

        $searchParams = array('attrId' => $attrId);
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/SkuValue/listPage',
            $searchParams
        );
        $data = array(
            'attrId' => $attrId,
            'attr' => $skuAttrinfo['attr'],
            'skuValueList' => $skuValueList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("sku_value_list", $data);
    }

    public function info()
    {
        $id = $this->getParam('id', 0);
        $info = array();
        $action = '/admin/SkuValue/add';
        $title = '新建SKU值';
        if ($id > 0) {
            $action = '/admin/SkuValue/edit';
            $info = SkuValueModel::findSkuValueById($id);
            if (!empty($info)) {
                $skuAttrinfo = SkuAttrModel::findSkuAttrById($info['attr_id']);
                if (!empty($skuAttrinfo)) {
                    $title = '编辑[' . $skuAttrinfo['attr'] . ']SKU值';
                } else {
                    echo "无效的sku属性";
                    return ;
                }
            } else {
                echo "无效的sku值";
                return ;
            }
        }
        $data = array(
            'title' => $title,
            'attrId' => $info['attr_id'],
            'valueId' => $info['id'],
            'info' => $info,
            'action' => $action,
        );
        $this->display("sku_value_info", $data);
    }

    public function addPage()
    {
        $attrId = $this->getParam('attrId', 0);
        $skuAttrinfo = SkuAttrModel::findSkuAttrById($attrId);
        if (empty($skuAttrinfo)) {
            echo "无效的sku属性";
            return ;
        }
        $title = '新建[' . $skuAttrinfo['attr'] . ']SKU值';
        $data = array(
            'attrId' => $attrId,
            'valueId' => 0,
            'title' => $title,
            'info' => array(),
            'action' => '/admin/SkuValue/add',
        );
        $this->display("sku_value_info", $data);
    }

    public function add()
    {
        $skuValueInfo = array();
        $error = '';
        if ($this->fetchFormParams($skuValueInfo, $error) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error);
            return ;
        }
        $ret = SkuValueModel::fetchSomeSkuValue(
            array('attr_id', 'value'),
            array($skuValueInfo['attrId'], $skuValueInfo['skuValue']),
            array('and'),
            1, 2);
        if (!empty($ret)) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '属性值已经存在');
            return ;
        }
        $ret = SkuValueModel::newOne(
            $skuValueInfo['attrId'],
            $skuValueInfo['skuValue'],
            $skuValueInfo['state'],
            $this->account
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败(数据可能重复)');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/SkuValue/listPage?attrId=' . $skuValueInfo['attrId']);
    }

    public function edit()
    {
        $skuValueInfo = array();
        $error = '';
        if ($this->fetchFormParams($skuValueInfo, $error) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error);
            return ;
        }
        if ($skuValueInfo['skuValue'] == '默认') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '不能编辑默认属性值');
            return ;
        }
        $updateData = array(
            'value' => $skuValueInfo['skuValue'],
            'state' => $skuValueInfo['state'],
            'mtime' => CURRENT_TIME,
            'm_user' => $this->account,
        );
        $ret = SkuValueModel::update(
            $skuValueInfo['valueId'],
            $updateData
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/SkuValue/listPage?attrId=' . $skuValueInfo['attrId']);
    }

    public function del()
    {
        $id = $this->getParam('id', 0);
        $attrId = $this->getParam('attrId', 0);
        if ($id == 0) {
            header('Location: /admin/SkuValue/listPage?attrId=' . $attrId);
            return ;
        }
        $ret = SkuValueModel::findSkuValueById($id);
        if (empty($ret) || $ret['value'] == '默认') {
            header('Location: /admin/SkuValue/listPage?attrId=' . $attrId);
            return ;
        }
        SkuValueModel::del($id);
        header('Location: /admin/SkuValue/listPage?attrId=' . $attrId);
    }

    public function getSkuValue()
    {
        $attrId = intval($this->getParam('attrId', 0));
        $data = SkuValueModel::fetchAllSkuValue($attrId);
        $this->ajaxReturn(0, '', '', $data);
    }

    private function fetchFormParams(&$skuValueInfo, &$error)
    {
        $skuValueInfo['attrId'] = $this->postParam('attrId', 0);
        $skuValueInfo['valueId'] = $this->postParam('valueId', 0);
        $skuValueInfo['skuValue'] = trim($this->postParam('skuValue', ''));
        $skuValueInfo['state'] = intval($this->postParam('state', 0));

        if (empty($skuValueInfo['skuValue'])) {
            $error = 'sku属性名称不能为空';
            return false;
        }
        if (strlen($skuValueInfo['skuValue']) > 60) {
            $error = 'sku属性名不能超过20个字符';
            return false;
        }
        return true;
    }
}
