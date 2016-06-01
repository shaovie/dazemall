<?php
/**
 * @Author shaowei
 * @Date   2015-12-26
 */

namespace src\api\controller;

use \src\common\Check;
use \src\common\Log;

class UserController extends ApiController
{
    public static wxShareLog()
    {
        Log::rinfo(json_encode($_POST));
        $shareType = $this->postParmas('type', 0);
        $shareParams = $this->postParmas('params', '');
        $this->ajaxReturn(0, '');
    }
}
