<?php
/**
 * @Author shaowei
 * @Date   2015-07-27
 */

namespace src\user\controller;

use \src\common\Util;
use \src\common\Check;
use \src\common\Log;
use \src\user\model\UserModel;
use \src\mall\model\OrderGoodsModel;
use \src\user\model\UserOrderModel;
use \src\user\model\UserAddressModel;
use \src\mall\model\GoodsModel;
use \src\pay\model\PayModel;

class OrderController extends UserController
{
    function __construct()
    {
        parent::__construct();

        $this->checkLoginAndNotice();
    }

    public function index()
    {
        $this->toPay();
    }

    // 待支付
    public function toPay()
    {
        $orderList = UserOrderModel::fetchSomeOrder(
            array('user_id', 'pay_state', 'order_state'),
            array($this->userId(), PayModel::PAY_ST_UNPAY, UserOrderModel::ORDER_ST_CREATED),
            array('and', 'and'),
            1,
            5
        );
        $data = $this->fillOrderList($orderList);
        $data['isToPay'] = true;
        $this->display('order_list', $data);
    }

    // 待收货
    public function toTakeDelivery()
    {
        $orderList = UserOrderModel::fetchSomeOrder(
            array('user_id', 'pay_state', 'delivery_state!=', 'delivery_state!='),
            array($this->userId(), PayModel::PAY_ST_SUCCESS, UserOrderModel::ORDER_DELIVERY_ST_CONFIRM, UserOrderModel::ORDER_DELIVERY_ST_RECV),
            array('and', 'and', 'and'),
            1,
            5
        );
        $data = $this->fillOrderList($orderList);
        $data['isToTakeDelivery'] = true;
        $this->display('order_list', $data);
    }
    
    // 订单详情
    public function orderToTakeDelivery()
    {
        $orderId = $this->getParam('orderId', '');
        if (empty($orderId)) {
            $this->showNotice('订单未找到', '/user/Order/toTakeDelivery');
            exit();
        }
        $data = $this->getOrderInfo($orderId);

        if (empty($data)) {
            $this->showNotice('服务器忙，请稍后重试~', '/user/Order/toTakeDelivery');
            exit();
        }
        $this->display('order_detail', $data);
    }

    // 已完成
    public function finished()
    {
        $orderList = UserOrderModel::fetchSomeOrder(
            array('user_id', 'order_state!='),
            array($this->userId(), UserOrderModel::ORDER_ST_CREATED),
            array('and'),
            1,
            5
        );
        $data = $this->fillOrderList($orderList);
        $data['isFinished'] = true;
        $this->display('order_list', $data);
    }

    public function orderFinished()
    {
        $orderId = $this->getParam('orderId', '');
        if (empty($orderId)) {
            $this->showNotice('订单未找到', '/User/MyOrder/finished');
            exit();
        }
        $data = $this->getOrderInfo($orderId);
        if (empty($data)) {
            $this->showNotice('服务器忙，请稍后重试~', '/User/MyOrder/finished');
            exit();
        }
        $this->display('order_detail', $data);
    }
    
    public function confirmTakeDelivery()
    {
        $orderId = $this->postParam('orderId', '');
        if (empty($orderId)) {
            $this->ajaxReturn(1, '参数错误');
            exit();
        }
        $apiData = array(
            'orderId' => $orderId,
        );        
        $response = WeMallApi::confirmTakeDelivery($apiData);
        if (isset($response['success']) && $response['success'] == 1) {
           $url = WE_MALL_URL_BASE . '/User/MyOrder/orderToTakeDelivery?order_id=' . $orderId;
           $this->ajaxReturn(0, '', $url);
        } else {
           $this->ajaxReturn(1, '确认收货-', $response['message']); 
        }
    }

    public function toPayOrderListNextPage()
    {
        $page = intval($this->getParam('page', 1));
        if ($page < 1)
            $page = 1;
        
        $orderList = UserOrderModel::fetchSomeOrder(
            array('user_id', 'pay_state', 'order_state'),
            array($this->userId(), PayModel::PAY_ST_UNPAY, UserOrderModel::ORDER_ST_CREATED),
            array('and', 'and'),
            $page,
            5
        );
        $data = $this->fillOrderList($orderList);
        $this->ajaxReturn(0, '', '', $data);
    }
    public function toTakeDeliveryOrderListNextPage()
    {
        $page = intval($this->getParam('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $orderList = UserOrderModel::fetchSomeOrder(
            array('user_id', 'pay_state', 'delivery_state!='),
            array($this->userId(), PayModel::PAY_ST_SUCCESS, UserOrderModel::ORDER_DELIVERY_ST_CONFIRM),
            array('and', 'and'),
            $page,
            5
        );
        $data = $this->fillOrderList($orderList);
        $this->ajaxReturn(0, '', '', $data);
    }
    public function finishedOrderListNextPage()
    {
        $page = intval($this->getParam('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $orderList = UserOrderModel::fetchSomeOrder(
            array('user_id', 'order_state!='),
            array($this->userId(), UserOrderModel::ORDER_ST_CREATED),
            array('and'),
            $page,
            5
        );
        $data = $this->fillOrderList($orderList);
        $this->ajaxReturn(0, '', '', $data);
    }

    private function fillOrderList($retOrderList)
    {
        $orderList = array();
        foreach ($retOrderList as $order) {
            $val = array();
            $leftTime = UserOrderModel::ORDER_PAY_LAST_TIME - (CURRENT_TIME - (int)$order['ctime']);
            if ($leftTime < 0)
                $leftTime = 0;
            $val['leftTime'] = $leftTime;
            $val['ctime'] = date('Y-m-d H:i:s', $order['ctime']);
            $showImageUrl = '';
            $goodsList = OrderGoodsModel::fetchOrderGoodsById($order['order_id']);
            foreach($goodsList as $v) {
                $goodsInfo = GoodsModel::findGoodsById($v['goods_id']);
                if (!empty($goodsInfo)) {
                    if (empty($showImageUrl)) {
                        $showImageUrl = $goodsInfo['image_url'];
                        break;
                    }
                }
            }
            $val['orderId'] = $order['order_id'];
            $val['showImageUrl'] = $showImageUrl;
            $val['goodsNumber'] = count($goodsList);
            $val['orderAmount'] = number_format($order['order_amount'], 2, '.', '');
            $val['deliveryStateDesc'] = '';
            if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_NOT)
                $val['deliveryStateDesc'] = '未发货';
            else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_ING)
                $val['deliveryStateDesc'] = '发货中';
            else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_RECV)
                $val['deliveryStateDesc'] = '已签收';

            $val['orderState'] = $order['order_state'];
            $val['orderStateDesc'] = '创建成功';
            if ($order['order_state'] == UserOrderModel::ORDER_ST_FINISHED)
                $val['orderStateDesc'] = '已完成';
            else if ($order['order_state'] == UserOrderModel::ORDER_ST_CANCELED)
                $val['orderStateDesc'] = '已取消';
            $orderList[] = $val;
        }
        $data = array(
            'orderList' => $orderList,
        );
        return $data;
    }

    private function getOrderInfo($orderId)
    {
        $order = array();
        $order = UserOrderModel::findOrderByOrderId($orderId);
        if (empty($order)) {
            return array();
        }

        $order['orderId'] = $orderId;
        $order['fullAddr'] = UserAddressModel::getFullAddr($order)['fullAddr'];
        $goodsList = OrderGoodsModel::fetchOrderGoodsById($orderId);
        foreach($goodsList as &$val) {
            $goodsInfo = GoodsModel::findGoodsById($val['goods_id']);
            if (!empty($goodsInfo)) {
                $val['name'] = $goodsInfo['name'];
                $val['img'] = $goodsInfo['image_url'];
            }
        }
        $order['goodsList'] = $goodsList;
        $order['payTypeDesc'] = PayModel::payTypeDesc($order['ol_pay_type']);
        $order['payAmount'] = number_format($order['ol_pay_amount'], 2, '.', '');
        if ((int)($order['order_amount'] * 100) == (int)($order['ac_pay_amount'] * 100)) {
            $order['payTypeDesc'] = '余额支付';
            $order['payAmount'] = number_format($order['ac_pay_amount'], 2, '.', '');
        }
        $order['couponPayment'] = number_format($order['coupon_pay_amount'], 2, '.', '');
        $order['orderAmount'] = number_format($order['order_amount'], 2, '.', '');
        $order['totalPrice'] = number_format($order['order_amount'] - $order['postage'], 2, '.', '');
        $order['acPayAmount'] = number_format($order['ac_pay_amount'], 2, '.', '');

        $order['deliveryStateDesc'] = '';
        if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_NOT)
            $order['deliveryStateDesc'] = '未发货';
        else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_ING)
            $order['deliveryStateDesc'] = '发货中';
        else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_RECV)
            $order['deliveryStateDesc'] = '已签收';
        $order['postage'] = number_format($order['postage'], 2, '.', '');

        $data = array(
            'order' => $order,
        );
        return $data;
    }
}

