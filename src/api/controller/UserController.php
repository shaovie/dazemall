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
        $shareType = $this->postParam('type', 0);
        $shareParams = $this->postParam('params', '');
        $this->ajaxReturn(0, '');
    }
}
