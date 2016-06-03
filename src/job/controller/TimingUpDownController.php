<?php
/**
 * @Author shaowei
 * @Date   2016-05-30
 */

namespace src\job\controller;

use \src\common\Nosql;
use \src\common\Log;
use \src\common\DB;
use \src\job\model\AsyncModel;
use \src\mall\model\GoodsModel;
use \src\mall\model\TimingUpDownModel;

class TimingUpDownController extends JobController
{
    protected function run($idx) { }

    public function doit()
    {
        $beginTime = time();
        do {
            $now = time();

            $this->updownOnce();
            $this->resumeOnce();
            $this->updownEveryday();
            $this->resumeEverday();

            if ($now - $beginTime > 30) { // 30秒脚本重新执行一次
                break;
            }
            usleep(500000);
        } while (true);
    }
    public function updownOnce()
    {
        $data = DB::getDB('r')->fetchSome(
            'm_timing_updown',
            '*',
            array('timing_type', 'state', 'begin_time<='),
            array(TimingUpDownModel::TT_TYPE_ONCE, TimingUpDownModel::ST_UNSET, date('Y-m-d H:i:s')),
            array('and', 'and'),
            array('begin_time'), array('asc'),
            array(200)
        );
        if (empty($data)) {
            return ;
        }
        $this->doModify($data);
    }
    public function updownEveryday()
    {
        $data = DB::getDB('r')->fetchSome(
            'm_timing_updown',
            '*',
            array('timing_type', 'state', 'begin_time<='),
            array(TimingUpDownModel::TT_TYPE_EVERYDAY, TimingUpDownModel::ST_UNSET, '2016-01-01 ' . date('H:i:s')),
            array('and', 'and'),
            array('begin_time'), array('asc'),
            array(200)
        );
        if (empty($data)) {
            return ;
        }
        $this->doModify($data);
    }

    private function doModify($data)
    {
        foreach ($data as $item) {
            $goodsInfo = GoodsModel::findGoodsById($item['goods_id'], 'r');
            if (!empty($goodsInfo)) {
                $oldState = $goodsInfo['state'];
                $toState = 0;
                TimingUpDownModel::setState($item['id'], TimingUpDownModel::ST_SET_OK);
                if (($item['opt_type'] == TimingUpDownModel::OT_TYPE_UP
                        && $oldState != GoodsModel::GOODS_ST_UP)
                    || ($item['opt_type'] == TimingUpDownModel::OT_TYPE_DOWN
                        && $oldState != GoodsModel::GOODS_ST_INVALID
                    )) {
                    $toState = GoodsModel::GOODS_ST_UP;
                    if ($item['opt_type'] == TimingUpDownModel::OT_TYPE_DOWN)
                        $toState = GoodsModel::GOODS_ST_INVALID;
                    GoodsModel::updateGoodsInfo(
                        $item['goods_id'],
                        array('state' => $toState)
                    );
                    TimingUpDownModel::setResumeState($item['id'], $oldState);
                }
            }
        }
    }

    public function resumeOnce()
    {
        $data = DB::getDB('r')->fetchSome(
            'm_timing_updown',
            '*',
            array('timing_type', 'state', 'end_time<='),
            array(TimingUpDownModel::TT_TYPE_ONCE, TimingUpDownModel::ST_SET_OK, date('Y-m-d H:i:s')),
            array('and', 'and'),
            array(), array(),
            array(200)
        );
        if (empty($data)) {
            return ;
        }
        $this->doResume($data);
    }
    public function resumeEverday()
    {
        $data = DB::getDB('r')->fetchSome(
            'm_timing_updown',
            '*',
            array('timing_type', 'state', 'end_time<='),
            array(TimingUpDownModel::TT_TYPE_EVERYDAY, TimingUpDownModel::ST_SET_OK, '2016-01-01 ' . date('H:i:s')),
            array('and', 'and'),
            array(), array(),
            array(200)
        );
        if (empty($data)) {
            return ;
        }
        $this->doResume($data);
    }

    private function doResume($data)
    {
        foreach ($data as $item) {
            $goodsInfo = GoodsModel::findGoodsById($item['goods_id'], 'r');
            if (!empty($goodsInfo)) {
                GoodsModel::updateGoodsInfo(
                    $item['goods_id'],
                    array('state' => $item['resume_state'])
                );
                if ($item['timing_type'] == TimingUpDownModel::TT_TYPE_ONCE)
                    TimingUpDownModel::setState($item['id'], TimingUpDownModel::ST_SET_RESUME);
                else
                    TimingUpDownModel::setState($item['id'], TimingUpDownModel::ST_UNSET);
            }
        }
    }
}

