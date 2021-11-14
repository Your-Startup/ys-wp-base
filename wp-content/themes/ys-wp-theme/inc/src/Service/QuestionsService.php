<?php
namespace RB\Site\Service;

class QuestionsService
{
    /**
     * Создает массивы по "типам фильтров для вопросов" из неотсортированных данных после получения их из БД
     *
     * @param array $data Сырой массив данных из БД
     *
     * @return array
     */
    public function getSortedFilters($data): array
    {
        $filters = [
            'period'    => $this->getSortedPeriodFilter(),
            'status'    => $this->getSortedStatusFilter(),
            'category'  => $this->getInitFilterData(),
            'bookmaker' => $this->getInitFilterData(),
        ];

        // корневые категории вопросов
        if (isset($data['root_categories'])) {
            $sorted = array_map(function($v) {
                return [
                    'label' => $v['title'],
                    'value' => $v['id']
                ];
            }, $data['root_categories']);
            $filters['category'] = array_merge($filters['category'], $sorted);
        }

        // букмекерские категории вопросов
        if (isset($data['bookmaker_categories'])) {
            $sorted = array_map(function($v) {
                return [
                    'label' => $v['title'],
                    'value' => $v['id']
                ];
            }, $data['bookmaker_categories']);
            $filters['bookmaker'] = array_merge($filters['bookmaker'], $sorted);
        }

        return $filters;
    }

    /**
     * Создает статические данные для "фильтра вопросов" по дате публикации (period)
     *
     * @return array
     */
    protected function getSortedPeriodFilter(): array
    {
        $filter = $this->getInitFilterData();
        $filter[] = [
            'label' => '30 дней',
            'value' => '30days'
        ];
        $filter[] = [
            'label' => '90 дней',
            'value' => '90days'
        ];

        return $filter;
    }

    /**
     * Создает статические данные для "фильтра вопросов" по статусу (status)
     *
     * @return array
     */
    protected function getSortedStatusFilter(): array
    {
        $filter = $this->getInitFilterData();
        $filter[] = [
            'label' => 'Открытые вопросы',
            'value' => 'opened'
        ];
        $filter[] = [
            'label' => 'Решенные вопросы',
            'value' => 'solved'
        ];

        return $filter;
    }

    /**
     * Создает базовый массив данных "фильтра для вопросов"
     *
     * @return array
     */
    protected function getInitFilterData(): array
    {
        $filter = [
            [
                'label' => 'Все',
                'value' => 'all'
            ]
        ];
        return $filter;
    }

    /**
     * Заполняет "типы фильтров для вопросов" оставшимися данными
     *
     * @param array $data Массив данных с четырьма фильтрами для вопросов
     *
     * @return array
     */
    public function getFilledFilters($data): array
    {
        $filters['period'] = [
            'label'  => 'Период',
            'filter' => 'period',
            'type'   => 'radio',
            'values' => $data['period']
        ];

        $filters['status'] = [
            'label'  => 'Статус',
            'filter' => 'status',
            'type'   => 'radio',
            'values' => $data['status']
        ];

        $filters['category'] = [
            'label'  => 'Категории',
            'filter' => 'category',
            'type'   => 'radio',
            'values' => $data['category']
        ];

        $filters['bookmaker'] = [
            'label'  => 'Букмекеры',
            'filter' => 'bookmaker',
            'type'   => 'radio',
            'values' => $data['bookmaker']
        ];

        return $filters;
    }
}