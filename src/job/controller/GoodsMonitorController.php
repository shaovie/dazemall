<?php
/**
 * @Author shaowei
 * @Date   2016-01-10
 */

namespace src\job\controller;

use \src\common\Nosql;
use \src\common\Log;
use \src\common\DB;
use \src\job\model\AsyncModel;
use \src\mall\model\GoodsModel;
use \src\user\model\WxUserModel;
use \src\mall\model\GlobalConfigModel;

class GoodsMonitorController extends JobController
{
    protected function run($idx) { }

    public function kucunAlarm()
    {
        $cfg = GlobalConfigModel::getConfig();
        $kucunAlarm = (int)$cfg['kucun_alarm'];
        if ($kucunAlarm < 0) {
            $kucunAlarm = 0;
        }
        $sql = 'select id,goods_id,sum(amount) as s from g_goods_sku'
            . ' group by goods_id having s<=' . $kucunAlarm;
        $goodsList = DB::getDB('r')->rawQuery($sql);
        if (empty($goodsList))
            return ;

        foreach ($goodsList as $goods) {
            $nk = Nosql::NK_GOODS_SOLD_OUT . $goods['goods_id'];
            $ret = Nosql::get($nk);
            if ($ret === false)
                $this->notifyUsers($cfg, $goods);
            Nosql::setEx($nk, Nosql::NK_GOODS_SOLD_OUT_EXPIRE, $goods['s']);
        }

        sleep(40);
    }

    public function notifyUsers($cfg, $goods)
    {
        if (empty($cfg['kucun_alarm_users'])
            || empty($cfg['kucun_alarm_tpl'])) {
            return;
        }
        $users = explode(',', $cfg['kucun_alarm_users']);
        foreach ($users as $user) {
            $wxUserInfo = WxUserModel::findUserByUserId($user);
            if (!empty($wxUserInfo['openid'])) {
                $tplMsg['touser'] = $wxUserInfo['openid'];
                $tplMsg['template_id'] = $cfg['kucun_alarm_tpl'];
                $tplMsg['url'] = '';
                $tplMsg['topcolor'] = '#FF0000';
                $tplMsg['data'] = array(
                    'first'    => array('value' => '有1件商品的库存数量低于' . $cfg['kucun_alarm']
                        . '件，请及时补充库存\n\n', 'color' => '#173177'),
                    'keyword1' => array('value' => $goods['goods_id'], 'color' => '#173177'),
                    'keyword2' => array('value' => GoodsModel::goodsName($goods['goods_id']),
                        'color' => '#173177'),
                    'keyword3' => array('value' => $goods['s'], 'color' => '#173177'),
                    'remark' => array('value' => "\n\n请您及时付款以免过期，前往支付>>", 'color' => '#173177')
                );
                AsyncModel::asyncSendTplMsg($wxUserInfo['openid'], $tplMsg, 0);
            }
        }
    }
}

