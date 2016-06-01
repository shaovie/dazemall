<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

use \src\mall\model\ActivityModel;
use \src\mall\model\ActivityGoodsModel;

class ActivityController extends MallController
{
    public function index()
    {
        $actId = $this->getParam('actId', 0);
        $actInfo = ActivityModel::findActivityById($actId);
        if (!empty($actInfo)) {
            $actInfo['image_urls'] = explode("|", $actInfo['image_urls']);
        }
        $goodsList = ActivityGoodsModel::fillGoodsList($actId);
        $data = array(
            'title' => empty($actInfo) ? '' : $actInfo['title'],
            'act' => $actInfo,
            'goodsList' => $goodsList,
            'ajaxUrl' => '/mall/Activity/getListData?actId=' . $actId,
        );
        $this->display('act_goods_list', $data);
    }

    public function getListData()
    {
        $this->ajaxReturn(0, '', '', array('goodsList' => array()));
    }
}

