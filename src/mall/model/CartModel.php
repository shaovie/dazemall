<?php
/**
 * @Author shaowei
 * @Date   2015-12-23
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Log;
use \src\common\DB;
use \src\user\model\UserCartModel;
use \src\mall\model\GlobalConfigModel;
use \src\mall\model\GoodsModel;

class CartModel
{
    const MAX_CART_GOODS_AMOUNT = 15;

    // 获得购物车列表
    public static function getCartList($userId, &$allTotalPrice)
    {
        if ($userId <= 0) {
            return array();
        }

        $cartList = UserCartModel::getCartList($userId);
        if (empty($cartList)) {
            return array();
        }

        $cartResult = array();
        foreach ($cartList as $cartGoods) {
            $data = self::fillCartGoodsInfo($cartGoods);
            if (empty($data))
                continue;
            $allTotalPrice += $data['totalPrice'];
            $cartResult[] = $data;
        }
        return $cartResult;
    }

    public static function fillCartGoodsInfo($cart)
    {
        $data = array();
        $goodsInfo = GoodsModel::findGoodsById($cart['goods_id']);
        if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
            return $data;
        }

        $goodsSku = GoodsSKUModel::getSKUInfo($cart['goods_id'], $cart['sku_attr'], $cart['sku_value']);
        if (empty($goodsSku))
            return $data;

        $data['id'] = $cart['id'];
        $data['goodsId'] = $cart['goods_id'];
        $data['amount']  = $cart['amount'];
        $data['salePrice'] = number_format($goodsSku['sale_price'], 2, '.', '');
        $data['totalPrice'] = number_format($goodsSku['sale_price'] * $cart['amount'], 2, '.', '');
        $data['name'] = $goodsInfo['name'];
        $sku = $cart['sku_attr'] . '：' . $cart['sku_value'];
        $data['sku'] = $sku;
        $data['skuAttr'] = $cart['sku_attr'];
        $data['skuValue'] = $cart['sku_value'];
        $data['imageUrl'] = $goodsInfo['image_url'];
        return $data;
    }

    public static function buildOrderGoodsList($userId, $cartIds, &$validCarts)
    {
        $cartList = UserCartModel::getCartList($userId);
        $goodsList = array();
        foreach ($cartList as $cart) {
            if (in_array($cart['id'], $cartIds)) {
                $goodsInfo = GoodsModel::findGoodsById($cart['goods_id']);
                if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID)
                    continue;
                $validCarts[] = $cart['id'];
                $goodsList[] = array(
                    'goodsId' => $cart['goods_id'],
                    'amount' => $cart['amount'],
                    'skuAttr' => $cart['sku_attr'],
                    'skuValue' => $cart['sku_value'],
                    'category_id' => $goodsInfo['category_id'],
                    'goodsName' => $goodsInfo['name'],
                    'attach' => '',
                );
            }
        }
        return $goodsList;
    }
}

