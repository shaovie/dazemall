<?php
/**
 * @Author shaowei
 * @Date   2015-12-27
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsSKUModel;

class MiaoShaGoodsModel
{
    public static function newOne($goodsId, $beginTime, $endTime, $price, $sort)
    {
        $data = array(
            'goods_id' => $goodsId,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'sort' => $sort,
            'price' => $price,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_miao_sha_goods', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findAllValidGoods($now)
    {
        $ret = DB::getDB('r')->fetchAll(
            'm_miao_sha_goods',
            '*',
            array('begin_time<=', 'end_time>='), array($now, $now),
            array('and'),
            array('sort'), array('desc')
        );
        return $ret === false ? array() : $ret;
    }

    public static function findAllValidPrevGoods($btime, $etime)
    {
        $ret = DB::getDB('r')->fetchAll(
            'm_miao_sha_goods',
            '*',
            array('begin_time>=', 'end_time<='), array($btime, $etime),
            array('and'),
            array('sort'), array('desc')
        );
        return $ret === false ? array() : $ret;
    }

    public static function getShowInfo()
    {
        $nowHour = intval(date('G', CURRENT_TIME));
        $data['titleList'] = array(
            array('title' => '10:00', 'active' => (($nowHour >= 10 && $nowHour < 17) ? 1 : 0)),
            array('title' => '17:00', 'active' => (($nowHour >= 17 && $nowHour < 21) ? 1 : 0)),
            array('title' => '21:00', 'active' => (($nowHour >= 21 || $nowHour < 10) ? 1 : 0)),
        );
        $goodsList = self::findAllValidGoods(CURRENT_TIME);
        $goodsList = self::fillShowGoodsList($goodsList, true, 0);
        $data['goodsList'] = $goodsList;
        return $data;
    }
    public static function fillShowGoodsList($goodsList, $alreadyStart, $leftTime)
    {
        if (empty($goodsList))
            return array();

        foreach ($goodsList as &$goods) {
            $goodsInfo = GoodsModel::findGoodsById($goods['goods_id']);
            if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
                continue ; 
            }
            $sku = GoodsSKUModel::findAllValidSKUInfo($goods['goods_id']);
            if (empty($sku)) {
                continue;
            }
            $leftAmount = -1;
            foreach ($sku as $item) {
                if (abs((float)$item['sale_price'] - (float)$goodsInfo['sale_price']) < 0.001) {
                    $leftAmount = $item['amount'];
                    break;
                }
            }
            if ($leftAmount == -1)
                continue;
            $goods['goods_id'] = $goodsInfo['id'];
            $goods['name'] = $goodsInfo['name'];
            $goods['image_url'] = $goodsInfo['image_url'];
            $goods['sale_price'] = $goodsInfo['sale_price'];
            $goods['leftTime'] = $leftTime;
            $goods['soldout'] = $leftAmount > 0 ? 0 : 1;
            $goods['start'] = $alreadyStart ? 1 : 0;
        }
        return $goodsList;
    }

    public static function fetchSomeGoods($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_miao_sha_goods',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchGoodsCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'm_miao_sha_goods',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }
    public static function del($id)
    {
        if ($id == 0) {
            return false;
        }
        $ret = DB::getDB('w')->delete(
            'm_miao_sha_goods',
            array('id'), array($id)
        );
        return $ret === false ? array() : $ret;
    }
}
