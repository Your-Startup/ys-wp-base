<?php

namespace YS\Core\Database;

use YS\Core\Cache;

abstract class Query implements QueryInterface
{
    /** Покдлючение к БД */
    protected $connection;
    /** Кэш */
    protected Cache $cache;
    /** Кэш группа для запросов */
    protected string $cacheGroup = 'db';
    /** Последний запрос */
    protected string $query = '';
    /** Количество строк, затронутых последним SQL-запросом */
    public int $rowCount = 0;
    /** Результат запроса */
    protected array $result;

    public string $lastError = '';

    /**
     * Возвращает тип запроса в верхнем регистре
     *
     * @param string $query Строка SQL запроса
     *
     * @return string
     */
    protected function getQueryType(string $query): string
    {
        $re = '#^\s*(?<type>EXPLAIN|SELECT|DELETE|UPDATE|INSERT|REPLACE)\s#i';
        preg_match($re, $query, $matches);

        if (!empty($matches['type'])) {
            $type = $matches['type'];
        }

        return isset($type) ? mb_strtoupper($type) : '';
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }
}