<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\mall\controller;

use \src\mall\model\GoodsModel;
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
        $data['inventory'] = 2;
        $this->display('goods', $data);
    }
}

