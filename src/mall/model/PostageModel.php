<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 *
 * @brief  邮费业务处理
 */

namespace src\mall\model;

use \src\mall\model\GlobalConfigModel;

class PostageModel
{
    // calculation postage
    public static function calcPostage($totalPrice, &$freePostage)
    {
        $globalConfig = GlobalConfigModel::getConfig();
        $freePostage = floatval($globalConfig['free_postage']);
        $postage = floatval($globalConfig['postage']);
        return $totalPrice >= $freePostage ? '0.00' : $postage;
    }
}

