<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:17
 * Created by PhpStorm.
 */

class IndexController extends BaseController {

    public function indexAction() {
        $users = (new UserModel())->getAll("SELECT u.id,username,avatar FROM user u LEFT JOIN user_profile p ON u.id=p.user_id");

        $this->getView()->assign('user', $this->user);
        $this->getView()->assign('users', $users);
    }

}