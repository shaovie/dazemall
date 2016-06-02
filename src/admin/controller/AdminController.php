<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Check;
use \src\common\Session;
use \src\common\Nosql;
use \src\admin\model\EmployeeModel;

class AdminController extends BaseController
{
    protected $account = '';

    public function __construct()
    {
        parent::__construct();

        $this->module = 'admin';

        $this->autoLogin();

        if (!$this->hadLogin()) {
            $this->toLogin();
        }
    }

    protected function autoLogin()
    {
        if ($this->doLogin() === -1) {
            $this->toLogin();
        }
    }

    public function hadLogin()
    {
        return !empty($this->account);
    }

    protected function doLogin()
    {
        $key = Session::getSid('emp', HT_HOST);
        $employeeInfo = Nosql::get(Nosql::NK_ADMIN_SESSOIN . $key);
        if (!empty($employeeInfo)) {
            $userAgent = '';
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
            }
            $employeeInfo = json_decode($employeeInfo, true);
            if ($employeeInfo['userAgent'] != $userAgent) {
                return false;
            }
            if (!empty($employeeInfo['account'])) {
                $this->account = $employeeInfo['account'];
                return true;
            }
            return false;
        }
        return -1;
    }

    protected function doLogout()
    {
        $key = Session::getSid('emp', HT_HOST);
        Nosql::del(Nosql::NK_ADMIN_SESSOIN . $key);
    }
    protected function toLogin()
    {
        //header('Location: /admin/Login');
        exit('<script>top.location.href="/admin/Login"</script>');
    }

    function pagination($total, $pindex, $psize, $url, $params)
    {
        $tpage = ceil($total / $psize);
        if($tpage <= 1) {
            return '';
        }
        $findex = 1;
        $lindex = $tpage;
        $cindex = $pindex;
        $cindex = min($cindex, $tpage);
        $cindex = max($cindex, 1);
        $cindex = $cindex;
        $pindex = $cindex > 1 ? $cindex - 1 : 1;
        $nindex = $cindex < $tpage ? $cindex + 1 : $tpage;
        $params['page'] = $findex;
        $furl = 'href="' . $url . '?' . http_build_query($params) . '"';
        $params['page'] = $pindex;
        $purl = 'href="' . $url . '?' . http_build_query($params) . '"';
        $params['page'] = $nindex;
        $nurl = 'href="' . $url . '?' . http_build_query($params) . '"';
        $params['page'] = $lindex; 
        $lurl = 'href="' . $url . '?' . http_build_query($params) . '"';
        $beforesize = 5;
        $aftersize = 4;

        $html = '<div class="dataTables_paginate paging_simple_numbers"><ul class="pagination">';
        if($cindex > 1) {
            $html .= "<li><a {$furl} class=\"paginate_button previous\">首页</a></li>";
            $html .= "<li><a {$purl} class=\"paginate_button previous\">&laquo;上一页</a></li>";
        }
        $rastart = max(1, $cindex - $beforesize);
        $raend = min($tpage, $cindex + $aftersize);
        if ($raend  - $rastart < $beforesize + $aftersize) {
            $raend = min($tpage, $rastart + $beforesize + $aftersize);
            $rastart= max(1, $raend - $beforesize - $aftersize);
        }
        for ($i = $rastart; $i <= $raend; $i++) {
            $params['page'] = $i;
            $aurl = 'href="'. $url . '?' . http_build_query($params) . '"';
            $html .= ($i == $cindex ? '<li class="paginate_button active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aurl}>" . $i . '</a></li>');
        }
        if($cindex < $tpage) {
            $html .= "<li><a {$nurl} class=\"paginate_button next\">下一页&raquo;</a></li>";
            $html .= "<li><a {$lurl} class=\"paginate_button next\">尾页</a></li>";
        }
        $html .= '</ul></div>';
        return $html;
    }
}
