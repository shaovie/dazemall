<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\api\controller;

use \src\common\Util;
use \src\common\BaseController;
use \src\mall\model\GoodsModel;
use \src\mall\model\GoodsLikeModel;

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
        $goodsId = intval($this->getParam('goodsId', 0));

        if (!$this->hadLogin()) {
            $this->ajaxReturn(ERR_NOT_LOGIN, '登录后才能点赞');
            return ;
        }
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
}

