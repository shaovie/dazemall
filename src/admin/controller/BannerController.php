<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\BannerModel;

class BannerController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = BannerModel::fetchBannerCount([], [], []);
        $bannerList = BannerModel::fetchSomeBanner2([], [], [], $page, self::ONE_PAGE_SIZE);
        foreach ($bannerList as &$banner) {
            $banner['showArea'] = BannerModel::showAreaDesc($banner['show_area']);
        }

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Banner/listPage',
            $searchParams
        );

        $data = array(
            'bannerList' => $bannerList,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("banner_list", $data);
    }

    public function search()
    {
        $bannerList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $keyword = trim($this->getParam('keyword', ''));
            if (empty($keyword)) {
                header('Location: /admin/Banner/listPage');
                return ;
            }
            if (!empty($keyword)) {
                $searchParams['keyword'] = $keyword;
                $banner = BannerModel::findBannerByRemark($keyword);
                if (!empty($banner)) {
                    $bannerList = $banner;
                    $totalNum = count($banner);
                }
            }
        } while(false);

        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/Banner/search', $searchParams);
        $data = array(
            'bannerList' => $bannerList,
            'totalBannerNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("banner_list", $data);
    }

    public function addPage()
    {
        $data = array(
            'title' => '新增',
            'banner' => array(),
            'action' => '/admin/Banner/add',
        );
        $this->display('banner_info', $data);
    }
    public function add()
    {
        $error = '';
        $bannerInfo = array();
        $ret = $this->fetchFormParams($bannerInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $bannerId = BannerModel::newOne(
            $bannerInfo['show_area'],
            empty($bannerInfo['begin_time']) ? 0 : strtotime($bannerInfo['begin_time']),
            empty($bannerInfo['end_time']) ? 0 : strtotime($bannerInfo['end_time']),
            $bannerInfo['image_url'],
            $bannerInfo['link_type'],
            $bannerInfo['link_value'],
            $bannerInfo['remark'],
            $bannerInfo['sort']
        );
        if ($bannerId === false || (int)$bannerId <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Banner/listPage');
    }
    public function editPage()
    {
        $bannerId = intval($this->getParam('bannerId', 0));

        $bannerInfo = BannerModel::findBannerById($bannerId);
        $data = array(
            'title' => '编辑',
            'banner' => $bannerInfo,
            'action' => '/admin/Banner/edit',
        );
        $this->display('banner_info', $data);
    }
    public function edit()
    {
        $error = '';
        $bannerInfo = array();
        $ret = $this->fetchFormParams($bannerInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $updateData = array();
        $updateData['remark'] = $bannerInfo['remark'];
        $updateData['sort'] = $bannerInfo['sort'];
        $updateData['begin_time'] = empty($bannerInfo['begin_time']) ? 0 : strtotime($bannerInfo['begin_time']);
        $updateData['end_time'] = empty($bannerInfo['end_time']) ? 0 : strtotime($bannerInfo['end_time']);
        $updateData['image_url'] = $bannerInfo['image_url'];
        $updateData['link_type'] = $bannerInfo['link_type'];
        $updateData['link_value'] = $bannerInfo['link_value'];
        $updateData['show_area'] = $bannerInfo['show_area'];
        $ret = BannerModel::update($bannerInfo['id'], $updateData);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功，请确认信息无误', '/admin/Banner/listPage');
    }
    public function del()
    {
        $bannerId = $this->getParam('bannerId', 0);
        if ($bannerId == 0) {
            header('Location: /admin/Banner');
            return ;
        }
        BannerModel::delBanner($bannerId);
        header('Location: /admin/Banner');
    }
    private function fetchFormParams(&$bannerInfo, &$error)
    {
        $bannerInfo['id'] = intval($this->postParam('bannerId', 0));
        $bannerInfo['remark'] = trim($this->postParam('remark', ''));
        $bannerInfo['sort'] = intval($this->postParam('sort', 0));
        $bannerInfo['image_url'] = trim($this->postParam('imageUrl', ''));
        $bannerInfo['show_area'] = intval($this->postParam('showArea', 0));
        $bannerInfo['link_type'] = intval($this->postParam('linkType', 0));
        $bannerInfo['link_value'] = trim($this->postParam('linkValue', ''));
        $bannerInfo['begin_time'] = trim($this->postParam('beginTime', ''));
        $bannerInfo['end_time'] = trim($this->postParam('endTime', ''));

        if (strlen($bannerInfo['remark']) > 120) {
            $error = '备注不能超过40个字符';
            return false;
        }
        if (strlen($bannerInfo['link_value']) >= 255) {
            $error = '链接值不能超过255字符';
            return false;
        }
        if ($bannerInfo['show_area'] == -1) {
            $error = '展示区域无效';
            return false;
        }

        if (!empty($bannerInfo['begin_time'])) {
            if (strtotime($bannerInfo['begin_time']) === false) {
                $error = '开始时间格式错误';
                return false;
            }
        }
        if (!empty($bannerInfo['end_time'])) {
            if (strtotime($bannerInfo['end_time']) === false) {
                $error = '结束时间格式错误';
                return false;
            }
        }
        return true;
    }

}
