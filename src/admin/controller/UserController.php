<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Util;
use \src\common\Check;
use \src\user\model\UserModel;

class UserController extends AdminController
{
    const ONE_PAGE_SIZE = 10;

    public function index()
    {
        $this->display("user_list");
    }

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = UserModel::fetchUserCount([], [], []);
        $userList = UserModel::fetchSomeUser([], [], [], $page, self::ONE_PAGE_SIZE);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/User/listPage', $searchParams);
        $data = array(
            'userList' => $userList,
            'totalUserNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("user_list", $data);
    }

    public function search()
    {
        $userList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $keyword = trim($this->getParam('keyword', ''));
            if (empty($keyword)) {
                header('Location: /admin/User/listPage');
                return ;
            }
            if (!empty($keyword)) {
                $searchParams['keyword'] = $keyword;
                if (Check::isPhone($keyword)) {
                    $user = UserModel::findUserByPhone($keyword, 'r');
                    if (!empty($user)) {
                        $userList[] = $user;
                        $totalNum = 1;
                    }
                } else if (is_numeric($keyword)) {
                    $user = UserModel::findUserById($keyword, 'r');
                    if (!empty($user)) {
                        $userList[] = $user;
                        $totalNum = 1;
                    }
                } else {
                    $user = UserModel::fetchUserByName($keyword, 'r');
                    if (!empty($user)) {
                        $userList = $user;
                        $totalNum = count($user);
                    }
                }
            }
        } while(false);

        $pageHtml = $this->pagination($totalNum, $page, self::ONE_PAGE_SIZE, '/admin/User/search', $searchParams);
        $data = array(
            'userList' => $userList,
            'totalUserNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("user_list", $data);
    }
}
