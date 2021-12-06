<?php

namespace YS\Core\Database\QueryBuilder;

use YS\Core\Database\QueryBuilder;
use YS\Core\Util\FieldsUtil;

class QueryBuilderUser extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->addSelect('u.*')
            ->addFrom(TABLE_WP_USERS, 'u');
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

    public function addUserIdQuery(string $id, array $fields = ['u.ID', 'u.user_nicename']): self
    {
        return $this->addIdQuery($id, $fields);
    }

    public function addFieldsQuery(array $fields, string $entityName): self
    {
        $fields = FieldsUtil::getMetaFieldsKeys($fields, $entityName);

        foreach ($fields as $field) {
            $umKey = 'um_' . $field;
            $this
                ->addSelect($umKey . '.meta_value', $field)
                ->addLeftJoin(
                    TABLE_WP_USERMETA, $umKey,
                    'ON u.ID = ' . $umKey . '.user_id AND ' . $umKey . '.meta_key = "' . $field . '"'
                );
        }

        return $this;
    }
}