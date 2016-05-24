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
use \src\user\model\UserModel;
use \src\user\model\UserOrderModel;
use \src\user\model\UserCartModel;
use \src\user\model\UserAddressModel;
use \src\mall\model\CartModel;
use \src\mall\model\OrderModel;
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

        $validCart = $this->getValidCartIds($cartIds);
        if (empty($validCart)) {
            echo '<h1>购物车数据错误</h1>';
            return ;
        }

        $goodsList = array();
        foreach ($validCart as $cart) {
            $data = CartModel::fillCartGoodsInfo($cart);
            if (empty($data))
                continue;
            $goodsList[] = $data;
        }
        $ret = $this->showPayPage('cart', '/mall/Pay/cartOrder', $goodsList, array());
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
            $this->showNotice('请求参数错误', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }

        $goodsSKU = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
        if (empty($goodsSKU)) {
            $this->showNotice('请选择商品SKU', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }

        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID) {
            $this->showNotice('该商品无效SKU', '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }
        $goodsSku = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
        if (empty($goodsSku)) {
            $this->showNotice('商品规格无效SKU', '/mall/Goods/detail?goodsId=' . $goodsId);
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
            )
        );
        $goodsInfo = array(
            'skuAttr' => $skuAttr,
            'skuValue' => $skuValue,
            'amount' => $amount,
        );
        $ret = $this->showPayPage('quickbuy', '/mall/Pay/quickOrder', $goodsList, $goodsInfo);
        if ($ret['code'] != 0) {
            $this->showNotice($ret['desc'], '/mall/Goods/detail?goodsId=' . $goodsId);
            return ;
        }
    }

    // 购物车结算方式下单
    public function cartOrder()
    {
        $this->checkLoginAndNotice();

        if (OrderModel::checkRepeatOrder($this->userId())) {
            $this->ajaxReturn(ERR_OPT_FREQ_LIMIT, '请不要重复提交订单...', '', ['orderId' => '']);
            return ;
        }

        $addrId = intval($this->postParam('address_id', 0));
        $cartIds = $this->postParam('ids', array());
        $payType = intval($this->postParam('pay_type', 0));
        $isCash  = intval($this->postParam('is_cash', 0));
        $couponId  = intval($this->postParam('coupon_id', 0));
        $addrId = 1;// TODO

        $orderId = trim($this->postParam('orderId', ''));
        if (!empty($orderId)) {
            $url = '/mall/Pay/payAgain?showwxpaytitle=1&orderId=' . $orderId;
            $this->ajaxReturn(0, '', $url, ['orderId' => '']); // 跳转到待支付
            exit();
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

        $orderDesc = $goodsList[0]['goodsName'];
        if (count($goodsList) > 1)
            $orderDesc .= '; ' . $goodsList[1]['goodsName'];
        $ret = $this->wxJsApiPay(
            $this->wxOpenId(),
            $ret['result']['orderId'],
            $orderDesc,
            $ret['result']['olPayAmount']
        );
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => '']);
            return ;
        }
        $this->ajaxReturn(0, '', '', $ret['result']);
    }

    public function quickOrder()
    {
        $this->checkLoginAndNotice();

        if (OrderModel::checkRepeatOrder($this->userId())) {
            $this->ajaxReturn(ERR_OPT_FREQ_LIMIT, '请不要重复提交订单...', '', ['orderId' => '']);
            return ;
        }

        $addrId = intval($this->postParam('address_id', 0));
        $goodsId = $this->postParam('ids', array());
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
        $addrId = 1;// TODO
        $goodsId = 14;// TODO

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

        $ret = $this->wxJsApiPay(
            $this->wxOpenId(),
            $ret['result']['orderId'],
            $goodsInfo['name'],
            $ret['result']['olPayAmount']
        );
        if ($ret['code'] != 0) {
            $this->ajaxReturn($ret['code'], $ret['desc'], '', ['orderId' => '']);
            return ;
        }
        $this->ajaxReturn(0, '', '', $ret['result']);
    }

    public function payAgain()
    {
    }

    //=
    private function showPayPage($orderType, $action, $goodsList, $goodsInfo)
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

        $cashAmount = UserModel::getCash($this->userId());
        $address = UserAddressModel::getDefaultAddr($this->userId());
        if (!empty($address)) {
            $address['fullAddr'] = UserAddressModel::getFullAddr($address);
        }
        $data = array(
            'orderType' => $orderType,
            'orderId'   => '',
            'goodsList' => $goodsList,
            'address'   => $address,
            'orderAmount'=> $orderAmount,
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

    // 统一下单接口
    private function wxJsApiPay(
        $payOpenId,
        $orderId,
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
            $orderId,
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
            'wxPaySucUrl' => APP_URL_BASE . '/pay/Pay/wxPayReturn',
            'orderId' => $orderId
        );
        $optResult['code'] = 0;
        $optResult['desc'] = '';
        $optResult['result'] = $data;
        return $optResult;
    }
}

