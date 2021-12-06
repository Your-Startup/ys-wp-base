<?php

namespace YS\Core\Database;

use YS\Core\Cache;

final class QueryWp extends Query
{
    public function __construct()
    {
        try {
            global $wpdb;
            $this->connection = $wpdb;

            if (!$this->connection instanceof \wpdb) {
                throw new \Exception('Нет подключения к БД.');
            }

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $this->cache = new Cache();
    }

    public function execute(): QueryWp
    {
        if (!$this->query) {
            return $this;
        }

        // Определяем тип запроса
        $queryType = $this->getQueryType($this->query);

        if ($queryType === 'SELECT') {
            $cacheKey = md5($this->query);
            $cache = $this->cache->get($cacheKey, $this->cacheGroup);
            $this->result = $cache ?: $this->connection->get_results($this->query, ARRAY_A);
            $this->rowCount = count($this->result);
            !$cache && $this->cache->set($cacheKey, $this->result, $this->cacheGroup);
        } else {
            $this->cache->off();
            $this->result = $this->rowCount = (int)$this->connection->query($this->query);
            $this->cache->on();
        }

        $this->lastError = $this->connection->last_error;
        return $this;
    }

    public function prepare(string $query, $args = []): QueryWp
    {
        $this->query = $args ? $this->connection->prepare($query, $args) : $query;

        return $this;
    }

    public function query(string $query)
    {
        // Определяем тип запроса
        $queryType = $this->getQueryType($this->query);

        if ($queryType === 'SELECT') {
            $this->result = $this->rowCount = $this->connection->get_results($query, ARRAY_A);
            $this->rowCount = count($this->result);
        } else {
            $this->result = $this->rowCount = (int)$this->connection->query($query);
        }

        $this->lastError = $this->connection->last_error;
        return $this->result;
    }

    public function fetch(): ?array
    {
        return $this->result ? array_shift($this->result) : null;
    }

    public function fetchAll(): array
    {
        return $this->result ?: [];
    }

    public function fetchColumn(int $colNumber = 0)
    {
        $result = $this->fetch();

        if (!$result) {
            return $result;
        }

        $keys = array_keys($result);
        $key  = $keys[$colNumber] ?? $keys[0];

        return $result[$key];
    }
}