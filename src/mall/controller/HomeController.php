<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

use \src\mall\model\BannerModel;
use \src\mall\model\GoodsModuleModel;
use \src\mall\model\GoodsModuleGListModel;

class HomeController extends MallController
{
    public function index()
    {
        $bannerList = BannerModel::fetchAllValidBanner(
            CURRENT_TIME,
            BannerModel::SHOW_AREA_HOME_TOP
        );
        $goodsModuleList = GoodsModuleModel::fetchAllValidModule(CURRENT_TIME);
        $moduleGoodsList = GoodsModuleGListModel::fillGoodsList($goodsModuleList);
        $data = array(
            'bannerList' => $bannerList,
            'goodsModuleList' => $moduleGoodsList,
        );
        $this->display('index', $data);
    }
}

