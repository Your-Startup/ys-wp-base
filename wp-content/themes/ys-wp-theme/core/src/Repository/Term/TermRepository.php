<?php

namespace YS\Core\Repository\Term;

use YS\Core\Database\QueryBuilder;
use YS\Core\Database\QueryBuilder\QueryBuilderTerm;
use YS\Core\Entity\AbstractEntity;
use YS\Core\Entity\Collection;
use YS\Core\Entity\Term\TermEntity;
//use YS\Core\Exception\NotFoundException;
use YS\Core\Repository\AbstractRepository;

class TermRepository extends AbstractRepository
{
    protected string $taxonomy    = 'category';
    protected string $entityClass = TermEntity::class;

    protected array $defaultParams = [
        'page'   => 1,
        'limit'  => 100,
        'filter' => [],
        'fields' => [
            'id',
            'title',
        ],
        'with_pagination' => false,
        'with_total'      => false,
    ];
    public function setTaxonomy($taxonomy): self
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }
    public function find(string $id, ?array $fields = ['all'], string $format = self::ARRAY_FORMAT)
    {
        $query = (new QueryBuilderTerm())
            ->addTaxonomyQuery($this->taxonomy)
            ->addTermIdQuery($id)
            ->addFieldsQuery($fields, $this->entityClass);

        // Получение данных
        $data = $this->db
            ->prepare($query->getQueryString(), [$this->taxonomy, $id])
            ->execute()
            ->fetch();

        if (!$data) {
            //throw new NotFoundException();
        }

        // Заполнение сущности
        $item = new $this->entityClass();
        $item->fromArray($data);

        // Lazy Load связанных полей
        $item->loadFields($fields);

        return $this->prepareItem($item, $fields, $format);
    }

    public function findAll(array $params = [], string $format = self::ARRAY_FORMAT)
    {
        $params = $this->prepareParams($params);

        $query = (new QueryBuilderTerm())
            ->addTaxonomyQuery($this->taxonomy)
            ->addFieldsQuery($params['fields'], $this->entityClass)
            ->addGroupBy('t.term_id');

        //$this->addFilterQuery($query, $params);
        //$this->addPaginationQuery($query, $params);

        // Подготовка запроса и получение данных
        $data = $this->db
            ->prepare($query->getQueryString())
            ->execute()
            ->fetchAll();

        // Формируем коллекцию сущностей
        $items = $this->prepareCollection($data, $params['fields'], $this->entityClass);
        // Готовим записи к выдаче
        $items = $this->prepareItems($items, $params, $format);
        // Формируем массив с данными для пагинации, если необходимо
        return $this->paginateItems($query, $items, $params);
    }

    /**
     * Возвращает категорию выбранную в качестве основной у записи
     *
     * Возможно пригодится
    */
    public function getMainCategory(string $postId, ?array $fields = ['all'])
    {
        $query = new QueryBuilder();
        $query
            ->addSelect('pm.meta_value', 'id')
            ->addFrom(TABLE_WP_POSTMETA, 'pm')
            ->addWhere('pm.post_id = %d')
            ->addWhere('pm.meta_key = %s');

        // Подготовка запроса и получение данных
        $this->db->prepare($query->getQueryString(), [$postId, '_yoast_wpseo_primary_' . $this->taxonomy]);
        $this->db->execute();

        $mainCatId = $this->db->fetch();
        if (!$mainCatId) {
            throw new NotFoundException();
        }
        $mainCatId = reset($mainCatId);

        return $this->find($mainCatId, $fields);
    }

    /**
     * Получает количество терминов привязанных к определенной записи.
     *
     * @param int|string $itemId ID записи
     *
     * @return int
     */
    public function getTermsCountForItem($itemId)
    {
        $query = new QueryBuilder();

        $query
            ->addSelect('COUNT(0)')
            ->addFrom(TABLE_WP_TERM_RELATIONSHIPS, 'tr')
            ->addJoin(
                TABLE_WP_TERM_TAXONOMY, 'tt',
                'ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = %s'
            )
            ->addWhere('tr.object_id = %d')
        ;

        $this->db->prepare($query->getQueryString(), [$this->taxonomy, $itemId]);
        $this->db->execute();
        $count = (int)$this->db->fetchColumn();

        return $count;
    }

    /**
     * Добавляет фильтрацию
     *
     * @param QueryBuilder $qb
     * @param array $params
     */
    protected function addFilterQuery(QueryBuilder $qb, array $params = [])
    {
        if (empty($params['filter'])) {
            return;
        }

        $filter = $params['filter'];

        // Фильтрация по записи(ям)
        if (!empty($filter['post'])) {
            $posts = array_filter(array_map('intval', (array)$filter['post']));
            $this->addTermsByPostsFilterQuery($qb, $posts);
        }
    }

    protected function addDescriptionFieldQuery(QueryBuilder $qb)
    {

    }

    protected function addPostsCountFieldQuery(QueryBuilder $qb)
    {
        $qb->addSelect('tt.count', 'postsCount');
    }

    protected function addParentFieldQuery(QueryBuilder $qb)
    {
        $qb->addSelect('tt.parent', 'parent');
    }

}