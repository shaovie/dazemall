<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\api\controller;

use \src\common\Util;
use \src\common\BaseController;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsSKUModel;
use \src\mall\model\GoodsLikeModel;
use \src\mall\model\MiaoShaGoodsModel;

class GoodsController extends ApiController
{
    public function moreComment()
    {
        $data = array();
        $this->ajaxReturn(0, '', '', $data);
    }

    public function likeInfo()
    {
        $goodsId = intval($this->getParam('goodsId', 0));
        $isLiked = GoodsLikeModel::hadLiked($this->userId(), $goodsId);
        $likeNum = 0;
        $goodsInfo = GoodsModel::findGoodsById($goodsId);
        if (!empty($goodsInfo))
            $likeNum = $goodsInfo['like_count'];

        $likeUsers = GoodsLikeModel::fetchLikeUsers($goodsId);
        
        $data = array(
            'isSupport'    => $isLiked ? 1 : 0,
            'supportTotal' => $likeNum,
            'supportList'  => $likeUsers,
        );
        $this->ajaxReturn(0, '', '', $data);
    }

    public function likeGoods()
    {
        $this->checkLoginAndNotice();

        $goodsId = intval($this->getParam('goodsId', 0));
        if (!GoodsLikeModel::hadLiked($this->userId(), $goodsId)) {
            GoodsLikeModel::likeGoods($this->userId(), $goodsId);
            GoodsModel::doLikeGoods($goodsId);
            $this->ajaxReturn(
                0,
                '',
                '',
                array('headImg' => Util::wxSmallHeadImgUrl($this->userInfo['headimgurl']))
            );
            return ;
        }
        $this->ajaxReturn(0, '', '', array('headImg' => ''));
    }

    public function search()
    {
        $key = $this->getParam('key', '');
        $page = intval($this->getParam('page', 1));
        $res = GoodsModel::search($key, $page);
        $goodsList = GoodsModel::fillShowGoodsListData($res);
        $this->ajaxReturn(0, '', '', array('goodsList' => $goodsList));
    }

    public function getMiaoShaList()
    {
        $hour = trim($this->getParam('hour', ''));
        if (empty($hour)) {
            return ;
        }
        $cHour = date('G', CURRENT_TIME); //当前时间
        $timeStamp = ($hour < $cHour) ? strtotime('+1 day') : CURRENT_TIME;
        $alreadyStart = false; //活动是否已开始
        switch ($hour) {
        case '10':
            if ($cHour >= 10 && $cHour < 17) {
                $alreadyStart = true;
            } else {
                $prevStart = date('Y-m-d 10:00:00', $timeStamp);
                $prevOver  = date('Y-m-d 16:59:59', $timeStamp);
            }
            break;
        case '17':
            if ($cHour >= 17 && $cHour < 21) {
                $alreadyStart = true;
            } else {
                $prevStart = date('Y-m-d 17:00:00', $timeStamp);
                $prevOver  = date('Y-m-d 20:59:59', $timeStamp);
            }
            break;
        case '21':
            if ($cHour >= 21 || $cHour < 10) {
                $alreadyStart = true;
            } else {
                $prevStart = date('Y-m-d 21:00:00', $timeStamp);
                $prevOver  = date('Y-m-d 09:59:59', strtotime('+1 day', $timeStamp));
            }
            break;
        default:
            $this->ajaxReturn(ERR_PARAMS_ERROR, '秒杀活动不存在');
            return;
        }
        if ($alreadyStart) {
            $goodsList = MiaoShaGoodsModel::findAllValidGoods(CURRENT_TIME);
        } else {
            $prevStart = strtotime($prevStart);
            $prevOver = strtotime($prevOver);
            $leftTime = $prevStart - CURRENT_TIME;
            if ($leftTime < 0)
                $leftTime = 0;
            $goodsList = MiaoShaGoodsModel::findAllValidPrevGoods($prevStart, $prevOver);
        }

        $goodsList = MiaoShaGoodsModel::fillShowGoodsList($goodsList, $alreadyStart, $leftTime);
        $this->ajaxReturn(0, '', '', array('list' => $goodsList, 'activityAlreadyStart' => $alreadyStart));
    }
}

