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

class BannerModel
{
    const SHOW_AREA_HOME_TOP = 1; // 首页顶部

    const LINK_TYPE_GOODS = 1; // 链接商品

    public static function newOne(
        $showArea,
        $beginTime,
        $endTime,
        $imageUrl,
        $linkType,
        $linkValue,
        $remark,
        $sort
    ) {
        $data = array(
            'show_area' => $showArea,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'image_url' => $imageUrl,
            'link_type' => $linkType,
            'link_value' => $linkValue,
            'remark' => $remark,
            'sort' => $sort,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_banner', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findBannerById($bannerId)
    {
        if (empty($bannerId)) {
            return array();
        }
        $ret = DB::getDB('r')->fetchOne(
            'm_banner',
            '*',
            array('id'), array($bannerId)
        );
        return $ret === false ? array() : $ret;
    }
    public static function findAllValidBanner($beginTime, $endTime, $showArea)
    {
        $ret = DB::getDB()->fetchAll(
            'm_banner',
            '*',
            array('begin_time >=', 'end_time <', 'show_area'), array($beginTime, $endTime, $showArea),
            array('and', 'and'),
            array('sort'), array('desc')
        );
        return $ret === false ? array() : $ret;
    }

    public static function update($bannerId, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_banner',
            $data,
            array('id'), array($bannerId),
            false,
            1
        );
        return $ret !== false;
    }

    public static function delBanner($bannerId)
    {
        $ret = DB::getDB('w')->delete(
            'm_banner',
            array('id'), array($bannerId),
            false,
            1
        );
        return $ret === false ? false : true;
    }
    public static function fetchSomeBanner($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_banner',
            '*',
            $conds, $vals,
            $rel,
            array('sort'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchSomeBanner2($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'm_banner',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchBannerCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'm_banner',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function showAreaDesc($area)
    {
        if ($area == 1) {
            return '首页顶部';
        }
        return 'null';
    }
}
