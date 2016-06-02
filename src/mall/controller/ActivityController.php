<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

use \src\common\WxSDK;
use \src\mall\model\ActivityModel;
use \src\mall\model\ActivityGoodsModel;

class ActivityController extends MallController
{
    public function index()
    {
        $actId = $this->getParam('actId', 0);
        $actInfo = ActivityModel::findActivityById($actId);
        if (empty($actInfo)
            || ($actInfo['begin_time'] > CURRENT_TIME
                || $actInfo['end_time'] < CURRENT_TIME)) {
            $this->showNotice('抱歉，活动已结束~~');
            return;
        }
        $actInfo['image_urls'] = explode('|', $actInfo['image_urls']);
        $goodsList = ActivityGoodsModel::fillGoodsList($actId);
        $title = empty($actInfo) ? '' : $actInfo['title'];
        $data = array(
            'title' => $title,
            'act' => $actInfo,
            'goodsList' => $goodsList,
            'ajaxUrl' => '/mall/Activity/getListData?actId=' . $actId,
        );

        $data['signPackage'] = WxSDK::getSignPackage();
        $shareCfg['title'] = $actInfo['wx_share_title'];
        $shareCfg['desc'] = $actInfo['wx_share_desc'];
        $shareCfg['img'] = $actInfo['wx_share_img'];
        $shareCfg['url'] = APP_URL_BASE . '/mall/Activity/index?actId=' . $actId;
        $data['shareCfg'] = $shareCfg;

        $this->display('act_goods_list', $data);
    }

    public function getListData()
    {
        $this->ajaxReturn(0, '', '', array('goodsList' => array()));
    }
}

