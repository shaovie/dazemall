<?php
/**
 * @Author shaowei
 * @Date   2016-01-10
 */

namespace src\job\controller;

use \src\common\Nosql;
use \src\common\Log;
use \src\job\model\AsyncModel;
use \src\user\model\UserOrderModel;
use \src\mall\model\OrderModel;
use \src\user\model\WxUserModel;
use \src\pay\model\PayModel;

class CancelOrderController extends JobController
{
    protected function run($idx) { }

    public function patrolCancelOrder()
    {
        if (date('H:i') != '05:30')
            return ;

        $orderList = UserOrderModel::fetchSomeOrder(
            array('ctime<', 'pay_state', 'order_state'),
            array(CURRENT_TIME - (UserOrderModel::ORDER_PAY_LAST_TIME + 30),
                PayModel::PAY_ST_UNPAY,
                UserOrderModel::ORDER_ST_CREATED),
            array('and', 'and'),
            1, 100
        );

        if (empty($orderList)) {
            return ;
        }
        foreach ($orderList as $order) {
            OrderModel::doCancelOrder($order['user_id'], $order['order_id'], 'timeout, sys cancel');
        }

        sleep(60);
    }

    public function cancel()
    {
        $nk = Nosql::NK_ASYNC_CANCEL_ORDER_QUEUE;
        $beginTime = time();

        do {
            $now = time();
            $size = intval(Nosql::lSize($nk));
            $n = 0;
            do {
                if ($n >= $size) {
                    break;
                }
                $rawMsg = Nosql::lPop($nk);
                if ($rawMsg === false
                    || !isset($rawMsg[0])) {
                    break;
                }
                $n++;
                $data = json_decode($rawMsg, true);
                if ($now - $data['ctime'] > UserOrderModel::ORDER_PAY_LAST_TIME) {
                    $this->doCancel($data);
                } else {
                    Nosql::rPush($nk, $rawMsg);
                }
            } while (true);

            if ($now - $beginTime > 30) { // 30秒脚本重新执行一次
                break;
            }
            sleep(1);
        } while (true);
    }

    private function doCancel($data)
    {
        $orderId = $data['orderId'];
        $orderInfo = UserOrderModel::findOrderByOrderId($orderId);
        if (empty($orderInfo)) {
            return ;
        }
        OrderModel::doCancelOrder($orderInfo['user_id'], $orderId, 'timeout, sys cancel');
    }
}

