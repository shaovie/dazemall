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
    public static function fillGoodsList($actId)
    {
        if (empty($actId))
            return array();

        $data = array();
        $goods = self::getAllGoods($actId);
        if (empty($goods))
            return array();

        $glist = array();
        foreach ($goods as $g) {
            $ginfo = GoodsModel::findGoodsById($g['goods_id']);
            if (!empty($ginfo) && $ginfo['state'] == GoodsModel::GOODS_ST_UP) {
                $v['goodsId'] = $ginfo['id'];
                $v['name'] = $ginfo['name'];
                $v['imageUrl'] = $ginfo['image_url'];
                $v['marketPrice'] = number_format($ginfo['market_price'], 2, '.', '');
                $v['salePrice'] = number_format($ginfo['sale_price'], 2, '.', '');
                $tag = explode('|', $ginfo['tag']);
                if (count($tag) < 2) {
                    $v['tagName'] = '';
                    $v['tagColor'] = 0;
                } else {
                    $v['tagName'] = $tag[0];
                    $v['tagColor'] = $tag[1];
                }
                $glist[] = $v;
            }
        }
        return $glist;
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
