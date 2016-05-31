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

class CouponCfgModel
{
    const COUPON_ST_INVALID  = 0; // 无效
    const COUPON_ST_VALID    = 1; // 有效

    public static function newOne(
        $beginTime,
        $endTime,
        $name,
        $remark,
        $couponAmount,
        $orderAmount,
        $categoryId,
        $state
    ) {
        if (empty($name)) {
            return false;
        }

        $data = array(
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'name' => $name,
            'remark' => $remark,
            'coupon_amount' => $couponAmount,
            'order_amount' => $orderAmount,
            'category_id' => $categoryId,
            'state' => $state,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_coupon_cfg', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findCouponById($couponId)
    {
        if (empty($couponId)) {
            return array();
        }
        $ret = DB::getDB()->fetchOne(
            'm_coupon_cfg',
            '*',
            array('id'), array($couponId)
        );
        return $ret === false ? array() : $ret;
    }

    public static function findSomeCouponsByIds($couponIds)
    {
        if (empty($couponIds)) {
            return array();
        }
        /*
        ksort($couponIds, SORT_NUMERIC);
        $idSet = implode(',', $couponIds);
        $ck = Cache::CK_COUPON_CFG_LIST_INFO . $idSet;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $sql = "select * from m_coupon_cfg where id in ($idSet)";
            $ret = DB::getDB()->rawQuery($sql);
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_COUPON_CFG_INFO_LIST_EXPIRE, json_encode($ret));
            }
        }
        */
        $idSet = implode(',', $couponIds);
        $sql = "select * from m_coupon_cfg where id in ($idSet)";
        $ret = DB::getDB()->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeCoupon($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_coupon_cfg',
            '*',
            $conds, $vals,
            $rel,
            array('sort'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeCoupon2($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_coupon_cfg',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchCouponCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'm_coupon_cfg',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }
    public static function update($id, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_coupon_cfg',
            $data,
            array('id'), array($id),
            false,
            1
        );
        return $ret !== false;
    }
    public static function del($id)
    {
        if ($id == 0) {
            return false;
        }
        $ret = DB::getDB('w')->delete(
            'm_coupon_cfg',
            array('id'), array($id)
        );
        return $ret === false ? array() : $ret;
    }
    public static function stateDesc($state)
    {
        if ($state == self::COUPON_ST_INVALID)
            return '无效';
        return '有效';
    }
}
