<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-26 16:35
 * Created by PhpStorm.
 */
class Auth {

    private $password;
    private $secret = 'dapianzi-carl';

    public function __construct($password) {
        $this->password = $password;
    }

    public function encrypt($salt) {
        return md5($this->password.$salt);
    }

    public function salt() {
        $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = '';
        for($i=0; $i<6; $i++) {
            $salt .= $alpha[rand(0, 62)];
        }
        return $salt;
    }

}