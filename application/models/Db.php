<?php

/**
 *
 * @Author: Carl
 * @Since: 2017/4/11 15:54
 * Created by PhpStorm.
 */
class DbModel extends DbClass
{
    protected $conf = 'app';
    // table name
    protected $table = '';
    // primary key
    protected $pk = 'id';

    public function __construct($tableName = '') {
        $confName = $this->conf;
        $conf = Yaf\Application::app()->getConfig()->$confName;
        parent::__construct($conf->dsn, $conf->username, $conf->password);

        //todo: auto fix table name
        $this->table = $tableName;
        if (empty($this->table)) {
            $this->table = strtolower(str_replace('Model', '', get_class($this)));
        }
    }

    public function all($where=array()) {
        if ($where) {
            $where_case = $this->makeWhereSQL($where, 'AND');
            return $this->getAll("SELECT * FROM {$this->table} WHERE {$where_case}", array_values($where));
        } else {
            return $this->getAll("SELECT * FROM {$this->table}");
        }
    }

    public function get($id) {
        return $this->getRow("SELECT * FROM {$this->table} WHERE {$this->pk}=?", array($id));
    }

    public function add($data) {
        return $this->insert($this->table, $data);
    }

    public function edit($id, $data) {
        return $this->update($this->table, $data, array($this->pk => $id));
    }

    public function set($ids, $field, $value) {
        return $this->update($this->table, array($field=>$value), array($this->pk => $ids));
    }

    public function del($ids) {
        return $this->delete($this->table, array($this->pk => $ids));
    }
}