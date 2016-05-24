<?php
/**
 * @Author shaowei
 * @Date   2015-07-27
 */

namespace src\user\controller;

use \src\common\Util;
use \src\common\Check;
use \src\user\model\UserModel;
use \src\mall\model\OrderGoodsModel;
use \src\user\model\UserOrderModel;
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
            array('user_id', 'pay_state'), array($this->userId(), PayModel::PAY_ST_UNPAY),
            array('and'),
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
            array('user_id', 'pay_state', 'delivery_state!='),
            array($this->userId(), PayModel::PAY_ST_SUCCESS, UserOrderModel::ORDER_DELIVERY_ST_CONFIRM),
            array('and', 'and'),
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
            array($this->userId(),UserOrderModel::ORDER_ST_CREATED),
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
        $orderId = $this->getParam('order_id', '');
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
        if ($page < 1) {
            $page = 1;
        }
        $data = $this->getOrderList(1, $page);
        $this->ajaxReturn(0, '', '', $data);
    }
    public function toTakeDeliveryOrderListNextPage()
    {
        $page = intval($this->getParam('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $data = $this->getOrderList(2, $page);
        $this->ajaxReturn(0, '', '', $data);
    }
    public function finishedOrderListNextPage()
    {
        $page = intval($this->getParam('page', 1));
        if ($page < 1) {
            $page = 1;
        }
        $data = $this->getOrderList(3, $page);
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
            $val['deliverfyStateDesc'] = '';
            if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_NOT)
                $val['deliverfyStateDesc'] = '未发货';
            else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_ING)
                $val['deliverfyStateDesc'] = '发货中';
            else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_RECV)
                $val['deliverfyStateDesc'] = '已签收';

            $val['orderState'] = $order['order_state'];
            $val['orderStateDesc'] = '创建成功';
            if ($order['order_state'] == UserOrderModel::ORDER_ST_FINISHED)
                $val['orderStateDesc'] = '完成';
            else if ($order['order_state'] == UserOrderModel::ORDER_ST_CREATED)
                $val['orderStateDesc'] = '取消';
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

        $order['fullAddr'] = UserAddressModel::getFullAddr($order);
        $goodsList = OrderGoodsModel::fetchOrderGoodsById($orderId);
        foreach($goodsList as &$val) {
            $goodsInfo = GoodsModel::findGoodsById($val['goods_id']);
            if (!empty($goodsInfo)) {
                $val['name'] = $goodsInfo['name'];
                $val['img'] = $goodsInfo['image_url'];
            }
        }
        $order['goodsList'] = $goodsList;

        $order['deliverfyStateDesc'] = '';
        if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_NOT)
            $order['deliverfyStateDesc'] = '未发货';
        else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_ING)
            $order['deliverfyStateDesc'] = '发货中';
        else if ($order['delivery_state'] == UserOrderModel::ORDER_DELIVERY_ST_RECV)
            $order['deliverfyStateDesc'] = '已签收';

        $order['iPayType'] = '微信';
        if ($order['onlinePayType'] == 1) {
            $order['iPayType'] = '支付宝';
        } else if ($order['onlinePayType'] == 2) {
            $order['iPayType'] = '微信';
        } else if ($order['onlinePayType'] == 3) {
            $order['iPayType'] = '银联';
        }
        $logisticsList = array();
        foreach ($order['productList'] as $product) {
            $skuTexts = explode('|', $product['skuTexts']);
            $skuValuesText = explode('|', $product['skuValuesText']);
            $sku = '';
            foreach ($skuTexts as $key => $val) {
                if (!empty($val)) {
                    $sku .= $val . ':' . $skuValuesText[$key] . ' ';
                }
            }
            $product['iSkuInfo'] = $sku;
            if (!empty($product['logisticsNumber'])
                && strlen($product['logisticsNumber']) > 1) {
                $logisticsId = $product['logisticsNumber'];
                if (!isset($logisticsList[$logisticsId])) {
                    $logisticsList[$logisticsId] = array($product);
                } else {
                    $logisticsList[$logisticsId][] = $product;
                }
            } else {
                // 没有物流编号的就放到一个数组里边集中显示
                if (!isset($logisticsList[0])) {
                    $logisticsList[0] = array($product);
                } else {
                    $logisticsList[0][] = $product;
                }
            }
        }
        unset($order['productList']);
        $order['logisticsList'] = $logisticsList;
        $data = array(
            'order' => $order,
        );
        return $data;
    }
}

