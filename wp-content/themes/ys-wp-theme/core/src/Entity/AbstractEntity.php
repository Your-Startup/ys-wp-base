<?php
namespace YS\Core\Entity;

use YS\Core\Routes\ItemRoute;
use YS\Core\Service\ContentFormatter;
use YS\Core\Util\ArrayUtil;
use YS\Core\Util\DateUtil;
use YS\Core\Util\FieldsUtil;
use YS\Core\Util\StringUtil;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;

abstract class AbstractEntity
{
    const COLUMNS_DEFAULT_FIELDS_MAP = [];

    protected array $metaFields = [];
    private array $columnsMap = [];
    private array $modifiedColumns;
    /**
     * Свойства сущности которые нужно запретить заполнять при массовом присвоении данных, при вызове `fromArray`.
     * (Нестандартные поля, которых нет в таблицах БД, которые необходимо заполнять своими данными)
     *
     * @var array
     */
    protected array $guarded = [];
    /**
     * Свойства сущности которые нужно запретить отдавать, при вызове `toArray`.
     * (То что нужно скрыть в итоговом массиве)
     *
     * @var array
     */
    protected array $hidden = [];
    /**
     * Свойства сущности с ленивой загрузкой (загрузка только при обращении к геттер методу).
     * Используется в `toArray`, чтобы оградить эти свойства от автоматического заполнения при
     * формировании массива полей.
     * (То что не нужно отдавать всегда, а только при дополнительном вызове)
     *
     * @var array
     */
    protected array $lazyLoad = [];

    protected ItemRoute $routeService;
    public const PROTECTED_FIELDS = [
        'guarded',
        'hidden',
        'lazyLoad',
        'routeService',
        'metaFields'
    ];

    private ?\ReflectionClass $reflection;

    public function __construct()
    {
        try {
            $this->reflection = new \ReflectionClass($this);
        } catch (\ReflectionException $e) {
            $this->reflection = null;
        }

        $this->initProperties();
        $this->setDefaultValues();

        // Services
        $this->routeService  = new ItemRoute();
    }

    /**
     * Инициализирует все свойства сущности значениями по умолчанию, если прописана строгая типизация
     * и заданный тип поддерживается.
     */
    private function initProperties()
    {
        if (!($columns = $this->getSortedProperties())) {
            return;
        }

        $defaults = [
            'int'    => 0,
            'float'  => 0,
            'double' => 0,
            'string' => '',
            'bool'   => false,
            'array'  => [],
        ];

        foreach ($columns as $column) {
            // Если тип не указан, идем дальше
            if (!$column->hasType()) {
                continue;
            }

            $type = $column->getType();
            $name = $column->getName();

            // Если полученный тип не является встроенным или значение в поле уже установлено, идем дальше
            if (!$type->isBuiltin() || isset($this->{$name})) {
                continue;
            }

            // Если прописано что указанный тип свойства может принимать null, ставим и идем дальше
            if ($type->allowsNull()) {
                $this->{$name} = null;
                continue;
            }

            // Если тип, объект класса `ReflectionNamedType`, его имя нужно получать через `getName` метод
            // У классов `ReflectionType` и `ReflectionUnionType` его нет.
            if (method_exists($type, 'getName')) {
                $type = $type->getName();
            }

            if (is_string($type) && isset($defaults[$type])) {
                $this->{$name} = $defaults[$type];
            }
        }
    }

    /**
     * Конвертирует сущность в массив
     *
     * @param string $keyType
     *
     * @return array
     */
    public function toArray(string $keyType = StringUtil::FORMAT_SNAKE_CASE): array
    {
        $data    = [];
        $columns = $this->getSortedProperties();

        foreach ($columns as $column) {
            // Получаем имя свойства
            $originalColumnName  = $columnName = $column->getName();

            // Проверяем на то что этого свойства нет в запрещенных
            if (
                in_array($originalColumnName, $this->hidden, true)
                || in_array($originalColumnName, self::PROTECTED_FIELDS, true)
            ) {
                continue;
            }

            // Преобразуем camelCase имя свойства в snake_case или наоборот
            $columnName = StringUtil::formatCase($originalColumnName, $keyType);

            // Если свойство есть в списке на ленивую загрузку, просто записываем его текущее значение.
            if (in_array($originalColumnName, $this->lazyLoad, true)) {
                $data[$columnName] = $this->{$originalColumnName};
                continue;
            }

            // Формируем геттер метод
            $getterFunc = 'get' . ucfirst($originalColumnName);

            // Получаем значение через геттер метод
            $data[$columnName] = $this->{$getterFunc}();

            if (is_a($data[$columnName], Collection::class)) {
                $data[$columnName] = $data[$columnName]->toArray();
            }

            // Конвертируем значение (если необходимо)
            $data[$columnName] = $this->applyTypeBasedFieldValueConversion($column, $data[$columnName]);
        }

        return array_merge($data, array_filter($this->metaFields));
    }

    /**
     * Отдает свойства сущности отсортированные по иерархии наследования классов
     *
     * @return array
     */
    private function getSortedProperties(): array
    {
        if (!$this->reflection) {
            return [];
        }

        // Получаем все не статичные protected свойства сущности
        $properties = $this->reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        // Удаляем все статичные свойства из списка
        $properties = array_filter($properties, fn($prop) => !$prop->isStatic());

        // Все вариант с usort отказывались работать, поэтому был написан такой менее красивый способ.
        $classHierarchy = array_flip(
            array_reverse(
                array_unique(
                    array_column($properties, 'class')
                )
            )
        );

        $sortedProperties = [];
        foreach ($properties as $prop) {
            $classIndex = $classHierarchy[$prop->class];
            $sortedProperties[$classIndex][] = $prop;
        }

        ksort($sortedProperties);
        $sortedProperties = array_merge(...$sortedProperties);
        return $sortedProperties;
    }

    public static function getFields(): array
    {
        try {
            $reflection = new \ReflectionClass(static::class);
        } catch (\ReflectionException $e) {
            return [];
        }

        // Получаем все не статичные protected свойства сущности
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);

        // Удаляем все статичные свойства из списка
        $properties = array_filter($properties, fn($prop) => !$prop->isStatic());

        $baseProperties = [];
        foreach ($properties as $prop) {
            $name = $prop->getName();
            !in_array($name, self::PROTECTED_FIELDS) && $baseProperties[] = $name;
        }

        return $baseProperties;
    }

    /**
     * Конвертирует значение свойства в соответствующий тип, который задан в Doc-блоке.
     *
     * @param \ReflectionProperty $column Свойство сущности
     * @param mixed $value Значение
     *
     * @return mixed
     */
    private function applyTypeBasedFieldValueConversion(\ReflectionProperty $column, $value)
    {
        $supportedTypes = ['int', 'bool', 'float', 'double'];

        if ($column->hasType()) {
            return $value;
        }

        if (preg_match('#@var\s+([^\s]+)#', $column->getDocComment(), $matches)) {
            [, $type] = $matches;

            if (in_array($type, $supportedTypes, true)) {
                $typeFunc = $type . 'val';
                $value = $typeFunc($value);
            }
        }

        return $value;
    }

    /**
     * Конвертирует значение свойства в соответствующий тип
     *
     * @param string $column Имя свойства сущности
     * @param mixed $value Исходное значение
     *
     * @return mixed
     */
    private function applyType(string $column, $value)
    {
        if (!$this->reflection) {
            return $value;
        }

        $property = $this->reflection->getProperty($column);
        if (!$property->hasType()) {
            return $value;
        }

        $type         = $property->getType();
        $isAllowsNull = $type->allowsNull();

        // Если полученный тип не является встроенным, возвращаем как есть
        if (!$type->isBuiltin() ) {
            return $value;
        }

        // Если значение null и прописано что указанный тип свойства может его принимать, возвращаем как есть
        if ($isAllowsNull && $value === null) {
            return $value;
        }

        // Если тип, объект класса `ReflectionNamedType`, его имя нужно получать через `getName` метод
        // У классов `ReflectionType` и `ReflectionUnionType` его нет.
        if (method_exists($type, 'getName')) {
            $type = $type->getName();
        }

        // Для числовых свойств которые поддерживают значение null, если нет значения, возвращаем null
        if ($isAllowsNull && $value === '' && in_array($type, ['int', 'float', 'double', true])) {
            return null;
        }

        // Если типа нет в поддерживаемых, возвращаем как есть
        $supportedTypes = ['int', 'bool', 'float', 'double', 'string', 'array'];
        if (!in_array($type, $supportedTypes, true)) {
            return $value;
        }

        if ($type === 'array' && ($value == 0 || $value == '')) {
            $value = [];
        } else {
            settype($value, $type);
        }

        return $value;
    }

    /**
     * Заполняет сущность, данными из массива
     *
     * @param array $data
     */
    public function fromArray(array $data)
    {
        // Сбрасываем список измененных свойств
        $this->modifiedColumns = [];

        // Производим маппинг свойств (столбцов)
        $columns = $this->mapColumns($data);

        foreach ($columns as $column => $value) {
            // Проверяем на то что этого свойства нет в запрещенных
            if (in_array($column, $this->guarded, true) || in_array($column, self::PROTECTED_FIELDS, true)) {
                continue;
            }

            // Устанавливаем в мета поля, если нет свойства
            if (!property_exists($this, $column)) {
                $map        = array_flip($this->getColumnsMap());
                $mapDefault = array_flip($this::COLUMNS_DEFAULT_FIELDS_MAP);
                if (isset($mapDefault[$column]) || isset($map[$column])) {
                    continue;
                }

                $this->metaFields[$column] = $value;

                continue;
            }

            // Сохраняем изначальное значение, чтобы проверить, изменится ли оно после вызова сеттера
            $oldValue = null;
            if (isset($this->{$column})) {
                $oldValue = $this->{$column};
            }

            // Формируем сеттер метод
            $setterFunc = 'set' . ucfirst($column);
            // Вызываем сеттер метод для установки значения свойства
            $this->{$setterFunc}($value);

            // Если свойство изменилось после вызова сеттера, заносим его в список измененных
            if (isset($this->{$column}) && $oldValue !== $this->{$column}) {
                $this->modifiedColumns[$column] = $this->{$column};
            }
        }
    }

    public function getColumnsMap()
    {
        return $this->columnsMap;
    }

    public function setColumnsMap(array $map)
    {
        $this->columnsMap += $map;
    }


    /**
     * Делает мапинг полей
     *
     * @param array $columns
     *
     * @return array
     */
    private function mapColumns(array $columns): array
    {
        foreach ($columns as $key => $value) {
            $keyFromMap = $this->columnsMap[$key] ?? $this::COLUMNS_DEFAULT_FIELDS_MAP[$key] ?? null;
            if (!$keyFromMap) {
                continue;
            }

            $columns[$keyFromMap] = $value;
            unset($columns[$key]);
        }

        return $columns;
    }

    public function getModifiedColumns(string $case = StringUtil::FORMAT_SNAKE_CASE)
    {
        if ($case === StringUtil::FORMAT_CAMEL_CASE) {
            return $this->modifiedColumns;
        }

        foreach ($this->modifiedColumns as $key => $val) {
            $nKey = StringUtil::formatCase($key, $case);

            if ($nKey !== $key) {
                $this->modifiedColumns[$nKey] = $val;
                unset($this->modifiedColumns[$key]);
            }
        }
        return $this->modifiedColumns;
    }

    /**
     * Производит валидацию полей
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate()
    {
        $validator  = Validation::createValidatorBuilder()
            ->addMethodMapping('setValidationConstraints')
            ->getValidator();
        $violations = $validator->validate($this);
        return $violations;
    }

    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $columnName = lcfirst(substr($name, 3));
            if (property_exists($this, $columnName)) {
                return $this->{$columnName};
            }
        }

        if (0 === strpos($name, 'set')) {
            $columnName = lcfirst(substr($name, 3));
            if (property_exists($this, $columnName)) {
                $this->{$columnName} = $this->applyType($columnName, $params[0]);
                return true;
            }
        }

        throw new \BadMethodCallException(sprintf('Попытка вызвать несуществующий метод: %s.', $name));
    }

    /**
     * Загружает необходимые поля для которых предусмотрена ленивая загрузка
     * TODO: вынести в сервис?

     * @param $fields
     */
    public function loadFields(?array $fields)
    {
        if (!$fields) {
            $fields = ['all'];
        }

        if ($fields === ['all']) {
            foreach ($this->lazyLoad as $field) {
                $getterFunc = 'get' . ucfirst($field);
                $this->{$field} = $this->{$getterFunc}();
            }
            return;
        }

        $topLevelFields = ArrayUtil::getKeysFromDotArray($fields);
        foreach ($topLevelFields as $field) {
            $field = StringUtil::formatCase($field, StringUtil::FORMAT_CAMEL_CASE);

            if ($this->reflection) {
                try {
                    $property = $this->reflection->getProperty($field);
                    $lazyLoad = FieldsUtil::parsePhpdoc($property, 'lazyLoad');
                } catch (\ReflectionException $e) {}
            }

            if (empty($lazyLoad)) {
                continue;
            }

            $getterFunc = 'get' . ucfirst($field);
            $this->{$field} = $this->{$getterFunc}();
        }
    }

    /**
     * TODO: 23.11.20 vadeemch81 / Возможно не нужен.
     */
    public function getLazyLoadFields($case = StringUtil::FORMAT_CAMEL_CASE)
    {
        $fields = [];

        foreach ($this->lazyLoad as $field) {
            $fields[] = StringUtil::formatCase($field, $case);
        }

        return $fields;
    }

    public function __toString()
    {
        return $this->toArray();
    }

    /**
     * Устанавливает значения по умолчанию для полей сущности
     *
     * @return void
     */
    protected function setDefaultValues() {}

    /**
     * Задает правила валидации полей
     *
     * @param ClassMetadata $metadata
     */
    public static function setValidationConstraints(ClassMetadata $metadata) {}

    public function setDate($value)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->date = DateUtil::mysqlDateTimeToTimestamp($value);
    }

    public function setPublishedAt($value)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->publishedAt = DateUtil::mysqlDateTimeToTimestamp($value);
    }

    public function setUpdatedAt($value)
    {
        /** @noinspection PhpUndefinedVariableInspection */
        /** @noinspection PhpUndefinedFieldInspection */
        $this->updatedAt = DateUtil::mysqlDateTimeToTimestamp($value);
    }

    public function getContent()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return ContentFormatter::prepareContent($this->content);
    }

    public function getDescription()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return ContentFormatter::prepareContent($this->description);
    }
}
