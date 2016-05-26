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

class EmployeeController extends AdminController
{
    // view
    public function addPage()
    {
        $data['title'] = '新增用户';
        $data['action'] = '/admin/Employee/add';
        $this->display('emp_info', $data);
    }

    public function info()
    {
        $account = $this->getParam('account', '');

        $employeeInfo = EmployeeModel::findEmployeeByAccount($account);
        if (empty($employeeInfo)) {
            echo '该账号未注册~';
            return ;
        }

        $data['title'] = '修改用户信息';
        $data['emp'] = $employeeInfo;
        $data['action'] = '/admin/Employee/edit';
        $this->display('emp_info', $data);

    }

    public function edit()
    {
        $account = $this->postParam('account', '');
        $name = $this->postParam('name', '');
        $phone = $this->postParam('phone', '');
        $passwd = $this->postParam('passwd', '');
        if (!Check::isName($account)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '账号无效');
            return ;
        }
        if (!Check::isName($name)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的姓名无效');
            return ;
        }
        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的手机号无效');
            return ;
        }
        if (!Check::isPasswd($passwd)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的密码无效');
            return ;
        }

        if ($this->account != $account && $this->account != 'admin') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您无权编辑');
            return ;
        }

        $employeeInfo = EmployeeModel::findEmployeeByAccount($account);
        if (empty($employeeInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该账号未注册~');
            return ;
        }
        $updateData = array('passwd' => md5($passwd), 'name' => $name, 'phone' => $phone);
        EmployeeModel::update($account, $updateData); 

        $this->ajaxReturn(0, '操作成功', '/admin/Employee/info?account=' . $account);
    }

    public function add()
    {
        $account = $this->postParam('account', '');
        $name = $this->postParam('name', '');
        $phone = $this->postParam('phone', '');
        if (!Check::isName($account)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的账号无效');
            return ;
        }
        if (!Check::isName($name)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的姓名无效');
            return ;
        }
        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的手机号无效');
            return ;
        }

        if ($this->account != 'admin') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您无权操作');
            return ;
        }

        $employeeInfo = EmployeeModel::findEmployeeByAccount($account);
        if (!empty($employeeInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该账号已注册~');
            return ;
        }

        $passwd = '123456';
        $empId = EmployeeModel::newOne($account, md5($passwd), $phone, $name); 

        $this->ajaxReturn(0, '操作成功，密码：' . $passwd, '/admin/Employee/info?account=' . $account);
    }

    public function listPage()
    {
        $data['empList'] = EmployeeModel::getAllEmp();
        $this->display('emp_list', $data);
    }

    public function stateOpt()
    {
        $account = $this->getParam('account', '');
        $employeeInfo = EmployeeModel::findEmployeeByAccount($account);
        if (empty($employeeInfo)) {
            echo '该账号未注册~';
            return ;
        }
        if ($this->account != 'admin') {
            echo '您无权操作';
            return ;
        }
        if ($this->account == $account) {
            echo '不能对自己进行操作';
            return ;
        }
        $updateData = array('state' => 0);
        if ($employeeInfo['state'] == 0) {
            $updateData = array('state' => 1);
        }
        EmployeeModel::update($account, $updateData); 
        EmployeeModel::onLogout($account);
        header('Location: /admin/Employee/listPage');
    }
}

