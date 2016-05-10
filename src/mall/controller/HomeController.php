<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 */

namespace src\mall\controller;

class HomeController extends MallController
{
    public function index()
    {
        var_dump($this->userInfo);
        var_dump($this->wxUserInfo);
        $this->display('index');
    }
}

