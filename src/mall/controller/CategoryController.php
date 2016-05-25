<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\mall\controller;

use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsCategoryModel;

class CategoryController extends MallController
{
    public function index()
    {
        $catId = intval($this->getParam('catId', 0));

        $cateName = GoodsCategoryModel::getCateName($catId);


        $data['catId'] = $catId;
        $data['parentCatId'] = $catId;
        $level = GoodsCategoryModel::calcLevel($catId);
        if ($level != 1) {
            $data['parentCatId'] = GoodsCategoryModel::getParentId($catId);
        }
        $data['catList'] = GoodsCategoryModel::getAllCategoryByParentId($data['parentCatId']);
        $goodsList = array();
        if ($catId == 0) {
            $goodsList = GoodsModel::fetchSomeGoods(array('state'), array(GoodsModel::GOODS_ST_UP), [],
                1, GoodsModel::CATEGORY_LIST_PAGESIZE);
        } else {
            $goodsList = GoodsModel::fetchGoodsByCategory($catId, 1, GoodsModel::CATEGORY_LIST_PAGESIZE);
        }
        $data['goodsList'] = GoodsModel::fillShowGoodsListData($goodsList);
        $data['title'] = $cateName;
        $data['ajaxUrl'] = '/api/Category/getListData?catId=' . $catId;
        $this->display('goods_list', $data);
    }
}

