<?php
/**
 * @Author shaowei
 * @Date   2015-12-25
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;

class GoodsSKUModel
{
    const SKU_ST_INVALID         = 0;  // 无效
    const SKU_ST_VALID           = 1;  // 无效

    public static function newOne($goodsId, $skuAttr, $skuValue, $price, $amount, $user)
    {
        if (empty($skuAttr) || empty($skuValue)) {
            return false;
        }
        $data = array(
            'goods_id' => $goodsId,
            'sku_attr' => $skuAttr,
            'sku_value' => $skuValue,
            'sale_price' => $price,
            'amount' => $amount,
            'state' => 1,
            'ctime' => CURRENT_TIME,
            'mtime' => CURRENT_TIME,
            'm_user' => $user,
        );
        $ret = DB::getDB('w')->insertOne('g_goods_sku', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function fetchAllSKUInfo($goodsId)
    {
        if (empty($goodsId)) {
            return array();
        }
        $ck = Cache::CK_GOODS_SKU . $goodsId; // TODO 商品被修改时一定要记得刷新缓存（比如供应商后台）
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB()->fetchAll(
                'g_goods_sku',
                '*',
                array('goods_id'), array($goodsId),
                array()
            );
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_GOODS_SKU_EXPIRE, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function findAllValidSKUInfo($goodsId)
    {
        $data = self::fetchAllSKUInfo($goodsId);
        if (empty($data))
            return array();
        $ret = array();
        foreach ($data as $item) {
            if ($item['state'] == self::SKU_ST_VALID
                && $item['sale_price'] > 0.0001) {
                $ret[] = $item;
            }
        }
        return $ret;
    }

    public static function getSKUInfo($goodsId, $skuAttr, $skuValue)
    {
        $ret = GoodsSKUModel::findAllValidSKUInfo($goodsId);
        foreach ($ret as $sku) {
            if ($skuAttr == $sku['sku_attr']
                && $skuValue == $sku['sku_value']) {
                return $sku;
            }
        }
        return array();
    }

    public static function getGoodsSkuAttr($goodsId)
    {
        $data = self::fetchAllSKUInfo($goodsId);
        if (empty($data))
            return '';
        return $data[0]['sku_attr'];
    }

    public static function setInventory($id, $goodsId, $amount, $user)
    {
        if (empty($id) || empty($goodsId)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'g_goods_sku',
            array('amount' => $amount, 'mtime' => CURRENT_TIME, 'm_user' => $user),
            array('id', 'goods_id'), array($id, $goodsId),
            array('and'),
            1
        );
        self::onUpdateData($goodsId);
        return $ret !== false;
    }

    // 检查库存，不用事务!
    public static function checkInventory(
        $goodsId,
        $skuAttr,
        $skuValue,
        $amount
    ) {
        $allValidSKUInfo = self::findAllValidSKUInfo($goodsId);
        if (empty($allValidSKUInfo)) {
            return false;
        }
        foreach ($allValidSKUInfo as $sku) {
            if ($skuAttr == $sku['sku_attr']
                && $skuValue == $sku['sku_value']) {
                return $sku['amount'] >= $amount;
            }
        }
        return false;
    }

    // 减库存: return -1 库存不足，return false 系统错误
    public static function reduceInventory($goodsId, $skuAttr, $skuValue, $amount)
    {
        $amount = (int)$amount;
        if (empty($goodsId) || $amount <= 0) {
            return false;
        }

        $sql = "update g_goods_sku set amount = amount - $amount"
            . " where goods_id = $goodsId"
            . " and sku_attr = '$skuAttr'"
            . " and sku_value = '$skuValue'"
            . " and state = " . self::SKU_ST_VALID
            . " and amount >= $amount";
        $ret = DB::getDB('w')->rawExec($sql);
        if ($ret === false) {
            return false;
        }
        self::onUpdateData($goodsId);
        return $ret > 0 ? true : -1;
    }

    // 加库存: return -1 SKU无效，return false 系统错误
    public static function addInventory($goodsId, $skuAttr, $skuValue, $amount)
    {
        $amount = (int)$amount;
        if (empty($goodsId) || $amount <= 0) {
            return false;
        }

        $sql = "update g_goods_sku set amount = amount + $amount"
            . " where goods_id = $goodsId"
            . " and sku_attr = '$skuAttr'"
            . " and sku_value = '$skuValue'"
            . " and state = " . self::SKU_ST_VALID;
        $ret = DB::getDB('w')->rawExec($sql);
        if ($ret === false) {
            return false;
        }
        self::onUpdateData($goodsId);
        return $ret > 0 ? true : -1;
    }

    public static function update($goodsId, $skuAttr, $skuValue, $data)
    {
        if (empty($goodsId) || empty($data)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'g_goods_sku',
            $data,
            array('goods_id', 'sku_attr', 'sku_value'),
            array($goodsId, $skuAttr, $skuValue),
            array('and', 'and'),
            1
        );
        if ($ret === false) {
            return false;
        }
        self::onUpdateData($goodsId);
        return $ret > 0;
    }

    //= private methods
    private static function onUpdateData($goodsId)
    {
        Cache::del(Cache::CK_GOODS_SKU . $goodsId);
    }
}

