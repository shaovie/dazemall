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

class ActivityModel
{
    const SHOW_AREA_HIDE     = 1; // 隐藏
    const SHOW_AREA_HOME     = 2; // 首页顶部

    public static function newOne(
        $title,
        $showArea,
        $imageUrl,
        $imageUrls,
        $beginTime,
        $endTime,
        $sort
    ) {
        $data = array(
            'title' => $title,
            'show_area' => $showArea,
            'image_url' => $imageUrl,
            'image_urls' => $imageUrls,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'sort' => $sort,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_activity', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findActivityById($id)
    {
        if (empty($id)) {
            return array();
        }
        $ret = DB::getDB('r')->fetchOne(
            'm_activity',
            '*',
            array('id'), array($id)
        );
        return $ret === false ? array() : $ret;
    }
    public static function findAllValidActivity($now, $showArea)
    {
        $sql = "select * from m_activity where (begin_time = 0 or begin_time <= $now)"
            . " and (end_time = 0 or end_time > $now)"
            . " and show_area = $showArea order by sort desc";
        $ret = DB::getDB()->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeActivity($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_activity',
            '*',
            $conds, $vals,
            $rel,
            array('sort'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeActivity2($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_activity',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchActivityCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'm_activity',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }
    public static function update($id, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_activity',
            $data,
            array('id'), array($id),
            false,
            1
        );
        return $ret !== false;
    }
    public static function del($id)
    {
        if ($id == 0) {
            return false;
        }
        $ret = DB::getDB('w')->delete(
            'm_activity',
            array('id'), array($id)
        );
        return $ret === false ? array() : $ret;
    }
    public static function showAreaDesc($area)
    {
        if ($area == self::SHOW_AREA_HIDE) {
            return '隐藏';
        }
        if ($area == self::SHOW_AREA_HOME) {
            return '首页';
        }
        return 'null';
    }
}
