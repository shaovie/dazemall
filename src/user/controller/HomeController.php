<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\user\controller;

use \src\common\Util;
use \src\common\Check;
use \src\user\model\UserModel;
use \src\user\model\UserCouponModel;

class HomeController extends UserController 
{
    public function index()
    {
        $headimgurl = empty($this->userInfo['headimgurl']) ? '' : $this->userInfo['headimgurl'];
        $user = array(
            'imageUrl' => Util::wxSmallHeadImgUrl($headimgurl),
            'nickname' => empty($this->userInfo) ? '未注册' : $this->userInfo['nickname'],
            'phone' => empty($this->userInfo['phone']) ? '未绑定手机号' : $this->userInfo['phone'],
            'cash' => empty($this->userInfo) ? '0.00' : number_format($this->userInfo['cash_amount'], 2, '.', ''),
            'couponAmount' => UserCouponModel::getUnusedCouponCount($this->userId()),
            'jifen' => 0,
            'id' => $this->userId(),
        );
        $data['user'] = $user;
        $this->display('home', $data);
    }

    public function address()
    {
        $this->display('address');
    }
}
