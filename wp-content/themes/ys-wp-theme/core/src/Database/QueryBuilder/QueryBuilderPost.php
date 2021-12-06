<?php

namespace YS\Core\Database\QueryBuilder;

use YS\Core\Database\QueryBuilder;
use YS\Core\Util\FieldsUtil;

class QueryBuilderPost extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->addSelect('p.*')
            ->addFrom(TABLE_WP_POSTS, 'p');
    }

    /**
     * Добавляет условие выбора объекта (по ID или slug)
     *
     * @param string $id ID или slug объекта
     * @param array $fields Столбцы используемые в условии
     */
    private function addIdQuery(string $id, array $fields): self
    {
        $format = '%s';
        $field  = $fields[1];

        if (is_numeric($id)) {
            $format = '%d';
            $field = $fields[0];
        }

        return $this->addWhere($field . ' = ' . $format);
    }

    public function addPostTypeQuery(string $postType): self
    {
        return $this->addWhere('p.post_type = "' . $postType . '"');
    }

    public function addPostIdQuery(string $id, array $fields = ['p.ID', 'p.post_name']): self
    {
        return $this->addIdQuery($id, $fields);
    }

    public function addFieldsQuery(array $fields, string $entityName): self
    {
        $fields = FieldsUtil::getMetaFieldsKeys($fields, $entityName);

        foreach ($fields as $field) {
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