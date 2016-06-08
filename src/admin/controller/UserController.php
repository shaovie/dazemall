<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\common\DB;
use \src\user\model\UserModel;
use \src\user\model\WxUserModel;
use \src\user\model\UserBillModel;
use \src\user\model\UserCouponModel;

class UserController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->display("user_list");
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = UserModel::fetchUserCount([], [], []);
        $userList = UserModel::fetchSomeUser([], [], [], $page, self::ONE_PAGE_SIZE);
        foreach ($userList as &$user) {
            $wxUserInfo = WxUserModel::findUserByUserId($user['id']);
            $user['openid'] = '';
            if (!empty($wxUserInfo))
                $user['openid'] = $wxUserInfo['openid'];
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/User/listPage',
            $searchParams
        );
        $data = array(
            'userList' => $userList,
            'totalUserNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("user_list", $data);
    }

    public function search()
    {
        $userList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $keyword = trim($this->getParam('keyword', ''));
            if (empty($keyword)) {
                header('Location: /admin/User/listPage');
                return ;
            }
            if (!empty($keyword)) {
                $searchParams['keyword'] = $keyword;
                if (Check::isPhone($keyword)) {
                    $user = UserModel::findUserByPhone($keyword, 'r');
                    if (!empty($user)) {
                        $userList[] = $user;
                        $totalNum = 1;
                    }
                } else if (is_numeric($keyword)) {
                    $user = UserModel::findUserById($keyword, 'r');
                    if (!empty($user)) {
                        $userList[] = $user;
                        $totalNum = 1;
                    }
                } else {
                    $user = UserModel::fetchUserByName($keyword, 'r');
                    if (!empty($user)) {
                        $userList = $user;
                        $totalNum = count($user);
                    }
                }
            }
        } while(false);

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/User/search',
            $searchParams
        );
        $data = array(
            'userList' => $userList,
            'totalUserNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("user_list", $data);
    }

    public function recharge()
    {
        $userId = $this->postParam('uid', 0);
        $money = (float)$this->postParam('money', 0.00);
        $remark = $this->postParam('remark', '');

        if ($money <= 0.0001) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '金额无效');
            return ;
        }

        // 余额退还
        if (DB::getDB('w')->beginTransaction() === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '系统错误');
            return false;
        }
        $ret = UserModel::addCash($userId, $money);
        if ($ret !== true) {
            DB::getDB('w')->rollBack();
            UserModel::onRollback($userId);
            $this->ajaxReturn(ERR_PARAMS_ERROR, '系统错误');
            return false;
        }
        $userCash = UserModel::getCash($userId);
        $ret = UserBillModel::newOne(
            $userId,
            '',
            '',
            UserBillModel::BILL_TYPE_IN,
            UserBillModel::BILL_FROM_SYS_RECHARGE,
            $money,
            $userCash + $money,
            empty($remark) ? $this->account . ' recharge in houtai' : $remark
        );
        if ($ret !== true) {
            DB::getDB('w')->rollBack();
            UserModel::onRollback($userId);
            $this->ajaxReturn(ERR_PARAMS_ERROR, '系统错误');
            return false;
        }
        if (DB::getDB('w')->commit() === false) {
            UserModel::onRollback($userId);
            $this->ajaxReturn(ERR_PARAMS_ERROR, '系统错误');
            return false;
        }
        UserModel::onCommit($userId);
        $this->ajaxReturn(0, '');
    }
    public function giveCoupon()
    {
        $userId = $this->postParam('uid', 0);
        $couponId = (int)$this->postParam('couponId', 0);
        UserCouponModel::giveCoupons($userId, array($couponId));
        $this->ajaxReturn(0, '');
    }
}
