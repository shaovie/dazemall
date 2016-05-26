<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\mall\model\DeliverymanModel;

class DeliverymanController extends AdminController
{
    // view
    public function addPage()
    {
        $data['title'] = '新增快递员';
        $data['action'] = '/admin/Deliveryman/add';
        $this->display('deliveryman_info', $data);
    }

    public function info()
    {
        $id = $this->getParam('id', '');

        $info = DeliverymanModel::findDeliverymanById($id);
        if (empty($info)) {
            echo '该账号不存在~';
            return ;
        }

        $data['title'] = '修改信息';
        $data['info'] = $info;
        $data['action'] = '/admin/Deliveryman/edit';
        $this->display('deliveryman_info', $data);

    }

    public function edit()
    {
        $id = $this->postParam('id', 0);
        $name = $this->postParam('name', '');
        $phone = $this->postParam('phone', '');
        if (!Check::isName($name)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的姓名无效');
            return ;
        }
        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的手机号无效');
            return ;
        }

        if ($this->account != 'admin') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您无权编辑');
            return ;
        }

        $info = DeliverymanModel::findDeliverymanById($id);
        if (empty($info)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '该账号未注册~');
            return ;
        }
        $updateData = array('name' => $name, 'phone' => $phone);
        DeliverymanModel::update($id, $updateData); 

        $this->ajaxReturn(0, '操作成功', '/admin/Deliveryman/listPage');
    }

    public function add()
    {
        $name = $this->postParam('name', '');
        $phone = $this->postParam('phone', '');
        
        if (!Check::isName($name)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的姓名无效');
            return ;
        }
        if (!Check::isPhone($phone)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您输入的手机号无效');
            return ;
        }

        if ($this->account != 'admin') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '您无权操作');
            return ;
        }

        $ret = DeliverymanModel::newOne($phone, $name, 1); 
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '操作失败');
            return ;
        }

        $this->ajaxReturn(0, '操作成功', '/admin/Deliveryman/listPage');
    }

    public function listPage()
    {
        $data['deliverymanList'] = DeliverymanModel::getAllDeliveryman();
        $this->display('deliveryman_list', $data);
    }

    public function stateOpt()
    {
        $id = $this->getParam('id', 0);
        $info = DeliverymanModel::findDeliverymanById($id);
        if (empty($id)) {
            echo '该账号未注册~';
            return ;
        }
        if ($this->account != 'admin') {
            echo '您无权操作';
            return ;
        }
        $updateData = array('state' => 0);
        if ($info['state'] == 0) {
            $updateData = array('state' => 1);
        }
        DeliverymanModel::update($id, $updateData); 
        header('Location: /admin/Deliveryman/listPage');
    }
}

