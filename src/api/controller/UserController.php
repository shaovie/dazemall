<?php
/**
 * @Author shaowei
 * @Date   2015-12-26
 */

namespace src\api\controller;

use \src\common\Check;
use \src\common\Log;
use \src\common\Nosql;
use \src\user\model\UserCouponModel;
use \src\user\model\UserCartModel;
use \src\user\model\UserBillModel;
use \src\mall\model\GoodsSKUModel;

class UserController extends ApiController
{
    public function walletList()
    {
        $this->checkLoginAndNotice();

        $page = intval($this->getParam('page', 1));
        if ($page < 1)
            $page = 1;
        $type = intval($this->getParam('type', 0));

        $pageSize = 20;

        $dataList = array();
        if ($type == 0) {
            $dataList = UserBillModel::getSomeBill(
                array('user_id'), array($this->userId()),
                false,
                $page, $pageSize
            );
        } else if ($type == 1) {
            $dataList = UserBillModel::getSomeBill(
                array('user_id', 'bill_type'), array($this->userId(), UserBillModel::BILL_TYPE_IN),
                array('and'),
                $page, $pageSize
            );
        } else if ($type == 2) {
            $dataList = UserBillModel::getSomeBill(
                array('user_id', 'bill_type'), array($this->userId(), UserBillModel::BILL_TYPE_OUT),
                array('and'),
                $page, $pageSize
            );
        }
        foreach ($dataList as &$val) {
            $val['amount'] = number_format($val['amount'], 2, '.', '');
            $val['desc'] = UserBillModel::getDesc($val['bill_from']);
            $val['ctime'] = date('m-d H:i', $val['ctime']);
        }
        $this->ajaxReturn(0, '', '', array('data' => $dataList));
    }

    public function getOrderCouponList()
    {
        $this->checkLoginAndNotice();

        $code = $this->getParam('code', '');
        $nk = Nosql::NK_PAY_ORDER_COUPON_CODE . $this->userId() . ':' . $code;
        $ret = Nosql::get($nk);
        if (empty($ret)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请求参数错误');
            return ;
        }

        $goodsList = json_decode($ret, true);

        $data = array();
        $couponList = UserCouponModel::getAvalidCouponListForOrder($this->userId(), $goodsList);
        $data['list'] = $couponList;
        $this->ajaxReturn(0, '', '', $data);
    }

    public function wxShareLog()
    {
        $shareType = $this->postParam('type', 0);
        $shareParams = $this->postParam('params', '');

        $this->ajaxReturn(0, '');
    }
}
