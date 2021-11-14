<?php
namespace YS\Core\Util;

class DateUtil
{
    /**
     * Конвертирует время и дату из MySQL формата в метку времени Unix
     *
     * @param string $dateTime Время и дата в MySQL формате
     *
     * @return int|null
     */
    public static function mysqlDateTimeToTimestamp(?string $dateTime): ?int
    {
        if (self::isTimestamp($dateTime)) {
            return $dateTime;
        }

        $timestamp = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime, new \DateTimeZone('UTC'));
        $timestamp = $timestamp ? $timestamp->getTimestamp() : null;

        return $timestamp;
    }

    /**
     * Проверяет что переданный аргумент является временной меткой
     *
     * @param int|string $timestamp Временная метка
     *
     * @return bool
     */
    public static function isTimestamp($timestamp): bool
    {
        try {
            new \DateTime('@' . $timestamp);
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Подготавливает временные промежутки
     * Возращвет массив с временными промежутками
     *
     * @param array $dates
     *
     * @return array
     * [from] меньшая дата - dateFrom
     * [to] большая дата - dateTo
     */
    public static function prepareDatesForFilter($dates)
    {
        $dates = [
            array_shift($dates),
            ($dates ? reset($dates) : null)
        ];

        $newDates = [];

        foreach ($dates as $date) {
            if (!$date) {
                continue;
            }

            try {
                $datePrepared     = new \DateTime($date, new \DateTimeZone('UTC'));
                $newDates[] = $datePrepared->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $timestamp = !self::isTimestamp($date) ? strtotime($date) : $date;
                $newDates[] = $timestamp ? gmdate('Y-m-d H:i:s', $timestamp) : null;
            }
        }

        usort($newDates, fn($a, $b) => $a <=> $b);
        return [
            'from' => $newDates[0] ?? null,
            'to'   => $newDates[1] ?? null
        ];
    }
}