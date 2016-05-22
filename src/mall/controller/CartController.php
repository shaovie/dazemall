<?php
/**
 * @Author shaowei
 * @Date   2015-12-23
 */

namespace src\mall\controller;

use \src\common\Check;
use \src\mall\model\CartModel;
use \src\mall\model\GoodsSKUModel;
use \src\user\model\UserCartModel;

class CartController extends MallController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $allTotalPrice = 0;
        $cartList = CartModel::getCartList($this->userId(), $allTotalPrice);
        $data['cartList'] = $cartList;
        $data['allTotalPrice'] = $allTotalPrice;
        $this->display('cart', $data);
    }
}

