<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\ActivityModel;
use \src\mall\model\ActivityGoodsModel;
use \src\mall\model\GoodsModel;

class ActivityController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->listPage();
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = ActivityModel::fetchActivityCount([], [], []);
        $actList = ActivityModel::fetchSomeActivity([], [], [], $page, self::ONE_PAGE_SIZE);
        if (!empty($actList)) {
            foreach ($actList as &$act) {
                $act['showArea'] = ActivityModel::showAreaDesc($act['show_area']);
            }
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Activity/listPage',
            $searchParams
        );

        $data = array(
            'actList' => $actList,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("activity_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增',
            'act' => array(),
            'action' => '/admin/Activity/add',
        );
        $this->display('activity_info', $data);
    }
    public function add()
    {
        $error = '';
        $actInfo = array();
        $ret = $this->fetchFormParams($actInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $actId = ActivityModel::newOne(
            $actInfo['title'],
            $actInfo['show_area'],
            $actInfo['image_url'],
            $actInfo['image_urls'],
            empty($actInfo['begin_time']) ? 0 : strtotime($actInfo['begin_time']),
            empty($actInfo['end_time']) ? 0 : strtotime($actInfo['end_time']),
            $actInfo['sort'],
            $actInfo['wx_share_title'],
            $actInfo['wx_share_desc'],
            $actInfo['wx_share_img']
        );
        if ($actId === false || (int)$actId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Activity/listPage');
    }

    public function editPage()
    {
        $actId = intval($this->getParam('actId', 0));

        $actInfo = ActivityModel::findActivityById($actId);
        if (!empty($actInfo)) {
            $actInfo['image_urls'] = explode("|", $actInfo['image_urls']);
        }
        $data = array(
            'title' => '编辑',
            'act' => $actInfo,
            'action' => '/admin/Activity/edit',
        );
        $this->display('activity_info', $data);
    }

    public function edit()
    {
        $error = '';
        $actInfo = array();
        $ret = $this->fetchFormParams($actInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $updateData = array();
        $updateData['title'] = $actInfo['title'];
        $updateData['show_area'] = $actInfo['show_area'];
        $updateData['sort'] = $actInfo['sort'];
        $updateData['begin_time'] = empty($actInfo['begin_time']) ? 0 : strtotime($actInfo['begin_time']);
        $updateData['end_time'] = empty($actInfo['end_time']) ? 0 : strtotime($actInfo['end_time']);
        $updateData['image_url'] = $actInfo['image_url'];
        $updateData['image_urls'] = $actInfo['image_urls'];
        $updateData['wx_share_title'] = $actInfo['wx_share_title'];
        $updateData['wx_share_desc'] = $actInfo['wx_share_desc'];
        $updateData['wx_share_img'] = $actInfo['wx_share_img'];
        $ret = ActivityModel::update($actInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Activity/listPage');
    }
    public function del()
    {
        $actId = $this->getParam('actId', 0);
        if ($actId == 0) {
            header('Location: /admin/Activity');
            return ;
        }
        ActivityModel::del($actId);
        header('Location: /admin/Activity');
    }
    public function goodsList()
    {
        $actId = intval($this->getParam('actId', 0));
        $actInfo = ActivityModel::findActivityById($actId);
        if (empty($actInfo)) {
            echo "没有此模块";
            return ;
        }
        $goodsList = ActivityGoodsModel::getAllGoods($actId);
        foreach ($goodsList as &$goods) {
            $goods['name'] = GoodsModel::goodsName($goods['goods_id']);
        }
        $data = array(
            'actId' => $actId,
            'title' => $actInfo['title'],
            'goodsList' => $goodsList,
        );
        $this->display('act_goods_list', $data);
    }
    public function addGoodsPage()
    {
        $actId = intval($this->getParam('actId', 0));
        $data = array(
            'title' => '添加商品',
            'actId' => $actId,
            'goods' => array(),
            'action' => '/admin/Activity/addGoods',
        );
        $this->display('act_goods_info', $data);
    }
    public function addGoods()
    {
        $actId = intval($this->postParam('actId', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $sort = intval($this->postParam('sort', 0));
        $actInfo = ActivityModel::findActivityById($actId);
        if (empty($actInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '模块ID无效');
            return ;
        }
        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'r');
        if (empty($goodsInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品ID无效');
            return ;
        }
        $ret = ActivityGoodsModel::newOne($actId, $goodsId, $sort);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '添加商品失败');
            return ;
        }
        $this->ajaxReturn(0, '添加商品成功', '/admin/Activity/goodsList?actId=' . $actId);
    }
    public function editGoodsPage()
    {
        $actId = intval($this->getParam('actId', 0));
        $goodsId = intval($this->getParam('goodsId', 0));

        $goodsInfo = ActivityGoodsModel::getGoodsInfo($actId, $goodsId);
        $data = array(
            'title' => '编辑',
            'actId' => $actId,
            'goods' => $goodsInfo,
            'action' => '/admin/Activity/editGoods',
        );
        $this->display('act_goods_info', $data);
    }
    public function editGoods()
    {
        $actId = intval($this->postParam('actId', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $sort = intval($this->postParam('sort', 0));
        $actInfo = ActivityModel::findActivityById($actId);
        if (empty($actInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '活动ID无效');
            return ;
        }
        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'r');
        if (empty($goodsInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品ID无效');
            return ;
        }
        $data = array('sort' => $sort);
        $ret = ActivityGoodsModel::update($actId, $goodsId, $data);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $this->ajaxReturn(0, '保存商品成功', '/admin/Activity/goodsList?actId=' . $actId);
    }
    public function delGoods()
    {
        $actId = intval($this->getParam('actId', 0));
        $goodsId = intval($this->getParam('goodsId', 0));
        if ($actId == 0 || $goodsId == 0) {
            header('Location: /admin/Activity');
            return ;
        }
        ActivityGoodsModel::del($actId, $goodsId);
        header('Location: /admin/Activity/goodsList?actId=' . $actId);
    }
    private function fetchFormParams(&$actInfo, &$error)
    {
        $actInfo['id'] = intval($this->postParam('actId', 0));
        $actInfo['show_area'] = intval($this->postParam('showArea', 0));
        $actInfo['title'] = trim($this->postParam('title', ''));
        $actInfo['sort'] = intval($this->postParam('sort', 0));
        $actInfo['image_url'] = trim($this->postParam('imageUrl', ''));
        $actInfo['image_urls'] = trim($this->postParam('imageUrls', ''));
        $actInfo['begin_time'] = trim($this->postParam('beginTime', ''));
        $actInfo['end_time'] = trim($this->postParam('endTime', ''));
        
        $actInfo['wx_share_title'] = trim($this->postParam('wx_share_title', ''));
        $actInfo['wx_share_desc'] = trim($this->postParam('wx_share_desc', ''));
        $actInfo['wx_share_img'] = trim($this->postParam('wx_share_img', ''));

        if (strlen($actInfo['title']) > 120) {
            $error = '备注不能超过40个字符';
            return false;
        }
        if (!empty($actInfo['begin_time'])) {
            if (strtotime($actInfo['begin_time']) === false) {
                $error = '开始时间格式错误';
                return false;
            }
        }
        if ($actInfo['show_area'] == -1) {
            $error = '展示区域无效';
            return false;
        }
        if (!empty($actInfo['end_time'])) {
            if (strtotime($actInfo['end_time']) === false) {
                $error = '结束时间格式错误';
                return false;
            }
        }
        $actInfo['image_urls'] = trim($actInfo['image_urls'], '|');
        $gs = explode('|', $actInfo['image_urls']);
        if (count($gs) > 9) {
            $error = '轮播图不能超过9张';
            return false;
        }
        if (strlen($actInfo['wx_share_title']) > 80
            || strlen($actInfo['wx_share_desc']) > 80) {
            $error = '微信分享标题或描述不能超过80个字符';
            return false;
        }
        return true;
    }

}
