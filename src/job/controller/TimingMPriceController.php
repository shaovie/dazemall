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
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\TimingMPriceModel;

class TimingMPriceController extends JobController
{
    protected function run($idx) { }

    public function doit()
    {
        $beginTime = time();
        do {
            $now = time();

            $this->modifyPrice();
            $this->resumePrice();

            if ($now - $beginTime > 30) { // 30秒脚本重新执行一次
                break;
            }
            usleep(500000);
        } while (true);
    }
    public function modifyPrice()
    {
        $data = DB::getDB('r')->fetchSome(
            'm_timing_mprice',
            '*',
            array('goods_sku_id>', 'state', 'begin_time<='),
            array(0, TimingMPriceModel::ST_UNSET, time()),
            array('and', 'and'),
            array('begin_time'), array('asc'),
            array(200)
        );
        if (empty($data)) {
            return ;
        }
        foreach ($data as $item) {
            $skuInfo = GoodsSKUModel::findSkuIfnoById($item['goods_sku_id'], 'w');
            if (!empty($skuInfo)) {
                $oldPrice = $skuInfo['sale_price'];
                GoodsSKUModel::setSalePrice(
                    $item['goods_sku_id'],
                    $skuInfo['goods_id'],
                    $item['to_price'],
                    'sys_timing_mprice set'
                );
                if ($item['synch_sale_price'] == 1)
                    GoodsModel::updateGoodsInfo($skuInfo['goods_id'],
                        array('sale_price' => $item['to_price']));
                TimingMPriceModel::setState($item['id'], TimingMPriceModel::ST_SET_OK);
                TimingMPriceModel::setResumePrice($item['id'], $oldPrice);
            }
        }
    }

    public function resumePrice()
    {
        $data = DB::getDB('r')->fetchSome(
            'm_timing_mprice',
            '*',
            array('goods_sku_id>', 'state', 'end_time<='),
            array(0, TimingMPriceModel::ST_SET_OK, time()),
            array('and', 'and'),
            array(), array(),
            array(200)
        );
        if (empty($data)) {
            return ;
        }
        foreach ($data as $item) {
            $skuInfo = GoodsSKUModel::findSkuIfnoById($item['goods_sku_id'], 'w');
            if (!empty($skuInfo)) {
                GoodsSKUModel::setSalePrice(
                    $item['goods_sku_id'],
                    $skuInfo['goods_id'],
                    $item['resume_price'],
                    'sys_timing_mprice resume'
                );
                if ($item['synch_sale_price'] == 1)
                    GoodsModel::updateGoodsInfo($skuInfo['goods_id'],
                        array('sale_price' => $item['resume_price']));
                TimingMPriceModel::setState($item['id'], TimingMPriceModel::ST_SET_RESUME);
            }
        }
    }
}

