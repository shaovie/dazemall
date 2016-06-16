<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\common\Log;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\SkuAttrModel;
use \src\mall\model\SkuValueModel;
use \src\mall\model\GoodsDetailModel;
use \src\mall\model\GoodsCategoryModel;
use \src\mall\model\TimingMPriceModel;

class GoodsController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = GoodsModel::fetchGoodsCount([], [], []);
        $goodsList = GoodsModel::fetchSomeGoods([], [], [], $page, self::ONE_PAGE_SIZE);
        foreach ($goodsList as &$goods) {
            $goods['state'] =  GoodsModel::getStateDesc($goods['state']);
            $cateName = GoodsCategoryModel::getCateName($goods['category_id']);
            $goods['category_name'] = GoodsCategoryModel::fullCateName($goods['category_id'], $cateName);
            $tag = explode('|', $goods['tag']);
            if (count($tag) < 2)
                $goods['tag'] = array();
            else
                $goods['tag'] = array('name' => $tag[0], 'color' => $tag[1]);
        }

        $categoryList = GoodsCategoryModel::getAllCategory();
        foreach ($categoryList as &$cat) {
            $cat['name'] = GoodsCategoryModel::fullCateName($cat['category_id'], $cat['name']);
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Goods/listPage',
            $searchParams
        );
        $data = array(
            'goodsList' => $goodsList,
            'categoryList' => $categoryList,
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
            $catId = intval($this->getParam('catId', -1));
            $searchParams['status'] = $state;
            $searchParams['catId'] = $catId;
            $keyword = trim($this->getParam('keyword', ''));
            if ($catId == -1 && $state == -1 && empty($keyword)) {
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
                if ($state >= 0) {
                    $totalNum = GoodsModel::fetchGoodsCount(array('state'), array($state), false);
                    $goodsList = GoodsModel::fetchSomeGoods(array('state'),
                        array($state), false, $page, self::ONE_PAGE_SIZE);
                } elseif ($catId > 0) {
                    $totalNum = GoodsModel::fetchAllGoodsCountByCategory($catId);
                    $goodsList = GoodsModel::fetchAllGoodsByCategory($catId, $page, self::ONE_PAGE_SIZE);
                }
            }
        } while(false);

        if (!empty($goodsList)) {
            foreach ($goodsList as &$goods) {
                $goods['state'] =  GoodsModel::getStateDesc($goods['state']);
                $cateName = GoodsCategoryModel::getCateName($goods['category_id']);
                $goods['category_name'] = GoodsCategoryModel::fullCateName($goods['category_id'], $cateName);
                $tag = explode('|', $goods['tag']);
                if (count($tag) < 2)
                    $goods['tag'] = array();
                else
                    $goods['tag'] = array('name' => $tag[0], 'color' => $tag[1]);
            }
        }

        $categoryList = GoodsCategoryModel::getAllCategory();
        foreach ($categoryList as &$cat) {
            $cat['name'] = GoodsCategoryModel::fullCateName($cat['category_id'], $cat['name']);
        }

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Goods/search',
            $searchParams
        );
        $data = array(
            'goodsList' => $goodsList,
            'categoryList' => $categoryList,
            'totalGoodsNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_list", $data);
    }

    public function addPage()
    {
        $defaultAttr = '默认';
        $data = array(
            'title' => '新增商品',
            'goods' => array(),
            'curSkuAttr' => $defaultAttr,
            'skuAttrList' => SkuAttrModel::fetchAllSkuAttr(),
            'allSkuValueList' => array(),
            'skuValueList' => array(),
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
            $goodsInfo['category_id'],//category_id
            $goodsInfo['market_price'],
            $goodsInfo['sale_price'],
            $goodsInfo['jifen'],
            $goodsInfo['sort'],
            $goodsInfo['state'],
            $goodsInfo['image_url'],
            $goodsInfo['detail'],
            $goodsInfo['image_urls'],
            $goodsInfo['skuAttr'],
            $goodsInfo['skuPrice'],
            $this->account
        );
        if ($goodsId === false || (int)$goodsId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Goods/listPage?goodsId=' . $goodsId);
    }
    public function editPage()
    {
        $goodsId = intval($this->getParam('goodsId', 0));

        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'w');
        if (!empty($goodsInfo['category_id'])) {
            $goodsInfo['cate_name'] = GoodsCategoryModel::getCateName($goodsInfo['category_id']);
        }
        $goodsDetailInfo = GoodsDetailModel::findGoodsDetailById($goodsId, 'w');
        if (!empty($goodsDetailInfo)) {
            $goodsInfo['image_urls'] = explode("|", $goodsDetailInfo['image_urls']);
            $goodsInfo['detail'] = $goodsDetailInfo['detail'];
        }
        $skuValueList = GoodsSKUModel::fetchAllSKUInfo($goodsId);
        $curSkuAttr = GoodsSKUModel::getGoodsSkuAttr($goodsId);
        $skuAttrList = SkuAttrModel::fetchAllSkuAttr();
        $attrId = 0;
        if (!empty($curSkuAttr)) {
            foreach ($skuAttrList as $item) {
                if ($item['attr'] == $curSkuAttr)
                    $attrId = $item['id'];
            }
        }
        $allSkuValueList = SkuValueModel::fetchAllSkuValue($attrId);
        $data = array(
            'title' => '编辑商品',
            'goods' => $goodsInfo,
            'skuAttrList' => SkuAttrModel::fetchAllSkuAttr(),
            'curSkuAttr' => $curSkuAttr,
            'skuValueList' => $skuValueList,
            'allSkuValueList' => $allSkuValueList,
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
        $updateData['sort'] = $goodsInfo['sort'];
        $updateData['image_url'] = $goodsInfo['image_url'];
        $updateData['category_id'] = $goodsInfo['category_id'];
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
    public function skuPage()
    {
        $goodsId = intval($this->getParam('goodsId', 0));
        $skuList = GoodsSKUModel::fetchAllSKUInfo($goodsId); 
        $mpList = array();
        foreach ($skuList as &$sku) {
            $sku['sku'] = $sku['sku_attr'] . '：' . $sku['sku_value'];
            $sku['sale_price'] = number_format($sku['sale_price'], 2, '.', '');
            $mpriceInfo = TimingMPriceModel::getInfo($sku['id']);
            $mpList[] = $mpriceInfo;
        }
        $data = array(
            'goodsName' => GoodsModel::goodsName($goodsId),
            'goodsId' => $goodsId,
            'skuList' => $skuList,
            'mpList' => $mpList,
        );
        $this->display('goods_sku_list', $data);
    }
    public function modifyKuCun()
    {
        $id = intval($this->postParam('id', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $amount = intval($this->postParam('amount', 0));
        if ($amount >= 0) {
            GoodsSKUModel::setInventory($id, $goodsId, $amount, $this->account);
            $this->ajaxReturn(0, '', '/admin/Goods/skuPage?goodsId=' . $goodsId);
            return ;
        }
        $this->ajaxReturn(ERR_PARAMS_ERROR, '不能改成负数');
    }
    
    public function modifySalePrice()
    {
        $id = intval($this->postParam('id', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $price = floatval($this->postParam('price', 0));
        $synchShowPrice = intval($this->postParam('synchShowPrice', 0));
        if ($price >= 0) {
            GoodsSKUModel::setSalePrice($id, $goodsId, $price, $this->account);
            if ($synchShowPrice) {
                GoodsModel::updateGoodsInfo($goodsId, array('sale_price' => $price));
            }
            $this->ajaxReturn(0, '', '/admin/Goods/skuPage?goodsId=' . $goodsId);
            return ;
        }
        $this->ajaxReturn(ERR_PARAMS_ERROR, '价格不正确');
    }

    public function modifyBarCode()
    {
        $id = intval($this->postParam('skuId', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $barCode = trim($this->postParam('barCode', ''));
        GoodsSKUModel::setBarCode($id, $goodsId, $barCode, $this->account);
        $this->ajaxReturn(0, '', '/admin/Goods/skuPage?goodsId=' . $goodsId);
        return ;
    }

    public function modifyTimingMPrice()
    {
        $skuId = intval($this->postParam('skuId', 0));
        $goodsId = intval($this->postParam('goodsId', 0));
        $mpriceId = intval($this->postParam('mpriceId', 0));
        $mpBeginTime = trim($this->postParam('mpBeginTime', ''));
        $mpEndTime = trim($this->postParam('mpEndTime', ''));
        $mpToPrice = floatval($this->postParam('mpToPrice', 0.0));
        $mpLimitNum = intval($this->postParam('mpLimitNum', 0));
        $setState = intval($this->postParam('setstate', 0));
        $synchShowPrice = intval($this->postParam('synchShowPrice', 0));

        $beginTime = strtotime($mpBeginTime);
        $endTime = strtotime($mpEndTime);
        if ($beginTime == false || $endTime == false || $beginTime >= $endTime) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '时间格式不对');
            return ;
        }
        if ($endTime <= CURRENT_TIME) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '结束时间不能小于当前时间');
            return ;
        }
        if ($mpToPrice <= 0.001 || (int)($mpToPrice * 100) == 0) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '价格不能为0或负数');
            return ;
        }
        if ($mpLimitNum < 0) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '限购数量负数');
            return ;
        }
        if ($mpriceId == 0) {
            TimingMPriceModel::newOne($skuId,
                $beginTime,
                $endTime,
                $mpToPrice,
                $mpLimitNum,
                $synchShowPrice
            );
            $this->ajaxReturn(0, '', '/admin/Goods/skuPage?goodsId=' . $goodsId);
            return ;
        }

        $updateData = array(
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'to_price' => $mpToPrice,
            'limit_num' => $mpLimitNum,
            'synch_sale_price' => $synchShowPrice,
        );
        if ($setState) {
            $updateData['state'] = 0;
        }
        TimingMPriceModel::update($mpriceId, $skuId, $updateData);
        $this->ajaxReturn(0, '操作成功', '/admin/Goods/skuPage?goodsId=' . $goodsId);
    }

    public function setTag()
    {
        $goodsId = intval($this->postParam('goodsId', 0));
        $name = trim($this->postParam('name', ''));
        $color = intval($this->postParam('color', 0));
        GoodsModel::setTag($goodsId, $name, $color);
        $this->ajaxReturn(0, '');
        return ;
    }
    private function fetchFormParams(&$goodsInfo, &$error)
    {
        $goodsInfo['id'] = intval($this->postParam('goodsId', 0));
        $goodsInfo['name'] = trim($this->postParam('name', ''));
        $goodsInfo['state'] = intval($this->postParam('state', -1));
        $goodsInfo['market_price'] = floatval($this->postParam('marketPrice', 0.00));
        $goodsInfo['sale_price'] = floatval($this->postParam('salePrice', 0.00));
        $goodsInfo['jifen'] = intval($this->postParam('jifen', 0));
        $goodsInfo['sort'] = intval($this->postParam('sort', 0));
        $goodsInfo['image_url'] = trim($this->postParam('imageUrl', ''));
        $goodsInfo['image_urls'] = trim($this->postParam('imageUrls', ''));
        $goodsInfo['detail'] = $this->postParam('detail', '');
        $goodsInfo['category_id'] = intval($this->postParam('cateId', 0));
        $goodsInfo['skuAttr'] = trim($this->postParam('skuAttr', ''));
        $goodsInfo['sku'] = trim($this->postParam('sku', ''));

        if (empty($goodsInfo['name'])) {
            $error = '商品名不能为空';
            return false;
        }
        if (strlen($goodsInfo['name']) > 120) {
            $error = '商品名不能超过40个字符';
            return false;
        }
        if (empty($goodsInfo['category_id'])) {
            $error = '商品分类不能为空';
            return false;
        }
        if (empty($goodsInfo['skuAttr'])) {
            $error = 'sku属性不能为空';
            return false;
        }
        if (empty($goodsInfo['sku'])) {
            $error = 'sku属性值不能为空';
            return false;
        }
        $skuValueInfo = explode('|', $goodsInfo['sku']);
        $skuPrice = array();
        foreach ($skuValueInfo as $skuValue) {
            $p = explode(':', $skuValue);
            if (empty($p) || empty($p[0]) || empty($p[1]) || intval($p[2]) < 0)
                continue;
            $skuPrice[] = array('skuValue' => $p[0], 'price' => $p[1], 'amount' => $p[2], 'bar_code' => trim($p[3]));
        }
        $goodsInfo['skuPrice'] = $skuPrice;
        if (empty($goodsInfo['skuPrice'])) {
            $error = 'sku属性值不能为空或价格为0或条码为空';
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
