<?php
/**
 * @Author shaowei
 * @Date   2015-08-18
 */

namespace src\weixin\controller;

use \src\common\Log;
use \src\common\WxSDK;
use \src\common\Util;
use \src\weixin\model\EventModel;

class GzhEventController extends WeiXinController
{
    public function test()
    {
        $openInfo = WxSDK::getOpenInfo('snsapi_base', WX_APP_ID, WX_APP_SECRET);
        var_dump($openInfo);
        header('Location: /weixin/GzhEvent/test2');
    }
    public function test2()
    {
        $openInfo = WxSDK::getOpenInfo('snsapi_userinfo', WX_APP_ID, WX_APP_SECRET);
        var_dump($openInfo);
        echo '<br/>';
        echo '<br/>';
        var_dump(WxSDK::getUserInfo($openInfo['openid'], 'snsapi_userinfo', $openInfo['access_token']));
    }

    public function createMenu()
    {
        $urlBase = APP_URL_BASE;
        $fromMenu = '?srcfrom=wxmenu';
        $menu = <<<EOT
{
    "button":[{
        "name":"大泽商城",
            "type":"view",
            "url":"$urlBase$fromMenu"
    },{
        "name":"我的",
            "sub_button":[{
                "name":"个人中心",
                "type":"view",
                "url":"$urlBase/user/Home/index$fromMenu"
            }]
    }]
}
EOT;
        $ret = WxSDK::createMenu($menu);
        var_dump($ret);
    }


    public function callback()
    {
        if (!$this->checkSignature()) {
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            echo $_GET['echostr'];
            exit();
        }

        $data = file_get_contents('php://input');
        $postObj = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $postData = json_decode(json_encode($postObj), true);
        if (empty($postData['EventKey'])) { // 微信后台返回空的时候，解析出来是 {}
            $postData['EventKey'] = '';
        }

        switch ($postData['MsgType']) {
        case 'event':
            $this->handleEvent($postData);
            break;
        case 'text':
            if (!$this->handleText($postData)) {
                $this->transferToCustomerService($postData);
            }
            break;
        case 'image':
        case 'voice':
        case 'video':
        case 'shortvideo':
        case 'location':
        case 'link':
            EventModel::onActivateForGZH($postData['FromUserName']);
            $this->transferToCustomerService($postData);
            break;
        }
        exit();
    }

    //= private methods
    private function handleEvent($postData)
    {
        if ($postData['Event'] == 'SCAN') { // 扫描二维码
            $this->onScan($postData);
        } elseif ($postData['Event'] == 'subscribe') { // 订阅
            $this->onSubscribe($postData);
        } elseif ($postData['Event'] == 'unsubscribe') { // 取消订阅
            $this->onUnsubscribe($postData);
        } elseif ($postData['Event'] == 'LOCATION') { // 上报地理位置
            $this->onLocation($postData);
        } elseif ($postData['Event'] == 'VIEW') { // 点击跳转菜单
            $this->onView($postData);
        } elseif ($postData['Event'] == 'CLICK') { // 点击菜单
            $this->onClick($postData);
        }
    }

    private function handleText($postData)
    {
        $openid = $postData['FromUserName'];
        EventModel::onActivateForGZH($openid);
        return EventModel::onText($openid, $postData['Content']);
    }

    private function onScan($postData)
    {
        $sceneId = $postData['EventKey'];
        $openid  = $postData['FromUserName'];

        Log::rinfo(json_encode($postData));
        EventModel::onScan($openid, $sceneId);

        EventModel::onActivateForGZH($openid);
    }

    private function onSubscribe($postData)
    {
        $openid = $postData['FromUserName'];

        if (strncmp($postData['EventKey'], 'qrscene_', 8) == 0) { // 扫描场景二维码的关注
            $sceneId = intval(substr($postData['EventKey'], 8));
            EventModel::onScanSubscribe($openid, $sceneId);
        } else { // 普通用户关注
            EventModel::onSubscribe($openid);
        }

        EventModel::onActivateForGZH($openid);
    }

    private function onUnsubscribe($postData)
    {
        $openid = $postData['FromUserName'];
    }

    private function onLocation($postData)
    {
        $openid = $postData['FromUserName'];
        $lat = $postData['Latitude'];
        $lng = $postData['Longitude'];
    }

    private function onView($postData)
    {
        $openid = $postData['FromUserName'];

        EventModel::onActivateForGZH($openid);
    }

    private function onClick($postData)
    {
        $openid = $postData['FromUserName'];

        EventModel::onActivateForGZH($openid);
    }

    private function transferToCustomerService($postData)
    {
        $toUserName = $postData['ToUserName'];
        $fromUserName = $postData['FromUserName'];
        $now = CURRENT_TIME;
        $msg = '<xml><ToUserName><![CDATA[' . $fromUserName . ']]></ToUserName>'
            . '<FromUserName><![CDATA[' . $toUserName . ']]></FromUserName>'
            . '<CreateTime>' . $now . '</CreateTime>'
            . '<MsgType><![CDATA[transfer_customer_service]]></MsgType></xml>';
        echo $msg;
        exit();
    }

    private function checkSignature()
    {
        $arr = array('tomandjerry', $_GET['timestamp'], $_GET['nonce']);
        sort($arr, SORT_STRING);
        $sigStr = implode($arr);

        return sha1($sigStr) == $_GET['signature'];
    }
}

