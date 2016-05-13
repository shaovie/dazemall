<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\mall\model\GoodsCategoryModel;

class GoodsCategoryController extends AdminController
{
    public function index()
    {
        $categoryList = array();
        $data = array(
            'categoryList' => $categoryList,
        );
        $this->display("category_list", $data);
    }

    public function catInfo()
    {
        $data = array(
            'parentCatId' => '酒水饮料 > 白酒',
        );
        $this->display("category_info", $data);
    }

    public function getCat()
    {
        $data = array(
            'cate_name' => 'xx',
            'id' => 12,
        );
        $this->ajaxReturn(0, $data);
    }

    public function addPage()
    {
        $parentCatId = $this->getParam('parentId', 0);
        $data = array(
            'parentCatId' => $parentCatId,
        );
        $this->display("category_info", $data);
    }
    public function add()
    {
        $catInfo = array();
        $ret = $this->fetchFormParams($catInfo, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }

        $categoryId = GoodsCategoryModel::();
        $ret = GoodsCategoryModel::newOne(
            $categoryId,
            $catInfo['name'],
            $catInfo['image_url'],
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/GoodsCategory);
    }
    public function edit()
    {
        var_dump($_POST);
    }

    private function fetchFormParams(&$catInfo, &$error)
    {
        $goodsInfo['parentId'] = trim($this->postParam('parentId', 0));
        $goodsInfo['name'] = trim($this->postParam('name', ''));
        $goodsInfo['sort'] = intval($this->postParam('sort', 0));
        $goodsInfo['image_url'] = trim($this->postParam('imageUrl', ''));

        if (empty($goodsInfo['name'])) {
            $error = '商品名不能为空';
            return false;
        }
        if (strlen($goodsInfo['name']) > 30) {
            $error = '商品名不能超过10个字符';
            return false;
        }
        return true;
    }
}
