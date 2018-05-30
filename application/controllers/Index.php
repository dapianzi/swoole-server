<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:17
 * Created by PhpStorm.
 */

class IndexController extends BaseController {

    protected $auth = FALSE;

    public function indexAction() {
        $this->getView()->display('games/pinball.html');
        return FALSE;
    }

}