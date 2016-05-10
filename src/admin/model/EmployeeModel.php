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

    public static function findEmployeeById($id, $fromDb = 'w')
    {
        if (empty($id)) {
            return array();
        }
        $ret = DB::getDB($fromDb)->fetchOne(
            'b_employee',
            '*',
            array('id'), array($id)
        );
        if (empty($ret)) {
            return array();
        }
        $ret['name'] = Util::emojiDecode($ret['name']);
        return $ret;
    }

    public static function findEmployeeByAccount($ac, $fromDb = 'w')
    {
        if (empty($ac)) {
            return array();
        }
        $ret = DB::getDB($fromDb)->fetchOne(
            'b_employee',
            '*',
            array('account'), array($account)
        );
        if (empty($ret)) {
            return array();
        }
        $ret['name'] = Util::emojiDecode($ret['name']);
        return $ret;
    }

    public static function onLoginOk($empId)
    {
        Session::setEmpSession($empId);
    }
}

