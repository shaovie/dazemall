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
        $goodsList = array();
        $data = array(
            'title' => empty($actInfo) ? '' : $actInfo['title'],
            'act' => $actInfo,
            'goodsList' => $goodsList,
            'ajaxUrl' => '/api/Activity/getListData?actId=' . $actId,
        );
        $this->display('activity', $data);
    }
}

