<?php

namespace YS\Core\Entity;

class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Коллекция сущностей
     *
     * @var AbstractEntity[]
     */
    private $collection = [];
    /**
     * Коллекция сущностей преобразованных в обычные ассоциативные массивы
     *
     * @var array
     */
    private $collectionAsArray;

    public function __construct(array $collection = []) {
        $this->collection = $collection;
    }

    public function addItem(AbstractEntity $entity)
    {
        $this->collection[] = $entity;
    }

    public function removeItem(AbstractEntity $entity)
    {
        // TODO: написать реализацию.
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->collection);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function toArray(): array
    {
        if (!isset($this->collectionAsArray)) {
           $this->collectionAsArray = array_map(function($item) {
               /** @var AbstractEntity $item */
               return $item->toArray();
           }, $this->collection);
        }

        return $this->collectionAsArray;
    }
}