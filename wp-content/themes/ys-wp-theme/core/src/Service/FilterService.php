<?php

namespace YS\Core\Service;

use YS\Core\Util\ArrayUtil;

class FilterService extends AbstractService
{
    /**
     * Фильтрует набор полей по ключам
     *
     * Помимо базовой фильтрации по простым ключам, возможна фильтрация вложенных массивов,
     * путем указания ключей массива через точку.

     * <br><br>
     * Пример:
     *
     * $fields = [
     *      'field1' => 'value',
     *      'field2' => [
     *          'subfield1' => 'subvalue',
     *          'subfield2' => 'subvalue'
     *       ],
     *      'field3' => 'value'
     * ];
     *
     * $keys = [ 'field1', 'field2.subfield2' ];
     *
     * <br><br>
     * Результат:
     * [
     *      'field1' => 'value',
     *      'field2' => [
     *          'subfield2' => 'subvalue'
     *      ]
     * ]
     *
     * @param array $fields Наборе полей в виде ассоциативного массива ключ-значение
     * @param array $keys Ключи которые необходимо оставить
     *
     * @return array Возвращает отфильтрованный массив.
     */
    public function filterFieldsByKeys(array $fields, array $keys)
    {
        $multiKeys = [];

        foreach ($keys as $index => $key) {
            $list = explode('.', $key);

            if (count($list) > 1) {
                $key = array_shift($list);
                $multiKeys[$key][] = implode('.', $list);
                unset($keys[$index]);
            }
        }

        foreach ($fields as $field => $value) {
            if (in_array($field, $keys, true)) {
                continue;
            }

            if (isset($multiKeys[$field])) {

                if (is_array($value) && $value) {
                    $value = ArrayUtil::isAssociative($value)
                        // Если значение ассоциативный массив, фильтруем дальше по внутренним полям.
                        ? $this->filterFieldsByKeys($value, $multiKeys[$field])
                        // Если значение нумерованный массив, фильтруем как коллекцию сущностей
                        : $this->filterCollectionByKeys($value, $multiKeys[$field])
                    ;
                } else {
                    // Если нет, считаем что получен некорректный формат данных и записываем null
                    $value = null;
                }

                // Если получили конечное значение или null, записываем в отфильтрованный массив полей
                if ($value || !isset($value)) {
                    $fields[$field] = $value;
                    continue;
                }
            }

            unset($fields[$field]);
        }

        return $fields;
    }

    /**
     * Фильтрует массив записей по ключам.
     *
     * @param array $collection Коллекция записей (коллекция сущностей преобразованных в массив)
     * @param array $keys Ключи которые необходимо оставить
     *
     * @uses FilterService::filterFieldsByKeys
     *
     * @return array Возвращает отфильтрованный массив записей.
     */
    public function filterCollectionByKeys(array $collection, array $keys)
    {
        foreach ($collection as &$item) {
            $item = $this->filterFieldsByKeys($item, $keys);
        }
        unset($item);

        return $collection;
    }
}