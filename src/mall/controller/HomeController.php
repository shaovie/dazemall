<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

use \src\mall\model\BannerModel;

class HomeController extends MallController
{
    public function index()
    {
        $bannerList = BannerModel::findAllValidBanner(CURRENT_TIME, BannerModel::SHOW_AREA_HOME_TOP);
        $data = array(
            'bannerList' => $bannerList,
        );
        $this->display('index', $data);
    }
}

