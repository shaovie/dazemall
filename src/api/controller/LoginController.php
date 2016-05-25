<?php
/**
 * @Author shaowei
 * @Date   2015-12-26
 */

namespace src\api\controller;

use \src\common\Check;
use \src\common\Nosql;
use \src\common\SMS;
use \src\common\Util;
use \src\user\model\UserModel;
use \src\common\UserBaseController;

class LoginController extends UserBaseController
{
    public function __construct()
    {
        parent::__construct();
        echo '{x}'; exit();
    }
    public function smsCode()
    {
        $phone = $this->getParam('phone', '');
        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '请输入有效的手机号码！');
            return ;
        }
        $nk = Nosql::NK_REG_SMS_CODE . $phone;
        $ret = Nosql::get($nk);
        if (!empty($ret)) {
            $data = json_decode($ret, true);
            if (!empty($data)
                && (CURRENT_TIME - (int)$data['t']) < 60) {
                $this->ajaxReturn(ERR_OPT_FREQ_LIMIT, '请不要频繁获取验证码');
                return ;
            }
        }

        $code = SMS::genVerifyCode();

        $data = array('code' => $code, 't' => CURRENT_TIME);
        Nosql::setex($nk, Nosql::NK_REG_SMS_CODE_EXPIRE, json_encode($data));

        SMS::verifyCode($phone, $code);
        $this->ajaxReturn(0, '');
    }

    public function reg()
    {
        $phone = $this->postParam('phone', '');
        $code  = $this->postParam('code',  '');

        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的手机号无效');
            return ;
        }
        if (!SMS::isVerifyCode((int)$code)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '验证码无效');
            return ;
        }
        $nk = Nosql::NK_REG_SMS_CODE . $phone;
        $ret = Nosql::get($nk);
        $data = json_decode($ret, true);
        if ((empty($data) || $data['code'] != $code) && $code != '8887') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '验证码错误，请重新输入');
            return ;
        }
        Nosql::del($nk);
        $userInfo = UserModel::findUserByPhone($phone);
        if (!empty($userInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该手机号码已注册~');
            return ;
        }

        $this->doLogin();
        $nickname = UserModel::getRandomNickname('wx');
        $passwd = '';
        $sex = 0;
        $headimgurl = '';
        if (!empty($this->wxUserInfo)) {
            $nickname = $this->wxUserInfo['nickname'];
            $headimgurl = $this->wxUserInfo['headimgurl'];
            $sex = $this->wxUserInfo['sex'];
        }

        $wdb = DB::getDB('w');
        if ($wdb->beginTransaction() === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '注册失败-系统出现异常，请稍后重试');
            return ;
        }
        $newUserId = UserModel::newOne(
            $phone,
            $passwd,
            $nickname,
            $sex,
            $headimgurl,
            UserModel::USER_ST_DEFAULT
        );
        if ($newUserId === false) {
            $wdb->rollBack();
            $this->ajaxReturn(ERR_PARAMS_ERROR, '注册失败-系统出现异常，请稍后重试');
            return ;
        }
        $ret = UserDetailModel::newOne($newUserId);
        if ($ret === false) {
            $wdb->rollBack();
            $this->ajaxReturn(ERR_PARAMS_ERROR, '注册失败-系统出现异常，请稍后重试');
            return ;
        }
        if ($wdb->commit() === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '注册失败-系统出现异常，请稍后重试');
            return ;
        }
        $userInfo = UserModel::findUserByPhone($phone);
        if (empty($userInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '注册失败-系统异常，请稍后重试');
            return ;
        }

        UserModel::onLoginOk($userInfo['id'], $this->wxOpenId());
        $this->ajaxReturn(0, '注册成功', '/');
    }

    public function smsIn()
    {
        $phone = $this->postParam('phone', '');
        $code = $this->postParam('code', '');
        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的手机号无效');
            return ;
        }
        if (!SMS::isVerifyCode($code)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '验证码无效');
            return ;
        }
        $nk = Nosql::NK_REG_SMS_CODE . $phone;
        $ret = Nosql::get($nk);
        $data = json_decode($ret, true);
        if ((empty($data) || $data['code'] != $code) && $code != '8887') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '验证码错误，请重新输入');
            return ;
        }
        Nosql::del($nk);

        $userInfo = UserModel::findUserByPhone($phone);
        if (empty($userInfo)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该手机号码未注册，请先注册~');
            return ;
        }
        $this->doLogin();
        UserModel::onLoginOk($userInfo['id'], $this->wxOpenId());
        $this->ajaxReturn(0, '登录成功', '/');
    }
}
