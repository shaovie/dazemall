<?php
/**
 * @Author shaowei
 * @Date   2015-12-24
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;

class GoodsDetailModel
{
    public static function newOne($goodsId, $detail, $imageUrls)
    {
        if (empty($goodsId)) {
            return false;
        }
        $data = array(
            'goods_id' => $goodsId,
            'detail' => $detail,
            'image_urls' => $imageUrls,
            'ctime' => CURRENT_TIME,
            'mtime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('g_goods_detail', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function update($goodsId, $data)
    {
        if (empty($goodsId) || empty($data)) {
            return false;
        }
        $ret = DB::getDB('w')->update(
            'g_goods_detail',
            $data,
            array('goods_id'), array($goodsId),
            false,
            1
        );
        self::onUpdateData($goodsId);
        return $ret !== false;
    }

    public static function findGoodsDetailById($goodsId, $fromDb = 'w')
    {
        if (empty($goodsId)) {
            return array();
        }
        $ck = Cache::CK_GOODS_DETAIL_INFO . $goodsId;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'g_goods_detail',
                '*',
                array('goods_id'), array($goodsId)
            );
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_GOODS_DETAIL_INFO_EXPIRE, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function getSortedImageUrls($imageUrls)
    {
        // {"1":{"sort":1,"url":"xx"}}
        if (empty($imageUrls)) {
            return array();
        }
        $imageUrls = json_decode($imageUrls, true);
        $func = function($v1, $v2) {
            if ($v1['sort'] == $v2['sort']) {
                return 0;
            }
            return $v1['sort'] > $v2['sort'] ? 1 : -1;
        };
        usort($imageUrls, $func);
        return $imageUrls;
    }

    private static function onUpdateData($goodsId)
    {
        Cache::del(Cache::CK_GOODS_DETAIL_INFO . $goodsId);
        self::findGoodsDetailById($goodsId, 'w');
    }
}

