<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

class LogoutController extends AdminController
{
    public function index()
    {
        $this->doLogout();
        $this->toLogin();
    }
}

