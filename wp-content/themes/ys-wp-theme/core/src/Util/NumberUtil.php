<?php

namespace RB\Site\Util;

use RB\Site\Service\AbstractService;

class NumberUtil extends AbstractService
{
    /**
     * Форматирует и возвращает положительное число, не равное нулю.
     * Значения меньше единицы, будут преобразованы в 1.
     *
     * @param string|float|int $number Число которое необходимо преобразовать
     *
     * @return float|int
     */
    public static function positiveNonZero($number)
    {
        $default = 1;

        if (!is_numeric($number)) {
            return $default;
        }

        $number = abs($number);
        return $number < 1 ? $default : $number;
    }
}