<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 16:22
 * Created by PhpStorm.
 */
class UserModel extends DbModel {
    protected $conf = 'user';

    public function getUser($username, $token=FALSE) {
        if (FALSE !== $token) {
            $sql = 'SELECT s.id,u.username, s.expire_time FROM user u,session s WHERE s.token=? AND u.id=s.user_id';
            $session = $this->getRow($sql, array($token));
            if ($session && $session['username']==$username) {
                if ($session['expire_time'] <= time()) {
                    // session expired
                    $this->delete('session', array('id'=>$session['id']));
                    return null;
                }
            } else {
                return null;
            }
        }
        $sql = 'SELECT u.id,u.username,u.email,u.mobile,s.token FROM user u LEFT JOIN session s ON u.id=s.user_id WHERE username=? ';
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

    public function saveUserToken($uid, $token, $expire=30) {
        $data = array(
            'user_id' => $uid,
            'token' => $token,
            'expire_time' => time()+$expire*86400,
        );
        $this->insert('session', $data);
    }
}