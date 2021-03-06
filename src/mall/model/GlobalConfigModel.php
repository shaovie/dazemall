<?php
/**
 * @Author shaowei
 * @Date   2015-12-24
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Log;
use \src\common\DB;

class GlobalConfigModel
{
    public static function getConfig()
    {
        $ck = Cache::CK_MALL_GLOBAL_CONFIG;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB('w')->fetchOne(
                's_global_config',
                '*',
                array(), array()
            );
            if (!empty($ret)) {
                Cache::set($ck, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function searchKey()
    {
        $ret = self::getConfig();
        return $ret['search_key'];
    }

    public static function update($data)
    {
        DB::getDB('w')->update('s_global_config', $data, [], []);
        Cache::del(Cache::CK_MALL_GLOBAL_CONFIG);
    }
}

