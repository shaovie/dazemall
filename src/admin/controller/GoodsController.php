<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\GoodsModel;

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

        $totalNum = GoodsModel::fetchGoodsCount([], [], []);
        $goodsList = GoodsModel::fetchSomeGoods([], [], [], $page, self::ONE_PAGE_SIZE);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/Goods/listPage', $searchParams);
        $data = array(
            'goodsList' => $goodsList,
            'totalGoodsNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_list", $data);
    }

    public function search()
    {
        $goodsList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $keyword = trim($this->getParam('keyword', ''));
            if (is_numeric($keyword)) {
                $searchParams['goodsId'] = $keyword;
                $goods = GoodsModel::findGoodsById($goodsId, 'r');
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

        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/Goods/search', $searchParams);
        $data = array(
            'goodsList' => $goodsList,
            'totalGoodsNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("goods_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增商品',
            'goods' => array(),
            'action' => '/admin/Goods/add',
        );
        $this->display('goods_info', $data);
    }
    public function add()
    {
        $error = '';
        $data = array(
            'title' => '新增商品',
            'goods' => array(),
            'action' => '/admin/Goods/add',
        );
        $goodsInfo = array();
        $ret = $this->fetchFormParams($goodsInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $goodsId = GoodsModel::newOne(
            $goodsInfo['name'],
            0,//category_id
            $goodsInfo['market_price'],
            $goodsInfo['sale_price'],
            $goodsInfo['jifen'],
            0,//$goodsInfo['sort'],
            $goodsInfo['state'],
            '', // imageUrl
            $goodsInfo['detail'],
            ''  // imageUrls
        );
        if ($goodsId === false || (int)$goodsId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Goods/editPage?goodsId=' . $goodsId);
    }
    public function editPage()
    {
        $goodsId = $this->getParam('goodsId', '');

        $goodsInfo = GoodsModel::findGoodsById($goodsId, 'w');
        $data = array(
            'title' => '编辑商品',
            'goods' => $goodsInfo,
            'action' => '/admin/Goods/edit',
        );
        $this->display('goods_info', $data);
    }
    public function edit()
    {
        $error = '';
        $data = array(
            'title' => '编辑商品',
            'goods' => array(),
            'action' => '/admin/Goods/edit',
        );
        $goodsInfo = array();
        $ret = $this->fetchFormParams($goodsInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $updateData = array();
        $updateData['name'] = $goodsInfo['name'];
        $updateData['state'] = $goodsInfo['state'];
        $updateData['market_price'] = $goodsInfo['market_price'];
        $updateData['sale_price'] = $goodsInfo['sale_price'];
        $updateData['jifen'] = $goodsInfo['jifen'];
        $updateData['image_url'] = $goodsInfo['image_url'];
        $ret = GoodsModel::updateGoodsInfo($goodsInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存商品失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Goods/editPage?goodsId=' . $goodsInfo['id']);
    }
    public function fetchFormParams(&$goodsInfo, &$error)
    {
        $goodsInfo['id'] = intval($this->postParam('goodsId', 0));
        $goodsInfo['name'] = trim($this->postParam('name', ''));
        $goodsInfo['state'] = intval($this->postParam('state', 0));
        $goodsInfo['market_price'] = floatval($this->postParam('marketPrice', 0.00));
        $goodsInfo['sale_price'] = floatval($this->postParam('salePrice', 0.00));
        $goodsInfo['jifen'] = intval($this->postParam('jifen', 0));
        $goodsInfo['image_url'] = $this->postParam('imageUrl', '');
        $goodsInfo['detail'] = $this->postParam('detail', '');

        if (empty($goodsInfo['name'])) {
            $error = '商品名不能为空';
            return false;
        }
        return true;
    }
}
