<?php

namespace YS\Core\Repository;

use YS\Core\Database;
use YS\Core\Database\QueryBuilder;
use YS\Core\Entity\AbstractEntity;
use YS\Core\Entity\Collection;
use YS\Core\Service\FilterService;
use YS\Core\Util\ArrayUtil;
use YS\Core\Util\StringUtil;

abstract class AbstractRepository implements RepositoryInterface
{
    /** Подключение к БД */
    protected Database\QueryInterface $db;
    protected FilterService $filterService;

    /** Параметры по умолчанию для списка */
    protected array $defaultParams;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->filterService = new FilterService();
    }

    /**
     * Готовит запись к выдаче
     *
     * @param array|AbstractEntity $item Список записей
     * @param array $fields Поля
     * @param string $format Формат возвращаемых данных
     *
     * @return array|AbstractEntity
     */
    protected function prepareItem($item, array $fields, string $format = self::ARRAY_FORMAT)
    {
        $hasFields = $fields && $fields !== ['all'];

        if (($hasFields || $format === self::ARRAY_FORMAT) && $item instanceof AbstractEntity) {
             $item = $item->toArray();
        }

        if ($hasFields) {
            $item = $this->filterService->filterFieldsByKeys($item, $fields);
        }

        return $item;
    }

    /**
     * Готовит записи к выдаче
     *
     * @param array|Collection $items Список записей
     * @param array $params Параметры фильтрации
     * @param string $format Формат возвращаемых данных
     *
     * @return array|Collection
     */
    protected function prepareItems($items, array $params = [], string $format = self::ARRAY_FORMAT)
    {
        $hasFields = !empty($params['fields']) && $params['fields'] !== ['all'];

        // Если задан параметр `fields` или $format равен ARRAY_FORMAT мы не можем вернуть коллекцию сущностей.
        // Преобразовываем в ассоциативные массивы.
        if (($hasFields || $format === self::ARRAY_FORMAT) && $items instanceof Collection) {
            $items = $items->toArray();
        }

        if ($hasFields) {
            $items = $this->filterService->filterCollectionByKeys($items, $params['fields']);
        }

        return $items;
    }

    /**
     * Подготавливает параметры к работе
     *
     * @param array $params Параметры запроса
     *
     * @return array Возвращает подготовленные параметры
     */
    protected function prepareParams(array $params = []): array
    {
        $params = array_merge($this->getDefaultsParams(), $params);

        if (!empty($params['filter']) && is_string($params['filter'])) {
            $params['filter'] = $this->getFiltersFromString($params['filter']);
        }

        if (!empty($params['sort']) && is_string($params['sort'])) {
            $params['sort'] = $this->getSortParamsFromString($params['sort']);
        }

        if (!empty($params['fields']) && is_string($params['fields'])) {
            $params['fields'] = $this->getFieldsFromString($params['fields']);
        }

        if (isset($params['with_pagination'])) {
            $params['with_pagination']  = (bool)filter_var($params['with_pagination'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($params['with_total'])) {
            $params['with_total']  = (bool)filter_var($params['with_total'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($params['page'])) {
            $params['page'] = (int)$params['page'];
        }

        if (isset($params['limit'])) {
            $params['limit'] = (int)$params['limit'];

            if (empty($params['with_total']) && !empty($params['with_pagination']) && $params['limit'] !== -1) {
                $params['limit'] += 1;
            }
        }

        return $params;
    }

    /**
     * Получает фильтры из строки
     *
     * @param string $filterStr
     *
     * @return array
     */
    protected function getFiltersFromString(string $filterStr = ''): array
    {
        $parsed = [];

        if (empty($filterStr)) {
            return $parsed;
        }

        $re = '#(?<filter>[^;\s]+?):(?<value>[^;]+)#';
        preg_match_all($re, $filterStr, $filters, PREG_SET_ORDER);

        foreach ($filters as $filter) {
            $value = trim($filter['value'], " ,\t\n\r\0\x0B");

            // is_numeric на случай фильтра с значением 0, прим. parent:0
            if (!$value && !is_numeric($value)) {
                continue;
            }

            $value = explode(',', $value);
            $parsed[$filter['filter']] = $value;
        }

        return $parsed;
    }

    /**
     * Получает поля из строки
     *
     * @param string $fieldsStr
     *
     * @return array
     */
    protected function getFieldsFromString(string $fieldsStr): array
    {
        if (empty($fieldsStr)) {
            return [];
        }

        $fields = array_filter(array_map('trim', explode(',', $fieldsStr)));
        // В случае если первое поле в списке это 'default', проводит дополнительные манипуляции.
        $fields = $this->processDefaultFields($fields);

        return $fields;
    }

    /**
     * Возвращает список полей по умолчанию, с соответствующими изменениями.
     * Если в начале поля ничего не стоит или прописан `+`, оно будет добавлено к списку по умолчанию,
     * а если `-`, удалено из него.
     *
     * Пример:
     * $fields = [ 'default', '+author', '-uri' ];
     * $defaults = [ 'id', 'title', 'uri' ];
     *
     * Итоговый набор полей, после работы метода: [ 'id', 'title', 'author' ]
     * Метод добавил поле 'author` и убрал поле 'uri'.
     *
     * @param array $fields Список полей
     * @param array $defaults Поля по умолчанию
     *
     * @return array Если первое поле в списке имеет значение 'default', вернет список полей по умолчанию
     *               (с изменениями или без), если нет, вернет оригинальный список полей, без изменений.
     */
    private function processDefaultFields(array $fields, array $defaults = []): array
    {
        if ($fields[0] !== 'default') {
            return $fields;
        }

        $defaults = $defaults ?: $this->getDefaultsParams();
        $defaults = $defaults['fields'] ?? [];

        for ($i = 1; $i < count($fields); $i++) {
            $field    = $fields[$i];
            $operator = mb_substr($field, 0, 1);

            if (!in_array($operator, ['+', '-'], true)) {
                $defaults[] = $field;
                continue;
            }

            $field = ltrim($field, $operator);

            if ($operator === '+') {
                $defaults[] = $field;
                continue;
            }

            if ($operator === '-') {
                $index = array_search($field, $defaults);
                if ($index !== false) {
                    unset($defaults[$index]);
                }
            }
        }

        return $defaults;
    }

    /**
     * Получает параметры сортировки из строки
     *
     * @param string $sortStr
     *
     * @return array
     */
    protected function getSortParamsFromString(string $sortStr): array
    {
        $parsed = [];

        if (empty($sortStr)) {
            return $parsed;
        }

        $re = '#(?<field>[^:,]+):(?<type>asc|desc)(?:,|$)#i';
        preg_match_all($re, $sortStr, $sort, PREG_SET_ORDER);

        foreach ($sort as $value) {
            $field = trim($value['field']);
            $type  = mb_strtoupper($value['type']);

            if (!$field || !$type) {
                continue;
            }

            $parsed[$field] = $type;
        }

        return $parsed;
    }

    /**
     * Добавляет сортировку
     *
     * @param QueryBuilder $qb
     * @param string $sort
     * @param string $direction
     */
    protected function addSortQuery(QueryBuilder $qb, string $sort, string $direction) {}

    /**
     * Добавляет сортировки в порядке их поступления
     *
     * @param QueryBuilder $qb
     * @param array $params
     */
    protected function addSortQueries(QueryBuilder $qb, $params = [])
    {
        if (empty($params['sort'])) {
            return;
        }

        foreach ($params['sort'] as $sort => $direction) {
            $this->addSortQuery($qb, $sort, $direction === 'ASC' ? 'ASC' : 'DESC');
        }
    }

    /**
     * Добавляет фильтрацию
     *
     * @param QueryBuilder $qb
     * @param array $params
     */
    protected function addFilterQuery(QueryBuilder $qb, array $params = []) {}

    /**
     * Добавляет пагинацию
     *
     * @param QueryBuilder $qb
     * @param array $params
     */
    protected function addPaginationQuery(QueryBuilder $qb, array $params)
    {
        if (!isset($params['limit'], $params['page']) || $params['limit'] == -1) {
            return;
        }

        $page = $params['page'] > 0 ? $params['page'] - 1 : 0;
        $offset = $page * $params['limit'];

        $qb->setLimit($params['limit'], $offset);
    }

    /**
     * Добавляет фильтрацию по терминам
     * TODO: будет перенесен в другое место
     *
     * @param QueryBuilder $qb
     * @param array $terms
     * @param string|null $taxonomy
     */
    protected function addTermsFilterQuery(QueryBuilder $qb, array $terms, ?string $taxonomy = null)
    {
        if (!$terms) {
            return;
        }

        $taxonomyClause = $taxonomy
            ? 'ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = "' . $taxonomy . '"'
            : 'USING (term_taxonomy_id)'
        ;

        $qb
            ->addJoin(TABLE_WP_TERM_RELATIONSHIPS, 'tr', 'ON tr.object_id = p.ID')
            ->addJoin(TABLE_WP_TERM_TAXONOMY, 'tt', $taxonomyClause)
        ;

        if (count($terms) > 1) {
            $terms = implode(',', $terms);
            $qb->addWhere('tt.term_id IN (' . $terms . ')');

        } elseif ($terms) {
            $term = reset($terms);
            $qb->addWhere('tt.term_id = ' . $term);
        }
    }

    /**
     * Добавляет фильтрацию терминов по постам
     * TODO: будет перенесен в другое место
     *
     * @param QueryBuilder $qb
     * @param array $posts
     */
    protected function addTermsByPostsFilterQuery(QueryBuilder $qb, array $posts)
    {
        if (!$posts) {
            return;
        }

        $clause = count($posts) > 1
            ? 'IN (' . implode(',', $posts) . ')'
            : '= ' . reset($posts)
        ;

        $qb->addJoin(
            TABLE_WP_TERM_RELATIONSHIPS, 'tr',
            'ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tr.object_id ' . $clause
        );
    }

    /**
     * Добавляет сортировку постов по gmt дате
     *
     * @param QueryBuilder $qb
     * @param string $direction
     */
    protected function addSortPostByDateQuery(QueryBuilder $qb, string $direction)
    {
        $qb->addOrderBy('p.post_date_gmt', $direction);
    }


    /**
     * Вызывает методы добавляющие недостающие части в запрос, для получения тех или иных полей
     *
     * Пример:
     *
     * Для поля `attachment` необходимо добавить определенные условия в запрос, если его запросили в параметре `fields`.
     * В класс своего репозитория добавляем метод `addAttachmentFieldQuery`, где `Attachment` название поля
     * в camelCase формате, с заглавной буквы.
     *
     * @param QueryBuilder $qb Объект запроса который строится
     * @param array $params Параметры
     */
    protected function addFieldsQuery(QueryBuilder $qb, array $params)
    {
        if (empty($params['fields'])) {
            return;
        }

        $addFieldQueryMethods = $this->getAddFieldQueryMethods();

        if ($params['fields'] === ['all']) {
            foreach ($addFieldQueryMethods as $addMethod) {
                $this->{$addMethod}($qb);
            }
            return;
        }

        $fields = ArrayUtil::getKeysFromDotArray(array_unique($params['fields']));

        foreach ($fields as $field) {
            $field = StringUtil::formatCase($field, StringUtil::FORMAT_CAMEL_CASE);
            $addMethod = 'add' . ucfirst($field) . 'FieldQuery'; // E.g. addAttachmentFieldQuery

            if (in_array($addMethod, $addFieldQueryMethods, true)) {
                $this->{$addMethod}($qb);
            }
        }
    }

    /**
     * Возвращает методы для добавления lazy load полей к запросу
     *
     * @return array
     */
    private function getAddFieldQueryMethods()
    {
        try {
            $currentClass         = new \ReflectionClass($this);
            $methods              = $currentClass->getMethods(\ReflectionMethod::IS_PROTECTED);
            $methods              = array_column($methods, 'name');
            $addFieldQueryMethods = array_filter(
                $methods,
                fn($method) => preg_match('#^add.+?FieldQuery$#', $method)
            );
        } catch (\Throwable $e) {
            $addFieldQueryMethods = [];
        }

        return $addFieldQueryMethods;
    }

    /**
     * Формирует массив записей с пагинацией или без нее
     *
     * @param QueryBuilder $qb Объект запроса который строится
     * @param array $items Список записей
     * @param array $params Параметры запроса
     *
     * @return array
     */
    protected function paginateItems(QueryBuilder $qb, array $items, array $params)
    {
        if (!$params['with_pagination']) {
            return $items;
        }

        // Получение общего кол-ва записей
        $total = null;

        if ($params['with_total']) {
            // Если нужно знать общее кол-во записей
            $total = $this->getTotalCount($qb);
            $hasMore = ($params['page'] * $params['limit']) < $total;
        } else {
            $hasMore = count($items) === (int)$params['limit'];
            $hasMore && array_pop($items);
        }

        // Формирование массива с пагинацией
        $posts = [
            'data'       => $items,
            'pagination' => [
                'total' => $total,
                'more'  => $hasMore
            ]
        ];

        return $posts;
    }

    /**
     * Возвращает общее кол-во записей для запроса
     *
     * @param QueryBuilder $qb Объект с запросом
     *
     * @return int
     */
    protected function getTotalCount(QueryBuilder $qb): int
    {
        // Удаляем лимит, удалем сортировку
        // возможно еще что то будем тут удалять, чтобы максимально облегчить запрос
        $qb
            ->removeSelect()
            ->removeOrderBy()
            ->removeLimit()
            ->removeGroupBy()
            ->addSelect('COUNT(0)')
        ;

        return (int)$this->db
            ->prepare($qb->getQueryString())
            ->execute()
            ->fetchColumn();
    }

    /**
     * Подготавливает коллекцию сущностей из массива данных
     *
     * @param array $data
     * @param array $fields
     * @param string|null $entityClass Строка, которая является результатом Entity:class
     *
     * @return array|Collection
     */
    protected function prepareCollection(array $data, array $fields = [], ?string $entityClass = null) {
        $collection = new Collection();

        if ($entityClass) {
            foreach ($data as $item) {
                $entity = new $entityClass();

                $entity->fromArray($item);

                // Lazy Load связанных полей
                $entity->loadFields($fields);

                $collection[] = $entity;
            }
        }

        return $collection;
    }

    /**
     * Возвращает параметры по умочланию для списка
     *
     * @return array
     */
    protected function getDefaultsParams(): array {
        return array_merge(
            [
                'page'            => 1,
                'limit'           => 50,
                'sort'            => [],
                'filter'          => [],
                'fields'          => [],
                'with_pagination' => true,
                'with_total'      => false
            ],
            $this->defaultParams
        );
    }
}
