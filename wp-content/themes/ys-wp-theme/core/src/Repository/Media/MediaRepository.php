<?php

namespace YS\Core\Repository\Media;

use YS\Core\Database\QueryBuilder\QueryBuilderPost;
use YS\Core\Entity\AbstractEntity;
use YS\Core\Entity\Media\MediaEntity;
use YS\Core\Repository\Post\PostRepository;

class MediaRepository extends PostRepository
{
    protected string $postType    = 'attachment';
    protected string $entityClass = MediaEntity::class;

    protected array $defaultParams = [
        'page'            => 1,
        'limit'           => 50,
        'filter'          => [],
        'fields'          => [
            'id',
            'title',
            'thumbnail_uri',
            'full_uri',
            'description',
            'date'
        ],
        'with_pagination' => true,
        'with_total'      => true,
    ];
}
