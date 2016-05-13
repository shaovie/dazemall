<?php
/**
 * @Author shaowei
 * @Date   2015-12-24
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsDetailModel;

class GoodsModel
{
    const GOODS_ST_INVALID         = 0;  // 无效
    const GOODS_ST_VALID           = 1;  // 有效
    const GOODS_ST_UP              = 2;  // 上架-展示在商城中
    const GOODS_ST_DOWN_VALID      = 3;  // 下架-有效
    const GOODS_ST_DOWN_INVALID    = 4;  // 下架-无效

    public static function newOne(
        $name,
        $categoryId,
        $marketPrice,
        $salePrice,
        $jifen,
        $sort,
        $state,
        $imageUrl,
        $detail,
        $imageUrls
    ) {
        if (empty($name)) {
            return array();
        }
        $wdb = DB::getDB('w');
        if ($wdb->beginTransaction() === false) {
            return false;
        }
        $data = array(
            'name' => $name,
            'category_id' => $categoryId,
            'market_price' => $marketPrice,
            'sale_price' => $salePrice,
            'image_url' => $imageUrl,
            'jifen' => $jifen,
            'sort' => $sort,
            'state' => $state,
            'ctime' => CURRENT_TIME,
        );
        $ret = $wdb->insertOne('g_goods', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        $goodsId = $ret;
        $ret = GoodsDetailModel::newOne($goodsId, $detail, $imageUrls);
        if ($ret === false) {
            $wdb->rollBack();
            return false;
        }
        if ($wdb->commit() === false) {
            return false;
        }
        self::onUpdateData($goodsId);
        return $goodsId;
    }
    public static function updateGoodsInfo($goodsId, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'g_goods',
            $data,
            array('id'), array($goodsId),
            false,
            1
        );
        self::onUpdateData($goodsId);
        return $ret !== false;
    }
    // 商品(外部判断状态)
    public static function findGoodsById($goodsId, $fromDb = 'w')
    {
        if (empty($goodsId)) {
            return array();
        }
        $ck = Cache::CK_GOODS_INFO . $goodsId; // TODO 商品被修改时一定要记得刷新缓存（比如供应商后台）
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'g_goods',
                '*',
                array('id'), array($goodsId)
            );
            if ($ret !== false) {
                Cache::setex($ck, Cache::CK_GOODS_INFO_EXPIRE, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function goodsName($goodsId)
    {
        $goodsInfo = self::findGoodsById($goodsId);
        return empty($goodsInfo) ? '' : $goodsInfo['name'];
    }

    public static function findGoodsByName($goodsName, $state)
    {
        if (empty($goodsName)) {
            return array();
        }
        if ($state >= 0)
            $sql = "select * from g_goods where instr(name, '{$goodsName}')>0 and state={$state}";
        else
            $sql = "select * from g_goods where instr(name, '{$goodsName}')>0";
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeGoods($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'g_goods',
            '*',
            $conds, $vals,
            $rel,
            array('sort'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchGoodsCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'g_goods',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function doLikeGoods($goodsId)
    {
        if (empty($goodsId)) {
            return false;
        }

        $sql = 'update g_goods set like_count = like_count + 1 where id = ' . $goodsId;
        $ret = DB::getDB('w')->rawExec($sql);
        if ($ret === false) {
            return false;
        }
        self::onUpdateData($goodsId);
        return $ret > 0 ? true : false;
    }

    private static function onUpdateData($goodsId)
    {
        Cache::del(Cache::CK_GOODS_INFO . $goodsId);
        self::findGoodsById($goodsId, 'w');
    }
}

