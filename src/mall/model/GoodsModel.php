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
use \src\mall\model\GoodsCategoryModel;

class GoodsModel
{
    const GOODS_ST_INVALID         = 0;  // 无效
    const GOODS_ST_VALID           = 1;  // 有效
    const GOODS_ST_UP              = 2;  // 上架-展示在商城中

    const CATEGORY_LIST_PAGESIZE   = 4;

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
        $imageUrls,
        $skuAttr,
        $skuInfo,
        $user
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
        foreach ($skuInfo as $sku) {
            $ret = GoodsSKUModel::newOne(
                $goodsId,
                $skuAttr,
                $sku['skuValue'],
                $sku['price'],
                $sku['amount'],
                $user
            );
            if ($ret === false) {
                $wdb->rollBack();
                return false;
            }
        }
        if ($wdb->commit() === false) {
            return false;
        }
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
        $ck = Cache::CK_GOODS_INFO . $goodsId;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'g_goods',
                '*',
                array('id'), array($goodsId)
            );
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_GOODS_INFO_EXPIRE, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function search($key, $page)
    {
        if (empty($key))
            return array();
        $nowPage = $page > 0 ? $page - 1 : $page;
        $pageSize = 12;
        $key = str_replace('_', '\_', $key);
        $key = str_replace('%', '\%', $key);

        $ck = Cache::CK_GOODS_SEARCH_RESULT . $key;
        if ($nowPage != 0)
            $ret = false;
        else
            $ret = Cache::get($ck);

        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $sql = 'select * from g_goods where state=' . self::GOODS_ST_UP
                . " and name like '%" . trim($key) . "%'"
                . ' order by sort desc, id desc limit ' . $pageSize * $nowPage . ', ' . $pageSize;
            $ret = DB::getDB('r')->rawQuery($sql);
            if (!empty($ret) && $nowPage == 0) {
                Cache::setEx($ck, Cache::CK_GOODS_SEARCH_RESULT_EXPIRE, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function goodsCategory($goodsId)
    {
        $goodsInfo = self::findGoodsById($goodsId);
        return empty($goodsInfo) ? 0 : $goodsInfo['category_id'];
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

    public static function getStateDesc($state)
    {
        if ($state == self::GOODS_ST_INVALID) {
            return '无效';
        }
        if ($state == self::GOODS_ST_VALID) {
            return '有效';
        }
        if ($state == self::GOODS_ST_UP) {
            return '上架销售';
        }
        return 'null';
    }

    public static function fetchAllGoodsCountByCategory($categoryId)
    {
        $level = GoodsCategoryModel::calcLevel($categoryId);
        if ($level == 1) {
            $lv_1 = (int)($categoryId / 1000000) * 1000000;
            $lv_2 = ((int)($categoryId / 1000000) + 1) * 1000000;
            $sql = 'select count(*) as c from g_goods where category_id > ' . $lv_1
                . ' and category_id < ' . $lv_2;
            $ret = DB::getDB('r')->rawQuery($sql);
            return empty($ret) ? 0 : (int)$ret[0]['c'];
        }
        
        return self::fetchGoodsCount(array('category_id'), array($categoryId), false);
    }
    public static function fetchAllGoodsByCategory($categoryId, $page, $pageSize)
    {
        $level = GoodsCategoryModel::calcLevel($categoryId);
        if ($level == 1) {
            $page = $page > 0 ? $page - 1 : $page;

            $lv_1 = (int)($categoryId / 1000000) * 1000000;
            $lv_2 = ((int)($categoryId / 1000000) + 1) * 1000000;
            $sql = 'select * from g_goods where category_id > ' . $lv_1
                . ' and category_id < ' . $lv_2
                . ' order by sort desc, id desc limit ' . ($page * $pageSize) . ',' . $pageSize;
            $ret = DB::getDB('r')->rawQuery($sql);
            return $ret === false ? array() : $ret;
        }
        
        return self::fetchSomeGoods(
            array('category_id', 'state'),
            array($categoryId, self::GOODS_ST_UP),
            array('and'),
            $page,
            $pageSize
        );
    }
    public static function fetchGoodsByCategory($categoryId, $page, $pageSize)
    {
        $level = GoodsCategoryModel::calcLevel($categoryId);
        if ($level == 1) {
            $page = $page > 0 ? $page - 1 : $page;

            $lv_1 = (int)($categoryId / 1000000) * 1000000;
            $lv_2 = ((int)($categoryId / 1000000) + 1) * 1000000;
            $sql = 'select * from g_goods where category_id > ' . $lv_1
                . ' and category_id < ' . $lv_2
                . ' and state = ' . self::GOODS_ST_UP
                . ' order by sort desc, id desc limit ' . ($page * $pageSize) . ',' . $pageSize;
            $ret = DB::getDB('r')->rawQuery($sql);
            return $ret === false ? array() : $ret;
        }
        
        return self::fetchSomeGoods(
            array('category_id', 'state'),
            array($categoryId, self::GOODS_ST_UP),
            array('and'),
            $page,
            $pageSize
        );
    }

    public static function fillShowGoodsListData($goodsList)
    {
        $data = array();
        if (empty($goodsList))
            return $data;
        foreach ($goodsList as $goods) {
            $g['goodsId'] = $goods['id'];
            $g['name'] = $goods['name'];
            $g['imageUrl'] = $goods['image_url'];
            $g['salePrice'] = number_format($goods['sale_price'], 2, '.', '');
            $g['discount'] = ($goods['sale_price'] / $goods['market_price']) * 10.0;
            if ($g['discount'] >= 10.0)
                $g['discount'] = 0.0;
            $g['discount'] = number_format($g['discount'], 1, '.', '');
            $data[] = $g;
        }
        return $data;
    }

    public static function fetchSomeGoods($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'g_goods',
            '*',
            $conds, $vals,
            $rel,
            array('sort', 'id'), array('desc', 'desc'),
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

