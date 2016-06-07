<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\admin\model\ReportModel;
use \src\mall\model\GoodsModel;

class ReportController extends AdminController
{
    public function order()
    {
        $btime = $this->getParam('btime', '');
        $etime = $this->getParam('etime', '');

        if (!empty($btime))
            $btime = strtotime($btime);
        if (!empty($etime))
            $etime = strtotime($etime);

        if ($btime > 0 && empty($etime))
            $etime = CURRENT_TIME;
        if ($etime > 0 && empty($btime))
            $btime = CURRENT_TIME;
        if (empty($btime) && empty($etime)) {
            $firstday = date('Y-m-01 00:00:00', CURRENT_TIME);
            $lastday = date('Y-m-d 23:59:59', strtotime("$firstday +1 month -1 day"));
            $btime = strtotime($firstday);
            $etime = strtotime($lastday);
        }

        $data = ReportModel::getOrderReport($btime, $etime);
        foreach($data as &$val) {
            $val['ctime'] = date('m月d日', $val['ctime'] - 86400);
        }
        $this->display('order_report', $data);
    }


    public function goods()
    {
        $btime = $this->getParam('btime', '');
        $etime = $this->getParam('etime', '');

        if (!empty($btime))
            $btime = strtotime($btime);
        if (!empty($etime))
            $etime = strtotime($etime);

        if ($btime > 0 && empty($etime))
            $etime = CURRENT_TIME;
        if ($etime > 0 && empty($btime))
            $btime = CURRENT_TIME;
        if (empty($btime) && empty($etime)) {
            $btime = strtotime('-1 day', strtotime(date('Y-m-d')));
            $etime = strtotime(date('Y-m-d')) - 1;
        }

        $data = ReportModel::getGoodsReport($btime, $etime);
        foreach($data as &$val) {
            $goodsInfo = GoodsModel::findGoodsById($val['goods_id']);
            $val['name'] = $goodsInfo['name'];
            $val['ctime'] = date('m月d日', $val['ctime'] - 86400);
        }

        $this->display('goods_report', $data);
    }


}
