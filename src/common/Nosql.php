<?php
/**
 * @Author shaowei
 * @Date   2015-07-18
 */

namespace src\common;

class Nosql
{
    private static $nosql = false;

    //= define keys
    // format   name1:[name2:]
    // 缓存KEY的前缀已经在Redis中配置过了，这里就不需要加了
    const NK_MONITOR_LOG             = 'monitor_log:';        const NK_MONITOR_LOG_EXPIRE = 60;
    const NK_USER_SESSOIN            = 'user_session:';       const NK_USER_SESSOIN_EXPIRE = 2592000;
    const NK_ADMIN_SESSOIN           = 'admin_session:';      const NK_ADMIN_SESSOIN_EXPIRE = 2592000;

    //= for pay
    const NK_PAY_NOTIFY_DE_DUPLICATION  = 'pay_notify_de_duplication:';
    const NK_PAY_NOTIFY_DE_DUPLICATION_EXPIRE = 86400;

    const NK_WX_UNIFIED_PAY_UNSUBSCRIBE = 'wx_unified_pay_unsubscribe:';
    const NK_WX_UNIFIED_PAY_UNSUBSCRIBE_EXPIRE = 86400;

    const NK_ORDER_ATTACH_INFO       = 'order_attach_info:'; const NK_ORDER_ATTACH_INFO_EXPIRE = 86400;
    const NK_ORDER_ID_RECORD         = 'order_id_record:'; const NK_ORDER_ID_RECORD_EXPIRE = 86400;
    const NK_ASYNC_ORDER_RESULT      = 'async_order_result:'; const NK_ASYNC_ORDER_RESULT_EXPIRE = 3600;
    const NK_LIMIT_ORDER_FREQ        = 'limit_order_freq:'; const NK_LIMIT_ORDER_FREQ_EXPIRE = 6;

    //= async job queue
    const NK_ASYNC_EMAIL_QUEUE       = 'async_email_queue:';
    const NK_ASYNC_WX_EVENT_QUEUE    = 'async_wx_event_queue:';
    const NK_ASYNC_SEND_WX_MSG_QUEUE = 'async_send_wx_msg_queue:';
    const NK_ASYNC_TIMEDSEND_WX_MSG_QUEUE = 'async_timedsend_wx_msg_queue:';
    const NK_ASYNC_SMS_QUEUE         = 'async_sms_queue:';
    const NK_ASYNC_DB_OPT_QUEUE      = 'async_db_opt_queue:';
    const NK_ASYNC_ORDER_QUEUE       = 'async_order_queue:';
    const NK_ASYNC_ORDER_CANCEL_QUEUE = 'async_order_cancel_queue:';
    const NK_ASYNC_ORDER_PAY_REMIND_QUEUE = 'async_order_pay_remind_queue:';
    const NK_ASYNC_CANCEL_ORDER_QUEUE = 'async_cancel_order_queue:';
    
    //= for weixin
    const NK_ACTIVATE_FOR_GZH        = 'activate_for_gzh:'; const NK_ACTIVATE_FOR_GZH_EXPIRE = 120;

    //= for user
    const NK_REG_SMS_CODE            = 'reg_sms_code:'; const NK_REG_SMS_CODE_EXPIRE = 1800;

    //= public static methods
    //
    private static function getNosql()
    {
        if (self::$nosql == false) {
            self::$nosql = new Redis(REDIS_NOSQL_HOST, REDIS_NOSQL_PORT, NOSQL_PREFIX . ':');
        }
        return self::$nosql;
    }
    public static function get($key)
    {
        return self::getNosql()->get($key);
    }
    public static function mGet($key)
    {
        return self::getNosql()->Get($key);
    }
    public static function set($key, $v)
    {
        return self::getNosql()->set($key, $v);
    }
    public static function setNx($key, $v)
    {
        return self::getNosql()->setNx($key, $v);
    }
    public static function setEx($key, $expire/*sec*/, $v)
    {
        return self::getNosql()->setEx($key, $expire, $v);
    }
    public static function expire($key, $expire/*sec*/)
    {
        return self::getNosql()->expire($key, $expire);
    }
    public static function setTimeout($key, $timeout/*sec*/)
    {
        return self::getNosql()->setTimeout($key, $timeout);
    }
    public static function del($key)
    {
        return self::getNosql()->del($key);
    }
    public static function incr($key)
    {
        return self::getNosql()->incr($key);
    }
    public static function lPush($key, $v)
    {
        return self::getNosql()->lPush($key, $v);
    }
    public static function rPush($key, $v)
    {
        return self::getNosql()->rPush($key, $v);
    }
    public static function lPop($key)
    {
        return self::getNosql()->lPop($key);
    }
    public static function lRange($key, $start, $end)
    {
        return self::getNosql()->lRange($key, $start, $end);
    }
    public static function lSize($key)
    {
        return self::getNosql()->lSize($key);
    }
    public static function lTrim($key, $start, $stop)
    {
        return self::getNosql()->lTrim($key, $start, $stop);
    }
}
