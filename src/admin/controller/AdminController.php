<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\common\Session;
use \src\common\Nosql;
use \src\admin\model\EmployeeModel;

class AdminController extends BaseController
{
    protected $account = '';

    public function __construct()
    {
        parent::__construct();

        $this->module = 'admin';

        $this->autoLogin();

        if (!$this->hadLogin()) {
            $this->toLogin();
        }
    }

    protected function autoLogin()
    {
        if ($this->doLogin() === -1) {
            $this->toLogin();
        }
    }

    public function hadLogin()
    {
        return !empty($this->account);
    }

    protected function doLogin()
    {
        $key = Session::getSid('emp');
        $employeeInfo = Nosql::get(Nosql::NK_ADMIN_SESSOIN . $key);
        if (!empty($employeeInfo)) {
            $userAgent = '';
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
            }
            $employeeInfo = json_decode($employeeInfo, true);
            if ($employeeInfo['userAgent'] != $userAgent) {
                return false;
            }
            if (!empty($employeeInfo['account'])) {
                $this->account = $employeeInfo['account'];
                return true;
            }
            return false;
        }
        return -1;
    }

    protected function doLogout()
    {
        $key = Session::getSid('emp');
        Nosql::del(Nosql::NK_ADMIN_SESSOIN . $key);
    }
    protected function toLogin()
    {
        header('Location: /admin/Login');
    }

}
