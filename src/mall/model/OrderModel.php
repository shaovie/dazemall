<?php
/**
 * @Author shaowei
 * @Date   2015-12-23
 */

namespace src\mall\model;

use \src\common\Nosql;
use \src\common\Cache;
use \src\common\Log;
use \src\common\Util;
use \src\common\DB;
use \src\pay\model\PayModel;
use \src\mall\model\GoodsModel;
use \src\mall\model\PostageModel;
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\TimingMPriceModel;
use \src\user\model\UserModel;
use \src\user\model\UserOrderModel;
use \src\user\model\UserAddressModel;
use \src\user\model\UserBillModel;
use \src\user\model\UserCouponModel;
use \src\job\model\AsyncModel;

class OrderModel
{
    const MAX_ORDER_QUEUE_SIZE = 2000;

    public static function checkRepeatOrder($userId)
    {
        $nk = Nosql::NK_LIMIT_ORDER_FREQ . $userId;
        $ret = Nosql::setNx($nk, 'x');
        if ($ret === true) {
            Nosql::expire($nk, Nosql::NK_LIMIT_ORDER_FREQ_EXPIRE);
            return false;
        }
        return true;
    }

    public static function createOrder(
        $orderPrefix,
        $orderEnv,
        $userId
    ) {
        $optResult = array('code' => ERR_SYSTEM_ERROR, 'desc' => '', 'result' => array());
        if (self::checkRepeatOrder($userId)) {
            $optResult['code'] = ERR_OPT_FREQ_LIMIT;
            $optResult['desc'] = '请不要重复提交订单...';
            return $optResult;
        }
        $size = AsyncModel::orderQueueSize();
        if ($size > self::MAX_ORDER_QUEUE_SIZE) {
            $optResult['code'] = ERR_SYSTEM_BUSY;
            $optResult['desc'] = '系统正在拼命处理订单，稍等后重试...';
            return $optResult;
        }

        $token = Util::getRandomStr(16);
        $data = array(
            'token' => $token,
            'orderPrefix' => $orderPrefix,
            'orderEnv' => $orderEnv,
            // TODO
        );
        AsyncModel::asyncCreateOrder($userId, $orderPrefix, $data);

        $optResult['code'] = 0;
        $optResult['desc'] = '';
        $optResult['result'] = array('token' => $token);
        $asyncResult = array('ctime' => CURRENT_TIME);
        $nk = Nosql::NK_ASYNC_ORDER_RESULT . $token;
        Nosql::setEx($nk, Nosql::NK_ASYNC_ORDER_RESULT_EXPIRE, json_encode($asyncResult));
        return $optResult;
    }

    /*=====================================业务逻辑======================================*/
    // 创建普通商品订单
    // return array('code' => 错误码, 'desc' => '错误描述', 'result' => array()) OR false
    public static function doCreateOrder(
        $orderPrefix,
        $orderEnv,
        $userId,
        $addrId,
        $goodsList, // [['goodsId' => n, 'amount' => n, 'category_id' => n,
                    // 'skuAttr' => '', 'skuValue' => '', 'attach' => ''],]
        $useAccountPay, // 0/1 是否使用账户余额支付
        $olPayType, // 第三方支付方式
        $couponId,  // 优惠券ID
        $remark,
        $attach
    ) {
        $optResult = array('code' => ERR_OPT_FAIL, 'desc' => '', 'result' => array());

        if (empty($userId)) {
            $optResult['desc'] = '登录失败，不能创建订单';
            return $optResult;
        }
        if (empty($goodsList)) {
            $optResult['code'] = ERR_OPT_FAIL;
            $optResult['desc'] = '商品列表为空';
            return $optResult;
        }
        // ! 检查数据有效性
        if (strlen($attach) > UserOrderModel::MAX_ATTACH_LEN) {
            $optResult['code'] = ERR_SYSTEM_ERROR;
            $optResult['desc'] = '系统异常';
            Log::error('order attach too lang: ' . $attach);
            return $optResult;
        }
        foreach ($goodsList as $goods) {
            if (strlen($goods['attach']) > OrderGoodsModel::MAX_ATTACH_LEN) {
                $optResult['code'] = ERR_SYSTEM_ERROR;
                $optResult['desc'] = '系统异常';
                Log::error('order goods attach too lang: ' . $goods['attach']);
                return $optResult;
            }
        }

        // ! 检查地址
        $addrInfo = UserAddressModel::getAddr($userId, $addrId);
        if (empty($addrInfo)) {
            $optResult['desc'] = '收货地址无效';
            return $optResult;
        }

        // 准备数据
        $payState = PayModel::PAY_ST_UNPAY;
        $ret = self::calcPrice($userId, $goodsList, $couponId);
        if ($ret['code'] != 0) {
            return $ret;
        }
        $toPayAmount = (float)$ret['result']['toPayAmount'];
        $couponPayAmount = (float)$ret['result']['couponPayAmount'];
        $orderAmount = (float)$ret['result']['orderAmount'];
        $postage = (float)$ret['result']['postage'];
        $goodsListSKUInfo = $ret['result']['goodsListSKUInfo'];

        $newOrderId = UserOrderModel::genOrderId($orderPrefix, $userId);
        if (empty($newOrderId)) {
            $optResult['code'] = ERR_SYSTEM_BUSY;
            $optResult['desc'] = '系统繁忙，创建订单失败，请稍后重试';
            return $optResult;
        }
        $newOrderPayId = UserOrderModel::genOrderId(UserOrderModel::ORDER_PRE_PAYMENT, $userId);
        if (empty($newOrderPayId)) {
            $optResult['code'] = ERR_SYSTEM_BUSY;
            $optResult['desc'] = '系统繁忙，创建订单失败，请稍后重试';
            return $optResult;
        }

        foreach ($goodsListSKUInfo as $idx => $goodsSKU) {
            // ! 检查限购
            $limitNum = 0;
            if (TimingMPriceModel::checkLimitBuy(
                    $goodsSKU['id'],
                    $goodsList[$idx]['amount'],
                    $limitNum)
            ) {
                $optResult['desc'] = '抱歉' . $goodsList[$idx]['goodsName'] . '仅限购' . $limitNum . '个';
                return $optResult;
            }

            // ! 检查库存
            if ($goodsSKU['amount'] < $goodsList[$idx]['amount']) {
                if (count($goodsList) == 1) {
                    $optResult['desc'] = '商品库存不足';
                } else {
                    $optResult['desc'] = GoodsModel::goodsName($goods['goodsId']) . '库存不足';
                }
                return $optResult;
            }
        }

        //= all ok
        if (DB::getDB('w')->beginTransaction() === false) {
            $optResult['code'] = ERR_SYSTEM_ERROR;
            $optResult['desc'] = '系统异常';
            Log::error('user ' . $userId . ' create order fail! begin transaction error');
            return $optResult;
        }
        // do 扣库存
        foreach ($goodsList as $goods) {
            $ret = GoodsSKUModel::reduceInventory(
                    $goods['goodsId'],
                    $goods['skuAttr'],
                    $goods['skuValue'],
                    $goods['amount']);
            if ($ret !== true) {
                DB::getDB('w')->rollBack();
                if ($ret === false) {
                    $optResult['code'] = ERR_SYSTEM_ERROR;
                    $optResult['desc'] = '系统异常';
                    Log::error('user ' . $userId . ' create order fail! reduce inventory system error');
                } else {
                    $optResult['code'] = ERR_OPT_FAIL;
                    if (count($goodsList) == 1) {
                        $optResult['desc'] = '商品库存不足';
                    } else {
                        $optResult['desc'] = GoodsModel::goodsName($goods['goodsId']) . '库存不足';
                    }
                }
                return $optResult;
            }
        }

        // do 扣余额
        $reduceCashAmount = 0.00;
        if ($useAccountPay == 1 && $toPayAmount > 0.0001) {
            $userCash = UserModel::getCash($userId);
            if ($userCash > 0.0001) {
                $reduceCashAmount = $userCash;
                if ($userCash >= $toPayAmount) {
                    $reduceCashAmount = $toPayAmount;
                    $payState = PayModel::PAY_ST_SUCCESS;
                }
                $ret = UserModel::reduceCash($userId, $reduceCashAmount);
                if ($ret !== true) {
                    if ($ret === false) {
                        $optResult['desc'] = '系统异常，扣除余额失败';
                        Log::error('user ' . $userId . ' create order fail! reduce cash system error');
                    } else {
                        $optResult['desc'] = '余额不足';
                    }
                    DB::getDB('w')->rollBack();
                    return $optResult;
                } else {
                    $ret = UserBillModel::newOne(
                        $userId,
                        $newOrderId,
                        $newOrderPayId,
                        UserBillModel::BILL_TYPE_OUT,
                        UserBillModel::BILL_FROM_ORDER_CASH_PAY,
                        $reduceCashAmount,
                        $userCash - $reduceCashAmount,
                        ''
                    );
                    if ($ret !== true) {
                        $optResult['desc'] = '系统异常，创建订单中断';
                        Log::error('user ' . $userId . ' create order fail! insert bill system error');
                        DB::getDB('w')->rollBack();
                        return $optResult;
                    }
                }
            } else {
                DB::getDB('w')->rollBack();
                $optResult['desc'] = '余额不足';
                return $optResult;
            }
        }
        $olPayAmount = $toPayAmount - $reduceCashAmount;

        // 使用优惠券
        if ($couponPayAmount > 0.0001) {
            if (UserCouponModel::useCoupon($userId, $couponId) === false) {
                DB::getDB('w')->rollBack();
                $optResult['desc'] = '使用优惠券失败';
                return $optResult;
            }
        }

        // 创建订单
        $ret = UserOrderModel::newOne(
            $newOrderId,
            $newOrderPayId,
            $orderEnv,
            $userId,
            $addrInfo['re_name'],
            $addrInfo['re_phone'],
            $addrInfo['addr_type'],
            $addrInfo['province_id'],
            $addrInfo['city_id'],
            $addrInfo['district_id'],
            $addrInfo['detail_addr'],
            $addrInfo['re_id_card'],
            $payState,
            $orderAmount,
            $olPayAmount,
            $reduceCashAmount,
            $olPayType,
            $couponPayAmount,
            $couponId,
            $postage,
            $remark,
            $attach
        );
        if ($ret !== true) {
            DB::getDB('w')->rollBack();
            $optResult['code'] = ERR_SYSTEM_ERROR;
            $optResult['desc'] = '系统异常，创建订单失败';
            Log::error('user ' . $userId . ' create order fail! insert order system error');
            return $optResult;
        }

        foreach ($goodsListSKUInfo as $idx => $goodsSKU) {
            $ret = OrderGoodsModel::newOne(
                $newOrderId,
                $goodsSKU['goods_id'],
                $goodsSKU['sku_attr'],
                $goodsSKU['sku_value'],
                $goodsList[$idx]['amount'],
                $goodsSKU['sale_price'],
                $goodsSKU['bar_code'],
                $goodsList[$idx]['attach']
            );
            if ($ret !== true) {
                DB::getDB('w')->rollBack();
                $optResult['code'] = ERR_SYSTEM_ERROR;
                $optResult['desc'] = '系统异常，创建订单失败';
                Log::error('user ' . $userId . ' create order fail! insert order_goods system error');
                return $optResult;
            }
        }

        if (DB::getDB('w')->commit() === false) {
            $optResult['code'] = ERR_SYSTEM_ERROR;
            $optResult['desc'] = '系统异常，创建订单失败';
            return $optResult;
        }

        self::onCreateOrderOk($newOrderId);
        $optResult['code'] = 0;
        $optResult['desc'] = '';
        $optResult['result'] = array('orderId' => $newOrderId, 'olPayAmount' => $olPayAmount);
        return $optResult;
    }

    // 计算价格
    public static function calcPrice($userId, $goodsList, $couponId)
    {
        $optResult = array('code' => ERR_OPT_FAIL, 'desc' => '', 'result' => array());
        $orderAmount = 0.0;
        $goodsListSKUInfo = array();
        foreach ($goodsList as $goods) {
            $ret = GoodsSKUModel::getSKUInfo(
                $goods['goodsId'],
                $goods['skuAttr'],
                $goods['skuValue']
            );
            if (!empty($ret)) {
                $goodsListSKUInfo[] = $ret;
                $orderAmount += (float)$ret['sale_price'] * (int)$goods['amount'];
            }
        }
        if (count($goodsList) != count($goodsListSKUInfo)) {
            $optResult['code'] = ERR_OPT_FAIL;
            $optResult['desc'] = '找不到商品或商品已下架';
            return $optResult;
        }
        if ($orderAmount <= 0.00001) {
            $optResult['code'] = ERR_OPT_FAIL;
            $optResult['desc'] = '找不到商品或商品已下架';
            return $optResult;
        }
        // 计算优惠金额
        $couponPayAmount = 0.0;
        if ($couponId > 0) {
            $couponInfo = UserCouponModel::getCouponById($userId, $couponId);
            if (empty($couponInfo)) {
                $optResult['desc'] = '优惠券不存在';
                return $optResult;
            }
            $func = function ($sku, $goods) {
                return array('category_id' => $goods['category_id'],
                    'sale_price' => $sku['sale_price'],
                    'amount' => $goods['amount'],
                );
            };
            $gl = array_map($func, $goodsListSKUInfo, $goodsList);
            $ret = UserCouponModel::checkCouponForPayment($couponInfo, $gl);
            if ($ret['code'] != 0) {
                return $ret;
            }
            $couponPayAmount = $couponInfo['coupon_amount'];
        }
        $toPayAmount = $orderAmount - $couponPayAmount;
        if ($toPayAmount < 0.0001) {
            $optResult['desc'] = '系统计算支付金额错误，下单失败';
            return $optResult;
        }
        // 计算邮费
        $freePostage = 0.00; 
        $postage = PostageModel::calcPostage($orderAmount, $freePostage);
        $orderAmount += $postage;
        $toPayAmount += $postage;
        $optResult['code'] = 0;
        $optResult['desc'] = '';
        $optResult['result'] = array(
            'orderAmount' => $orderAmount,
            'toPayAmount' => $toPayAmount,
            'couponPayAmount' => $couponPayAmount,
            'postage' => $postage,
            'freePostage' => $freePostage,
            'goodsListSKUInfo' => $goodsListSKUInfo,
        );
        return $optResult;
    }

    public static function doCancelOrder($userId, $orderId, $remark)
    {
        if (empty($userId) || empty($orderId)) {
            return false;
        }
        $orderInfo = UserOrderModel::findOrderByOrderId($orderId);
        if (empty($orderInfo)) {
            return false;
        }
        if ($orderInfo['user_id'] != $userId) {
            return false;
        }
        if ($orderInfo['pay_state'] != PayModel::PAY_ST_UNPAY) {
            return false;
        }
        if ($orderInfo['order_state'] != UserOrderModel::ORDER_ST_CREATED) {
            return false;
        }

        if ($orderInfo['ac_pay_amount'] < 0.0001) {
            UserOrderModel::cancelOrder($userId, $orderId, $remark);
            UserCouponModel::refundCoupon($userId, $orderInfo['coupon_id']);
            return true;
        }

        // 余额退还
        if (DB::getDB('w')->beginTransaction() === false) {
            return false;
        }
        UserOrderModel::cancelOrder($userId, $orderId, $remark);
        $ret = UserModel::addCash($userId, $orderInfo['ac_pay_amount']);
        if ($ret !== true) {
            DB::getDB('w')->rollBack();
            UserOrderModel::onRollback($orderId);
            UserModel::onRollback($userId);
            return false;
        }
        $userCash = UserModel::getCash($userId);
        $ret = UserBillModel::newOne(
            $userId,
            $orderId,
            '',
            UserBillModel::BILL_TYPE_IN,
            UserBillModel::BILL_FROM_ORDER_CASH_REFUND,
            $orderInfo['ac_pay_amount'],
            $userCash + $orderInfo['ac_pay_amount'],
            'cancel order and refund cash'
        );
        if ($ret !== true) {
            DB::getDB('w')->rollBack();
            UserOrderModel::onRollback($orderId);
            UserModel::onRollback($userId);
            return false;
        }
        if (DB::getDB('w')->commit() === false) {
            UserOrderModel::onRollback($orderId);
            UserModel::onRollback($userId);
            return false;
        }
        UserOrderModel::onCommit($orderId);
        UserModel::onCommit($userId);
        return true;
    }

    public static function onCreateOrderOk($orderId)
    {
        AsyncModel::asyncCancelOrder($orderId);
    }
}

