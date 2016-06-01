<?php
/**
 * @Author shaowei
 * @Date   2015-12-26
 */

namespace src\api\controller;

use \src\common\Check;
use \src\common\Log;
use \src\user\model\UserCouponModel;
use \src\user\model\UserCartModel;
use \src\user\model\UserBillModel;
use \src\mall\model\GoodsSKUModel;

class UserController extends ApiController
{
    public function walletList()
    {
        $this->checkLoginAndNotice();

        $page = intval($this->getParam('page', 1));
        if ($page < 1)
            $page = 1;
        $type = intval($this->getParam('type', 0));

        $pageSize = 20;

        $dataList = array();
        if ($type == 0) {
            $dataList = UserBillModel::getSomeBill(
                array('user_id'), array($this->userId()),
                false,
                $page, $pageSize
            );
        } else if ($type == 1) {
            $dataList = UserBillModel::getSomeBill(
                array('user_id', 'bill_type'), array($this->userId(), UserBillModel::BILL_TYPE_IN),
                array('and'),
                $page, $pageSize
            );
        } else if ($type == 2) {
            $dataList = UserBillModel::getSomeBill(
                array('user_id', 'bill_type'), array($this->userId(), UserBillModel::BILL_TYPE_OUT),
                array('and'),
                $page, $pageSize
            );
        }
        foreach ($dataList as &$val) {
            $val['amount'] = number_format($val['amount'], 2, '.', '');
            $val['desc'] = UserBillModel::getDesc($val['bill_from']);
            $val['ctime'] = date('m-d H:i', $val['ctime']);
        }
        $this->ajaxReturn(0, '', '', array('data' => $dataList));
    }

    public function getOrderCouponList()
    {
        $this->checkLoginAndNotice();

        $uid = $this->userId();
        $data = array();

        $goodsId = $this->getParam('productId', 0);
        $skuAttr = $this->getParam('skuAttr', '');
        $skuValue = $this->getParam('skuValue', '');
        $amount = $this->getParam('num', 1);
        $cartIds = $this->getParam('cartId', '');

        $couponList = array();
        $goodsList = array();
        if (!empty($goodsId) && !empty($skuAttr) && !empty($skuValue)) {
            $goodsSkuInfo = GoodsSKUModel::getSKUInfo($goodsId, $skuAttr, $skuValue);
            if (!empty($goodsSkuInfo)) {
                $goodsSkuInfo['category_id'] = GoodsModel::goodsCategory($goodsId);
                $goodsList[] = $goodsSkuInfo;
            }
        } else if(!empty($cartIds)) {
            $cartIds = explode(',' , $cartIds);
            if (!empty($cartIds)) {
                $cartList = UserCartModel::getCartList($this->userId());
                foreach ($cartList as $cart) {
                    if (!in_array($cart['id'], $cartIds))
                        continue;
                    
                    $goodsInfo = GoodsModel::findGoodsById($cart['goods_id']);
                    if (empty($goodsInfo) || $goodsInfo['state'] == GoodsModel::GOODS_ST_INVALID)
                        continue;
                    $goodsSkuInfo = GoodsSKUModel::getSKUInfo($cart['goods_id'],
                        $cart['sku_attr'], $cart['sku_value']);
                    if (empty($goodsSkuInfo))
                        continue;
                    $goodsList[] = array(
                        'sale_price' => $goodsSkuInfo['sale_price'],
                        'category_id' => $goodsInfo['category_id'],
                    );
                }
            }
        }

        $couponList = UserCouponModel::getAvalidCouponListForOrder($this->userId(), $goodsList);
        $data['list'] = $couponList;
        $this->ajaxReturn(0, '', '', $data);
    }

    public function wxShareLog()
    {
        $shareType = $this->postParam('type', 0);
        $shareParams = $this->postParam('params', '');

        $this->ajaxReturn(0, '');
    }
}
