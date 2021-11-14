<?php
namespace RB\Core\Service;

abstract class AdditionalFieldsService extends AbstractService
{
    /** @var MetaService */
    protected $meta;

    /** @var int ID записи */
    protected $itemId;

    /**
     * Загружает мета данные для дальнейшего использования, если подключен сервис (MetaService)
     *
     * @param int $id ID записи
     */
    private function preloadMeta($id)
    {
        if (isset($this->meta)) {
            $this->meta->loadMeta($id);
        }
    }

    /**
     * Записывает значения дополнительных полей в массив данных.
     *
     * @param array $data Массив данных
     * @param array $fields Дополнительные поля которые необходимо добавить
     *
     * @return array
     */
    public function addFields(array $data, array $fields = ['all']): array
    {
        if (!isset($data['id'])) {
            return $data;
        }

        $this->itemId = (int)$data['id'];
        $this->preloadMeta($this->itemId);

        // Получаем маппинг полей для записи
        $fieldsMap = $this->getFieldsMapping();

        $fields === ['all'] && $fields = array_keys($fieldsMap);

        // Загружаем только необходимые поля из `$fields`
        foreach ($fields as $field) {
            if (!isset($fieldsMap[$field])) {
                continue;
            }

            $dataKey = $fieldsMap[$field]['field'];
            if (isset($data[$dataKey])) {
                continue;
            }

            $func = $fieldsMap[$field]['func'];
            $args = $fieldsMap[$field]['args'];
            $data[$dataKey] = call_user_func($func, ...$args);
        }

        return $data;
    }

    /**
     * Отдает информацию о том как и какие поля загружать
     *
     * Формат:
     *
     * запрошенное_поле => [
     *      'field' => 'полеСущности',
     *      'func' => 'Метод который нужно вызвать для получения поля`
     *      'args' => 'Аргументы передаваемые в метод`
     * ]
     *
     * Пример:
     *
     * Было запрошено поле с ленивой загрузкой, `logo`.
     * Для того чтобы получить значение этого поля, нужно заполнить поле `logoId`.
     * Получать значение поля мы будем из меты, поэтому воспользуемся сервисом MetaService и методом `getField`.
     * Мы знаем что это поле лежит в мете под ключом `fs_general_logo` и нам нужно получить его в виде числа.
     * Поэтому в аргументы передаем ['fs_general_logo', 'intval'], где 'intval' ф-ия которая будет вызвана для
     * того чтобы привести значение поля к числу.
     *
     * 'logo' => [
     *      'field' => 'logoId',
     *      'func' => [$this->meta, 'getField'],
     *      'args' => ['fs_general_logo', 'intval']
     * ],
     *
     * @return array
     */
    abstract protected function getFieldsMapping(): array;
}