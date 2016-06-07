<?php
/**
 * @Author shaowei
 * @Date   2015-12-23
 */

namespace src\user\model;

use \src\common\Nosql;
use \src\common\Cache;
use \src\common\Log;
use \src\common\DB;
use \src\common\Util;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsSKUModel;
use \src\user\model\UserModel;
use \src\user\model\UserBillModel;
use \src\pay\model\PayModel;
use \src\job\model\AsyncModel;
use \src\user\model\UserAddressModel;
use \src\mall\model\OrderGoodsModel;
use \src\mall\model\DeliverymanModel;

class UserOrderModel
{
    const MAX_ATTACH_LEN        = 255;

    // 订单前缀
    const ORDER_PRE_COMMON      = '10';  // 普通单品订单
    const ORDER_PRE_PAYMENT     = '11';  // 在线支付订单号

    // 订单状态
    const ORDER_ST_CREATED      = 0;
    const ORDER_ST_FINISHED     = 1;
    const ORDER_ST_CANCELED     = 2;

    // 订单状态
    const ORDER_DELIVERY_ST_NOT = 0;
    const ORDER_DELIVERY_ST_ING = 1;
    const ORDER_DELIVERY_ST_RECV = 2; // 签收
    const ORDER_DELIVERY_ST_CONFIRM = 3; // 确认收货

    // 下单环境
    const ORDER_ENV_IOS         = 1;
    const ORDER_ENV_ANDROID     = 2;
    const ORDER_ENV_WEIXIN      = 3;

    //
    const ORDER_PAY_LAST_TIME   = 1800; // 订单支付持续时间 

    public static function newOne(
        $orderId,
        $orderPayId,
        $orderEnv,
        $userId,
        $reName,
        $rePhone,
        $addrType,
        $provinceId,
        $cityId,
        $districtId,
        $detailAddr,
        $reIdCard,
        $payState,
        $orderAmount,
        $olPayAmount,
        $acPayAmount,
        $olPayType,
        $couponPayAmount,
        $couponId,
        $postage,
        $attach
            ) {
                if (empty($orderId)
                    || empty($orderPayId)
                    || empty($orderEnv)
                    || empty($userId)) {
                    return false;
                }

                $data = array(
                    'order_id' => $orderId,
                    'order_pay_id' => $orderPayId,
                    'user_id' => $userId,
                    're_name' => Util::emojiEncode($reName),
                    're_phone' => $rePhone,
                    'addr_type' => $addrType,
                    'province_id' => $provinceId,
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                    'detail_addr' => $detailAddr,
                    're_id_card' => $reIdCard,
                    'pay_state' => $payState,
                    'order_state' => self::ORDER_ST_CREATED,
                    'order_amount' => $orderAmount,
                    'ol_pay_amount' => $olPayAmount,
                    'ac_pay_amount' => $acPayAmount,
                    'ol_pay_type' => $olPayType,
                    'coupon_pay_amount' => $couponPayAmount,
                    'coupon_id' => $couponId,
                    'postage' => $postage,
                    'order_env' => $orderEnv,
                    'remark' => '',
                    'attach' => $attach,
                    'ctime' => CURRENT_TIME,
                    'mtime' => CURRENT_TIME,
                    'm_user' => 'sys'
                        );
                $ret = DB::getDB('w')->insertOne('o_order', $data);
                if ($ret === false || (int)$ret <= 0) {
                    return false;
                }
                return true;
            }

    public static function findOrderByOrderPayId($orderPayId, $fromDb = 'w')
    {
        if (empty($orderPayId)) {
            return array();
        }
        $ret = DB::getDB($fromDb)->fetchOne(
            'o_order',
            '*',
            array('order_pay_id'), array($orderPayId)
        );
        if (empty($ret)) {
            return array();
        }
        $ret['re_name'] = Util::emojiDecode($ret['re_name']);
        return $ret;
    }

    public static function findOrderByOrderId($orderId, $fromDb = 'w')
    {
        if (empty($orderId)) {
            return array();
        }
        $ck = Cache::CK_ORDER_INFO . $orderId;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'o_order',
                '*',
                array('order_id'), array($orderId)
            );
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_ORDER_INFO_EXPIRE, json_encode($ret));
            }
        }
        if (empty($ret)) {
            return array();
        }
        $ret['re_name'] = Util::emojiDecode($ret['re_name']);
        return $ret;
    }

    public static function fetchSomeOrder($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'o_order',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeUserOrder($userId, $page, $pageSize)
    {
        if (empty($userId))
            return array();
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'o_order',
            '*',
            array('user_id'), array($userId),
            false,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }
    public static function fetchOrderCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'o_order',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function fetchSoldAmount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchOne(
            'o_order',
            'sum(order_amount) as c',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret['c'];
    }

    public static function changePayType($userId, $orderId, $payType)
    {
        if (empty($userId) || empty($orderId)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'o_order',
            array('ol_pay_type' => $payType),
            array('order_id', 'user_id', 'pay_state'),
            array($orderId, $userId, PayModel::PAY_ST_UNPAY),
            array('and', 'and')
        );
        self::onUpdateData($orderId);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }
    public static function changeOrderPayId($userId, $orderId, $orderPayId)
    {
        if (empty($userId) || empty($orderId) || empty($orderPayId)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'o_order',
            array('order_pay_id' => $orderPayId),
            array('order_id', 'user_id', 'pay_state'),
            array($orderId, $userId, PayModel::PAY_ST_UNPAY),
            array('and', 'and')
        );
        self::onUpdateData($orderId);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function manualConfirmPayOk($orderId, $mUser)
    {
        if (empty($orderId)) {
            return false;
        }

        $orderInfo = self::findOrderByOrderId($orderId);
        if (empty($orderInfo)) 
            return ;
        if ($orderInfo['order_state'] == UserOrderModel::ORDER_ST_CANCELED
            || $orderInfo['pay_state'] != PayModel::PAY_ST_UNPAY) {
            return false;
        }
        $ret = DB::getDB('w')->update(
            'o_order',
            array('pay_state' => PayModel::PAY_ST_SUCCESS,
                'pay_time' => CURRENT_TIME,
                'sys_remark' => $mUser . ' manual confirm pay ok',
            ),
            array('order_id'), array($orderId)
        );
        self::onUpdateData($orderInfo['order_id']);
        $ret = UserBillModel::newOne(
            $orderInfo['user_id'],
            $orderInfo['order_id'],
            '',
            UserBillModel::BILL_TYPE_OUT,
            UserBillModel::BILL_FROM_ORDER_OL_PAY,
            $orderInfo['ol_pay_amount'],
            UserModel::getCash($orderInfo['user_id']),
            $mUser . ' manual confirm pay ok' 
        );

        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        self::payOkNotifyToAdmin($orderInfo);
        return true;
    }
    public static function manualConfirmCancel($orderId, $mUser)
    {
        if (empty($orderId)) {
            return false;
        }

        $orderInfo = self::findOrderByOrderId($orderId);
        if (empty($orderInfo)) 
            return ;
        if ($orderInfo['pay_state'] == PayModel::PAY_ST_SUCCESS)
            return false;
        if ($orderInfo['order_state'] == UserOrderModel::ORDER_ST_CANCELED
            || $orderInfo['order_state'] == UserOrderModel::ORDER_ST_FINISHED) {
            return false;
        }
        return self::cancelOrder(
            $orderInfo['user_id'],
            $orderId,
            $mUser . ' manual confirm cancel order'
        );
    }
    public static function manualConfirmSign($orderId, $mUser)
    {
        if (empty($orderId)) {
            return false;
        }

        $orderInfo = self::findOrderByOrderId($orderId);
        if (empty($orderInfo)) 
            return ;
        if ($orderInfo['order_state'] == UserOrderModel::ORDER_ST_CANCELED
            || $orderInfo['delivery_state'] != UserOrderModel::ORDER_DELIVERY_ST_ING) {
            return false;
        }
        $ret = DB::getDB('w')->update(
            'o_order',
            array('delivery_state' => UserOrderModel::ORDER_DELIVERY_ST_RECV,
                'sys_remark' => $mUser . ' manual confirm sign',
            ),
            array('order_id'), array($orderId)
        );
        self::onUpdateData($orderInfo['order_id']);

        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }
    public static function onPayOk($orderPayId)
    {
        if (empty($orderPayId)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'o_order',
            array('pay_state' => PayModel::PAY_ST_SUCCESS, 'pay_time' => CURRENT_TIME),
            array('order_pay_id'), array($orderPayId)
        );
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        $orderInfo = self::findOrderByOrderPayId($orderPayId);
        if (!empty($orderInfo)) {
            AsyncModel::asyncDBOpt('give_order_full_coupons', array('order' => $orderInfo));
            $ret = UserBillModel::newOne(
                $orderInfo['user_id'],
                $orderInfo['order_id'],
                $orderPayId,
                UserBillModel::BILL_TYPE_OUT,
                UserBillModel::BILL_FROM_ORDER_OL_PAY,
                $orderInfo['ol_pay_amount'],
                UserModel::getCash($orderInfo['user_id']),
                'ol pay ok'
            );
            self::onUpdateData($orderInfo['order_id']);
            self::payOkNotifyToAdmin($orderInfo);
        }
        return true;
    }

    public static function cancelOrder($userId, $orderId, $sysRemark)
    {
        if (empty($userId) || empty($orderId)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'o_order',
            array('order_state' => self::ORDER_ST_CANCELED, 'sys_remark' => $sysRemark),
            array('order_id', 'user_id', 'order_state'),
            array($orderId, $userId, self::ORDER_ST_CREATED),
            array('and', 'and')
        );
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        self::onUpdateData($orderId);
        return true;
    }

    public static function userConfirmDeliveryOk($userId, $orderId)
    {
        if (empty($userId) || empty($orderId)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'o_order',
            array('delivery_state' => self::ORDER_DELIVERY_ST_CONFIRM,
                'order_state' => self::ORDER_ST_FINISHED),
            array('order_id', 'user_id'), array($orderId, $userId),
            array('and')
        );
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        self::onUpdateData($orderId);
        return true;
    }
    public static function confirmDelivery($deliverymanId, $orderId)
    {
        if (empty($orderId)) {
            return false;
        }

        $ret = DB::getDB('w')->update(
            'o_order',
            array('deliveryman_id' => $deliverymanId,
                'delivery_time' => CURRENT_TIME,
                'delivery_state' => self::ORDER_DELIVERY_ST_ING),
            array('order_id'),
            array($orderId)
        );
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        self::onUpdateData($orderId);
        self::onStartDelivery($orderId, $deliverymanId);
        return true;
    }

    public static function genOrderId($prefix, $userId)
    {
        for ($i = 0; $i < 10; $i++) {
            $orderId = $prefix
                . date('ymd', CURRENT_TIME)
                . str_pad(mt_rand(1, 999999), 7, '0', STR_PAD_LEFT)
                . str_pad(($userId % 100), 2, '0', STR_PAD_LEFT);
            $nk = Nosql::NK_ORDER_ID_RECORD . $orderId;
            $ret = Nosql::get($nk);
            if (empty($ret)) {
                Nosql::setEx($nk, Nosql::NK_ORDER_ID_RECORD_EXPIRE, 'x');
                return $orderId;
            }
        }
        Log::fatal('gen order id fail! prefix = ' . $prefix . ' user id = ' . $userId);
        return '';
    }

    public static function onCommit($orderId)
    {
        self::onUpdateData($orderId);
    }
    public static function onRollback($orderId)
    {
        self::onUpdateData($orderId);
    }

    private static function onUpdateData($orderId)
    {
        Cache::del(Cache::CK_ORDER_INFO . $orderId);
        self::findOrderByOrderId($orderId, 'w');
    }

    private static function payOkNotifyToAdmin($orderInfo)
    {
        $nk = Nosql::NK_PAYOK_ORDER_FOR_NOTIFY_ADMIN_QUEUE;
        $lsize = Nosql::lSize($nk);
        if ((int)$lsize > 100) {
            Nosql::del($nk);
        }
        Nosql::rPush($nk, $orderInfo['order_id']);
    }

    private static function onStartDelivery($orderId, $deliverymanId)
    {
        $orderInfo = self::findOrderByOrderId($orderId);
        if (empty($orderInfo))
            return ;
        $wxUserInfo = WxUserModel::findUserByUserId($orderInfo['user_id']);
        if (!empty($wxUserInfo['openid'])) {
            $goodsList = OrderGoodsModel::fetchOrderGoodsById($orderId);
            foreach($goodsList as &$val) {
                $goodsInfo = GoodsModel::findGoodsById($val['goods_id']);
                if (!empty($goodsInfo)) {
                    $val['name'] = $goodsInfo['name'];
                }
            }
            $orderDesc = $goodsList[0]['name'];
            if (count($goodsList) > 1)
                $orderDesc .= '; ' . $goodsList[1]['name'];
            $fullAddr = UserAddressModel::getFullAddr($orderInfo);
            $fullAddr = $fullAddr['fullAddr'];
            $deliverymanInfo = DeliverymanModel::findDeliverymanById($deliverymanId);
            $deliverymanName = '商城配送';
            $deliverymanPhone = '400-803-0530';
            if (!empty($deliverymanInfo)) {
                $deliverymanName = $deliverymanInfo['name'];
                $deliverymanPhone = $deliverymanInfo['phone'];
            }

            $tplMsg['touser'] = $wxUserInfo['openid'];
            $tplMsg['template_id'] = TMP_DELIVERY_NOTIFY;
            $tplMsg['url'] = 'http://' . APP_HOST . '/user/Order/toTakeDelivery';
            $tplMsg['topcolor'] = '#FF0000';
            $tplMsg['data'] = array(
                'first'    => array('value' => "您好，您的订单已经发货啦！\n", 'color' => '#173177'),
                'keyword1' => array('value' => $orderDesc, 'color' => '#173177'),
                'keyword2' => array('value' => date('Y-m-d H:i:s', $orderInfo['ctime']),
                    'color' => '#173177'),
                'keyword3' => array('value' => $fullAddr, 'color' => '#173177'),
                'keyword4' => array('value' => $deliverymanName, 'color' => '#173177'),
                'keyword5' => array('value' => $deliverymanPhone, 'color' => '#173177'),
                'remark' => array('value' => "\n祝您购物愉快！", 'color' => '#173177')
            );
            AsyncModel::asyncSendTplMsg($wxUserInfo['openid'], $tplMsg, 0);
        }
    }
}

