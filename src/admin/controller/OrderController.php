<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\user\model\UserOrderModel;
use \src\user\model\UserAddressModel;
use \src\user\model\UserModel;
use \src\pay\model\PayModel;

class OrderController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->display("order_list");
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = UserOrderModel::fetchOrderCount([], [], []);
        $orderList = UserOrderModel::fetchSomeOrder([], [], [], $page, self::ONE_PAGE_SIZE);
        $error = '';
        $searchParams = [];
        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/Order/listPage', $searchParams);
        $data = array(
            'orderList' => $orderList,
            'totalOrderNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("order_list", $data);
    }

    public function search()
    {
        $orderList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $orderId = trim($this->getParam('orderId', ''));
            $beginTime = trim($this->getParam('beginTime', ''));
            $endTime = trim($this->getParam('endTime', ''));
            $phone = trim($this->getParam('phone', ''));
            $rePhone = trim($this->getParam('rePhone', ''));
            if ((!empty($rePhone) && !Check::isPhone($rePhone))
                || (!empty($phone) && !Check::isPhone($phone))) {
                $error = '手机号码无效';
                break;
            }

            if (!empty($orderId)) {
                $searchParams['orderId'] = $orderId;
                $order = UserOrderModel::findOrderByOrderId($orderId, 'r');
                if (!empty($order)) {
                    $orderList[] = $order;
                }
            } else {
                $conds = array();
                $vals = array();
                $rel = array();
                if (!empty($beginTime)) {
                    $searchParams['beginTime'] = $beginTime;
                    $dt = strtotime($beginTime);
                    if ($dt !== false) {
                        $conds[] = 'ctime>=';
                        $vals[] = $dt;
                        if (count($conds) > 1) {
                            $rel[] = 'and';
                        }
                    }
                }
                if (!empty($endTime)) {
                    $searchParams['endTime'] = $endTime;
                    $dt = strtotime($endTime);
                    if ($dt !== false) {
                        $conds[] = 'ctime<=';
                        $vals[] = $dt;
                        if (count($conds) > 1) {
                            $rel[] = 'and';
                        }
                    }
                }
                if (!empty($phone)) {
                    $searchParams['phone'] = $phone;
                    $userInfo = UserModel::findUserByPhone($phone);
                    if (!empty($userInfo)) {
                        $conds[] = 'user_id';
                        $vals[] = $userInfo['id'];
                        if (count($conds) > 1) {
                            $rel[] = 'and';
                        }
                    }
                }
                if (!empty($rePhone)) {
                    $searchParams['rePhone'] = $rePhone;
                    $conds[] = 're_phone';
                    $vals[] = $rePhone;
                    if (count($conds) > 1) {
                        $rel[] = 'and';
                    }
                }
                if (!empty($conds)) {
                    $totalNum = UserOrderModel::fetchOrderCount($conds, $vals, $rel);
                    $orderList = UserOrderModel::fetchSomeOrder(
                        $conds,
                        $vals,
                        $rel,
                        $page,
                        self::ONE_PAGE_SIZE
                    );
                }
            }
        } while(false);
        if (empty($searchParams)) {
            header('Location: /admin/Order/listPage');
            return ;
        }

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Order/search',
            $searchParams
        );
        $data = array(
            'orderList' => $orderList,
            'totalOrderNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("order_list", $data);
    }

    public function info()
    {
        $orderId = trim($this->getParam('orderId', ''));
        $order = UserOrderModel::findOrderByOrderId($orderId, 'r');
        $orderInfo = $order;
        $orderInfo = array(
            'stateDesc' => '无',
            'paymentDesc' => '微信',
            'addr' => $addr,
        );
        $data = array(
            'info' => $orderInfo,
            'orderGoods' => $orderGoods,
            'orderId' => 2323,
            'stateDesc' => '待支付',
        );
        $this->display("order_info", $data);
    }

    public function orderPrint()
    {
        $orderId = trim($this->getParam('orderId', ''));
        $order = UserOrderModel::findOrderByOrderId($orderId, 'r');
        $data = $this->fillPrintOrderInfo($order);
        $this->display('order_print', $data);
    }

    private function fillPrintOrderInfo($order)
    {
        if (empty($order))
            return array();
        $data = array();
        $data['userName'] = '';
        $userInfo = UserModel::findUserById($order['user_id']);
        if (!empty($userInfo))
            $data['userName'] = $userInfo['nickname'];
        $orderInfo = array();
        $orderInfo['ctime'] = $order['ctime'];
        $orderInfo['payType'] = PayModel::payTypeDesc($order['ol_pay_type']);
        $orderInfo['orderId'] = $order['order_id'];
        $orderInfo['payTime'] = '';
        if ($order['pay_state'] == PayModel::PAY_ST_SUCCESS)
            $orderInfo['payTime'] = date('Y-m-d H:i:s', $order['pay_time']);
        else if ($order['pay_state'] == PayModel::PAY_ST_UNPAY)
            $orderInfo['payTime'] = '未支付';
        else if ($order['pay_state'] == PayModel::PAY_ST_PAYING)
            $orderInfo['payTime'] = '支付中';
        else
            $orderInfo['payTime'] = '未知';
        $orderInfo['deliveryTime'] = $order['delivery_time'];
        $orderInfo['fullAddr'] = UserAddressModel::getFullAddr($order);
        $orderInfo['reName'] = $order['re_name'];
        $orderInfo['rePhone'] = $order['re_phone'];
        $orderInfo['orderAmount'] = $order['order_amount'];

        $orderInfo['goodsList'] = array(array('name' => '鲜嫩滑 白豆腐500g', 'price' => 10.00, 'amount' => 23)); // TODO

        $data['order'] = $orderInfo;
        return $data;
    }
}
