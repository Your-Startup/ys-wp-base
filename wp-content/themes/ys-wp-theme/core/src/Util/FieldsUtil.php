<?php

namespace YS\Core\Util;

use ReflectionClass;
use ReflectionProperty;
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
        if (!class_exists($entity)) {
            return [];
        }

        $metaFields = [];
        $entity     = new $entity;

        $map        = array_flip($entity->getColumnsMap());
        $mapDefault = array_flip($entity::COLUMNS_DEFAULT_FIELDS_MAP);

        try {
            $entityReflection = new ReflectionClass($entity);
        } catch (\ReflectionException $e) {
            return [];
        }

        if ($fields === ['all']) {
            // Получаем все не статичные protected свойства сущности
            $properties = $entityReflection->getProperties(\ReflectionProperty::IS_PROTECTED);

            // Удаляем все статичные свойства из списка
            $properties = array_filter($properties, fn($prop) => !$prop->isStatic());

            $fields = [];
            foreach ($properties as $property) {
                $field = $property->getName();
                if (in_array($field, $entity::PROTECTED_FIELDS)) {
                    continue;
                }

                $fields[] = $field;
            }
        }

        foreach ($fields as $field) {
            self::prepareField($metaFields, $field, $entityReflection, $map, $mapDefault);
        }

        return $metaFields;
    }

    /**
     * @param ReflectionProperty $property
     * @param string|null $get
     *
     * @return array|bool
     */
    public static function parsePhpdoc(ReflectionProperty $property, string $get = null)
    {
        // Retrieve the full PhpDoc comment block
        $doc = $property->getDocComment();

        // Trim each line from space and star chars
        $lines = array_map(fn($line) => trim($line, " *"), explode("\n", $doc));


        // Retain lines that start with an @
        $lines = array_filter($lines, function ($line) {
            return str_starts_with($line, "@");
        });

        $args = [];

        // Push each value in the corresponding @param array
        foreach ($lines as $line) {
            [$param, $value] = explode(' ', $line, 2);

            if ($param === "@lazyLoad") {
                $args['lazyLoad'] = true;
                continue;
            }

            if ($param === "@param") {
                $value = trim($value, "$");
                $value = trim($value, "\r");
            }

            $param = trim($param, "@");
            $args[$param][] = $value;
        }

        if ($get) {
            return $args[$get] ?? false;
        }

        return $args;
    }


    private static function prepareField(array &$metaFields, string $field, ReflectionClass $entityReflection, array $map = [], array $mapDefault = [])
    {
        $key = StringUtil::formatCase($field);

        try {
            $property = $entityReflection->getProperty($key);
            $args     = self::parsePhpdoc($property);

            if (isset($args['lazyLoad'])) {
                return;
            }

            // Если для поля нужны данные из других полей
            if (isset($args['param'])) {
                foreach ($args['param'] as $param) {
                    self::prepareField($metaFields, $param, $entityReflection);
                }

                return;
            }
        } catch (\ReflectionException $e) {}

        // Если поле дефолтное
        if (isset($mapDefault[$key])) {
            return;
        }

        // Если заправшивает доп поле из меты
        $metaKey = $map[$key] ?? $field;
        !in_array($metaKey, $metaFields) && $metaFields[] = $metaKey;
    }
}
