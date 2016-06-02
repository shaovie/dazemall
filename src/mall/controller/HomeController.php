<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

use \src\common\WxSDK;
use \src\mall\model\BannerModel;
use \src\mall\model\GoodsModuleModel;
use \src\mall\model\ActivityModel;
use \src\mall\model\GoodsModuleGListModel;
use \src\mall\model\GlobalConfigModel;

class HomeController extends MallController
{
    public function index()
    {
        $bannerList = BannerModel::fillShowBannerList(BannerModel::SHOW_AREA_HOME_TOP);
        $goodsModuleList = GoodsModuleModel::fetchAllValidModule(CURRENT_TIME);
        $moduleGoodsList = GoodsModuleGListModel::fillGoodsList($goodsModuleList);
        $actList = ActivityModel::findAllValidActivity(CURRENT_TIME, ActivityModel::SHOW_AREA_HOME);
        $data = array(
            'searchKey' => GlobalConfigModel::searchKey(),
            'bannerList' => $bannerList,
            'goodsModuleList' => $moduleGoodsList,
            'actList' => $actList,
        );

        $data['signPackage'] = WxSDK::getSignPackage();
        $shareCfg['title'] = '大泽商城 百姓商城';
        $shareCfg['desc'] = '快速送达，源头正品，坏件必赔，全城最惠';
        $shareCfg['img'] = 'http://cdn2.dazemall.com/images/160531/b3b8dc3bf37391a01aebbe4898b1cdb0.jpg';
        $shareCfg['url'] = APP_URL_BASE . '/';
        $data['shareCfg'] = $shareCfg;

        $this->display('index', $data);
    }
}

