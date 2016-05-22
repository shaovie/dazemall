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

    public static function fillCartGoodsInfo($cartGoods)
    {
        $data = array();
        $goodsInfo = GoodsModel::findGoodsById($cartGoods['goods_id']);
        if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
            return $data;
        }

        $data['id'] = $cartGoods['id'];
        $data['goodsId'] = $cartGoods['goods_id'];
        $data['amount']  = $cartGoods['amount'];
        $data['salePrice'] = number_format($goodsInfo['sale_price'], 2, '.', '');
        $data['totalPrice'] = number_format($goodsInfo['sale_price'] * $cartGoods['amount'], 2, '.', '');
        $data['name'] = $goodsInfo['name'];
        $data['sku'] = ''; // TODO
        $data['imageUrl'] = $goodsInfo['image_url'];
        return $data;
    }
}

