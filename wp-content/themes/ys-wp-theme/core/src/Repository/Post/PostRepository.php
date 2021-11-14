<?php

namespace YS\Core\Repository\Post;

use YS\Core\Database\QueryBuilder\QueryBuilderPost;
use YS\Core\Entity\AbstractEntity;
use YS\Core\Entity\Collection;
use YS\Core\Entity\Post\PostEntity;
use YS\Core\Repository\AbstractRepository;

class PostRepository extends AbstractRepository
{
    protected string $postType    = 'post';
    protected string $entityClass = PostEntity::class;

    protected array $defaultParams = [
        'page'            => 1,
        'limit'           => 50,
        'sort'            => 'date:desc',
        'filter'          => [],
        'fields'          => [
            'id',
            'title',
            'author',
            'published_at',
            'attachment',
            'comments_count',
            'user_video',
            'uri',
            'is_exclusive'
        ],
        'with_pagination' => true,
        'with_total'      => true,
    ];

    public function find(string $id, ?array $fields = ['all'], string $format = self::ARRAY_FORMAT): AbstractEntity|array
    {
        $query = (new QueryBuilderPost())
            ->addPostTypeQuery($this->postType)
            ->addPostIdQuery($id)
            ->addFieldsQuery($fields, $this->entityClass);

        $this->db->getCache()->off();

        // Получение данных
        $data = $this->db
            ->prepare($query->getQueryString(), $id)
            ->execute()
            ->fetch();

        if (empty($data)) {
            //throw new NotFoundException();
        }

        // Заполнение сущности
        $item = new $this->entityClass;
        $item->fromArray($data);

        // Lazy Load связанных полей
        $item->loadFields($fields);

        return $this->prepareItem($item, $fields, $format);
    }

    public function findAll(array $params = [], string $format = self::ARRAY_FORMAT): array|Collection
    {
        $params = $this->prepareParams($params);

        //$validator = NewsValidator::getValidator();
        //$validator->validate($params);

        $query = (new QueryBuilderPost())
            ->addPostTypeQuery($this->postType)
            ->addFieldsQuery($params['fields'], $this->entityClass)
            ->addWhere('p.post_status = "publish"')
            ->addGroupBy('p.ID');

        //$this->addFilterQuery($query, $params);
        //$this->addPaginationQuery($query, $params);

        // Получение данных
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
}
