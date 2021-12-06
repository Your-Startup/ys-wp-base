<?php

namespace YS\Core\Database\QueryBuilder;

use YS\Core\Database\QueryBuilder;
use YS\Core\Util\FieldsUtil;

class QueryBuilderTerm extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->addSelect('t.*')
            ->addSelect('tt.description')
            ->addSelect('tt.count', 'postsCount')
            ->addSelect('tt.parent', 'parent')
            ->addFrom(TABLE_WP_TERMS, 't');
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

    public function addTaxonomyQuery(string $taxonomy): self
    {
        return $this
            ->addJoin(TABLE_WP_TERM_TAXONOMY, 'tt', 'ON t.term_id = tt.term_id')
            ->addWhere('tt.taxonomy = "' . $taxonomy . '"');
    }

    public function addTermIdQuery(string $id, array $fields = ['t.term_id', 't.slug']): self
    {
        return $this->addIdQuery($id, $fields);
    }

    public function addFieldsQuery(array $fields, string $entityName): self
    {
        $fields = FieldsUtil::getMetaFieldsKeys($fields, $entityName);

        foreach ($fields as $field) {
            $pmKey = 'tm_' . $field;
            $this
                ->addSelect($pmKey . '.meta_value', $field)
                ->addLeftJoin(
                    TABLE_WP_TERMMETA, $pmKey,
                    'ON t.term_id = ' . $pmKey . '.term_id AND ' . $pmKey . '.meta_key = "' . $field . '"'
                );
        }

        return $this;
    }
}