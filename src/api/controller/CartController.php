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
use \src\mall\model\TimingMPriceModel;
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
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }
        if (count($skuList) > 1) {
            $this->ajaxReturn(0, '', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }

        $sku = $skuList[0];
        $optResult = $this->doAddCart($goodsId, $sku, 1);
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

        $sku = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
        if (empty($sku)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请选择商品SKU');
            return ;
        }

        $optResult = $this->doAddCart($goodsId, $sku, $amount);
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

        // for limit buy
        $cartList = UserCartModel::getCartList($this->userId());
        if (empty($cartList)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }
        foreach ($cartList as $cart) {
            if ($cart['id'] == $cartId) {
                $sku = GoodsSKUModel::getSKUInfo($cart['goods_id'], $cart['sku_attr'], $cart['sku_value']);
                if (empty($sku)) {
                    $this->ajaxReturn(ERR_PARAMS_ERROR, '商品SKU无效');
                    return ;
                }
                if ($sku['amount'] < $amount) {
                    $this->ajaxReturn(ERR_PARAMS_ERROR, '商品库存不足');
                    return ;
                }
                $limitNum = 0;
                if (TimingMPriceModel::checkLimitBuy($sku['id'], $amount, $limitNum)) {
                    $this->ajaxReturn(ERR_OPT_FAIL, '抱歉，该商品仅限购' . $limitNum . '个');
                    return ;
                }
                break;
            }
        }
        // for end

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

    private function doAddCart($goodsId, $sku, $amount)
    {
        $optResult = array('code' => ERR_PARAMS_ERROR, 'desc' => '', 'result' => array());
        if ($goodsId <= 0
            || $amount <= 0) {
            $optResult['desc'] = '参数错误';
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
            $sku['sku_attr'],
            $sku['sku_value']
        );

        $curNum = 0;
        if (!empty($cartGoods)) {
            $curNum = $cartGoods['amount'];
        }
        if ($sku['amount'] < $curNum + $amount) {
            $optResult['code'] = ERR_OPT_FAIL;
            $optResult['desc'] = '商品库存不足';
            return $optResult;
        }
        // for limit buy
        $limitNum = 0;
        if (TimingMPriceModel::checkLimitBuy($sku['id'], $curNum + $amount, $limitNum)) {
            $optResult['code'] = ERR_OPT_FAIL;
            $optResult['desc'] = '抱歉，该商品仅限购' . $limitNum . '个';
            return $optResult;
        }
        // for end

        if (!empty($cartGoods)) {
            UserCartModel::modifyAmount(
                $this->userId(),
                $cartGoods['id'],
                (int)$cartGoods['amount'] + $amount
            );
            $optResult['code'] = 0;
            $optResult['desc'] = '';
            return $optResult;
        }

        $ret = UserCartModel::newOne(
            $this->userId(),
            $goodsId,
            $sku['sku_attr'],
            $sku['sku_value'],
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

