<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\TimingUpDownModel;
use \src\mall\model\GoodsModel;

class TimingUpDownController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = TimingUpDownModel::fetchTimingUpDownCount([], [], []);
        $timingList = TimingUpDownModel::fetchSomeTimingUpDown([], [], [], $page, self::ONE_PAGE_SIZE);
        foreach ($timingList as &$item) {
            $item['timingDesc'] = TimingUpDownModel::showTimingDesc($item['timing_type']);
            $item['optDesc'] = TimingUpDownModel::showOptTypeDesc($item['opt_type']);
            $item['stateDesc'] = TimingUpDownModel::showStateDesc($item['state']);
            $item['goodsName'] = GoodsModel::goodsName($item['goods_id']);
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/TimingUpDown/listPage',
            $searchParams
        );

        $data = array(
            'timingList' => $timingList,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("timingupdown_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增',
            'info' => array(),
            'action' => '/admin/TimingUpDown/add',
        );
        $this->display('timingupdown_info', $data);
    }
    public function add()
    {
        $error = '';
        $info = array();
        $ret = $this->fetchFormParams($info, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $ret = TimingUpDownModel::findTimingUpDownByGoodsId($info['goods_id']);
        if (!empty($ret)) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '该商品已经存在');
            return ;
        }

        $newId = TimingUpDownModel::newOne(
            $info['goods_id'],
            $info['begin_time'],
            $info['end_time'],
            $info['timing_type'],
            $info['opt_type']
        );
        if ($newId === false || (int)$newId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/TimingUpDown/listPage');
    }
    public function editPage()
    {
        $id = intval($this->getParam('id', 0));

        $action = '/admin/TimingUpDown/edit';
        $info = TimingUpDownModel::findTimingUpDownByGoodsId($id);
        if (empty($info)) {
            $action = '/admin/TimingUpDown/add';
            if ($id > 0)
                $info['goods_id'] = $id;
        }
        $data = array(
            'title' => '编辑',
            'info' => $info,
            'action' => $action,
        );
        $this->display('timingupdown_info', $data);
    }
    public function edit()
    {
        $error = '';
        $info = array();
        $updateData = array();

        $ret = $this->fetchFormParams($info, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }
        $ret = TimingUpDownModel::findTimingUpDownById($info['id']);
        if (!empty($ret) && $ret['state'] == TimingUpDownModel::ST_SET_OK) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '正在进行中不可编辑', '');
            return ;
        }
        if (!empty($ret) && $ret['state'] == TimingUpDownModel::ST_SET_RESUME) {
            $updateData['state'] = TimingUpDownModel::ST_UNSET;
        }
        $updateData['opt_type'] = $info['opt_type'];
        $updateData['timing_type'] = $info['timing_type'];
        $updateData['begin_time'] = $info['begin_time'];
        $updateData['end_time'] = $info['end_time'];
        $ret = TimingUpDownModel::update($info['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/TimingUpDown/listPage');
    }
    public function del()
    {
        $id = $this->getParam('id', 0);
        if ($id == 0) {
            header('Location: /admin/TimingUpDown/listPage');
            return ;
        }
        TimingUpDownModel::delTimingUpDown($id);
        header('Location: /admin/TimingUpDown/listPage');
    }
    private function fetchFormParams(&$info, &$error)
    {
        $info['id'] = intval($this->postParam('id', 0));
        $info['goods_id'] = intval($this->postParam('goods_id', 0));
        $info['opt_type'] = intval($this->postParam('optType', 0));
        $info['timing_type'] = intval($this->postParam('timingType', 0));
        $info['begin_time'] = trim($this->postParam('beginTime', ''));
        $info['end_time'] = trim($this->postParam('endTime', ''));

        if ($info['opt_type'] != TimingUpDownModel::OT_TYPE_UP
            && $info['opt_type'] != TimingUpDownModel::OT_TYPE_DOWN) {
            $error = '请选择操作类型';
            return false;
        }
        if ($info['timing_type'] != TimingUpDownModel::TT_TYPE_ONCE
            && $info['timing_type'] != TimingUpDownModel::TT_TYPE_EVERYDAY) {
            $error = '请选择定时类型';
            return false;
        }
        if (empty($info['begin_time'])) {
            $error = '开始时间格式错误';
            return false;
        }
        if (empty($info['end_time'])) {
            $error = '结束时间格式错误';
            return false;
        }
        $etime = strtotime($info['end_time']);
        $btime = strtotime($info['begin_time']);
        if ($info['timing_type'] == TimingUpDownModel::TT_TYPE_EVERYDAY) {
            $info['begin_time'] = '2016-01-01 ' . date('H:i:s', $btime);
            $info['end_time'] = '2016-01-01 ' . date('H:i:s', $etime);
            $etime = strtotime($info['end_time']);
            $btime = strtotime($info['begin_time']);
        }
        if ($btime === false) {
            $error = '开始时间格式错误';
            return false;
        }
        if ($etime === false) {
            $error = '结束时间格式错误';
            return false;
        }
        if ($etime <= $btime) {
            $error = '结束时间不能小于等于开始时间';
            return false;
        }

        return true;
    }

}
