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

        }
        $this->getView()->assign('user', $this->user);
    }
}