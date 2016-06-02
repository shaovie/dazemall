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
        $globalConfig = GlobalConfigModel::getConfig();
        $data = array(
            'searchKey' => $globalConfig['search_key'],
            'bannerList' => $bannerList,
            'goodsModuleList' => $moduleGoodsList,
            'actList' => $actList,
        );

        $data['signPackage'] = WxSDK::getSignPackage();
        $shareCfg['title'] = $globalConfig['wx_share_title'];
        $shareCfg['desc'] = $globalConfig['wx_share_desc'];
        $shareCfg['img'] = $globalConfig['wx_share_img'];
        $shareCfg['url'] = APP_URL_BASE . '/';
        $data['shareCfg'] = $shareCfg;

        $this->display('index', $data);
    }
}

