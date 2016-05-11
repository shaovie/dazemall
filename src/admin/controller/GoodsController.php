<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\admin\model\GoodrModel;

class GoodsController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->display("goods_list");
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = 100;
        $goodsList = array();
        $this->showGoodsList(
            $totalNum,
            $goodsList,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Goods/listPage',
            array(),
            array(),
            ''
        );
    }

    public function search()
    {
        $goodsList = array();
        $totalNum = 0;
        $error = '';
        $urlParams = array();
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $goodsId = trim($this->getParam('goodsId', ''));

            if (!empty($goodsId)) {
                $searchParams['goodsId'] = $goodsId;
                $goods = UserOrderModel::findOrderByOrderId($goodsId, 'r');
                if (!empty($goods)) {
                    $goodsList[] = $goods;
                }
            } else {
                $conds = array();
                $vals = array();
                $rel = array();
                if (!empty($beginTime)) {
                    $searchParams['beginTime'] = $beginTime;
                    $dt = strtotime($beginTime);
                    if ($dt !== false) {
                        $conds[] = 'ctime>=';
                        $vals[] = $dt;
                        if (count($conds) > 1) {
                            $rel[] = 'and';
                        }
                        $urlParams['beginTime'] = $beginTime;
                    }
                }
                if (!empty($conds)) {
                    // TODO
                }
            }
        } while(false);

        $data = $this->showGoodsList(
            100,
            $goodsList,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Goods/search',
            $urlParams,
            $searchParams,
            $error
        );
    }

    private function showGoodsList(
        $totalNum,
        $goodsList,
        $curPage,
        $pageSize,
        $url,
        $urlParams,
        $searchParams,
        $error
    ) {
        $data = array(
            'goodsList' => $goodsList,
            'totalGoodsNum' => $totalNum,
            'pageHtml' => $this->pagination($totalNum, $curPage, $pageSize, $url, $urlParams),
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_list", $data);
    }
}
