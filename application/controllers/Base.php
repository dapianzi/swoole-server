<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:27
 * Created by PhpStorm.
 */

class BaseController extends Yaf\Controller_Abstract
{
    protected $user = null;
    protected $base_uri = '';
    protected $csrf_token = '';
    protected $auth = TRUE;
    protected $is_ajax = FALSE;

    public function init() {
        // inti config
        $conf = Yaf\Application::app()->getConfig();
        $this->base_uri = $conf->application->baseUri;
        $this->getView()->assign('BASE_URI', $this->base_uri);

        $this->is_ajax = $this->isAjax();

    }


    protected function getParam($key, $default='') {
        $s = $this->getRequest()->getParam($key, $default);
        return gf_valid_inputs($s, $default);
    }

    protected function getQuery($key, $default='') {
        $get = gf_get_query();
        $s = isset($get[$key]) ? $get[$key] : $default;
        return gf_valid_inputs($s, $default);
    }

    protected function getPost($key, $default='') {
        $post = gf_get_post();
        $s = isset($post[$key]) ? $post[$key] : $default;
        return gf_valid_inputs($s, $default);
    }

    protected function isAjax() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return TRUE;
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return TRUE;
        }
        return FALSE;
    }

}