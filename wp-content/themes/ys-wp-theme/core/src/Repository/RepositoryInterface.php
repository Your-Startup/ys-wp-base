<?php

namespace YS\Core\Repository;

use YS\Core\Entity\AbstractEntity;
use YS\Core\Entity\Collection;

interface RepositoryInterface
{
    const ARRAY_FORMAT  = 'array';
    const ENTITY_FORMAT = 'entity';

    /**
     * Получает запись
     *
     * @param string $id ID записи
     * @param array|null $fields Какие поля возвращать
     * @param string $format Формат возвращаемых данных
     *
     * @return AbstractEntity|array
     */
    public function find(string $id, ?array $fields = ['all'], string $format = self::ARRAY_FORMAT);

    /**
     * Получает список записей
     *
     * @param array $params Параметры фильтрации
     * @param string $format Формат возвращаемых данных
     *
     * @return array|Collection
     */
    public function findAll(array $params = [], string $format = self::ARRAY_FORMAT);
}
