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
    public function wxShareLog()
    {
        Log::rinfo(json_encode($_POST));
        $shareType = $this->postParams('type', 0);
        $shareParams = $this->postParams('params', '');
        $this->ajaxReturn(0, '');
    }
}
