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

    public function doc()
    {
        $this->display('doc');
    }

    public function edit()
    {
        $freePostage = floatval($this->postParam('freePostage', ''));
        $postage = floatval($this->postParam('postage', ''));
        $kucunAlarm = intval($this->postParam('kucun_alarm', 0));
        $kucunAlarmUsers = trim($this->postParam('kucun_alarm_users', ''));
        $kucunAlarmTpl = trim($this->postParam('kucun_alarm_tpl', ''));
        $searchKey = trim($this->postParam('search_key', ''));
        $wxShareTitle = trim($this->postParam('wx_share_title', ''));
        $wxShareDesc = trim($this->postParam('wx_share_desc', ''));
        $wxShareImg = trim($this->postParam('wx_share_img', ''));

        if ($freePostage < 0.0 || $postage < 0.0 || $kucunAlarm < 0) {
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
            'kucun_alarm' => $kucunAlarm,
            'kucun_alarm_users' => $kucunAlarmUsers,
            'kucun_alarm_tpl' => $kucunAlarmTpl,
            'search_key' => $searchKey,
            'wx_share_title' => $wxShareTitle,
            'wx_share_desc' => $wxShareDesc,
            'wx_share_img' => $wxShareImg,
        );
        GlobalConfigModel::update($updateData);

        $this->ajaxReturn(0, '操作成功', '/admin/GlobalConfig/info');
    }
}

