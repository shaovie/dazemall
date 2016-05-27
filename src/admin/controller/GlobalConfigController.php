<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\mall\model\GlobalConfigModel;

class GlobalConfigController extends AdminController
{
    public function info()
    {
        $info = GlobalConfigModel::getConfig();

        $data['info'] = $info;
        $data['action'] = '/admin/GlobalConfig/edit';
        $this->display('global_config', $data);

    }

    public function edit()
    {
        $freePostage = floatval($this->postParam('freePostage', ''));
        $postage = floatval($this->postParam('postage', ''));

        if ($freePostage < 0.0 || $postage < 0.0) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '数据无效');
            return ;
        }

        if ($this->account != 'admin') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您无权操作');
            return ;
        }

        $updateData = array(
            'free_postage' => $freePostage,
            'postage' => $postage,
        );
        GlobalConfigModel::update($updateData);

        $this->ajaxReturn(0, '操作成功', '/admin/GlobalConfig/info');
    }
}

