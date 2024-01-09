<?php

namespace App\Services;

use PDO;

class DatabaseService
{
    protected PDO $pdo;

    public function __construct()
    {
        $databaseConfig = array(
            'host' => config('DB_HOST'),
            'database' => config('DB_NAME'),
            'username' => config('DB_USER_NAME'),
            'password' => config('DB_PASSWORD')
        );

        $dsn = "mysql:host={$databaseConfig['host']};dbname={$databaseConfig['database']}";
        $username = $databaseConfig['username'];
        $password = $databaseConfig['password'];

        $this->pdo = new PDO($dsn, $username, $password);
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
    }
}