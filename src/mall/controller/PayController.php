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
        $this->checkLoginAndNotice();

        $cartIds = $this->postParam('cartId', array());

        $cartList = UserCartModel::getCartList($this->userId());
        $validCart = array();
        foreach ($cartList as $cart) {
            if (in_array($cart['id'], $cartIds)) {
                $validCart[] = $cart;
            }
        }
        if (empty($validCart)) {
            echo '<h1>购物车数据错误</h1>';
            return ;
        }

        $this->showPayPage($cartList);
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
            ? 0.00 : number_format($globalConfig['postage'], 2, '.', '');
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
        );
        $this->display('pay', $data);
    }
}

