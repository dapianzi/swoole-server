<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:26
 * Created by PhpStorm.
 */
class IndexController extends BaseController {

    public function indexAction($ab = '') {
        echo $ab;
        echo $this->_module;
        exit;
        return FALSE;
    }


}