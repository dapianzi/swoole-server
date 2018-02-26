<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-25 11:16
 * Created by PhpStorm.
 */
class AvatarModel extends DbModel {
    protected $conf = 'user';
    protected $table = 'avatar';

    public function getDefault() {
        $sql = "SELECT id,avatar_hash,avatar_url FROM {$this->table} WHERE is_default=1";
        return $this->getAll($sql);
    }

    public function getAvatar($hash) {
        $sql = "SELECT id,avatar_url FROM {$this->table} WHERE avatar_hash=?";
        return $this->getRow($sql, array($hash));
    }

}