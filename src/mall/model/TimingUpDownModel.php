<?php
/**
 * @Author shaowei
 * @Date   2015-12-27
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;

class TimingUpDownModel
{
    const TT_TYPE_ONCE      = 1;   // 一次性
    const TT_TYPE_EVERYDAY  = 2;   // 每天

    const OT_TYPE_UP        = 1;   // 上架
    const OT_TYPE_DOWN      = 2;   // 下架

    const ST_UNSET          = 0;
    const ST_SET_OK         = 1;
    const ST_SET_RESUME     = 2;

    public static function newOne(
        $goodsId,
        $beginTime,
        $endTime,
        $timingType,
        $optType
    ) {
        $data = array(
            'goods_id' => $goodsId,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'timing_type' => $timingType,
            'opt_type' => $optType,
            'state' => self::ST_UNSET,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_timing_updown', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findTimingUpDownByGoodsId($goodsId)
    {
        if (empty($goodsId)) {
            return array();
        }
        $ret = DB::getDB('r')->fetchOne(
            'm_timing_updown',
            '*',
            array('goods_id'), array($goodsId)
        );
        return $ret === false ? array() : $ret;
    }

    public static function findTimingUpDownById($id)
    {
        if (empty($id)) {
            return array();
        }
        $ret = DB::getDB('r')->fetchOne(
            'm_timing_updown',
            '*',
            array('id'), array($id)
        );
        return $ret === false ? array() : $ret;
    }

    public static function setResumeState($id, $resumeState)
    {
        if (empty($id)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_timing_updown',
            array('resume_state' => $resumeState, 'mtime' => CURRENT_TIME),
            array('id', 'state'), array($id, self::ST_SET_OK),
            array('and'),
            1
        );
        return true;
    }

    public static function setState($id, $state)
    {
        if (empty($id)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_timing_updown',
            array('state' => $state, 'mtime' => CURRENT_TIME),
            array('id'), array($id),
            array(),
            1
        );
        return true;
    }

    public static function update($id, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_timing_updown',
            $data,
            array('id'), array($id),
            false,
            1
        );
        return $ret !== false;
    }

    public static function delTimingUpDown($id)
    {
        $ret = DB::getDB('w')->delete(
            'm_timing_updown',
            array('id'), array($id),
            false,
            1
        );
        return $ret === false ? false : true;
    }
    public static function fetchSomeTimingUpDown($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_timing_updown',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchTimingUpDownCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'm_timing_updown',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function showTimingDesc($v)
    {
        if ($v == self::TT_TYPE_ONCE) {
            return '一次性';
        }
        if ($v == self::TT_TYPE_EVERYDAY) {
            return '每天';
        }
        return 'null';
    }
    public static function showOptTypeDesc($v)
    {
        if ($v == self::OT_TYPE_UP) {
            return '上架';
        }
        if ($v == self::OT_TYPE_DOWN) {
            return '下架';
        }
        return 'null';
    }
    public static function showStateDesc($v)
    {
        if ($v == self::ST_UNSET) {
            return '未开始';
        }
        if ($v == self::ST_SET_OK) {
            return '操作成功';
        }
        if ($v == self::ST_SET_RESUME) {
            return '已恢复';
        }
        return 'null';
    }
}
