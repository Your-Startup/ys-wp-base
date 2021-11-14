<?php

namespace YS\Core;

class Cache
{
    const MINUTE_IN_SECONDS = 60;
    const HOUR_IN_SECONDS   = 60  * self::MINUTE_IN_SECONDS;
    const DAY_IN_SECONDS    = 24  * self::HOUR_IN_SECONDS;
    const WEEK_IN_SECONDS   = 7   * self::DAY_IN_SECONDS;
    const MONTH_IN_SECONDS  = 30  * self::DAY_IN_SECONDS;
    const YEAR_IN_SECONDS   = 365 * self::MONTH_IN_SECONDS;

    /**
     * Флаг отвечающий за работу кэширования
     *
     * @var bool
     */
    private bool $active = true;

    /**
     * Записывает данные в кэш
     *
     * @param string $key
     * @param $value
     * @param string $group
     * @param int $expiration
     *
     * @return bool
     */
    public function set(string $key, $value, string $group = '', int $expiration = self::MINUTE_IN_SECONDS * 2): bool
    {
        return wp_cache_set($key, $value, $group, $expiration);
    }

    /**
     * Получает данные из кэша
     *
     * @param string $key
     * @param string $group
     *
     * @return bool|mixed
     */
    public function get(string $key, string $group = '')
    {
        if (!$this->active) {
            return false;
        }

        return wp_cache_get($key, $group);
    }

    /**
     * Удаляет кэш
     *
     * @param string $key
     * @param string $group
     * @param int $time
     *
     * @return bool
     */
    public function delete(string $key, string $group = '', int $time = 0): bool
    {
        return wp_cache_delete($key, $group, $time);
    }

    /**
     * Включает работу кэша
     */
    public function on()
    {
        $this->active = true;
    }

    /**
     * Выключает работу кэша
     */
    public function off()
    {
        $this->active = false;
    }
}
