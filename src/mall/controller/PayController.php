<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\mall\controller;

use \src\common\Check;
use \src\common\WxSDK;
use \src\common\Util;
use \src\common\Log;
use \src\pay\model\PayModel;
use \src\user\model\UserModel;
use \src\user\model\UserOrderModel;
use \src\user\model\UserCartModel;
use \src\user\model\UserAddressModel;
use \src\mall\model\CartModel;
use \src\mall\model\OrderModel;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\OrderGoodsModel;
use \src\mall\model\GlobalConfigModel;
use \src\mall\model\PostageModel;

class PayController extends MallController
{
    public function __construct()
    {
        parent::__construct();
    }

    // 购物车结算
    public function cartPay()
    {
        $this->checkLoginAndNotice();

        $cartIds = $this->postParam('cartId', array());

        $orderId = trim($this->postParam('orderId', ''));
        if (!empty($orderId)) {
            $url = '/mall/Pay/payAgain?showwxpaytitle=1&orderId=' . $orderId;
            header('Location: ' . $url);
            exit();
        }

        $validCart = $this->getValidCartIds($cartIds);
        if (empty($validCart)) {
            $this->showNotice('购物车数据错误', '/mall/Cart');
            return ;
        }

        $goodsList = array();
        foreach ($validCart as $cart) {
            $data = CartModel::fillCartGoodsInfo($cart);
            if (empty($data))
                continue;
            $goodsList[] = $data;
        }
        $ret = $this->showPayPage('cart', '/mall/Pay/cartOrder', $goodsList, [], []);
        if ($ret['code'] != 0) {
            $this->showNotice($ret['desc'], '/mall/Cart');
            return ;
        }
    }

    // 立即购买
    public function quickBuy()
    {
        $this->checkLoginAndNotice();

        $goodsId = intval($this->postParam('goodsId', 0));
        $skuAttr = trim($this->postParam('skuAttr', ''));
        $skuValue = trim($this->postParam('skuValue', ''));
        $amount = intval($this->postParam('amount', 0));

        if ($goodsId <= 0
            || $amount <= 0
            || !Check::isSkuAttr($skuAttr)
            || !Check::isSkuValue($skuValue)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请求参数错误');
            return ;
        }

        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该商品无效SKU');
            return ;
        }
        $goodsSku = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
        if (empty($goodsSku)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品规格无效SKU');
            return ;
        }
        if ($goodsSku['amount'] < $amount) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品库存不足');
            return ;
        }

        $this->ajaxReturn(0, '', '/mall/Pay/showQuickBuy?'
            . 'goodsId=' . $goodsId
            . '&skuAttr=' . $skuAttr
            . '&skuValue=' . $skuValue
            . '&amount=' . $amount
        );
    }

    public function showQuickBuy()
    {
        $this->checkLoginAndNotice();

        $goodsId = intval($this->getParam('goodsId', 0));
        $skuAttr = trim($this->getParam('skuAttr', ''));
        $skuValue = trim($this->getParam('skuValue', ''));
        $amount = intval($this->getParam('amount', 0));

        if ($goodsId <= 0
            || $amount <= 0
            || !Check::isSkuAttr($skuAttr)
            || !Check::isSkuValue($skuValue)) {
            $this->showNotice('请求参数错误', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }

        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
            $this->showNotice('该商品无效', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }
        $goodsSku = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
        if (empty($goodsSku)) {
            $this->showNotice('商品规格无效SKU', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }
        if ($goodsSku['amount'] < $amount) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '商品库存不足');
            return ;
        }
        $goodsList = array(
            array(
                'id' => $goodsId,
                'imageUrl' => $goodsInfo['image_url'],
                'name' => $goodsInfo['name'],
                'sku' => $skuAttr . '：' . $skuValue,
                'salePrice' => number_format($goodsSku['sale_price'], 2, '.', ''),
                'amount' => $amount,

                // for calc price
                'skuAttr' => $skuAttr,
                'skuValue' => $skuValue,
                'goodsId' => $goodsId,
            )
        );
        $goodsInfo = array(
            'skuAttr' => $skuAttr,
            'skuValue' => $skuValue,
            'amount' => $amount,
        );
        $ret = $this->showPayPage('quickbuy', '/mall/Pay/quickOrder', $goodsList, $goodsInfo, []);
        if ($ret['code'] != 0) {
            $this->showNotice($ret['desc'], '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }
    }

    // 购物车结算方式下单
    public function cartOrder()
    {
        $this->checkLoginAndNotice();

        $addrId = intval($this->postParam('address_id', 0));
        $cartIds = $this->postParam('cartId', array());
        $payType = intval($this->postParam('pay_type', 0));
        $isCash  = intval($this->postParam('is_cash', 0));
        $couponId  = intval($this->postParam('coupon_id', 0));

        $orderId = trim($this->postParam('orderId', ''));
        if (!empty($orderId)) {
            $url = '/mall/Pay/payAgain?showwxpaytitle=1&orderId=' . $orderId;
            $this->ajaxReturn(0, '', $url, ['orderId' => '']); // 跳转到待支付
            exit();
        }

        if (OrderModel::checkRepeatOrder($this->userId())) {
            $this->ajaxReturn(ERR_OPT_FREQ_LIMIT, '请不要重复提交订单...', '', ['orderId' => '']);
            return ;
        }

        $validCarts = array();
        $goodsList = CartModel::buildOrderGoodsList($this->userId(), $cartIds, $validCarts);
        if (empty($goodsList)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '购物车数据错误', '', ['orderId' => '']);
            return ;
        }
        $ret = OrderModel::doCreateOrder(
            UserOrderModel::ORDER_PRE_COMMON,
            UserOrderModel::ORDER_ENV_WEIXIN,
            $this->userId(),
            $addrId,
            $goodsList,
            $isCash,
            $payType,
            $couponId,
            ''
        );
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => '']);
            return ;
        }

        UserCartModel::delCarts($this->userId(), $validCarts);

        $orderInfo = UserOrderModel::findOrderByOrderId($ret['result']['orderId']);
        if (empty($orderInfo)) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '创建订单失败', '', ['orderId' => '']);
            return ;
        }

        $orderDesc = $goodsList[0]['goodsName'];
        if (count($goodsList) > 1)
            $orderDesc .= '; ' . $goodsList[1]['goodsName'];
        $ret = $this->payOrder($orderInfo, $payType, $orderDesc);
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => $orderInfo['order_id']]);
            return ;
        }
        $this->ajaxReturn(0, '', '', $ret['result']);
    }

    public function quickOrder()
    {
        $this->checkLoginAndNotice();

        $addrId = intval($this->postParam('address_id', 0));
        $goodsId = $this->postParam('cartId', array());
        if (count($goodsId) != 1) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请求参数有误...', '', ['orderId' => '']);
            return 0;
        }
        $payType = intval($this->postParam('pay_type', 0));
        $isCash  = intval($this->postParam('is_cash', 0));
        $couponId  = intval($this->postParam('coupon_id', 0));
        $skuAttr  = trim($this->postParam('skuAttr', ''));
        $skuValue = trim($this->postParam('skuValue', ''));
        $amount = intval($this->postParam('amount', 0));
        $goodsId = $goodsId[0];

        if ($goodsId <= 0
            || $amount <= 0
            || !Check::isSkuAttr($skuAttr)
            || !Check::isSkuValue($skuValue)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请求参数错误');
            return ;
        }

        $orderId = trim($this->postParam('orderId', ''));
        if (!empty($orderId)) {
            $url = '/mall/Pay/payAgain?showwxpaytitle=1&orderId=' . $orderId;
            $this->ajaxReturn(0, '', $url, ['orderId' => '']); // 跳转到待支付
            exit();
        }

        if (OrderModel::checkRepeatOrder($this->userId())) {
            $this->ajaxReturn(ERR_OPT_FREQ_LIMIT, '请不要重复提交订单...', '', ['orderId' => '']);
            return ;
        }


        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (empty($goodsInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该商品无效');
            return ;
        }
        $goodsList = array(
            array(
                'goodsId' => $goodsId,
                'amount' => $amount,
                'skuAttr' => $skuAttr,
                'skuValue' => $skuValue,
                'category_id' => $goodsInfo['category_id'],
                'goodsName' => $goodsInfo['name'],
                'attach' => '',
            )
        );
        $ret = OrderModel::doCreateOrder(
            UserOrderModel::ORDER_PRE_COMMON,
            UserOrderModel::ORDER_ENV_WEIXIN,
            $this->userId(),
            $addrId,
            $goodsList,
            $isCash,
            $payType,
            $couponId,
            ''
        );
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => '']);
            return ;
        }

        $orderInfo = UserOrderModel::findOrderByOrderId($ret['result']['orderId']);
        if (empty($orderInfo)) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '创建订单失败', '', ['orderId' => '']);
            return ;
        }
        $ret = $this->payOrder($orderInfo, $payType, $goodsInfo['name']);
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => '']);
            return ;
        }
        $this->ajaxReturn(0, '', '', $ret['result']);
    }

    public function payAgain()
    {
        $this->checkLoginAndNotice();

        $orderId = trim($this->getParam('orderId', ''));
        $orderInfo = UserOrderModel::findOrderByOrderId($orderId);
        if (empty($orderInfo)) {
            $this->showNotice('订单不存在', '/user/Order');
            return ;
        }
        if ($orderInfo['pay_state'] == PayModel::PAY_ST_SUCCESS) {
            header('Location: /user/Order/toTakeDelivery');
            return ;
        }
        $orderGoods = OrderGoodsModel::fetchOrderGoodsById($orderId);
        if (empty($orderGoods)) {
            $this->showNotice('订单商品不存在', '/user/Order');
            return ;
        }

        $goodsList = array();
        foreach ($orderGoods as $goods) {
            $goodsInfo = GoodsModel::findGoodsById($goods['goods_id']);
            if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
                continue ;
            }
            $data = array(
                    'id' => $goods['goods_id'],
                    'imageUrl' => $goodsInfo['image_url'],
                    'name' => $goodsInfo['name'],
                    'sku' => $goods['sku_attr'] . '：' . $goods['sku_value'],
                    'salePrice' => number_format($goods['price'], 2, '.', ''),
                    'amount' => $goods['amount'],

                    // for calc price
                    'skuAttr' => $goods['sku_attr'],
                    'skuValue' => $goods['sku_value'],
                    'goodsId' => $goods['goods_id'],
            );
            $goodsList[] = $data;
        }

        $ret = $this->showPayPage('payagain', '/mall/Pay/doPayAgain', $goodsList, [], $orderInfo);
        if ($ret['code'] != 0) {
            $this->showNotice($ret['desc'], '/mall/Cart');
            return ;
        }
    }
    public function doPayAgain()
    {
        $this->checkLoginAndNotice();

        $orderId = trim($this->postParam('orderId', ''));
        $payType = intval($this->postParam('pay_type', 0));
        $isCash  = intval($this->postParam('is_cash', 0));

        if (empty($orderId)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误', '', ['orderId' => $orderId]);
            return ;
        }
        if (PayModel::checkPayType($payType) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '支付类型不支持', '', ['orderId' => $orderId]);
            return ;
        }

        $orderInfo = UserOrderModel::findOrderByOrderId($orderId);
        if (empty($orderInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '订单不存在', '', ['orderId' => $orderId]);
            return ;
        }
        $orderGoods = OrderGoodsModel::fetchOrderGoodsById($orderId);
        if (empty($orderGoods)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '订单商品不存在', '', ['orderId' => $orderId]);
            return ;
        }
        $goodsList = array();
        foreach ($orderGoods as $goods) {
            $goodsInfo = GoodsModel::findGoodsById($goods['goods_id']);
            if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
                continue ;
            }
            $data = array('name' => $goodsInfo['name']);
            $goodsList[] = $data;
        }
        if (empty($goodsList)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '订单商品无效', '', ['orderId' => $orderId]);
            return ;
        }
        if ($orderInfo['ol_pay_type'] != $payType) {
            UserOrderModel::changePayType($this->userId(), $orderId, $payType);
        }
        $newOrderPayId = UserOrderModel::genOrderId(UserOrderModel::ORDER_PRE_PAYMENT, $this->userId());
        if (empty($newOrderPayId)) {
            $this->ajaxReturn(ERR_SYSTEM_BUSY, '系统繁忙，请稍后重试', '', ['orderId' => $orderId]);
            return ;
        }
        $ret = UserOrderModel::changeOrderPayId($this->userId(), $orderId, $newOrderPayId);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '创建支付订单失败', '', ['orderId' => $orderId]);
            return ;
        }
        $orderInfo = UserOrderModel::findOrderByOrderId($orderId); // !!!!

        $orderDesc = $goodsList[0]['name'];
        if (count($goodsList) > 1)
            $orderDesc .= '; ' . $goodsList[1]['name'];
        $ret = $this->payOrder($orderInfo, $payType, $orderDesc);
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => '']);
            return ;
        }
        $this->ajaxReturn(0, '', '', $ret['result']);
    }

    public function wxPayReturn()
    {
        $orderId = $this->getParam('orderId', '');

        $data = array('title' => '', 'payAmount' => '0.00', 'orderId' => '无');
        if (empty($orderId)) {
            $data['title'] = '支付异常';
        } else {
            $orderInfo = UserOrderModel::findOrderByOrderId($orderId);
            $data['orderId'] = $orderId;
            if (empty($orderInfo)) {
                $data['title'] = '支付异常';
            } else {
                $data['title'] = '支付完成';
                if ($orderInfo['pay_state'] == PayModel::PAY_ST_SUCCESS) {
                    $data['title'] = '支付成功';
                }
                $data['payAmount'] = number_format($orderInfo['ol_pay_amount'], 2, '.', '');
            }
        }

        $this->display('wx_pay_return', $data);
    }

    //=
    private function showPayPage($orderType, $action, $goodsList, $goodsInfo, $orderInfo)
    {
        $optResult = array('code' => ERR_OPT_FAIL, 'desc' => '', 'result' => array());
        $ret = OrderModel::calcPrice($this->userId(), $goodsList, 0 /* TODO */);
        if ($ret['code'] != 0)
            return $ret;
        $toPayAmount = (float)$ret['result']['toPayAmount'];
        $couponPayAmount = (float)$ret['result']['couponPayAmount'];
        $orderAmount = (float)$ret['result']['orderAmount'];
        $postage = (float)$ret['result']['postage'];
        $freePostage = (float)$ret['result']['freePostage'];

        $cashAmount = 0.00;
        if (!empty($orderInfo)) {
            $cashAmount = $orderInfo['ac_pay_amount'];
        } else {
            $cashAmount = UserModel::getCash($this->userId());
        }
        $address = UserAddressModel::getDefaultAddr($this->userId());
        if (!empty($address)) {
            $address['fullAddr'] = UserAddressModel::getFullAddr($address)['fullAddr'];
        }
        if (!empty($orderInfo)) {
            $leftTime = UserOrderModel::ORDER_PAY_LAST_TIME - (CURRENT_TIME - (int)$orderInfo['ctime']);
            if ($leftTime < 0)
                $leftTime = 0;
            $orderInfo['leftTime'] = $leftTime;
            $orderInfo['ctime'] = date('Y-m-d H:i:s', $orderInfo['ctime']);
        }
        $data = array(
            'title'     => empty($orderInfo) ? '支付' : '待支付',
            'payLastTime' => (int)(UserOrderModel::ORDER_PAY_LAST_TIME / 60),
            'orderType' => $orderType,
            'orderInfo'   => $orderInfo,
            'goodsList' => $goodsList,
            'address'   => $address,
            'toPayAmount'=> number_format($toPayAmount, 2, '.', ''),
            'orderAmount'=> number_format($orderAmount, 2, '.', ''),
            'postage'   => number_format($postage, 2, '.', ''),
            'freePostage'=>number_format($freePostage, 2, '.', ''),
            'cash'      => number_format($cashAmount, 2, '.', ''),
            'coupon'    => array(),
            'action'    => $action,
        );
        if (!empty($goodsInfo))
            $data['goodsInfo'] = $goodsInfo;

        $this->display('pay', $data);
        $optResult['code'] = 0;
        $optResult['desc'] = '';
        return $optResult;
    }

    private function getValidCartIds($cartIds)
    {
        $cartList = UserCartModel::getCartList($this->userId());
        $validCart = array();
        foreach ($cartList as $cart) {
            if (in_array($cart['id'], $cartIds)) {
                $validCart[] = $cart;
            }
        }
        return $validCart;
    }

    private function payOrder($orderInfo, $payType, $orderDesc)
    {
        if ($payType == PayModel::PAY_TYPE_WX) {
            $ret = $this->wxJsApiPay(
                $this->wxOpenId(),
                $orderInfo['order_id'],
                $orderInfo['order_pay_id'],
                $orderDesc,
                $orderInfo['ol_pay_amount']
            );
            return $ret;
        }
        return array('code' => ERR_PARAMS_ERROR, 'desc' => '支付方式不支持', 'result' => array());
    }

    // 统一下单接口
    private function wxJsApiPay(
        $payOpenId,
        $orderId,
        $orderPayId,
        $orderDesc,
        $totalAmount
    ) {
        $optResult = array('code' => ERR_OPT_FAIL, 'desc' => '', 'result' => array());

        if (EDITION != 'online')
            $totalAmount = 0.01;

        $jsParams = WxSDK::jsApiPay(
            WX_PAY_MCHID,
            WX_PAY_APP_ID,
            WX_PAY_KEY,
            $payOpenId,
            $orderPayId,
            $orderDesc,
            ceil($totalAmount * 100), // 防止超过2位小数
            Util::getIp(),
            APP_URL_BASE . '/pay/PayNotify/wxUnified'
        );
        if ($jsParams === false) {
            $optResult['desc'] = '向微信申请支付失败，稍重试';
            return $optResult;
        }
        $data = array(
            'wxPayParams'=> $jsParams,
            'wxPaySucUrl' => APP_URL_BASE . '/mall/Pay/wxPayReturn?orderId=' . $orderId,
            'orderId' => $orderId
        );
        $optResult['code'] = 0;
        $optResult['desc'] = '';
        $optResult['result'] = $data;
        return $optResult;
    }
}

