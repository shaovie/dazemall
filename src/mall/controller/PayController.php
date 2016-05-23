<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\mall\controller;

use \src\common\Check;
use \src\user\model\UserModel;
use \src\user\model\UserCartModel;
use \src\user\model\UserAddressModel;
use \src\mall\model\CartModel;
use \src\mall\model\OrderModel;
use \src\mall\model\GlobalConfigModel;

class PayController extends MallController
{
    public function __construct()
    {
        parent::__construct();
    }

    // 购物车结算
    public function cartPay()
    {
        //$this->checkLoginAndNotice();

        $cartIds = $this->postParam('cartId', array());

        $validCart = $this->getValidCartIds($cartIds);
        if (!empty($validCart)) {
            echo '<h1>购物车数据错误</h1>';
            return ;
        }

        $this->showPayPage($cartList);
    }

    public function cartOrder()
    {
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

        $validCart = $this->getValidCartIds($cartIds);
        if (!empty($validCart)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '购物车数据错误', '', ['orderId' => '']);
            return ;
        }

        $address = UserAddressModel::getAddr($this->userId(), $addrId);
        if (empty($address)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请选择收货地址', '', ['orderId' => '']);
            return ;
        }

        $goodsList = array();
        $ret = OrderModel::doCreateOrder(
            UserOrderModel::ORDER_PRE_COMMON,
            UserOrderModel::ORDER_ENV_WEIXIN,
            $this->userId(),
            $address,
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

        $jsParams = PayModel::wxJsApiPay(
        $data = ['wxPayParams'=> $jsParams, 'wxPaySucUrl' => $url, 'orderId' => $orderId];
        $this->ajaxReturn(0, '', '', $data);

    }

    public function payAgain()
    {
    }

    private function showPayPage($cartList)
    {
        $goodsList = array();
        $totalPrice = 0.00;
        $postageTotalPrice = 0.00;
        foreach ($cartList as $cartGoods) {
            $data = CartModel::fillCartGoodsInfo($cartGoods);
            if (empty($data))
                continue;
            $totalPrice += $data['totalPrice'];
            $postageTotalPrice += $data['totalPrice'];
            $goodsList[] = $data;
        }
        $globalConfig = GlobalConfigModel::getConfig();
        $cashAmount = UserModel::getCash($this->userId());
        $address = UserAddressModel::getDefaultAddr($this->userId());
        if (!empty($address)) {
            $address['fullAddr'] = UserAddressModel::getFullAddr($address);
        }
        $postage = $postageTotalPrice >= $globalConfig['free_postage']
            ? '0.00' : number_format($globalConfig['postage'], 2, '.', '');
        $totalPrice += $postage;
        $data = array(
            'orderId'   => '',
            'goodsList' => $goodsList,
            'address'   => $address,
            'totalPrice'=> $totalPrice,
            'postage'   => $postage,
            'freePostage'=>number_format($globalConfig['freePostage'], 2, '.', ''),
            'cash'      => number_format($cashAmount, 2, '.', ''),
            'coupon'    => '',
            'action'    => '/mall/Pay/cartOrder',
        );
        $this->display('pay', $data);
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
        if (!empty($validCart)) {
            echo '<h1>购物车数据错误</h1>';
            return ;
        }
    }
}

