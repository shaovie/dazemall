<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\pay\model;

class PayModel
{
    // 支付方式
    const PAY_TYPE_ALI   = 1; // 支付宝
    const PAY_TYPE_WX    = 2; // 微信

    // 支付状态
    const PAY_ST_UNPAY   = 0; // 未支付
    const PAY_ST_SUCCESS = 1; // 支付成功

    public static function onCreateOrderOk(
        $orderId,
        $orderAttach
    ) {
        // 构造一个订单业务数据集，用来后续业务使用，针对一些不敏感的数据
        $nk = Nosql::NK_ORDER_ATTACH_INFO . $orderId;
        Nosql::setEx($nk, Nosql::NK_ORDER_ATTACH_INFO_EXPIRE, json_encode($orderAttach));
    }

    public static function checkPayType($payType)
    {
        if ($payType != self::PAY_TYPE_ALI
            && $payType != self::PAY_TYPE_WX) {
            return false;
        }
        return true;
    }

    public static function payTypeDesc($t)
    {
        if ($t == self::PAY_TYPE_ALI) {
            return '支付宝';
        } else if ($t == self::PAY_TYPE_WX) {
            return '微信';
        }
        return '';
    }
}


