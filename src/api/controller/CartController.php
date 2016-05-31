<?php
/**
 * @Author shaowei
 * @Date   2015-12-23
 */

namespace src\api\controller;

use \src\common\Check;
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\GoodsModel;
use \src\mall\model\CartModel;
use \src\user\model\UserCartModel;

class CartController extends ApiController
{
    public function getCartAmount()
    {
        $cartAmount = UserCartModel::getCartAmount($this->userId());
        $this->ajaxReturn(0, '', '', array('number' => $cartAmount));
    }

    public function autoAdd()
    {
        $this->checkLoginAndNotice();

        $goodsId = (int)$this->postParam('goodsId', 0);

        $skuList = GoodsSKUModel::findAllValidSKUInfo($goodsId);
        if (empty($skuList)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '');
            return ;
        }
        if (count($skuList) > 1) {
            $this->ajaxReturn(0, '', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }

        $optResult = $this->doAddCart($goodsId, $skuList[0]['sku_attr'], $skuList[0]['sku_value'], 1);
        if ($optResult['code'] != 0) {
            $this->ajaxReturn($optResult['code'], $optResult['desc']);
            return ;
        }
        $this->ajaxReturn(0, '');
    }

    // 加入购物车
    public function add()
    {
        $this->checkLoginAndNotice();

        $goodsId = (int)$this->postParam('goodsId', 0);
        $skuAttr = trim($this->postParam('skuAttr', ''));
        $skuValue = trim($this->postParam('skuValue', ''));
        $amount = (int)$this->postParam('amount', 0);

        $optResult = $this->doAddCart($goodsId, $skuAttr, $skuValue, $amount);
        if ($optResult['code'] != 0) {
            $this->ajaxReturn($optResult['code'], $optResult['desc']);
            return ;
        }
        $this->ajaxReturn(0, '');
    }

    public function modifyAmount()
    {
        $this->checkLoginAndNotice();

        $cartId = (int)$this->postParam('id', 0);
        if ($cartId <= 0) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }
        $amount = (int)$this->postParam('number', 0);
        if ($amount <= 0) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }

        UserCartModel::modifyAmount($this->userId(), $cartId, $amount);
        $this->ajaxReturn(0, '', '', array('amount' => $amount));
    }

    // 删除商品
    public function del()
    {
        $this->checkLoginAndNotice();

        $cartId = (int)$this->postParam('id', 0);
        if ($cartId <= 0) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }
        UserCartModel::delCart($this->userId(), $cartId);
        $this->ajaxReturn(0, '');
    }

    private function doAddCart($goodsId, $skuAttr, $skuValue, $amount)
    {
        $optResult = array('code' => ERR_PARAMS_ERROR, 'desc' => '', 'result' => array());
        if ($goodsId <= 0
            || !Check::isSkuAttr($skuAttr)
            || !Check::isSkuValue($skuValue)
            || $amount <= 0) {
            $optResult['desc'] = '参数错误';
            return $optResult;
        }

        $goodsSKU = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
        if (empty($goodsSKU)) {
            $optResult['desc'] = '请选择商品SKU';
            return $optResult;
        }

        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
            $optResult['desc'] = '该商品无效';
            return $optResult;
        }

        $cartAmount = UserCartModel::getCartAmount($this->userId());
        if ($cartAmount > CartModel::MAX_CART_GOODS_AMOUNT) {
            $optResult['code'] = ERR_OPT_FAIL;
            $optResult['desc'] = '您的购物车已满，赶快结算吧';
            return $optResult;
        }

        $cartGoods = UserCartModel::getCartGoods(
            $this->userId(),
            $goodsId,
            $skuAttr,
            $skuValue
        );
        if (!empty($cartGoods)) {
            UserCartModel::modifyAmount(
                $this->userId(),
                $cartGoods['id'],
                $cartGoods['amount'] + $amount
            );
            $optResult['code'] = 0;
            $optResult['desc'] = '';
            return $optResult;
        }

        $ret = UserCartModel::newOne(
            $this->userId(),
            $goodsId,
            $skuAttr,
            $skuValue,
            $amount,
            '' // attach
        );
        if ($ret === false) {
            $optResult['code'] = ERR_SYSTEM_ERROR;
            $optResult['desc'] = '系统错误，加入购物车失败';
            return $optResult;
        }
        $optResult['code'] = 0;
        $optResult['desc'] = '';
        return $optResult;
    }
}

