<?php
/**
 * @Author shaowei
 * @Date   2016-01-10
 */

namespace src\job\controller;

use \src\common\Nosql;
use \src\common\Log;
use \src\common\DB;
use \src\job\model\AsyncModel;
use \src\mall\model\OrderModel;
use \src\mall\model\goodsModel;
use \src\pay\model\PayModel;

class ReportController extends JobController
{
    protected function run($idx) { }

    public function create()
    {
        if (date('H:i') != '05:20') {
            return ;
        }
        $this->order();
        $this->goods();

        sleep(60);
    }

    public function order()
    {
        $orderNum = 0;
        $sellerAmount = 0;
        $btime = strtotime('-1 day', strtotime(date('Y-m-d')));
        $etime = strtotime(date('Y-m-d'));

        $sql = 'select count(*) as oc, sum(order_amount) as oa from o_order where'
            . ' ctime >= ' . $btime . ' and ctime < ' . $etime
            . ' and pay_state = ' . PayModel::PAY_ST_SUCCESS;
        $data = DB::getDB('r')->rawQuery($sql);
        if (!empty($data)) {
            $orderNum = $data[0]['oc'];
            $sellerAmount = $data[0]['oa'];
        }
        $data = array(
            'order_num' => $orderNum,
            'seller_amount' => $sellerAmount,
            'begin_time' => $btime,
            'end_time' => $etime - 1,
            'ctime' => CURRENT_TIME
        );
        $ret = DB::getDB('w')->insertOne('r_order_per_day', $data);
        if ($ret === false) {
           Log::error('order report per day failed!');
        }
    }

    public function goods()
    {
        $btime = strtotime('-1 day', strtotime(date('Y-m-d')));
        $etime = strtotime(date('Y-m-d'));
        $sql = 'select order_id from o_order where ctime >= ' . $btime . ' and ctime < ' . $etime
            . ' and pay_state = ' . PayModel::PAY_ST_SUCCESS;
        $data = DB::getDB('r')->rawQuery($sql);
        if (!empty($data)) {
            $orderId = array();
            foreach($data as $val) {
                array_push($orderId, "'" . $val['order_id'] . "'");
            }
            $sql = 'SELECT goods_id,sum(amount) num,sum(price*amount) total_price,sku_attr,sku_value from o_order_goods where'
                . ' order_id in(' . implode(',' , $orderId) . ')'
                . ' GROUP BY goods_id,sku_attr,sku_value';
            $data = DB::getDB('r')->rawQuery($sql);
            if (!empty($data)) {
               foreach($data as $val) {
                  $report = array(
                      'goods_id' => $val['goods_id'],
                      'sku_attr' => $val['sku_attr'],
                      'sku_value' => $val['sku_value'],
                      'seller_num' => $val['num'],
                      'seller_amount' => $val['total_price'],
                      'begin_time' => $btime,
                      'end_time' => $etime - 1,
                      'ctime' => CURRENT_TIME
                  );
                  $ret = DB::getDB('w')->insertOne('r_goods_per_day', $report);
                  if ($ret === false) {
                     Log::error('goods(' . $val['goods_id'] . ') report per day failed!');
                  }

               }
            }
        }
    }
}

