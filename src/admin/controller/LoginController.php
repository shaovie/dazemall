<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\mall\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\admin\model\EmployeeModel;

class LoginController extends BaseController
{
    // view
    public function index()
    {
        $this->display('xx');
    }
    public function in()
    {
        $account = $this->postParam('account', '');
        $passwd = $this->postParam('passwd', '');
        if (Check::isName($account)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的账号无效');
            return ;
        }
        if (Check::isPasswd($passwd)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '密码格式不正确');
            return ;
        }

        $employeeInfo = EmployeeModel::findEmployeeByAccount($account);
        if (empty($employeeInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该账号未注册~');
            return ;
        }
        if ($employeeInfo['passwd'] != md5($passwd)) {
            $this->ajaxReturn(ERR_PASSWD_ERROR, '您输入的密码不正确，请重新输入');
            return ;
        }
        EmployeeModel::onLoginOk($employeeInfo['id']);
        $this->ajaxReturn(0, '登录成功', '/admin/Home');
    }
}

