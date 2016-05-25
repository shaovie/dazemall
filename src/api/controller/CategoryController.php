<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\api\controller;

use \src\common\BaseController;
use \src\mall\model\GoodsModel;

class CategoryController extends BaseController
{
    public function getListData()
    {
        $catId = intval($this->getParam('catId', 0));
        $page  = intval($this->getParam('page', 1));
        if ($page < 1)
            $page = 1;

        $goodsList = array();
        if ($catId == 0) {
            $goodsList = GoodsModel::fetchSomeGoods(array('state'), array(GoodsModel::GOODS_ST_UP), [],
                1, GoodsModel::CATEGORY_LIST_PAGESIZE);
        } else {
            $goodsList = GoodsModel::fetchGoodsByCategory($catId, $page, GoodsModel::CATEGORY_LIST_PAGESIZE);
        }
        $goodsList = GoodsModel::fillShowGoodsListData($goodsList);
        $this->ajaxReturn(0, '', '', array('goodsList' => $goodsList));
    }
}

