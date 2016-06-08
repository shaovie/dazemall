<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Nosql;
use \src\common\Check;
use \src\user\model\UserOrderModel;
use \src\user\model\UserAddressModel;
use \src\user\model\UserModel;
use \src\user\model\WxUserModel;
use \src\pay\model\PayModel;
use \src\job\model\AsyncModel;
use \src\mall\model\OrderGoodsModel;
use \src\mall\model\GoodsModel;
use \src\mall\model\DeliverymanModel;

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
        $totalSoldAmount = UserOrderModel::fetchSoldAmount(['pay_state'], [PayModel::PAY_ST_SUCCESS], []);
        $orderList = UserOrderModel::fetchSomeOrder([], [], [], $page, self::ONE_PAGE_SIZE);
        $orderList = $this->fillOrderListInfo($orderList);
        $error = '';
        $searchParams = [];
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Order/listPage',
            $searchParams
        );
        $data = array(
            'orderList' => $orderList,
            'totalOrderNum' => $totalNum,
            'totalSoldAmount' => $totalSoldAmount,
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
        $totalSoldAmount = -1;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $orderId = trim($this->getParam('orderId', ''));
            $beginTime = trim($this->getParam('beginTime', ''));
            $endTime = trim($this->getParam('endTime', ''));
            $phone = trim($this->getParam('phone', ''));
            $rePhone = trim($this->getParam('rePhone', ''));
            $deliveryState = intval($this->getParam('deliveryState', -1));
            $today = intval($this->getParam('today', 0));
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
                if ($today == 1) {
                    $searchParams['today'] = 1;
                    $dt = strtotime(date('Y-m-d', CURRENT_TIME) . ' 00:00:00');
                    $conds[] = 'ctime>=';
                    $vals[] = $dt;
                    if (count($conds) > 1) {
                        $rel[] = 'and';
                    }
                    $dt += 86400 - 1;
                    $conds[] = 'ctime<=';
                    $vals[] = $dt;
                    if (count($conds) > 1) {
                        $rel[] = 'and';
                    }

                    $tconds = $conds;
                    $tvals = $vals;
                    $trel = $rel;
                    $tconds[] = 'pay_state';
                    $tvals[] = PayModel::PAY_ST_SUCCESS;
                    if (count($tconds) > 1) {
                        $trel[] = 'and';
                    }
                    $totalSoldAmount = UserOrderModel::fetchSoldAmount($tconds, $tvals, $trel);
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
                if ($deliveryState != -1) {
                    $searchParams['deliveryState'] = $deliveryState;
                    $conds[] = 'delivery_state';
                    $vals[] = $deliveryState;
                    if (count($conds) > 1) {
                        $rel[] = 'and';
                    }
                    if ($deliveryState == UserOrderModel::ORDER_DELIVERY_ST_NOT) {
                        $conds[] = 'pay_state';
                        $vals[] = PayModel::PAY_ST_SUCCESS;
                        if (count($conds) > 1) {
                            $rel[] = 'and';
                        }
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

        $orderList = $this->fillOrderListInfo($orderList);
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
        if ($totalSoldAmount >= 0)
            $data['totalSoldAmount'] = $totalSoldAmount;
        $this->display("order_list", $data);
    }

    public function info()
    {
        $orderId = trim($this->getParam('orderId', ''));
        $order = UserOrderModel::findOrderByOrderId($orderId, 'r');
        $data = $this->fillPrintOrderInfo($order);
        $data['deliverymanList'] = DeliverymanModel::getAllDeliveryman();
        $this->display('order_info', $data);
    }

    public function orderPrint()
    {
        $orderId = trim($this->getParam('orderId', ''));
        $order = UserOrderModel::findOrderByOrderId($orderId, 'r');
        $data = $this->fillPrintOrderInfo($order);
        $this->display('order_print', $data);
    }

    public function newOrder()
    {
        $nk = Nosql::NK_PAYOK_ORDER_FOR_NOTIFY_ADMIN_QUEUE;
        $ret = Nosql::lPop($nk);
        echo $ret === false ? '' : $ret;
    }

    // 确认发货
    public function confirmDelivery()
    {
        $orderId = trim($this->postParam('orderId', ''));
        $deliverymanId = $this->postParam('id', '');
        if ($deliverymanId <= 0)
            $deliverymanId = 0;
        UserOrderModel::confirmDelivery($deliverymanId, $orderId);
        $this->ajaxReturn(0, '', '/admin/Order/info?orderId=' . $orderId);
    }
    public function confirmPayOk()
    {
        $orderId = trim($this->postParam('orderId', ''));
        UserOrderModel::manualConfirmPayOk($orderId, $this->account);
        $this->ajaxReturn(0, '', '/admin/Order/info?orderId=' . $orderId);
    }
    public function confirmCancel()
    {
        $orderId = trim($this->postParam('orderId', ''));
        UserOrderModel::manualConfirmCancel($orderId, $this->account);
        $this->ajaxReturn(0, '', '/admin/Order/info?orderId=' . $orderId);
    }
    public function confirmSign()
    {
        $orderId = trim($this->postParam('orderId', ''));
        UserOrderModel::manualConfirmSign($orderId, $this->account);
        $this->ajaxReturn(0, '', '/admin/Order/info?orderId=' . $orderId);
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
        $orderInfo['orderState'] = $order['order_state'];
        $orderInfo['payTime'] = '';
        $orderInfo['payState'] = $order['pay_state'];
        if ($order['pay_state'] == PayModel::PAY_ST_SUCCESS)
            $orderInfo['payTime'] = date('Y-m-d H:i:s', $order['pay_time']);
        else if ($order['pay_state'] == PayModel::PAY_ST_UNPAY)
            $orderInfo['payTime'] = '未支付';
        else
            $orderInfo['payTime'] = '未知';

        $orderInfo['deliveryState'] = $order['delivery_state'];
        $orderInfo['deliveryTime'] = '';
        if (!empty($order['delivery_time'])) 
            $orderInfo['deliveryTime'] = date('Y-m-d H:i:s', $order['delivery_time']);
        $orderInfo['deliverymanName'] = DeliverymanModel::getDeliverymanName($order['deliveryman_id']);
        $fullAddr = UserAddressModel::getFullAddr($order);
        $orderInfo['regionAddr'] = $fullAddr['regionAddr'];
        $orderInfo['detailAddr'] = $fullAddr['detail'];
        $orderInfo['fullAddr'] = $fullAddr['fullAddr'];
        $orderInfo['reName'] = $order['re_name'];
        $orderInfo['rePhone'] = $order['re_phone'];
        $orderInfo['orderAmount'] = $order['order_amount'];
        $orderInfo['sysRemark'] = $order['sys_remark'];
        $orderInfo['remark'] = $order['remark'];
        $orderInfo['postage'] = $order['postage'];
        $orderInfo['couponPayAmount'] = $order['coupon_pay_amount'];

        $goodsList = OrderGoodsModel::fetchOrderGoodsById($order['order_id']);
        foreach($goodsList as &$val) {
            $goodsInfo = GoodsModel::findGoodsById($val['goods_id']);
            if (!empty($goodsInfo)) {
                $val['name'] = $goodsInfo['name'];
                $val['img'] = $goodsInfo['image_url'];
            }
        }
        $orderInfo['goodsList'] = $goodsList;
        $data['order'] = $orderInfo;
        return $data;
    }

    private function fillOrderListInfo($orderList)
    {
        foreach ($orderList as &$order) {
            $order['olPayAmount'] = number_format($order['ol_pay_amount'], 2, '.', '');
            $order['olPayTypeDesc'] = PayModel::payTypeDesc($order['ol_pay_type']);

            $order['payStateDesc'] = $order['pay_state'];
            if ($order['pay_state'] == PayModel::PAY_ST_UNPAY)
                $order['payStateDesc'] = '未支付';
            else if ($order['pay_state'] == PayModel::PAY_ST_SUCCESS)
                $order['payStateDesc'] = '支付成功';

            $order['orderState'] = $order['order_state'];
            $order['orderStateDesc'] = '创建成功';
            if ($order['order_state'] == UserOrderModel::ORDER_ST_FINISHED)
                $order['orderStateDesc'] = '完成';
            else if ($order['order_state'] == UserOrderModel::ORDER_ST_CANCELED)
                $order['orderStateDesc'] = '取消';

            $order['sysRemark'] = $order['sys_remark'];
            $order['deliverymanName'] = '';
            $order['deliveryStateDesc'] = '';
            if ($order['pay_state'] == PayModel::PAY_ST_SUCCESS) {
                if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_NOT)
                    $order['deliveryStateDesc'] = '未发货';
                else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_ING)
                    $order['deliveryStateDesc'] = '发货中';
                else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_RECV)
                    $order['deliveryStateDesc'] = '已签收';
                else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_CONFIRM)
                    $order['deliveryStateDesc'] = '确认收货';
            }
        }
        return $orderList;
    }
}
