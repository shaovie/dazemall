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

class GoodsModuleGListModel
{
    public static function newOne(
        $moduleId,
        $goodsId,
        $sort
    ) {
        $data = array(
            'module_id' => $moduleId,
            'goods_id' => $goodsId,
            'sort' => $sort,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('m_goods_module_glist', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function getAllGoods($moduleId)
    {
        if (empty($moduleId)) {
            return array();
        }
        $ret = DB::getDB()->fetchAll(
            'm_goods_module_glist',
            '*',
            array('module_id'), array($moduleId),
            false,
            array('sort'), array('asc')
        );
        return $ret === false ? array() : $ret;
    }

    public static function getGoodsInfo($moduleId, $goodsId)
    {
        if (empty($moduleId) || empty($goodsId)) {
            return array();
        }
        $ret = DB::getDB()->fetchOne(
            'm_goods_module_glist',
            '*',
            array('module_id', 'goods_id'), array($moduleId, $goodsId),
            array('and')
        );
        return $ret === false ? array() : $ret;
    }

    public static function del($moduleId, $goodsId)
    {
        if ($moduleId == 0 || $goodsId == 0) {
            return false;
        }
        $ret = DB::getDB('w')->delete(
            'm_goods_module_glist',
            array('module_id', 'goods_id'), array($moduleId, $goodsId),
            array('and')
        );
        return $ret === false ? array() : $ret;
    }

    public static function update($moduleId, $goodsId, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'm_goods_module_glist',
            $data,
            array('module_id', 'goods_id'), array($moduleId, $goodsId),
            false,
            1
        );
        return $ret !== false;
    }

    public static function fillGoodsList($moduleList)
    {
        if (empty($moduleList))
            return array();

        $data = array();
        foreach ($moduleList as $module) {
            $goods = self::getAllGoods($module['id']);
            if (empty($goods))
                continue;

            $glist = array();
            foreach ($goods as $g) {
                $ginfo = GoodsModel::findGoodsById($g['goods_id']);
                if (!empty($ginfo) && $ginfo['state'] == GoodsModel::GOODS_ST_UP) {
                    $v['goodsId'] = $ginfo['id'];
                    $v['name'] = $ginfo['name'];
                    $v['imageUrl'] = $ginfo['image_url'];
                    $v['marketPrice'] = number_format($ginfo['market_price'], 2, '.', '');
                    $v['salePrice'] = number_format($ginfo['sale_price'], 2, '.', '');
                    $glist[] = $v;
                }
            }
            
            $r['title'] = $module['title'];
            $r['goodsList'] = $glist;
            $data[] = $r;
        }
        return $data;
    }
}
