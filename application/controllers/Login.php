<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:18
 * Created by PhpStorm.
 */
class LoginController extends BaseController {

    protected $auth = FALSE;

    public function indexAction() {

        $action = $this->getPost('action', '');
        if ($action == 'login') {
            Fn::ajaxSuccess(array(
                'user' => 'dapianzi',
                'token' => 'dapianzi don\'t(still) love xiaxue any(very) more(much).',
            ));
            Fn::ajaxError(100, '用户名或密码错误');
        }
        $this->getView()->assign('user', $this->user);
    }

    public function cookieAction() {
        $user = $this->getPost('user', '');
        $token = $this->getPost('token', '');
        $user = new UserModel();
        $userinfo = $user->getUser($user, $token);
        if ($userinfo) {

        }
        Fn::ajaxSuccess(array(
            'user' => 'dapianzi',
            'token' => 'dapianzi don\'t(still) love xiaxue any(very) more(much).',
        ));
        //Fn::ajaxError(200, '用户名或签名无效');
    }
}