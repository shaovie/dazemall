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

class TimingMPriceModel
{
    const ST_UNSET      = 0;
    const ST_SET_OK     = 1;
    const ST_SET_RESUME = 2;

    public static function newOne(
        $goodsSkuId,
        $beginTime,
        $endTime,
        $toPrice,
        $limitNum,
        $synchShowPrice
    ) {
        if (empty($goodsSkuId)
            || empty($beginTime)
            || empty($endTime)
            || empty($toPrice)) {
            return false;
        }
        $data = array(
            'goods_sku_id' => $goodsSkuId,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'limit_num' => $limitNum,
            'to_price' => $toPrice,
            'state' => self::ST_UNSET,
            'synch_sale_price' => $synchShowPrice,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_timing_mprice', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function getInfo($skuId)
    {
        if (empty($skuId)) {
            return array();
        }
        $ret = DB::getDB('r')->fetchOne(
            'm_timing_mprice',
            '*',
            array('goods_sku_id'), array($skuId),
            false
        );
        return $ret === false ? array() : $ret;
    }

    public static function checkLimitBuy($skuId, $curNum, &$limitNum)
    {
        $info = self::getInfo($skuId);
        if (empty($info))
            return false;
        $limitNum = $info['limit_num'];
        if ($info['state'] != self::ST_SET_OK
            || (int)$info['limit_num'] >= $curNum) {
            return false;
        }
        return true;
    }

    public static function setResumePrice($id, $resumePrice)
    {
        if (empty($id) || empty($resumePrice)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_timing_mprice',
            array('resume_price' => $resumePrice, 'mtime' => CURRENT_TIME),
            array('id', 'state'), array($id, self::ST_SET_OK),
            array('and'),
            1
        );
        return true;
    }

    public static function setState($id, $state)
    {
        if (empty($id)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_timing_mprice',
            array('state' => $state, 'mtime' => CURRENT_TIME),
            array('id'), array($id),
            array(),
            1
        );
        return true;
    }

    public static function update($id, $skuId, $data)
    {
        if (empty($id) || empty($skuId) || empty($data)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'm_timing_mprice',
            $data,
            array('id', 'goods_sku_id'), array($id, $skuId),
            array('and'),
            1
        );
        if ($ret === false) {
            return false;
        }
        return $ret > 0;
    }
}

