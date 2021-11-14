<?php
namespace YS\Core;

use YS\Core\Database\QueryInterface;
use YS\Core\Database\QueryWp;

final class Database
{
    /** Экземпляр текущего класса */
    private static $instance;

    /** Покдлючение к БД */
    private QueryInterface $connection;

    private function __construct(QueryInterface $adapter)
    {
       $this->connection = $adapter;
    }

    /**
     * @return mixed
     */
    public static function getConnection()
    {
        if (Database::$instance === null) {
            Database::$instance = new Database(new QueryWp());
        }

        return Database::$instance->connection;
    }

    private function __clone() {}
    public function __wakeup() {}
}