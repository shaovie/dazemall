<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\common;

class Session
{
    public static $cookie = array(
        'pre' => COOKIE_PREFIX . '_',
        'path'=> '/',
        'domain' => '',
        'expire' => 25920000 // 10 个月
    );

    public static function getSid($key, $domain)
    {
        $key = self::$cookie['pre'] . $key;
        if (!empty($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        $r = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '')
            . Util::getIP() . CURRENT_TIME;
        $value = md5($r);
        setcookie(
            $key,
            $value,
            CURRENT_TIME + self::$cookie['expire'],
            self::$cookie['path'],
            $domain
        );
        return $value;
    }

    public static function setUserSession(
        $userId,
        $wxOpenId
    ) {
        $data['userId'] = $userId;
        $data['wxOpenId'] = $wxOpenId;
        $data['userAgent'] = isset($_SERVER['HTTP_USER_AGENT']) ?
            $_SERVER['HTTP_USER_AGENT'] : '';
        $key = self::getSid('user', APP_HOST);
        Nosql::set(Nosql::NK_USER_SESSOIN . $key, json_encode($data));
    }

    public static function setEmpSession($account)
    {
        $data['account'] = $account;
        $data['userAgent'] = isset($_SERVER['HTTP_USER_AGENT']) ?
            $_SERVER['HTTP_USER_AGENT'] : '';
        $key = self::getSid('emp', HT_HOST);
        Nosql::set(Nosql::NK_ADMIN_SESSOIN . $key, json_encode($data));
    }

    public static function delEmpSession($account)
    {
        $key = self::getSid('emp', HT_HOST);
        Nosql::del(Nosql::NK_ADMIN_SESSOIN . $key);
    }
}

