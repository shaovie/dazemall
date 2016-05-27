<?php
/**
 * @Author shaowei
 * @Date   2015-12-26
 */

namespace src\api\controller;

use \src\user\model\UserAddressModel;
use \src\common\Check;
use \src\common\Log;

class UserAddressController extends ApiController
{
    public function getAll()
    {
        $this->checkLoginAndNotice();

        $addrList = UserAddressModel::getAddrList($this->userId());

        if (empty($addrList)) {
            $this->ajaxReturn(0, '', '', array());
            return ;
        }

        $retList = array();
        $sysCityCodeBook = include(CONFIG_PATH . '/city_code_book.php');
        foreach ($addrList as $addr) {
            $v['id'] = $addr['id'];
            $v['reName'] = $addr['re_name'];
            $v['rePhone'] = $addr['re_phone'];
            $v['provinceId'] = $addr['province_id'];
            $v['provinceName'] = '';
            if (isset($sysCityCodeBook[$addr['province_id']])) {
                $v['provinceName'] = $sysCityCodeBook[$addr['province_id']];
            }
            $v['cityId'] = $addr['city_id'];
            $v['cityName'] = '';
            if (isset($sysCityCodeBook[$addr['city_id']])) {
                $v['cityName'] = $sysCityCodeBook[$addr['city_id']];
            }
            $v['districtId'] = $addr['district_id'];
            $v['districtName'] = '';
            if (isset($sysCityCodeBook[$addr['district_id']])) {
                $v['districtName'] = $sysCityCodeBook[$addr['district_id']];
            }
            $v['detailAddr'] = $addr['detail_addr'];
            $v['isDefault'] = $addr['is_default'];
            $retList[] = $v;
        }
        $this->ajaxReturn(0, '', '', array('data' => $retList));
    }

    public function setDefault()
    {
        $this->checkLoginAndNotice();

        $addrId = $this->getParam('id', 0);
        if (empty($addrId)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }
        UserAddressModel::clearDefaultAddr($this->userId());
        $ret = UserAddressModel::setDefaultAddr($this->userId(), $addrId, 1);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '设置默认地址失败');
            return ;
        }
        $this->ajaxReturn(0, '');
    }

    public function edit()
    {
        $this->checkLoginAndNotice();

        $addrId = $this->postParam('id', 0);
        $reName = $this->postParam('name', ''); // 收件人
        $rePhone = $this->postParam('phone', '');
        $provinceId = (int)$this->postParam('provinceId', 0);
        $cityId = (int)$this->postParam('cityId', 0);
        $districtId = (int)$this->postParam('districtId', 0);
        $detail = $this->postParam('detailAddr', '');
        $reIdCard = $this->postParam('reIdCard', '');
        $isDefault = (int)$this->postParam('isDefault', 0);

        $sysCityCodeBook = include(CONFIG_PATH . '/city_code_book.php');
        $reName = preg_replace('/\s|　/', '', $reName);
        if (empty($reName) || strlen($reName) > 30
            || !Check::isPhone($rePhone)
            || !isset($sysCityCodeBook[$provinceId])
            || !isset($sysCityCodeBook[$cityId])
            || !isset($sysCityCodeBook[$districtId])
            || empty($detail) || strlen($detail) > 255) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '输入不合法，请重新输入');
            return ;
        }

        if ($isDefault == 1) {
            UserAddressModel::clearDefaultAddr($this->userId());
        }

        if ($addrId > 0) {
            $ret = UserAddressModel::update(
                $this->userId(),
                $addrId,
                array(
                    're_name' => $reName,
                    're_phone' => $rePhone,
                    'province_id' => $provinceId,
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                    'detail_addr' => $detail,
                    're_id_card' => $reIdCard,
                    'is_default' => $isDefault
                )
            );
        } else {
            $ret = UserAddressModel::newOne(
                $this->userId(),
                $reName,
                $rePhone,
                UserAddressModel::ADDR_TYPE_UNKNOW,
                $provinceId,
                $cityId,
                $districtId,
                $detail,
                $reIdCard,
                $isDefault
            );
        }
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '系统异常，保存地址失败');
            return ;
        }
        $this->ajaxReturn(0, '');
    }

    public function del()
    {
        $this->checkLoginAndNotice();

        $addrId = $this->getParam('id', 0);
        if (empty($addrId)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '参数错误');
            return ;
        }
        $ret = UserAddressModel::delOne($this->userId(), $addrId);
        if ($ret === false) {
            $this->ajaxReturn(ERR_SYSTEM_ERROR, '删除地址失败');
            return ;
        }
        $this->ajaxReturn(0, '');
    }

    public function getAllProvince()
    {
        $data = array();
        $sysCityCode = include(CONFIG_PATH . '/city_code.php');
        foreach ($sysCityCode as $key => $val) {
            if ($key == 370000)
                $data[] = array('region_name' => $val['name'], 'region_id' => $key);
        }
        $this->ajaxReturn(0, '', '', array('data' => $data));
    }
    public function getAllCity()
    {
        $provinceId = intval($this->getParam('province_id', 0));
        $data = array();
        if ($provinceId > 0) {
            $sysCityCode = include(CONFIG_PATH . '/city_code.php');
            if (isset($sysCityCode[$provinceId])) {
                $cityList = $sysCityCode[$provinceId]['city'];
                foreach ($cityList as $key => $val) {
                    if ($key == 371700)
                        $data[] = array('region_name' => $val['name'], 'region_id' => $key);
                }
            }
        }
        $this->ajaxReturn(0, '', '', array('data' => $data));
    }
    public function getAllDistrict()
    {
        $provinceId = intval($this->getParam('province_id', 0));
        $cityId = intval($this->getParam('city_id', 0));
        $data = array();
        if ($provinceId > 0 && $cityId > 0) {
            $sysCityCode = include(CONFIG_PATH . '/city_code.php');
            if (isset($sysCityCode[$provinceId])) {
                $distList = $sysCityCode[$provinceId]['city'][$cityId]['district'];
                foreach ($distList as $key => $val) {
                    $data[] = array('region_name' => $val, 'region_id' => $key);
                }
            }
        }
        $this->ajaxReturn(0, '', '', array('data' => $data));
    }
}

