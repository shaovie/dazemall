<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

use \src\mall\model\BannerModel;
use \src\mall\model\GoodsModuleModel;
use \src\mall\model\ActivityModel;
use \src\mall\model\GoodsModuleGListModel;

class HomeController extends MallController
{
    public function index()
    {
        $bannerList = BannerModel::fillShowBannerList(BannerModel::SHOW_AREA_HOME_TOP);
        $goodsModuleList = GoodsModuleModel::fetchAllValidModule(CURRENT_TIME);
        $moduleGoodsList = GoodsModuleGListModel::fillGoodsList($goodsModuleList);
        $actList = ActivityModel::findAllValidActivity(CURRENT_TIME, ActivityModel::SHOW_AREA_HOME);
        $data = array(
            'bannerList' => $bannerList,
            'goodsModuleList' => $moduleGoodsList,
            'actList' => $actList,
        );
        $this->display('index', $data);
    }
}

