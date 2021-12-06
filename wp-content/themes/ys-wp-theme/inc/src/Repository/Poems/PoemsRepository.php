<?php

namespace YS\Site\Repository\Poems;

use YS\Core\Repository\Post\PostRepository;
use YS\Site\Entity\Poems\PoemsEntity;

class PoemsRepository extends PostRepository
{
    protected string $postType    = 'poems';
    protected string $entityClass = PoemsEntity::class;

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
}
