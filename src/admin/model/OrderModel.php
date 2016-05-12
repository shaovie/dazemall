<?php
/**
 * @Author shaowei
 * @Date   2016-05-10
 */

namespace src\admin\model;

use \src\common\DB;

class OrderModel
{
    public static function fetchSomeOrder($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'o_order',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('asc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchOrderCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'o_order',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }
}
