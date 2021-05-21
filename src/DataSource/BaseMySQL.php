<?php

namespace App\Datasource;

use Exception;
use PDO;

class BaseMySQL
{
    private PDO $connection;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $dns = 'mysql:host=' . $_ENV['DATABASE_SERVER_NAME'] . ':' . $_ENV['DATABASE_SERVER_PORT'] . ';'
            . 'dbname=' .  $_ENV['DATABASE_NAME'] . ';';
        $user = $_ENV['DATABASE_USER'];
        $password = $_ENV['DATABASE_PASSWORD'];

        try {
            $this->connection = new PDO($dns, $user, $password);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
