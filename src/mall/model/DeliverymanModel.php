<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\mall\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;
use \src\common\Session;

class DeliverymanModel
{
    public static function newOne($phone, $name, $state)
    {
        $data = array(
            'phone' => $phone,
            'name' => Util::emojiEncode($name),
            'state' => $state,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('b_deliveryman', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        self::onUpdateData($ret);
        return (int)$ret;
    }

    public static function findDeliverymanById($id)
    {
        if (empty($id)) {
            return array();
        }
        $ck = Cache::CK_DELIVERYMAN_INFO_FOR_ID . $id;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB('w')->fetchOne(
                'b_deliveryman',
                '*',
                array('id'), array($id)
            );
            if (!empty($ret)) {
                Cache::set($ck, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function getDeliverymanName($id)
    {
        $ret = self::findDeliverymanById($id);
        if (!empty($ret))
            return $ret['name'];
        return '';
    }

    public static function getAllDeliveryman()
    {
        $ck = Cache::CK_ALL_DELIVERYMAN;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB('r')->fetchAll(
                'b_deliveryman',
                '*',
                [], []
            );
            if (!empty($ret)) {
                Cache::set($ck, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function update($id, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'b_deliveryman',
            $data,
            array('id'), array($id),
            false,
            1
        );
        self::onUpdateData($id);
        return $ret !== false;
    }
    private static function onUpdateData($id)
    {
        Cache::del(Cache::CK_DELIVERYMAN_INFO_FOR_ID . $id);
        Cache::del(Cache::CK_ALL_DELIVERYMAN);
    }
}

