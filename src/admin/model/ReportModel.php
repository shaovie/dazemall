<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\admin\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;
use \src\common\Session;

class ReportModel
{
    public static function getOrderReport($btime, $etime)
    {
        $ret = DB::getDB('r')->fetchAll(
            'r_order_per_day',
            '*',
            array('begin_time>=', 'end_time<='), array($btime, $etime),
            array('and'),
            array('ctime'), array('desc')
        );

        return $ret === false ? array() : $ret;
    }

    public static function getGoodsReport($btime, $etime)
    {
        $ret = DB::getDB('r')->fetchAll(
            'r_goods_per_day',
            '*',
            array('begin_time>=', 'end_time<='), array($btime, $etime),
            array('and'),
            array('seller_num'), array('desc')
        );

        return $ret === false ? array() : $ret;
    }
}

