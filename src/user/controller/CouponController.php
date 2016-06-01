<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\user\controller;

use \src\common\Util;
use \src\common\Check;
use \src\common\Log;
use \src\user\model\UserModel;
use \src\user\model\UserCouponModel;

class CouponController extends UserController
{
    public function myCoupon()
    {
        $couponList = array();
        $type = $this->getParam('type', 0);
        if ($type == 0) {
            $couponList = UserCouponModel::getSomeUnusedCoupon($this->userId(), 1, 200);
        } elseif ($type == 1) {
            $couponList = UserCouponModel::getSomeUsedCoupon($this->userId(), 1, 200);
        } elseif ($type == 2) {
            $couponList = UserCouponModel::getSomeExpiredCoupon($this->userId(), 1, 200);
        }

        $data = array(
            'type' => $type,
            'couponList' => $couponList,
        );
        $this->display('my_coupon', $data);
    }
}
