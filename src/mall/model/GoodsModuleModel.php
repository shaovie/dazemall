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

class GoodsModuleModel
{
    public static function newOne(
        $title,
        $imageUrl,
        $beginTime,
        $endTime,
        $sort
    ) {
        $data = array(
            'title' => $title,
            'image_url' => $imageUrl,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'sort' => $sort,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_goods_module', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findGoodsModuleById($moduleId)
    {
        if (empty($moduleId)) {
            return array();
        }
        $ret = DB::getDB('r')->fetchOne(
            'm_goods_module',
            '*',
            array('id'), array($moduleId)
        );
        return $ret === false ? array() : $ret;
    }

    public static function fetchAllValidModule($now)
    {
        $sql = "select * from m_goods_module where (begin_time = 0 or begin_time <= $now)"
            . " and (end_time = 0 or end_time >= $now)"
            . " order by sort desc";
        $ret = DB::getDB()->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }

    public static function update($moduleId, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_goods_module',
            $data,
            array('id'), array($moduleId),
            false,
            1
        );
        return $ret !== false;
    }

    public static function delModule($moduleId)
    {
        $ret = DB::getDB('w')->delete(
            'm_goods_module',
            array('id'), array($moduleId),
            false,
            1
        );
        return $ret === false ? false : true;
    }

    public static function fetchSomeGoodsModule($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_goods_module',
            '*',
            $conds, $vals,
            $rel,
            array('sort'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeGoodsModule2($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_goods_module',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchGoodsModuleCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'm_goods_module',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

}
