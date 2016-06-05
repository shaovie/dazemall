<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\CouponCfgModel;
use \src\mall\model\CouponGiveCfgModel;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsCategoryModel;

class CouponController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->listPage();
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = CouponCfgModel::fetchCouponCount([], [], []);
        $couponList = CouponCfgModel::fetchSomeCoupon2([], [], [], $page, self::ONE_PAGE_SIZE);
        if (!empty($couponList)) {
            foreach ($couponList as &$coupon) {
                $coupon['stateDesc'] = CouponCfgModel::stateDesc($coupon['state']);
                $coupon['category'] = GoodsCategoryModel::getCateName($coupon['category_id']);
            }
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Coupon/listPage',
            $searchParams
        );

        $data = array(
            'couponList' => $couponList,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("coupon_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增',
            'coupon' => array(),
            'action' => '/admin/Coupon/add',
        );
        $this->display('coupon_info', $data);
    }
    public function add()
    {
        $error = '';
        $couponInfo = array();
        $ret = $this->fetchFormParams($couponInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $couponId = CouponCfgModel::newOne(
            empty($couponInfo['begin_time']) ? 0 : strtotime($couponInfo['begin_time']),
            empty($couponInfo['end_time']) ? 0 : strtotime($couponInfo['end_time']),
            $couponInfo['name'],
            $couponInfo['remark'],
            $couponInfo['coupon_amount'],
            $couponInfo['order_amount'],
            $couponInfo['category_id'],
            $couponInfo['state']
        );
        if ($couponId === false || (int)$couponId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Coupon/listPage');
    }

    public function editPage()
    {
        $couponId = intval($this->getParam('couponId', 0));

        $couponInfo = CouponCfgModel::findCouponById($couponId);
        $data = array(
            'title' => '编辑',
            'coupon' => $couponInfo,
            'action' => '/admin/Coupon/edit',
        );
        $this->display('coupon_info', $data);
    }

    public function edit()
    {
        $error = '';
        $couponInfo = array();
        $ret = $this->fetchFormParams($couponInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $updateData = array();
        $updateData['name'] = $couponInfo['name'];
        $updateData['remark'] = $couponInfo['remark'];
        $updateData['state'] = $couponInfo['state'];
        $updateData['begin_time'] = empty($couponInfo['begin_time']) ? 0 : strtotime($couponInfo['begin_time']);
        $updateData['end_time'] = empty($couponInfo['end_time']) ? 0 : strtotime($couponInfo['end_time']);
        $updateData['category_id'] = $couponInfo['category_id'];
        $updateData['coupon_amount'] = $couponInfo['coupon_amount'];
        $updateData['order_amount'] = $couponInfo['order_amount'];
        $ret = CouponCfgModel::update($couponInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Coupon/listPage');
    }

    public function configPage()
    {
        $data = array(
            'coupon' => CouponGiveCfgModel::getConfig(),
            'action' => '/admin/Coupon/config',
        );
        $this->display('coupon_config', $data);
    }

    public function config()
    {
        $userRegCoupons = trim($this->postParam('userReg', ''));
        $orderAmount = floatval($this->postParam('orderAmount', 0.0));
        $orderFullCoupons = trim($this->postParam('orderFullCoupons', ''));

        $updateData['user_reg_coupon'] = $userRegCoupons;
        $updateData['order_amount'] = $orderAmount;
        $updateData['order_full_coupon'] = $orderFullCoupons;
        CouponGiveCfgModel::update($updateData);
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Coupon/listPage');
    }

    private function fetchFormParams(&$couponInfo, &$error)
    {
        $couponInfo['id'] = intval($this->postParam('couponId', 0));
        $couponInfo['category_id'] = intval($this->postParam('categoryId', 0));
        $couponInfo['name'] = trim($this->postParam('name', ''));
        $couponInfo['remark'] = trim($this->postParam('remark', ''));
        $couponInfo['state'] = intval($this->postParam('state', -1));
        $couponInfo['begin_time'] = trim($this->postParam('beginTime', ''));
        $couponInfo['end_time'] = trim($this->postParam('endTime', ''));
        $couponInfo['order_amount'] = floatval($this->postParam('orderAmount', 0.00));
        $couponInfo['coupon_amount'] = floatval($this->postParam('couponAmount', 0.00));

        if (empty($couponInfo['name']) || strlen($couponInfo['name']) > 120) {
            $error = '名称不能为空或不能超过40个字符';
            return false;
        }
        if (strlen($couponInfo['remark']) > 120) {
            $error = '备注不能超过40个字符';
            return false;
        }
        if (!empty($couponInfo['begin_time'])) {
            if (strtotime($couponInfo['begin_time']) === false) {
                $error = '开始时间格式错误';
                return false;
            }
        }
        if (!empty($couponInfo['end_time'])) {
            if (strtotime($couponInfo['end_time']) === false) {
                $error = '结束时间格式错误';
                return false;
            }
        }
        if ($couponInfo['state'] == -1) {
            $error = '请选择状态';
            return false;
        }
        return true;
    }

}
