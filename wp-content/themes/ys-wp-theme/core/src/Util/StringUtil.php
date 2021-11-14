<?php
namespace YS\Core\Util;

use YS\Core\Service\AbstractService;


class StringUtil extends AbstractService
{
    const FORMAT_SNAKE_CASE = 'snakecase';
    const FORMAT_CAMEL_CASE = 'camelcase';

    public static function toCamelCase(string $key, $ucfirst = false): string
    {
        $key = ucwords(str_replace(['-', '_'], ' ', $key));
        $key = str_replace(' ', '', $key);
        return $ucfirst ? ucfirst($key) : lcfirst($key);
    }

    public static function toSnakeCase(string $key): string
    {
        $key = ltrim(strtolower(
            preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $key)
        ), '_');

        return $key;
    }

    public static function formatCase(string $value, string $case = self::FORMAT_CAMEL_CASE)
    {
        if ($case === self::FORMAT_CAMEL_CASE) {
            $value = self::toCamelCase($value);
        } elseif ($case === self::FORMAT_SNAKE_CASE) {
            $value = self::toSnakeCase($value);
        }

        return $value;
    }

    /**
     * Обертка для Wordpress функции `wpautop`.
     * Заменяет двойной перенос строки на HTML конструкцию <p>...</p>, а одинарный на <br>.
     * @see wpautop
     *
     * @param string $text
     * @param bool $br
     *
     * @return string
     */
    public static function autoP($text, $br = true)
    {
        return wpautop($text, $br);
    }
}