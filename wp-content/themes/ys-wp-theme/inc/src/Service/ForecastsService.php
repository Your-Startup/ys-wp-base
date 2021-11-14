<?php
namespace RB\Site\Service;

class ForecastsService
{
    public function prepareArgsForShowFilter(string $show): array
    {
        $args    = [];
        $allowed = ['new', 'today', 'tomorrow', 'popular', 'actual', 'unfinished', 'unfinished_popular'];

        if (!in_array($show, $allowed)) {
            return $args;
        }

        try {
            $dtStart = new \DateTime('now', new \DateTimeZone('UTC'));
            $dtEnd   = new \DateTime('now', new \DateTimeZone('UTC'));
        } catch (\Throwable $e) {
            return $args;
        }

        $realTime      = time();
        $dateStart     = null;
        $dateEnd       = null;
        $eventDuration = 3 * HOUR_IN_SECONDS;

        switch ($show) {
            // Последние (Новые)
            case 'new' :
                $args['order[createDate]'] = 'desc';
                break;
            // На сегодня
            case 'today' :
                // 24 часа + 5.5 часов чтобы показывать и ночные прогнозы
                $dateStart = $dtStart->setTimestamp($realTime - $eventDuration);
                $dateEnd   = $dtEnd->setTimestamp($dtStart->getTimestamp() + (29.5 * HOUR_IN_SECONDS));
                break;
            // На завтра
            case 'tomorrow' :
                // 24 часа + 5.5 часов чтобы показывать и ночные прогнозы
                $dateStart = $dtStart->setTimestamp($realTime + DAY_IN_SECONDS)->setTime(0, 0, 0);
                $dateEnd   = $dtEnd->setTimestamp($dtStart->getTimestamp() + (29.5 * HOUR_IN_SECONDS));
                break;
            // Популярные
            case 'popular' :
                $dateStart = $dtStart->setTimestamp($realTime - (3 * DAY_IN_SECONDS));
                $args['order[customViews]'] = 'desc';
                break;
            // Недавние прогнозы, включая завершенные
            case 'actual' :
                $dateStart = $dtStart->setTimestamp($realTime - (2 * DAY_IN_SECONDS))->setTime(0, 0, 0);
                $dateEnd   = $dtEnd->setTimestamp($realTime + DAY_IN_SECONDS)->setTime(23, 59, 59);
                $args['order[createDate]'] = 'desc';
                break;
            // Незавершенные популярные прогнозы
            case 'unfinished_popular':
                $dateStart = $dtStart->setTimestamp($realTime - $eventDuration);
                $args['order[customViews]'] = 'desc';
                break;
            // Незавершенные прогнозы
            case 'unfinished' :
                $dateStart = $dtStart->setTimestamp($realTime - $eventDuration);
                break;
        }

        $dateStart && $args['bets.coupons.event.dateStart[after]']  = $dateStart->format('Y-m-d H:i:s');
        $dateEnd   && $args['bets.coupons.event.dateStart[before]'] = $dateEnd->format('Y-m-d H:i:s');

        return $args;
    }
}
