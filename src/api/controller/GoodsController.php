<?php
/**
 * @Author shaowei
 * @Date   2016-05-22
 */

namespace src\api\controller;

use \src\common\BaseController;
use \src\mall\model\GoodsModel;

class GoodsController extends BaseController
{
    public function moreComment()
    {
        $data = array();
        $this->ajaxReturn(0, '', '', $data);
    }

    public function likeInfo()
    {
        $goodsId = intval($this->getParam('g', 0));
        $likeNum = 10;
        $data = array(
            'isSupport'    => !empty($isLiked['isLiked']) ? 0 : 1,
            'supportTotal' => $likeNum,
            'supportList'  => array(array('headImg' => ''));
        );
        $this->ajaxReturn(0, '', '', $data);
}

