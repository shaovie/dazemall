<?php
/**
 * @Author shaowei
 * @Date   2015-12-24
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Util;
use \src\common\Log;
use \src\common\DB;

class GoodsCategoryModel
{
    public static function newOne($categoryId, $name, $imageUrl, $state, $sort)
    {
        $categoryId = self::genCategoryId($categoryId);
        if ($categoryId == false) {
            return false;
        }
        $data = array(
            'category_id' => $categoryId,
            'name' => $name,
            'sort' => $sort,
            'state' => $state,
            'image_url' => $imageUrl,
            'ctime' => CURRENT_TIME,
            'mtime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('g_category', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return true;
    }

    public static function findCategoryById($categoryId, $fromDb = 'w')
    {
        if (empty($categoryId)) {
            return array();
        }
        $ck = Cache::CK_GOODS_CATEGORY_INFO . $categoryId;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB($fromDb)->fetchOne(
                'g_category',
                '*',
                array('category_id'), array($categoryId)
            );
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_GOODS_CATEGORY_INFO_EXPIRE, json_encode($ret));
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function getAllCategory()
    {
        $ret = DB::getDB('r')->fetchAll(
            'g_category',
            '*',
            array('id>'), array('0'),
            false,
            array('sort'), array('desc')
        );
        return $ret === false ? array() : $ret;
    }

    public static function getAllCategoryByParentId($categoryId)
    {
        if ($categoryId == 0) {
            $sql = 'select * from g_category where (category_id % 1000000) = 0 and state=1 order by sort desc';
        } else {
            $level = self::calcLevel($categoryId);
            if ($level == 1) {
                $v = (int)($categoryId / 1000000);
                $sql = "select * from g_category where (category_id % 1000000) != 0 and (category_id % 1000) = 0 and floor(category_id / 1000000) = $v and state=1 order by sort desc";
            } else if ($level == 2) {
                $v = (int)($categoryId / 1000);
                $sql = "select * from g_category where (category_id % 1000) != 0 and floor(category_id / 1000) = $v and state=1 order by sort desc";
            } else {
                return array();
            }
        }

        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }

    public static function delCategory($categoryId)
    {
        $ret = DB::getDB('w')->delete(
            'g_category',
            array('category_id'), array($categoryId),
            false,
            1
        );
        Cache::del(Cache::CK_GOODS_CATEGORY_INFO . $categoryId);
        return $ret === false ? false : true;
    }

    public static function update($catId, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'g_category',
            $data,
            array('category_id'), array($catId),
            false,
            1
        );
        self::onUpdateData($catId);
        return $ret !== false;
    }

    public static function getParentId($categoryId)
    {
        $level = self::calcLevel($categoryId);
        if ($level == 1) {
            return 0;
        } else if ($level == 2) {
            return (int)($categoryId / 1000000) * 1000000;
        } else {
            return (int)($categoryId / 1000) * 1000;
        }
    }

    public static function calcLevel($categoryId)
    {
        if ($categoryId == 0)
            return 1;
        $level1 = (int)($categoryId / 1000000);
        $level2 = (int)((int)($categoryId / 1000) % 1000);
        $level3 = (int)($categoryId % 1000);
        if ($level1 != 0 && $level2 == 0 && $level3 == 0) { // 一级分类
            return 1;
        } else if ($level1 != 0 && $level2 != 0 && $level3 == 0) { // 二级分类
            return 2;
        }
        return 3;
    }

    public static function getCateName($categoryId)
    {
        if ($categoryId == 0)
            return '全部';
        $cateInfo = self::findCategoryById($categoryId);
        if (!empty($cateInfo))
            return $cateInfo['name'];
        return '无';
    }

    public static function fullCateName($categoryId, $name)
    {
        $parentId = self::getParentId($categoryId);
        if ($parentId == 0) {
            return $name;
        }
        $fullName = '';
        $parentInfo = self::findCategoryById($parentId);
        if (!empty($parentInfo)) {
            $fullName = $parentInfo['name'];
            $parentId = self::getParentId($parentInfo['category_id']);
            if ($parentId == 0) {
                return $fullName . ' > ' . $name;
            }
            $parentInfo = self::findCategoryById($parentId);
            if (!empty($parentInfo)) {
                $fullName = $parentInfo['name'] . ' > ' . $fullName;
            }
            return $fullName . ' > ' . $name;
        } else {
            $fullName = '无';
        }

        return $fullName . ' > ' . $name;
    }

    public static function checkBelongCategoryOrNot($masterId, $categoryId)
    {
        if ($masterId == $categoryId) {
            return true;
        }
        $level1 = (int)($masterId / 1000000);
        $level2 = (int)((int)($masterId / 1000) % 1000);
        $level3 = (int)($masterId % 1000);
        if ($level1 != 0 && $level2 == 0 && $level3 == 0) { // 一级分类
            if ((int)($categoryId / 1000000) == $level1) {
                return true;
            }
        } elseif ($level1 != 0 && $level2 != 0 && $level3 == 0) { // 二级分类
            if ((int)($categoryId / 1000) == $level1 * 1000 + $level2) {
                return true;
            }
        } else { // 三级
            return $masterId == $categoryId;
        }
        return false;
    }

    //= private methods
    private static function genCategoryId($categoryId)
    {
        $level1 = (int)($categoryId / 1000000);
        $level2 = (int)((int)($categoryId / 1000) % 1000);
        $level3 = (int)($categoryId % 1000);

        if ($level1 == 0) { // 增加一级分类
            $sql = 'select max(category_id) as m from g_category';
            $ret = DB::getDB('w')->rawQuery($sql);
            if ($ret === false) {
                return false;
            }
            if (empty($ret) || empty($ret[0]['m'])) {
                return 100000000; // 初始以100000000开始，虽然会少用9个，但会整齐好看一些
            }
            $max = (int)$ret[0]['m'];
            if ((int)($max / 1000000) == 999) {
                Log::fatal('category_id ' . $categoryId . ' level1 max = 999, out of limit!');
                return false;
            }
            return ((int)($max / 1000000) + 1) * 1000000;
        } else if ($level2 == 0 && $level3 == 0) { // 增加二级分类
            $sql = 'select max(category_id) as m from g_category where'
            . ' category_id >= ' . ($level1 * 1000000)
            . ' and category_id < ' . (($level1 + 1) * 1000000);
            $ret = DB::getDB('w')->rawQuery($sql);
            if ($ret === false) {
                return false;
            }
            if (empty($ret) || empty($ret[0]['m'])) {
                return $level1 * 1000000 + 1000;
            }
            $maxLevel2 = ((int)(((int)$ret[0]['m']) / 1000) % 1000);
            if ($maxLevel2 == 999) {
                Log::fatal('category_id ' . $categoryId . ' level2 max = 999, out of limit!');
                return false;
            }
            return $level1 * 1000000 + ($maxLevel2 + 1) * 1000;
        } else if ($level3 == 0) { // 增加三级分类
            $sql = 'select max(category_id) as m from g_category where'
            . ' category_id >= ' . ($level1 * 1000000 + $level2 * 1000)
            . ' and category_id < ' . ($level1 * 1000000 + ($level2 + 1) * 1000);
            $ret = DB::getDB('w')->rawQuery($sql);
            if ($ret === false) {
                return false;
            }
            if (empty($ret) || empty($ret[0]['m'])) {
                return $level1 * 1000000 + $level2 * 1000 + 1;
            }
            $maxLevel3 = ((int)$ret[0]['m']) % 1000;
            if ($maxLevel3 == 999) {
                Log::fatal('category_id ' . $categoryId . ' level3 max = 999, out of limit!');
                return false;
            }
            return $level1 * 1000000 + $level2 * 1000 + $maxLevel3 + 1;
        }
        Log::error('error category_id(' . $categoryId . ') when generate category id');
        return false;
    }
    private static function onUpdateData($catId)
    {
        Cache::del(Cache::CK_GOODS_CATEGORY_INFO . $catId);
        self::findCategoryById($catId, 'w');
    }
}

