<?php
/**
 * Created by PhpStorm.
 * @Author: Carl
 * @since: 2018/5/8 0008 9:53
 */

class GamesController extends Yaf_Controller_Abstract
{

    public function init() {
        $this->getView()->assign('user', ['username'=>'dapianzi', 'id'=> 0]);
    }

    public function indexAction($id='') {
        $this->getView()->display('games/index'.$id.'.html');
        return FALSE;
    }

    public function PinBallAction() {

    }

    public function FlippyBirdAction() {

    }

    public function DuetAction() {

    }
}