<?php
/**
 * @Author shaowei
 * @Date   2015-12-24
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Log;
use \src\common\DB;

class CouponGiveCfgModel
{
    public static function getConfig()
    {
        $ck = Cache::CK_COUPON_GIVE_CONFIG;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB('w')->fetchOne(
                'm_coupon_give_cfg',
                '*',
                array(), array()
            );
            if (!empty($ret)) {
                Cache::set($ck, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function getUserRegCoupons()
    {
        $cfg = self::getConfig();
        if (empty($cfg['user_reg_coupon'])) {
            return ;
        }
        return explode(',', $cfg['user_reg_coupon']);
    }

    public static function getOrderFullCoupons($orderAmount)
    {
        $cfg = self::getConfig();
        if (empty($cfg['order_full_coupon'])) {
            return array();
        }
        if (empty($cfg['order_amount'])) {
            return array();
        }
        if ((float)$cfg['order_amount'] > $orderAmount)
            return array();
        return explode(',', $cfg['order_full_coupon']);
    }

    public static function update($data)
    {
        DB::getDB('w')->update('m_coupon_give_cfg', $data, [], []);
        Cache::del(Cache::CK_COUPON_GIVE_CONFIG);
    }
}

