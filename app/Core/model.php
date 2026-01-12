<?php

require_once __DIR__ . '/db_untils.php';

class Model {
    protected $db;

    public function __construct() {
        $this->db = new DB_UTILS();
    }

    public function connect() {
        return Database::getConnection();
    }
}