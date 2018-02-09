<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 16:22
 * Created by PhpStorm.
 */
class UserModel extends DbModel {

    public function getUser($username, $token=FALSE) {
        if (FALSE !== $token) {
            $sql = 'SELECT u.username, s.expire_time FROM user u,session s WHERE token=? AND u.id=s.user_id';
            $session = $this->getRow($sql, array($token));
            if ($session && $session['username']==$username && $session['expire_time']>time()) {
                //pass
            } else {
                return null;
            }
        }
        $sql = 'SELECT u.id,username,email,mobile FROM user u WHERE username=? ';
        return $this->getRow($sql, array($username));
    }

    public function userLogin($username, $password) {
        $user = $this->getRow('SELECT id,username,password,salt,status FROM user WHERE username=?', array($username));
        if ($user && $user['password'] === md5($password.$user['salt'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}