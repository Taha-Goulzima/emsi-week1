<?php
class DB {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=js_profiles;charset=utf8',
            'fred',
            'zap',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function getPDO() {
        return $this->pdo;
    }
}
