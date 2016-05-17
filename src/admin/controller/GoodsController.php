<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsDetailModel;
use \src\mall\model\GoodsCategoryModel;

class GoodsController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->display("goods_list");
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = GoodsModel::fetchGoodsCount([], [], []);
        $goodsList = GoodsModel::fetchSomeGoods([], [], [], $page, self::ONE_PAGE_SIZE);
        foreach ($goodsList as &$goods) {
            $goods['state'] =  GoodsModel::getStateDesc($goods['state']);
            $cateName = GoodsCategoryModel::getCateName($goods['category_id']);
            $goods['category_name'] = GoodsCategoryModel::fullCateName($goods['category_id'], $cateName);
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/Goods/listPage', $searchParams);
        $data = array(
            'goodsList' => $goodsList,
            'totalGoodsNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_list", $data);
    }

    public function search()
    {
        $goodsList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $state = intval($this->getParam('status', -1));
            $searchParams['status'] = $state;
            $keyword = trim($this->getParam('keyword', ''));
            if ($state == -1 && empty($keyword)) {
                header('Location: /admin/Goods/listPage');
                return ;
            }
            if (!empty($keyword)) {
                $searchParams['keyword'] = $keyword;
                if (is_numeric($keyword)) {
                    $goods = GoodsModel::findGoodsById($keyword, 'r');
                    if (!empty($goods)) {
                        $goodsList[] = $goods;
                        $totalNum = 1;
                    }
                } else {
                    $goods = GoodsModel::findGoodsByName($keyword, $state);
                    if (!empty($goods)) {
                        $goodsList = $goods;
                        $totalNum = count($goods);
                    }
                }
            } else {
                if ($state >= 0)
                    $totalNum = GoodsModel::fetchGoodsCount(array('state'), array($state), false);
                    $goodsList = GoodsModel::fetchSomeGoods(array('state'), array($state), false, $page, self::ONE_PAGE_SIZE);
            }
        } while(false);

        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/Goods/search', $searchParams);
        $data = array(
            'goodsList' => $goodsList,
            'totalGoodsNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增商品',
            'goods' => array(),
            'action' => '/admin/Goods/add',
        );
        $this->display('goods_info', $data);
    }
    public function add()
    {
        $error = '';
        $goodsInfo = array();
        $ret = $this->fetchFormParams($goodsInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $goodsId = GoodsModel::newOne(
            $goodsInfo['name'],
            0,//category_id
            $goodsInfo['market_price'],
            $goodsInfo['sale_price'],
            $goodsInfo['jifen'],
            0,//$goodsInfo['sort'],
            $goodsInfo['state'],
            $goodsInfo['image_url'],
            $goodsInfo['detail'],
            $goodsInfo['image_urls']
        );
        if ($goodsId === false || (int)$goodsId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Goods/editPage?goodsId=' . $goodsId);
    }
    public function editPage()
    {
        $goodsId = intval($this->getParam('goodsId', 0));

        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'w');
        $goodsDetailInfo = GoodsDetailModel::findGoodsDetailById($goodsId, 'w');
        if (!empty($goodsDetailInfo)) {
            $goodsInfo['image_urls'] = explode("|", $goodsDetailInfo['image_urls']);
            $goodsInfo['detail'] = $goodsDetailInfo['detail'];
        }
        $data = array(
            'title' => '编辑商品',
            'goods' => $goodsInfo,
            'action' => '/admin/Goods/edit',
        );
        $this->display('goods_info', $data);
    }
    public function edit()
    {
        $error = '';
        $goodsInfo = array();
        $ret = $this->fetchFormParams($goodsInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $updateData = array();
        $updateData['name'] = $goodsInfo['name'];
        $updateData['state'] = $goodsInfo['state'];
        $updateData['market_price'] = $goodsInfo['market_price'];
        $updateData['sale_price'] = $goodsInfo['sale_price'];
        $updateData['jifen'] = $goodsInfo['jifen'];
        $updateData['image_url'] = $goodsInfo['image_url'];
        $ret = GoodsModel::updateGoodsInfo($goodsInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $updateData = array();
        $updateData['detail'] = $goodsInfo['detail'];
        $updateData['image_urls'] = $goodsInfo['image_urls'];
        $ret = GoodsDetailModel::update($goodsInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品详情失败~');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Goods/editPage?goodsId=' . $goodsInfo['id']);
    }
    private function fetchFormParams(&$goodsInfo, &$error)
    {
        $goodsInfo['id'] = intval($this->postParam('goodsId', 0));
        $goodsInfo['name'] = trim($this->postParam('name', ''));
        $goodsInfo['state'] = intval($this->postParam('state', -1));
        $goodsInfo['market_price'] = floatval($this->postParam('marketPrice', 0.00));
        $goodsInfo['sale_price'] = floatval($this->postParam('salePrice', 0.00));
        $goodsInfo['jifen'] = intval($this->postParam('jifen', 0));
        $goodsInfo['image_url'] = trim($this->postParam('imageUrl', ''));
        $goodsInfo['image_urls'] = trim($this->postParam('imageUrls', ''));
        $goodsInfo['detail'] = $this->postParam('detail', '');

        if (empty($goodsInfo['name'])) {
            $error = '商品名不能为空';
            return false;
        }
        if (strlen($goodsInfo['name']) > 120) {
            $error = '商品名不能超过40个字符';
            return false;
        }
        if ($goodsInfo['state'] != GoodsModel::GOODS_ST_INVALID
            && $goodsInfo['state'] != GoodsModel::GOODS_ST_VALID
            && $goodsInfo['state'] != GoodsModel::GOODS_ST_UP
        ) {
            $error = '上架状态无效';
            return false;
        }
        $goodsInfo['image_urls'] = trim($goodsInfo['image_urls'], '|');
        $gs = explode('|', $goodsInfo['image_urls']);
        if (count($gs) > 9) {
            $error = '轮播图不能超过9张';
            return false;
        }
        return true;
    }

}
