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
        $categoryList = GoodsCategoryModel::getAllCategory();
        foreach ($categoryList as &$cat) {
            $cat['name'] = GoodsCategoryModel::fullCateName($cat['category_id'], $cat['name']);
        }
        $data = array(
            'categoryList' => $categoryList,
        );
        $this->display("category_list", $data);
    }

    public function catInfo()
    {
        $catId = $this->getParam('catId', 0);
        $info = array();
        $title = '新建分类';
        $action = '/admin/GoodsCategory/add';
        if ($catId > 0) {
            $title = '编辑分类';
            $action = '/admin/GoodsCategory/edit';
            $info = GoodsCategoryModel::findCategoryById($catId);
        }
        $data = array(
            'title' => $title,
            'parentId' => 0,
            'catId' => $catId,
            'info' => $info,
            'action' => $action,
        );
        $this->display("category_info", $data);
    }

    public function getCat()
    {
        $catId = $this->postParam('cateId', 0);
        $data = array();
        $data['category'] = array();
        $ret = GoodsCategoryModel::getAllCategoryByParentId($catId);
        if (!empty($ret)) {
            foreach ($ret as $item) {
                $data['category'][] = array('cate_name' => $item['name'], 'id' => $item['category_id']);
            }
        }
        $this->ajaxReturn(0, '', '', $data);
    }

    public function addPage()
    {
        $parentId = $this->getParam('parentId', 0);
        if (GoodsCategoryModel::calcLevel($parentId) == 2) {
            echo "不能再添加子分类";
            return ;
        }
        $data = array(
            'title' => '新建分类',
            'parentId' => $parentId,
            'catId' => 0,
            'action' => '/admin/GoodsCategory/add',
        );
        $this->display("category_info", $data);
    }

    public function add()
    {
        $catInfo = array();
        $error = '';
        if ($this->fetchFormParams($catInfo, $error) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error);
            return ;
        }
        if ($catInfo['parentId'] % 1000 != 0
            || GoodsCategoryModel::calcLevel($catInfo['parentId']) == 2) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '不能添加子类');
            return ;
        }
        $ret = GoodsCategoryModel::newOne(
            $catInfo['parentId'],
            $catInfo['name'],
            $catInfo['state'],
            $catInfo['sort']
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/GoodsCategory');
    }

    public function del()
    {
       /* $catId = $this->getParam('catId', 0);
        if ($catId == 0) {
            header('Location: /admin/GoodsCategory');
            return ;
        }
        GoodsCategoryModel::delCategory($catId);
        header('Location: /admin/GoodsCategory');*/
    }

    public function edit()
    {
        $catInfo = array();
        $error = '';
        if ($this->fetchFormParams($catInfo, $error) === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error);
            return ;
        }
        $updateData = array(
            'name' => $catInfo['name'],
            'sort' => $catInfo['sort'],
            'state' => $catInfo['state']
        );
        $ret = GoodsCategoryModel::update(
            $catInfo['catId'],
            $updateData
        );
        if ($ret === false || (int)$ret <= 0) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '保存失败');
            return ;
        }
        $this->ajaxReturn(0, '保存成功', '/admin/GoodsCategory');
    }

    private function fetchFormParams(&$catInfo, &$error)
    {
        $catInfo['catId'] = $this->postParam('catId', 0);
        $catInfo['parentId'] = $this->postParam('parentId', 0);
        $catInfo['name'] = trim($this->postParam('name', ''));
        $catInfo['sort'] = intval($this->postParam('sort', 0));
        $catInfo['state'] = intval($this->postParam('state', 0));

        if (empty($catInfo['name'])) {
            $error = '分类名称不能为空';
            return false;
        }
        if (strlen($catInfo['name']) > 30) {
            $error = '商品名不能超过10个字符';
            return false;
        }
        return true;
    }
}
