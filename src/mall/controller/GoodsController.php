<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\mall\controller;

use \src\common\WxSDK;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\GoodsDetailModel;

class GoodsController extends MallController
{
    public function detail()
    {
        $goodsId = intval($this->getParam('goodsId', 0));

        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (empty($goodsInfo)) {
            echo '<h1>商品不存在</h1>';
            return ;
        }
        $goodsDetail = GoodsDetailModel::findGoodsDetailById($goodsId);
        if (!empty($goodsDetail)) {
            $data['imageUrls'] = explode('|', $goodsDetail['image_urls']);
            $data['goodsDetail'] = $goodsDetail['detail'];
        }
        $data['salePrice'] = number_format($goodsInfo['sale_price'], 2, '.', '');
        $data['name'] = $goodsInfo['name'];
        $data['goodsId'] = $goodsId;
        $data['imageUrl'] = $goodsInfo['image_url'];

        $skuJson = array();
        $skuValue = GoodsSKUModel::findAllValidSKUInfo($goodsId);
        if (!empty($skuValue)) {
            foreach ($skuValue as $sku) {
                $skuJson[] = array(
                    'goodsId' => $sku['goods_id'],
                    'skuAttr' => $sku['sku_attr'],
                    'skuValue' => $sku['sku_value'],
                    'price' => $sku['sale_price'],
                    'amount' => $sku['amount']
                );
            }
        }
        $data['skuAttr'] = GoodsSKUModel::getGoodsSkuAttr($goodsId);
        $data['skuValue'] = $skuValue;
        $data['defaultSku'] = $skuValue[0];
        $data['skuJson'] = json_encode($skuJson);

        /*
        $data['signPackage'] = WxSDK::getSignPackage();
        $shareCfg['title'] = $data['name'];
        $shareCfg['desc'] = '';
        $shareCfg['img'] = $data['imageUrl'];
        $shareCfg['url'] = APP_URL_BASE . '/mall/Goods/detail?goodsId=' . $goodsId;
        $data['shareCfg'] = $shareCfg;
        */
        $this->display('goods', $data);
    }
}

