<?php
/**
 * @Author shaowei
 * @Date   2015-12-24
 */

namespace src\mall\model;

use \src\common\Cache;
use \src\common\Log;
use \src\common\DB;
use \src\common\Util;
use \src\user\model\UserModel;

class GoodsLikeModel
{
    public static function newOne($userId, $goodsId)
    {
        if (empty($userId)
            || empty($goodsId)) {
            return false;
        }

        $data = array(
            'goods_id'  => $goodsId,
            'user_id'   => $userId,
            'ctime'     => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('g_goods_like', $data);
        if ($ret === false) {
            return false;
        }
        return true;
    }

    public static function fetchLikeUsers($goodsId)
    {
        $ck = Cache::CK_GOODS_LIKE_USERS . $goodsId;
        $ret = Cache::get($ck);
        if ($ret !== false) {
            $ret = json_decode($ret, true);
        } else {
            $ret = DB::getDB()->fetchSome(
                'g_goods_like',
                '*',
                array('goods_id'), array($goodsId),
                array(),
                array('id'), array('desc'),
                array(14)
            );
            if (!empty($ret)) {
                $data = array();
                foreach ($ret as $item) {
                    $userInfo = UserModel::findUserById($item['user_id']);
                    if (!empty($userInfo)) {
                        $v['headImg'] = Util::wxSmallHeadImgUrl($userInfo['headimgurl']);
                        $data[] = $v;
                    }
                }
                if (!empty($data)) {
                    Cache::setEx($ck, Cache::CK_GOODS_LIKE_USERS_EXPIRE, json_encode($data));
                }
                $ret = $data;
            }
        }
        return $ret === false ? array() : $ret;
    }

    public static function likeGoods($userId, $goodsId)
    {
        self::newOne($userId, $goodsId);
        Cache::del(Cache::CK_GOODS_HAD_LIKE . $goodsId . ':' . $userId);
        Cache::del(Cache::CK_GOODS_LIKE_USERS . $goodsId);
    }

    // return true or false
    public static function hadLiked($userId, $goodsId)
    {
        if (empty($userId) || empty($goodsId)) {
            return false;
        }
        $ck = Cache::CK_GOODS_HAD_LIKE . $goodsId . ':' . $userId;
        $ret = Cache::get($ck);
        if ($ret === false) {
            $ret = DB::getDB()->fetchCount(
                'g_goods_like',
                array('goods_id', 'user_id'), array($goodsId, $userId),
                array('and')
            );
            if (!empty($ret)) {
                Cache::setEx($ck, Cache::CK_GOODS_HAD_LIKE_EXPIRE, (string)$ret);
            }
        }
        return (int)$ret > 0;
    }
}

