<?php
/**
 * @Author shaowei
 * @Date   2015-12-27
 */

namespace src\user\model;

use \src\common\DB;
use \src\common\Log;
use \src\common\Util;
use \src\common\Cache;
use \src\mall\model\GoodsCategoryModel;
use \src\mall\model\CouponCfgModel;
use \src\mall\model\CouponGiveCfgModel;
use \src\user\model\WxUserModel;
use \src\job\model\AsyncModel;

class UserCouponModel
{
    const COUPON_ST_UNUSED = 0;  // 未使用
    const COUPON_ST_USED   = 1;  // 已使用

    public static function newOne(
        $userId,
        $couponId,
        $beginTime,
        $endTime,
        $name,
        $remark,
        $couponAmount,
        $orderAmount,
        $categoryId
    ) {
        if (empty($userId) || empty($couponId)) {
            return false;
        }

        $data = array(
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'use_time' => 0,
            'state' => self::COUPON_ST_UNUSED,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'name' => $name,
            'remark' => $remark,
            'coupon_amount' => $couponAmount,
            'order_amount' => $orderAmount,
            'category_id' => $categoryId,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('u_coupon', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function getCouponById($userId, $couponId)
    {
        if (empty($userId) || empty($couponId)) {
            return false;
        }
        $ret = DB::getDB()->fetchOne(
            'u_coupon',
            '*',
            array('id', 'user_id'), array($couponId, $userId),
            array('and')
        );
        return $ret === false ? array() : $ret;
    }

    public static function getUnusedCouponCount($userId)
    {
        if (empty($userId))
            return array();
        $ret = DB::getDB()->fetchCount(
            'u_coupon',
            array('user_id', 'state', 'end_time>'), array($userId, self::COUPON_ST_UNUSED, CURRENT_TIME),
            array('and', 'and')
        );
        if ($ret === false)
            return 0;
        return $ret;
    }

    public static function getSomeUnusedCoupon($userId, $page, $size)
    {
        if (empty($userId))
            return array();
        return self::getSomeCoupon(
            array('user_id', 'state', 'end_time>'), array($userId, self::COUPON_ST_UNUSED, CURRENT_TIME),
            array('and', 'and'),
            array('begin_time'), array('asc'),
            $page,
            $size
        );
    }

    public static function getSomeUsedCoupon($userId, $page, $size)
    {
        if (empty($userId))
            return array();
        return self::getSomeCoupon(
            array('user_id', 'state'), array($userId, self::COUPON_ST_USED),
            array('and'),
            array('begin_time'), array('asc'),
            $page,
            $size
        );
    }

    public static function getSomeExpiredCoupon($userId, $page, $size)
    {
        if (empty($userId))
            return array();
        return self::getSomeCoupon(
            array('user_id', 'state', 'end_time<='), array($userId, self::COUPON_ST_UNUSED, CURRENT_TIME),
            array('and', 'and'),
            array('end_time'), array('desc'),
            $page,
            $size
        );
    }

    public static function useCoupon($userId, $couponId)
    {
        if (empty($userId) || empty($couponId)) {
            return false;
        }
        $data = array('state' => self::COUPON_ST_USED, 'use_time' => CURRENT_TIME);
        $ret = DB::getDB('w')->update(
            'u_coupon',
            $data,
            array('id', 'user_id', 'state'),
            array($couponId, $userId, self::COUPON_ST_UNUSED),
            array('and', 'and'),
            1
        );
        if ($ret === false) {
            return false;
        }
        return $ret > 0;
    }

    public static function refundCoupon($userId, $couponId)
    {
        if (empty($userId) || empty($couponId)) {
            return false;
        }
        $data = array('state' => self::COUPON_ST_UNUSED, 'use_time' => 0);
        $ret = DB::getDB('w')->update(
            'u_coupon',
            $data,
            array('id', 'user_id', 'state'),
            array($couponId, $userId, self::COUPON_ST_USED),
            array('and', 'and'),
            1
        );
        if ($ret === false) {
            return false;
        }
        return $ret > 0;
    }

    public static function getAvalidCouponListForOrder($userId, $goodsList)
    {
        if (empty($userId) || empty($goodsList)) {
            return array();
        }
        $ret = DB::getDB()->fetchAll(
            'u_coupon',
            '*',
            array('user_id', 'state', 'begin_time <', 'end_time >'),
            array($userId, self::COUPON_ST_UNUSED, CURRENT_TIME, CURRENT_TIME),
            array('and', 'and', 'and')
        );

        if ($ret === false) {
            return array();
        }
        $couponList = array();
        foreach ($ret as $coupon) {
            $totalPrice = 0.0;
            foreach ($goodsList as $goods) {
                if ($coupon['category_id'] == 0 // 无品类限制
                    || GoodsCategoryModel::checkBelongCategoryOrNot(
                        $coupon['category_id'],
                        $goods['categoryId'])
                ) {
                    $totalPrice += $goods['salePrice'];
                }
            }
            if ($totalPrice > 0.0001) {
                if ((float)$coupon['order_amount'] < 0.0001 // 不限制订单金额
                    || (float)$totalPrice >= (float)$coupon['order_amount']) {
                    $couponList[] = $coupon;
                }
            }
        }
        return $couponList;
    }

    public static function getBestCoupon($couponList)
    {
        if (empty($couponList)) {
            return array();
        }
        $func = function($v1, $v2) {
            if ((float)$v1['coupon_amount'] - (float)$v2['coupon_amount'] < 0.001) {
                return 0;
            }
            return (float)$v1['coupon_amount'] > (float)$v2['coupon_amount'] ? 1 : -1;
        };
        usort($couponList, $func);
        return end($couponList);
    }

    // 计算优惠券优惠金额
    public static function checkCouponForPayment($couponInfo, $goodsList)
    {
        $result = array('code' => ERR_OPT_FAIL, 'desc' => '', 'result' => array());
        if ($couponInfo['state'] == self::COUPON_ST_USED) {
            $result['desc'] = '优惠券已使用';
            return $result;
        }
        if ($couponInfo['begin_time'] > CURRENT_TIME
            || $couponInfo['end_time'] < CURRENT_TIME) {
            $result['desc'] = '优惠券不在有效期内';
            return $result;
        }

        $totalPrice = 0.0;
        foreach ($goodsList as $goods) {
            if ($couponInfo['category_id'] == 0 // 无品类限制
                || GoodsCategoryModel::checkBelongCategoryOrNot(
                    $couponInfo['category_id'],
                    $goods['category_id'])
            ) {
                $totalPrice += $goods['sale_price'];
            }
        }
        if ($totalPrice < 0.0001) {
            $result['desc'] = '优惠券品类不符，不能使用';
            return $result;
        }
        if ((float)$couponInfo['order_amount'] > 0.0001
            && (float)$totalPrice < (float)$couponInfo['order_amount']) {
            $result['desc'] = '符合优惠券条件的订单商品金额不足，优惠券不能使用';
            return $result;
        }

        $result['code'] = 0;
        $result['desc'] = '';
        return $result;
    }

    public static function giveCoupons($userId, $coupons)
    {
        if (empty($coupons) || empty($userId))
            return ;
        foreach ($coupons as $couponId) {
            Log::rinfo('find ' . $couponId);
            $couponCfgInfo = CouponCfgModel::findCouponById($couponId);
            Log::rinfo('findout ' . json_encode($couponCfgInfo));
            if (empty($couponCfgInfo)
                || $couponCfgInfo['state'] == CouponCfgModel::COUPON_ST_INVALID) {
                continue;
                $ret = self::newOne(
                    $userId,
                    $couponCfgInfo['id'],
                    $couponCfgInfo['begin_time'],
                    $couponCfgInfo['end_time'],
                    $couponCfgInfo['name'],
                    $couponCfgInfo['remark'],
                    $couponCfgInfo['coupon_amount'],
                    $couponCfgInfo['order_amount'],
                    $couponCfgInfo['category_id']
                );
                if ($ret !== false) {
                    $wxUserInfo = WxUserModel::findUserByUserId($userId);
                    if (!empty($wxUserInfo['openid'])) {
                        $tplMsg['touser'] = $wxUserInfo['openid'];
                        $tplMsg['template_id'] = TMP_SERVER_NOTIFY;
                        $tplMsg['url'] = 'http://' . APP_HOST . '/user/Coupon/myCoupon';
                        $tplMsg['topcolor'] = '#FF0000';
                        $tplMsg['data'] = array(
                            'first'    => array('value' => '恭喜您，系统赠送您一张优惠券"' . $couponCfgInfo['name'] . '"'
                                . "\n", 'color' => '#173177'),
                            'keyword1' => array('value' => '发放成功', 'color' => '#173177'),
                            'keyword2' => array('value' => date('Y-m-d H:i:s', CURRENT_TIME), 'color' => '#173177'),
                            'keyword3' => array('value' => '请及时使用，以免过期哦', 'color' => '#173177'),
                            'remark' => array('value' => '祝您购物愉快 ^_^ ~',
                                    'color' => '#173177')
                            );
                        AsyncModel::asyncSendTplMsg($wxUserInfo['openid'], $tplMsg, 0);
                    }
                }
            }
        }
    }

    public static function onNewUser($userId)
    {
        $coupons = CouponGiveCfgModel::getUserRegCoupons();
        if (!empty($coupons)) {
            self::giveCoupons($userId, $coupons);
        }
    }
    // 消费成功送券
    public static function onConsumeSuccess($orderInfo)
    {
        if (empty($orderInfo))
            return ;
        $orderAmount = $orderInfo['order_amount'] - $orderInfo['postage'];
        $coupons = CouponGiveCfgModel::getOrderFullCoupons($orderAmount);
        if (empty($coupons))
            return ;
        self::giveCoupons($orderInfo['user_id'], $coupons);
    }

    private static function getSomeCoupon($conds, $vals, $rels, $order, $orderType, $page, $size)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB()->fetchSome(
            'u_coupon',
            '*',
            $conds, $vals,
            $rels,
            $order, $orderType,
            array($page * $size, $size)
        );

        return $ret === false ? array() : $ret;
    }
}

