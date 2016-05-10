<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\mall\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\common\SMS;
use \src\common\Nosql;

class RegisterController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->module = 'user';
    }

    public function index()
    {
        $this->display('xx');
    }
}

