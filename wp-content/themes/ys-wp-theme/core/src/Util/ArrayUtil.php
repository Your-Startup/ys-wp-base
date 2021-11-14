<?php
namespace YS\Core\Util;

use YS\Core\Service\AbstractService;

class ArrayUtil extends AbstractService
{
    /**
     *
     * Возвращает ключи первого уровня из массива с точечной записью ключей (dot.key)
     *
     * <br><br>
     * Пример:
     *
     * $keys = [ 'field1', 'field2.subfield2' ];
     *
     * <br><br>
     * Результат:
     * [ 'field1', 'field2' ]
     *
     * @param array $array Исходный массив
     *
     * @return array
     */
    public static function getKeysFromDotArray(array $array = [])
    {
        $keys  = [];

        foreach ($array as $key) {
            $list = explode('.', $key);

            if (count($list) > 1) {
                $key = array_shift($list);
            }

            $keys[] = $key;
        }

        return $keys;
    }

    public static function isAssociative(array $arr)
    {
        return $arr && array_keys($arr) !== range(0, count($arr) - 1);
    }
}