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

    public static function getAllEmp()
    {
        $ret = DB::getDB('r')->fetchAll(
            'b_employee',
            '*',
            [], []
        );
        return $ret === false ? array() : $ret;
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
            if (!empty($ret)) {
                Cache::set($ck, json_encode($ret));
            }
        }
        if (empty($ret)) {
            return array();
        }
        $ret['name'] = Util::emojiDecode($ret['name']);
        return $ret;
    }

    public static function update($account, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'b_employee',
            $data,
            array('account'), array($account),
            false,
            1
        );
        self::onUpdateData($account);
        return $ret !== false;
    }

    private static function onUpdateData($account)
    {
        Cache::del(Cache::CK_EMPLOYEE_INFO_FOR_AC . $account);
        self::findEmployeeByAccount($account, 'w');
    }

    public static function onLoginOk($account)
    {
        Session::setEmpSession($account);
    }

    public static function onLogout($account)
    {
        Session::delEmpSession($account);
    }
}

