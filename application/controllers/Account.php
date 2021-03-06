<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:18
 * Created by PhpStorm.
 */
class AccountController extends BaseController {

    protected $auth = FALSE;

    public function loginAction() {
        $data = $this->getRequest()->getParams();
        if ($data['username'] == '王辛巴123' && $data['password'] == md5('123456')) {
            echo json_encode(['token' => md5(time().rand(100, 999))]);
        } else {
            echo json_encode(['token' => '']);
        }
        return false;
    }

    public function listAction() {
        $list = [
            'uids' => [1,2,3,4,5],
            'items' => [
                ['id' => 1, 'name' => 'dapianzi'],
                ['id' => 2, 'name' => 'carl'],
                ['id' => 3, 'name' => '王辛巴'],
                ['id' => 4, 'name' => '`!`@"1"s的'],
                ['id' => 5, 'name' => '<>{}*\/,.;[]'],
            ]
        ];
        echo json_encode($list);
        return FALSE;
    }

//
//    public function indexAction() {
//        $action = $this->getPost('action', '');
//        if ($action == 'login') {
//            $username = $this->getPost('username');
//            $password = $this->getPost('password');
//
//            $user_model = new UserModel();
//            if ($user_model->userLogin($username, $password)) {
//                $user_info = $user_model->getUser($username);
//                // create new token
//                $token = Fn::uuid($username);
//                $user_model->saveUserToken($user_info['id'], $token);
//                $_SESSION['user'] = $username;
//                Fn::ajaxSuccess(array(
//                    'uid' => $user_info['id'],
//                    'user' => $user_info['username'],
//                    'token' => $token,
//                ));
//            }
//            Fn::ajaxError(100, '用户名或密码错误');
//        }
//        $this->getView()->assign('user', $this->user);
//    }
//
//    // never reached
//    public function cookieAction() {
//        $user = $this->getPost('user', '');
//        $token = $this->getPost('token', '');
//        $user_model = new UserModel();
//        $user_info = $user_model->getUser($user, $token);
//        if ($user_info) {
//            $token = Fn::uuid($user);
//            $user_model->saveUserToken($user_info['id'], $token);
//            $_SESSION['user'] = $user;
//            Fn::ajaxSuccess(array(
//                'uid' => $user_info['id'],
//                'user' => $user_info['username'],
//                'token' => $token,
//            ));
//        }
//        Fn::ajaxError(200, '用户名或签名无效');
//    }
//
//    public function signupAction() {
//
//        $action = $this->getPost('action', '');
//        if ($action == 'signup') {
//            $username = $this->getPost('username');
//            $password = $this->getPost('password');
//
//            $user_model = new UserModel();
//            if ($user_model->getUser($username)) {
//                Fn::ajaxError(300, '用户'.$username.'已存在');
//            } else {
//                $auth = new Auth($password);
//                $salt = $auth->salt();
//                $data = array(
//                    'username' => $username,
//                    'salt' => $salt,
//                    'password' => $auth->encrypt($salt)
//                );
//                $uid = $user_model->add($data);
//                // user profiles
//                $avatar = $this->getPost('avatar', '1');
//                $user_model->setUserProfile($uid, array(
//                    'avatar' => (new AvatarModel())->getAvatar($avatar),
//                ));
//                Fn::ajaxSuccess($username);
//            }
//        }
//        // avatars
//        $avatars = (new AvatarModel())->getDefault();
//        $this->getView()->assign('avatars', $avatars);
//        $this->getView()->assign('user', $this->user);
//    }
//
//    public function outAction() {
//        // clear session
//        unset($_SESSION['user']);
//        $_SESSION['user'] = null;
//        // clear cookies
//        setcookie('user', '', time()-3600);
//        setcookie('token', '', time()-3600);
//        (new UserModel())->clearSession($this->user['id'], $_COOKIE['token']);
//        $this->redirect($this->base_uri.'/');
//        return FALSE;
//    }
}