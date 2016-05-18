<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\GoodsModuleModel;
use \src\mall\model\GoodsModuleGListModel;
use \src\mall\model\GoodsModel;

class GoodsModuleController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->display("goods_module_list");
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = GoodsModuleModel::fetchGoodsModuleCount([], [], []);
        $moduleList = GoodsModuleModel::fetchSomeGoodsModule2([], [], [], $page, self::ONE_PAGE_SIZE);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/GoodsModule/listPage',
            $searchParams
        );

        $data = array(
            'moduleList' => $moduleList,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_module_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增',
            'module' => array(),
            'action' => '/admin/GoodsModule/add',
        );
        $this->display('goods_module_info', $data);
    }
    public function add()
    {
        $error = '';
        $moduleInfo = array();
        $ret = $this->fetchFormParams($moduleInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $moduleId = GoodsModuleModel::newOne(
            $moduleInfo['title'],
            $moduleInfo['image_url'],
            empty($moduleInfo['begin_time']) ? 0 : strtotime($moduleInfo['begin_time']),
            empty($moduleInfo['end_time']) ? 0 : strtotime($moduleInfo['end_time']),
            $moduleInfo['sort']
        );
        if ($moduleId === false || (int)$moduleId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/GoodsModule/listPage');
    }
    public function editPage()
    {
        $moduleId = intval($this->getParam('moduleId', 0));

        $moduleInfo = GoodsModuleModel::findGoodsModuleById($moduleId);
        $data = array(
            'title' => '编辑',
            'module' => $moduleInfo,
            'action' => '/admin/GoodsModule/edit',
        );
        $this->display('goods_module_info', $data);
    }
    public function edit()
    {
        $error = '';
        $moduleInfo = array();
        $ret = $this->fetchFormParams($moduleInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $updateData = array();
        $updateData['title'] = $moduleInfo['title'];
        $updateData['sort'] = $moduleInfo['sort'];
        $updateData['begin_time'] = empty($moduleInfo['begin_time']) ? 0 : strtotime($moduleInfo['begin_time']);
        $updateData['end_time'] = empty($moduleInfo['end_time']) ? 0 : strtotime($moduleInfo['end_time']);
        $updateData['image_url'] = $moduleInfo['image_url'];
        $ret = GoodsModuleModel::update($moduleInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/GoodsModule/listPage');
    }
    public function del()
    {
        $moduleId = $this->getParam('moduleId', 0);
        if ($moduleId == 0) {
            header('Location: /admin/GoodsModule');
            return ;
        }
        GoodsModuleModel::delModule($moduleId);
        header('Location: /admin/GoodsModule');
    }
    public function goodsList()
    {
        $moduleId = intval($this->getParam('moduleId', 0));
        $moduleInfo = GoodsModuleModel::findGoodsModuleById($moduleId);
        if (empty($moduleInfo)) {
            echo "没有此模块";
            return ;
        }
        $goodsList = GoodsModuleGListModel::getAllGoods($moduleId);
        foreach ($goodsList as &$goods) {
            $goods['name'] = GoodsModel::goodsName($goods['goods_id']);
        }
        $data = array(
            'moduleId' => $moduleId,
            'title' => $moduleInfo['title'],
            'goodsList' => $goodsList,
        );
        $this->display('goods_module_glist', $data);
    }
    public function addGoodsPage()
    {
        $moduleId = intval($this->getParam('moduleId', 0));
        $data = array(
            'title' => '添加商品',
            'moduleId' => $moduleId,
            'goods' => array(),
            'action' => '/admin/GoodsModule/addGoods',
        );
        $this->display('goods_module_ginfo', $data);
    }
    public function addGoods()
    {
        $moduleId = intval($this->postParam('moduleId', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $sort = intval($this->postParam('sort', 0));
        $moduleInfo = GoodsModuleModel::findGoodsModuleById($moduleId);
        if (empty($moduleInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '模块ID无效');
            return ;
        }
        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'r');
        if (empty($goodsInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品ID无效');
            return ;
        }
        $ret = GoodsModuleGListModel::newOne($moduleId, $goodsId, $sort);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '添加商品失败');
            return ;
        }
        $this->ajaxReturn(0, '添加商品成功', '/admin/GoodsModule/goodsList?moduleId=' . $moduleId);
    }
    public function editGoodsPage()
    {
        $moduleId = intval($this->getParam('moduleId', 0));
        $goodsId = intval($this->getParam('goodsId', 0));

        $goodsInfo = GoodsModuleGListModel::getGoodsInfo($moduleId, $goodsId);
        $data = array(
            'title' => '编辑',
            'moduleId' => $moduleId,
            'goods' => $goodsInfo,
            'action' => '/admin/GoodsModule/editGoods',
        );
        $this->display('goods_module_ginfo', $data);
    }
    public function editGoods()
    {
        $moduleId = intval($this->postParam('moduleId', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $sort = intval($this->postParam('sort', 0));
        $moduleInfo = GoodsModuleModel::findGoodsModuleById($moduleId);
        if (empty($moduleInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '模块ID无效');
            return ;
        }
        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'r');
        if (empty($goodsInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品ID无效');
            return ;
        }
        $data = array('sort' => $sort);
        $ret = GoodsModuleGListModel::update($moduleId, $goodsId, $data);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $this->ajaxReturn(0, '保存商品成功', '/admin/GoodsModule/goodsList?moduleId=' . $moduleId);
    }
    public function delGoods()
    {
        $moduleId = intval($this->getParam('moduleId', 0));
        $goodsId = intval($this->getParam('goodsId', 0));
        if ($moduleId == 0 || $goodsId == 0) {
            header('Location: /admin/GoodsModule');
            return ;
        }
        GoodsModuleGListModel::del($moduleId, $goodsId);
        header('Location: /admin/GoodsModule/goodsList?moduleId=' . $moduleId);
    }
    private function fetchFormParams(&$moduleInfo, &$error)
    {
        $moduleInfo['id'] = intval($this->postParam('moduleId', 0));
        $moduleInfo['title'] = trim($this->postParam('title', ''));
        $moduleInfo['sort'] = intval($this->postParam('sort', 0));
        $moduleInfo['image_url'] = trim($this->postParam('imageUrl', ''));
        $moduleInfo['begin_time'] = trim($this->postParam('beginTime', ''));
        $moduleInfo['end_time'] = trim($this->postParam('endTime', ''));

        if (strlen($moduleInfo['title']) > 120) {
            $error = '备注不能超过40个字符';
            return false;
        }
        if (!empty($moduleInfo['begin_time'])) {
            if (strtotime($moduleInfo['begin_time']) === false) {
                $error = '开始时间格式错误';
                return false;
            }
        }
        if (!empty($moduleInfo['end_time'])) {
            if (strtotime($moduleInfo['end_time']) === false) {
                $error = '结束时间格式错误';
                return false;
            }
        }
        return true;
    }

}
