<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\admin\model\EmployeeModel;

class LoginController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->module = 'admin';
    }

    // view
    public function index()
    {
        if ($_SERVER['HTTP_HOST'] != HT_HOST) {
            echo '拒绝访问';
            exit();
        }
        $this->display('login');
    }

    public function in()
    {
        $account = $this->postParam('account', '');
        $passwd = $this->postParam('passwd', '');
        if (!Check::isName($account)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的账号无效');
            return ;
        }
        if (!Check::isPasswd($passwd)) {
            $this->ajaxReturn(ERR_PASSWD_ERROR, '密码格式不正确');
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
        EmployeeModel::onLoginOk($account);
        $this->ajaxReturn(0, '登陆成功', '/admin/Home');
    }
}

