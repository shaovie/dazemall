<?php
/**
 * @Author shaowei
 * @Date   2016-01-10
 */

namespace src\job\controller;

use \src\common\Nosql;
use \src\common\Log;
use \src\job\model\AsyncModel;
use \src\mall\model\OrderModel;
use \src\pay\model\PayModel;

class ReportController extends JobController
{
    protected function run($idx) { }

    public function report()
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
        $stime = strtotime('-1 day', strtotime(date('Y-m-d')));
        $etime = strtotime(date('Y-m-d'));

        $sql = 'select count(*) as oc, sum(order_amount) as oa from o_order where'
            . ' ctime >= ' . $stime . ' and ctime < ' . $etime
            . ' and pay_state = ' . PayModel::PAY_ST_SUCCESS;
        $data DB::getDB('r')->rawQuery($sql);
        if (!empty($data)) {
            $orderNum = $data[0]['oc'];
            $sellerAmount = $data[0]['oa'];
        }
        $data = array(
            'order_num' => $orderNum,
            'seller_amount' => $sellerAmount,
            'ctime' => CURRENT_TIME
        );
        $ret = DB::getDB('w')->insertOne('r_order_per_day', $data);
        if ($ret === false) {
           Log::error('order report per day failed!');
        }
    }

    public function goods()
    {
    }
}

