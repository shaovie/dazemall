<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\MiaoShaGoodsModel;
use \src\mall\model\GoodsModel;

class MiaoShaGoodsController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->listPage();
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = MiaoShaGoodsModel::fetchGoodsCount([], [], []);
        $actList = MiaoShaGoodsModel::fetchSomeGoods([], [], [], $page, self::ONE_PAGE_SIZE);
        if (!empty($actList)) {
            foreach ($actList as &$act) {
                $act['goodsName'] = GoodsModel::goodsName($act['goods_id']);
            }
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/MiaoShaGoods/listPage',
            $searchParams
        );

        $data = array(
            'actList' => $actList,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("miaosha_goods_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增',
            'act' => array(),
            'action' => '/admin/MiaoShaGoods/add',
        );
        $this->display('miaosha_goods_info', $data);
    }
    public function add()
    {
        $error = '';
        $actInfo = array();
        $ret = $this->fetchFormParams($actInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $actId = MiaoShaGoodsModel::newOne(
            $actInfo['goods_id'],
            $actInfo['begin_time'],
            $actInfo['end_time'],
            $actInfo['price'],
            $actInfo['sort']
        );
        if ($actId === false || (int)$actId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/MiaoShaGoods/listPage');
    }

    public function del()
    {
        $actId = $this->getParam('id', 0);
        if ($actId == 0) {
            header('Location: /admin/MiaoShaGoods/listPage');
            return ;
        }
        MiaoShaGoodsModel::del($actId);
        header('Location: /admin/MiaoShaGoods/listPage');
    }
    private function fetchFormParams(&$actInfo, &$error)
    {
        $actInfo['goods_id'] = trim($this->postParam('goods_id', ''));
        $actInfo['sort'] = intval($this->postParam('sort', 0));
        $actInfo['begin_time'] = trim($this->postParam('beginTime', ''));
        $actInfo['end_time'] = trim($this->postParam('endTime', ''));
        $actInfo['price'] = floatval($this->postParam('price', 0.0));

        $ret = GoodsModel::findGoodsById($actInfo['goods_id']);
        if (empty($ret)) {
            $error = '商品ID错误';
            return false;
        }
        if ($actInfo['price'] < 0.001) {
            $error = '价格有误';
            return false;
        }
        if (empty($actInfo['begin_time']) || strtotime($actInfo['begin_time']) === false) {
            $error = '开始时间格式错误';
            return false;
        }
        $actInfo['begin_time'] = strtotime($actInfo['begin_time']);
        if (empty($actInfo['end_time']) || strtotime($actInfo['end_time']) === false) {
            $error = '结束时间格式错误';
            return false;
        }
        $actInfo['end_time'] = strtotime($actInfo['end_time']);
        
        return true;
    }

}
