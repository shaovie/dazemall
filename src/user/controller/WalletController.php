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
use \src\user\model\UserBillModel;

class WalletController extends UserController
{
    const PAGE_SIZE = 20;

    public function index()
    {
        $couponList = array();

        $allList = UserBillModel::getSomeBill(
            array('user_id'), array($this->userId()),
            false,
            1, self::PAGE_SIZE
        );
        $outList = UserBillModel::getSomeBill(
            array('user_id', 'bill_type'), array($this->userId(), UserBillModel::BILL_TYPE_OUT),
            array('and'),
            1, self::PAGE_SIZE
        );
        $inList = UserBillModel::getSomeBill(
            array('user_id', 'bill_type'), array($this->userId(), UserBillModel::BILL_TYPE_IN),
            array('and'),
            1, self::PAGE_SIZE
        );
        foreach ($allList as &$val) {
            $val['amount'] = number_format($val['amount'], 2, '.', '');
            $val['desc'] = UserBillModel::getDesc($val['bill_from']);
            $val['ctime'] = date('m-d H:i', $val['ctime']);
        }
        foreach ($outList as &$val) {
            $val['amount'] = number_format($val['amount'], 2, '.', '');
            $val['desc'] = UserBillModel::getDesc($val['bill_from']);
            $val['ctime'] = date('m-d H:i', $val['ctime']);
        }
        foreach ($inList as &$val) {
            $val['amount'] = number_format($val['amount'], 2, '.', '');
            $val['desc'] = UserBillModel::getDesc($val['bill_from']);
            $val['ctime'] = date('m-d H:i', $val['ctime']);
        }

        $data = array(
            'cash' => UserModel::getCash($this->userId()),
            'allList' => $allList,
            'outList' => $outList,
            'inList' => $inList,
        );
        $this->display('wallet', $data);
    }
}
