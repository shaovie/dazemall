<?php
/**
 * @Author shaowei
 * @Date   2015-12-27
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\DB;
use \src\common\Log;

class ActivityGoodsModel
{
    public static function newOne(
        $actId,
        $goodsId,
        $sort
    ) {
        $data = array(
            'act_id' => $actId,
            'goods_id' => $goodsId,
            'sort' => $sort,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_activity_goods', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }
    public static function getAllGoods($actId)
    {
        if (empty($actId)) {
            return array();
        }
        $ret = DB::getDB()->fetchAll(
            'm_activity_goods',
            '*',
            array('act_id'), array($actId),
            array(),
            array('id'), array('desc')
        );
        return $ret === false ? array() : $ret;
    }
    public static function findSomeValidActGoods($actId, $beginTime, $endTime, $nextId, $size)
    {
        if (empty($actId) || $size <= 0) {
            return array();
        }
        $ret = DB::getDB()->fetchSome(
            'm_activity_goods',
            '*',
            array('act_id', 'begin_time >=', 'end_time <', 'id>'), array($actId, $beginTime, $endTime, $nextId),
            array('and', 'and', 'and'),
            array('id'), array('asc'),
            array($size)
        );
        return $ret === false ? array() : $ret;
    }
    public static function getGoodsInfo($actId, $goodsId)
    {
        if (empty($actId) || empty($goodsId)) {
            return array();
        }
        $ret = DB::getDB()->fetchOne(
            'm_activity_goods',
            '*',
            array('act_id', 'goods_id'), array($actId, $goodsId),
            array('and')
        );
        return $ret === false ? array() : $ret;
    }
    public static function update($actId, $goodsId, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_activity_goods',
            $data,
            array('act_id', 'goods_id'), array($actId, $goodsId),
            false,
            1
        );
        return $ret !== false;
    }
    public static function del($id, $goodsId)
    {
        if ($id == 0 || $goodsId == 0) {
            return false;
        }
        $ret = DB::getDB('w')->delete(
            'm_activity_goods',
            array('act_id', 'goods_id'), array($id, $goodsId),
            array('and')
        );
        return $ret === false ? array() : $ret;
    }
}
