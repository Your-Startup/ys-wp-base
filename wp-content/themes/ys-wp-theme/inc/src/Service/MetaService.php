<?php
namespace YS\Core\Service;

use YS\Core\Database\{
    QueryBuilder,
    Query
};

/**
 * Класс для работы с мета полями.
 *
 * Подходит для тех целей когда нужно получить значения мета полей конкретного объекта,
 * не выполняя при этом каждый раз запрос в БД.
 */
class MetaService extends AbstractService
{
    /** @var array */
    private $meta;
    /** @var Query */
    private $connection;

    public function __construct(Query $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Загружает мета поля в память
     *
     * @param string|int $id ID объекта (post_id, term_id, user_id)
     * @param string $type (Необязательный), Тип меты.<br>
     *      Допустимые значения "post", "term", "user".<br>
     *      По умолчанию: "post"
     */
    public function loadMeta($id, $type = 'post'): void
    {
        $idKey     = 'meta.post_id';
        $metaTable = TABLE_WP_POSTMETA;

        if ($type === 'term') {
            $idKey = 'meta.term_id';
            $metaTable = TABLE_WP_TERMMETA;
        }

        if ($type === 'user') {
            $idKey = 'meta.user_id';
            $metaTable = TABLE_WP_USERMETA;
        }

        $query = new QueryBuilder();

        $query
            ->addSelect('meta.meta_key', 'key')
            ->addSelect('meta.meta_value', 'value')

            ->addFrom($metaTable, 'meta')

            ->addWhere($idKey . ' = %d')
            ->addWhere('meta.meta_value <> ""')
        ;

        // Получение данных
        $this->connection->prepare($query->getQueryString(), $id);
        $this->connection->execute();

        $data = $this->connection->fetchAll();

        if (!$data) {
            $this->meta = [];
        } else {
            $this->meta = array_column($data, 'value', 'key');
        }
    }

    /**
     * Возвращает значение поля и если необходимо применяет колбэк для его обработки
     *
     * @param string $key Ключ поля
     * @param string|callable $sanitizeCallback Функция для обработки возвращаемого значения
     *
     * @return mixed|null
     */
    public function getField(string $key, $sanitizeCallback = null)
    {
        $value = $this->meta[$key] ?? '';

        $value = is_callable($sanitizeCallback)
            ? $sanitizeCallback($value)
            : $value
        ;

        return $value;
    }

    /**
     * @param string $key
     * @param string|null $subfieldKey
     *
     * @return array|null
     */
    public function getRepeaterField(string $key, ?string $subfieldKey = null)
    {
        if (empty($this->meta[$key])) {
            return [];
        }

        // TODO: написать реализацию, учитывая вложенные репитер поля.
    }

    /**
     * Устанавливает массив мета данных
     *
     * @param array $meta Мета данные
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    /**
     * Возвращает массив мета данных
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}