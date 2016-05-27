<?php
/**
 * @Author shaowei
 * @Date   2016-05-23
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;

class SkuAttrModel
{
    public static function newOne($attr, $state, $user)
    {
        if (empty($attr)) {
            return false;
        }
        $data = array(
            'attr' => $attr,
            'state' => $state,
            'ctime' => CURRENT_TIME,
            'mtime' => CURRENT_TIME,
            'm_user' => $user,
        );
        $ret = DB::getDB('w')->insertOne('g_sku_attr', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function update($id, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'g_sku_attr',
            $data,
            array('id'), array($id),
            false,
            1
        );
        return $ret !== false;
    }

    public static function findSkuAttrById($id, $fromDb = 'w')
    {
        if (empty($id)) {
            return array();
        }
        $ret = DB::getDB($fromDb)->fetchOne(
            'g_sku_attr',
            '*',
            array('id'), array($id)
        );
        return $ret === false ? array() : $ret;
    }

    public static function findSkuAttrByAttr($attr, $fromDb = 'w')
    {
        if (empty($attr)) {
            return array();
        }
        $ret = DB::getDB($fromDb)->fetchOne(
            'g_sku_attr',
            '*',
            array('attr'), array($attr)
        );
        return $ret === false ? array() : $ret;
    }

    public static function fetchAllSkuAttr()
    {
        $ret = DB::getDB('r')->fetchAll(
            'g_sku_attr',
            '*',
            array('state'), array(1)
        );
        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeSkuAttr($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'g_sku_attr',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchSkuAttrCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'g_sku_attr',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function getStateDesc($state)
    {
        if ($state == 0) {
            return '无效';
        }
        if ($state == 1) {
            return '有效';
        }
        return 'null';
    }

}

