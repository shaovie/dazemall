<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\admin\model\OrderModel;
use \src\user\model\UserOrderModel;
use \src\user\model\UserModel;

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

        $totalNum = OrderModel::fetchOrderCount([], []);
        $orderList = OrderModel::fetchSomeOrder([], [], [], $page, self::ONE_PAGE_SIZE);
        $this->showOrderList(
            $totalNum,
            $orderList,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Order/listPage',
            array(),
            array(),
            ''
        );
    }

    public function search()
    {
        $orderList = array();
        $totalNum = 0;
        $error = '';
        $urlParams = array();
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
                        $urlParams['beginTime'] = $beginTime;
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
                        $urlParams['endTime'] = $endTime;
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
                        $urlParams['phone'] = $phone;
                    }
                }
                if (!empty($rePhone)) {
                    $searchParams['rePhone'] = $rePhone;
                    $conds[] = 're_phone';
                    $vals[] = $rePhone;
                    if (count($conds) > 1) {
                        $rel[] = 'and';
                    }
                    $urlParams['rePhone'] = $rePhone;
                }
                if (!empty($conds)) {
                    $orderList = OrderModel::fetchSomeOrder(
                        $conds,
                        $vals,
                        $rel,
                        $page,
                        self::ONE_PAGE_SIZE
                    );
                }
            }
        } while(false);

        $data = $this->showOrderList(
            100,
            $orderList,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Order/search',
            $urlParams,
            $searchParams,
            $error
        );
    }

    private function showOrderList(
        $totalOrderNum,
        $orderList,
        $curPage,
        $pageSize,
        $url,
        $urlParams,
        $searchParams,
        $error
    ) {
        $data = array(
            'orderList' => $orderList,
            'totalOrderNum' => $totalOrderNum,
            'pageHtml' => $this->pagination($totalOrderNum, $curPage, $pageSize, $url, $urlParams),
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("order_list", $data);
    }
}
