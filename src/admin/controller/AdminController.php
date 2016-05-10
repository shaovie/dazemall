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

class AdminController extends BaseController
{
    protected $employeeInfo = array();

    public function __construct()
    {
        parent::__construct();

        $this->module = 'admin';

        $this->autologin();
    }

    protected function autoLogin()
    {
        if ($this->doLogin() === -1) {
            $this->toLogin();
        }
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
            if (!empty($employeeInfo['id'])) {
                $this->employeeInfo = EmployeeModel::findEmployeeById($employeeInfo['id']);
            }
            return false;
        }
        return -1;
    }

    protected function toLogin()
    {
        header('Location: /admin/Login');
    }

}
