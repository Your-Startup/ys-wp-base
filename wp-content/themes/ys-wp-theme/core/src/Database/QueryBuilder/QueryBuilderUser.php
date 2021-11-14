<?php

namespace YS\Core\Database\QueryBuilder;

use YS\Core\Database\QueryBuilder;

class QueryBuilderUser extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->addSelect('p.*')
            ->addFrom(TABLE_WP_POSTS, 'p')
            ->addWhere('p.post_status = "publish"');
    }

    /**
     * Добавляет условие выбора объекта (по ID или slug)
     *
     * @param string $id ID или slug объекта
     * @param array $fields Столбцы используемые в условии
     */
    private function addIdQuery(string $id, array $fields)
    {
        $format = '%s';
        $field  = $fields[1];

        if (is_numeric($id)) {
            $format = '%d';
            $field = $fields[0];
        }

        $this->addWhere($field . ' = ' . $format);
    }

    public function addPostTypeQuery(string $postType): static
    {
        $this->addWhere('p.post_type = "' . $postType . '"');
        return $this;
    }

    protected function addUserIdQuery(string $id, array $fields = ['u.ID', 'u.user_nicename']): static
    {
        $this->addIdQuery($id, $fields);
        return $this;
    }

    public function addFieldsQuery(array $defaultFields, array $params): static
    {
        if (empty($params['fields'])) {
            return $this;
        }

        /*if ($params['fields'] === ['all']) {
            return;
        }*/

        foreach ($params['fields'] as $field) {
            //if (isset($this->defaultFields[$field])) {
            if (in_array($field, $defaultFields)) {
                continue;
            }

            $pmKey = 'pm_' . $field;
            $this
                ->addSelect($pmKey . '.meta_value', $field)
                ->addLeftJoin(
                    TABLE_WP_POSTMETA, $pmKey,
                    'ON p.ID = ' . $pmKey . '.post_id AND ' . $pmKey . '.meta_key = "' . $field . '"'
                );
        }

        return $this;
    }
}