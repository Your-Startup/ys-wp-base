<?php

namespace RB\Site\Service;

use RB\Site\Entity\News\NewsEntity;

class NewsService
{
    private array      $categories = [];
    private ?array     $primaryCat = null;
    private array      $childCat   = [];
    private NewsEntity $entity;

    /**
     * Подготавливает список категорий перед выводом
     *
     * @param NewsEntity $entityObject
     */
    public function prepareCategories(NewsEntity $entityObject): void
    {
        $this->entity     = $entityObject;
        $this->categories = $this->entity->getCategories();

        // Получаем индекс основной категории. Заносим ее в отдельный массив и удаляем из основного
        $this->setPrimaryCat();
        // Выборка категорий наследников (в this->categories остаются только категории верхнего уровня)
        $this->setChildCat();
        // Сортировка категорий родитель => наследник(и)
        $this->sortCategories();

        // Очищаем массив от не нужных данных перед отправкой (вызывать всегда в самом конце)
        //$this->cleanFields();
        $this->entity->setCategories($this->categories);
    }

    private function setPrimaryCat()
    {
        $primaryCatId = $this->entity->getPrimaryCatId();
        if ($primaryCatId) {
            $primaryCatIndex  = array_search($primaryCatId, array_column($this->categories, 'id'));
            $this->primaryCat = $this->categories[$primaryCatIndex];
            unset($this->categories[$primaryCatIndex]);
        }
    }

    private function setChildCat()
    {
        $topLevel = [];
        foreach ($this->categories as $category) {
            $parent = (int)$category['parent'];
            if ($parent !== 0) {
                $this->childCat[$parent][] = $category;
                continue;
            }
            $topLevel[] = $category;
        }
        $this->categories = $topLevel;
    }

    private function sortCategories()
    {
        foreach ($this->childCat as $parentId => $childrens) {
            $parentIndex = array_search($parentId, array_column($this->categories, 'id'));
            if ($parentIndex !== false) {
                $parentIndex++;
                array_splice($this->categories, $parentIndex, 0, $childrens);
                continue;
            }
            $this->categories = array_merge($this->categories, $childrens);
        }
        // Если основная категория есть помещаем ее в начало списка
        if ($this->primaryCat) {
            array_unshift($this->categories, $this->primaryCat);
        }
    }

    private function cleanFields()
    {
        foreach ($this->categories as &$item) {
            unset($item['parent']);
        }
    }
}