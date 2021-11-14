<?php

namespace YS\Core\Util;

use YS\Core\Entity\AbstractEntity;
use YS\Core\Service\AbstractService;
use YS\Site\Entity\PostEntity;

class FieldsUtil extends AbstractService
{
    /**
     * Заменяем ключи местами по массиву маппинга
     *
     * @param array $data
     * @param array $fieldsMap
     * @return array
     */
    public static function mapDataFields(array $data, array $fieldsMap)
    {
        $fieldsMap = array_map(fn($value) => StringUtil::formatCase($value, StringUtil::FORMAT_SNAKE_CASE), $fieldsMap);
        $fieldsMap = array_flip($fieldsMap);

        foreach ($data as $field => $value) {
            if (isset($fieldsMap[$field])) {
                $key        = $fieldsMap[$field];
                $data[$key] = $value;
                if ($key === $field) {
                    continue;
                }
                unset($data[$field]);
            }
        }

        return $data;
    }

    public static function getMetaFieldsKeys(array $fields, string $entity): array
    {
        $metaFields = [];
        $entity .= '::getFields';
        $properties = $entity();

        foreach ($fields as $field) {
            if (in_array($field, $properties)) {
                continue;
            }

            $metaFields[] = $field;
        }

        return $metaFields;
    }
}
