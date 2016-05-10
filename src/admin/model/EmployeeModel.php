<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\admin\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;
use \src\common\Session;

class EmployeeModel
{
    public static function newOne(
        $account,
        $passwd,
        $phone,
        $name
    ) {
        $data = array(
            'account' => $account,
            'phone' => $phone,
            'passwd' => $passwd,
            'name' => Util::emojiEncode($name),
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('b_employee', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return (int)$ret;
    }

    public static function findEmployeeByAccount($account, $fromDb = 'w')
    {
        if (empty($account)) {
            return array();
        }
        $ck = Cache::CK_EMPLOYEE_INFO_FOR_AC . $account;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'b_employee',
                '*',
                array('account'), array($account)
            );
            if ($ret !== false) {
                Cache::set($ck, json_encode($ret));
            }
        }
        if (empty($ret)) {
            return array();
        }
        $ret['name'] = Util::emojiDecode($ret['name']);
        return $ret;
    }

    public static function onLoginOk($account)
    {
        Session::setEmpSession($account);
    }
}

