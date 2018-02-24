<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:17
 * Created by PhpStorm.
 */

class IndexController extends BaseController {

    public function indexAction() {

        if ($this->user) {

        }
        $this->getView()->assign('user', $this->user);
    }

}