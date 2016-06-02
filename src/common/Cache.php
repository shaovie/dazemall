<?php
/**
 * @Author shaowei
 * @Date   2015-07-18
 */

namespace src\common;

class Cache
{
    private static $cache = false;

    //= define keys
    // format   name1:[name2:]
    // 缓存KEY的前缀已经在Redis中配置过了，这里就不需要加了

    //= for weixin
    const CK_WX_ACCESS_TOKEN         = 'wx_access_token:';        // expire probably 7200-300
    const CK_WX_JSAPI_TICKET         = 'wx_jsapi_ticket:';        // expire probably 7200-300
    const CK_SHORT_URL               = 'short_url:';              // forever
    const CK_WX_TMP_SCENE_QRCODE     = 'wx_tmp_scene_qrcode:'; const CK_WX_TMP_SCENE_QRCODE_EXPIRE = 3600;

    //= for baidu
    const CK_BAIDU_IP_TO_LOCATION    = 'baidu_ip2location:';     // forever
    const CK_BAIDU_LAT_LNG_TO_ADDR   = 'baidu_lat_lng_2_addr:';  // forever
    const CK_BAIDU_CITY_INFO         = 'baidu_city_info:';       // forever
    const CK_BAIDU_WX_GEOCONV        = 'baidu_wx_geoconv:';      // forever
    
    //= for user
    const CK_USER_INFO_FOR_PHONE     = 'user_info_for_phone:';
    const CK_USER_INFO_FOR_ID        = 'user_info_for_id:';
    const CK_WX_USER_INFO            = 'wx_user_info:';
    const CK_WX_USER_INFO_FOR_UID    = 'wx_user_info_for_uid:';
    const CK_USER_DETAIL_INFO        = 'user_detail_info:';
    const CK_USER_ADDR_LIST          = 'user_addr_list:';        // forever
    const CK_ORDER_INFO              = 'order_info:'; const CK_ORDER_INFO_EXPIRE = 7200;
    const CK_CART_LIST               = 'cart_list:'; const CK_CART_LIST_EXPIRE = 86400;
    const CK_GOODS_HAD_LIKE          = 'goods_had_like:'; const CK_GOODS_HAD_LIKE_EXPIRE = 86400;
    const CK_GOODS_LIKE_USERS        = 'goods_like_users'; const CK_GOODS_LIKE_USERS_EXPIRE = 86400;
    const CK_GOODS_COMMENT_HAD_LIKE  = 'goods_comment_had_like:'; const CK_GOODS_COMMENT_HAD_LIKE_EXPIRE = 86400;
    const CK_GOODS_HAD_COMMENT       = 'goods_had_comment:'; const CK_GOODS_HAD_COMMENT_EXPIRE = 86400;
    const CK_GOODS_INFO              = 'goods_info:'; const CK_GOODS_INFO_EXPIRE = 86400;
    const CK_GOODS_DETAIL_INFO       = 'goods_detail_info:'; const CK_GOODS_DETAIL_INFO_EXPIRE = 86400;
    const CK_GOODS_SKU               = 'goods_sku:'; const CK_GOODS_SKU_EXPIRE = 86400;
    const CK_GOODS_CATEGORY_INFO     = 'goods_category_info:'; const CK_GOODS_CATEGORY_INFO_EXPIRE = 86400;
    const CK_COUPON_CFG_INFO         = 'coupon_cfg_info:'; const CK_COUPON_CFG_INFO_EXPIRE = 86400;
    const CK_COUPON_CFG_LIST_INFO    = 'coupon_cfg_list_info:'; const CK_COUPON_CFG_INFO_LIST_EXPIRE = 86400;
    const CK_GOODS_SEARCH_RESULT     = 'goods_search_result:'; const CK_GOODS_SEARCH_RESULT_EXPIRE = 300;
    const CK_ACTIVITY_INFO           = 'activity_info:'; const CK_ACTIVITY_INFO_EXPIRE = 86400;
    const CK_ACTIVITY_LIST           = 'activity_list:'; const CK_ACTIVITY_LIST_EXPIRE = 86400;

    //= for employee
    const CK_EMPLOYEE_INFO_FOR_AC    = 'employee_info_for_ac:';
    const CK_DELIVERYMAN_INFO_FOR_ID = 'deliveryman_info_for_id:';
    const CK_ALL_DELIVERYMAN         = 'all_deliveryman:';

    //= global
    const CK_MALL_GLOBAL_CONFIG      = 'mall_global_config:';
    const CK_COUPON_GIVE_CONFIG      = 'coupon_give_config:';

    //= public static methods
    //
    private static function getCache()
    {
        if (self::$cache == false) {
            self::$cache = new Redis(REDIS_CACHE_HOST, REDIS_CACHE_PORT, CACHE_PREFIX . ':');
        }
        return self::$cache;
    }
    public static function get($key)
    {
        return self::getCache()->get($key);
    }
    public static function mGet($key)
    {
        return self::getCache()->mGet($key);
    }
    public static function set($key, $v)
    {
        return self::getCache()->set($key, $v);
    }
    public static function setEx($key, $expire/*sec*/, $v)
    {
        return self::getCache()->setEx($key, $expire, $v);
    }
    public static function expire($key, $expire/*sec*/)
    {
        return self::getCache()->expire($key, $expire);
    }
    public static function setTimeout($key, $timeout/*sec*/)
    {
        return self::getCache()->setTimeout($key, $timeout);
    }
    public static function del($key)
    {
        return self::getCache()->del($key);
    }
    public static function incr($key)
    {
        return self::getCache()->incr($key);
    }
    public static function lPush($key, $v)
    {
        return self::getCache()->lPush($key, $v);
    }
    public static function rPush($key, $v)
    {
        return self::getCache()->rPush($key, $v);
    }
    public static function lPop($key)
    {
        return self::getCache()->lPop($key);
    }
    public static function lRange($key, $start, $end)
    {
        return $ret;
    }
    public static function lSize($key)
    {
        return self::getCache()->lSize($key);
    }
    public static function lTrim($key, $start, $stop)
    {
        return self::getCache()->lTrim($key, $start, $stop);
    }
}
