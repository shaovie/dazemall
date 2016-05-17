<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\user\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;
use \src\common\Session;
use \src\user\model\WxUserModel;
use \src\user\model\UserDetailModel;

class UserModel
{
    const USER_ST_DEFAULT = 0; // 用户初始状态

    public static function newOne(
        $phone,
        $passwd,
        $nickname,
        $sex,
        $headimgurl
    ) {
        $wdb = DB::getDB('w');
        if ($wdb->beginTransaction() === false) {
            return false;
        }
        if (!empty($phone)) {
            $ret = self::findUserByPhone($phone, 'w');
            if (!empty($ret)) {
                $wdb->rollBack();
                return false;
            }
        }
        $data = array(
            'phone' => $phone,
            'passwd' => $passwd,
            'nickname' => Util::emojiEncode($nickname),
            'sex' => $sex,
            'headimgurl' => $headimgurl,
            'state' => self::USER_ST_DEFAULT,
            'ctime' => CURRENT_TIME,
            'mtime' => CURRENT_TIME
        );
        $ret = $wdb->insertOne('u_user', $data);
        if ($ret === false || (int)$ret <= 0) {
            $wdb->rollBack();
            return false;
        }
        $userId = $ret;
        $ret = UserDetailModel::newOne($userId);
        if ($ret === false) {
            $wdb->rollBack();
            return false;
        }
        if ($wdb->commit() === false) {
            return false;
        }
        self::onUpdateData($userId);
        return true;
    }

    public static function findUserById($userId, $fromDb = 'w')
    {
        if (empty($userId)) {
            return array();
        }
        $ck = Cache::CK_USER_INFO_FOR_ID . $userId;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'u_user',
                '*',
                array('id'), array($userId)
            );
            if ($ret !== false) {
                Cache::set($ck, json_encode($ret));
            }
        }
        if (empty($ret)) {
            return array();
        }
        $ret['nickname'] = Util::emojiDecode($ret['nickname']);
        return $ret;
    }

    public static function fetchUserByName($nickname, $fromDb = 'w')
    {
        if (empty($nickname)) {
            return array();
        }
        $ret = DB::getDB($fromDb)->fetchSome(
            'u_user',
            '*',
            array('nickname'), array(Util::emojiEncode($nickname))
        );
        if (empty($ret)) 
            return array();
        foreach ($ret as &$user) {
            $user['nickname'] = Util::emojiDecode($user['nickname']);
        }
        return $ret;
    }

    public static function getCash($userId)
    {
        $ret = self::findUserById($userId);
        if (empty($ret)) {
            return 0.0;
        }
        return $ret['cash_amount'];
    }

    // 扣账户余额: return -1 余额不足，return false 系统错误
    public static function reduceCash($userId, $amount)
    {
        $amount = number_format((float)$amount, 2, '.', '');
        if (empty($userId) || $amount <= 0.0001) {
            return false;
        }

        $sql = "update u_user set cash_amount = cash_amount - $amount"
            . " where user_id = $userId and amount >= $amount";
        $ret = DB::getDB('w')->rawExec($sql);
        if ($ret === false) {
            return false;
        }
        self::onUpdateData($userId);
        return $ret > 0 ? true : -1;
    }

    public static function addCash($userId, $amount)
    {
        $amount = number_format((float)$amount, 2, '.', '');
        if (empty($userId) || $amount <= 0.0001) {
            return false;
        }

        $sql = "update u_user set cash_amount = cash_amount + $amount"
            . " where user_id = $userId";
        $ret = DB::getDB('w')->rawExec($sql);
        if ($ret === false) {
            return false;
        }
        self::onUpdateData($userId);
        return $ret > 0 ? true : -1;
    }

    public static function findUserByPhone($phone, $fromDb = 'w')
    {
        if (empty($phone)) {
            return array();
        }
        $ck = Cache::CK_USER_INFO_FOR_PHONE . $phone;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'u_user',
                '*',
                array('phone'), array($phone)
            );
            if ($ret !== false) {
                Cache::set($ck, json_encode($ret));
            }
        }
        if (empty($ret)) {
            return array();
        }
        $ret['nickname'] = Util::emojiDecode($ret['nickname']);
        return $ret;
    }

    public static function fetchSomeUser($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'u_user',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchUserCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'u_user',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    private static function onUpdateData($userId)
    {
        Cache::del(Cache::CK_USER_INFO_FOR_ID . $userId);
        $userInfo = self::findUserById($userId, 'w');
        if (!empty($userInfo['phone'])) {
            Cache::del(Cache::CK_USER_INFO_FOR_PHONE . $userInfo['phone']);
            self::findUserByPhone($userInfo['phone'], 'w');
        }
    }

    //= 业务逻辑
    public static function onLoginOk($userId, $wxOpenId)
    {
        Session::setUserSession($userId, $wxOpenId);
    }

    public static function getRandomNickname($prefix)
    {
        return $prefix . Util::getRandomStr(5); // TODO
    }

    public static function bindWeixin()
    {
    }
}

