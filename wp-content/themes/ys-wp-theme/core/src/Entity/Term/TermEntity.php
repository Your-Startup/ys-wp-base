<?php

namespace YS\Core\Entity\Term;

use YS\Core\Entity\AbstractEntity;
use YS\Core\Util\SeoUtil;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class TermEntity extends AbstractEntity
{
    public static string $taxonomy = '';

    protected int    $id;
    protected int    $parent;
    protected string $title;
    protected string $slug;

    protected string $description;
    protected int    $postsCount;
    protected string $uri;

    //protected string $seoTitle;
    //protected string $seoDescription;

    const COLUMNS_DEFAULT_FIELDS_MAP = [
        'term_id' => 'id',
        'name'    => 'title'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->setColumnsMap([]);
    }

    public function getSeoTitle()
    {
        return SeoUtil::getTaxonomySeoTitle($this->id, static::$taxonomy);
    }

    public function getSeoDescription()
    {
        return SeoUtil::getTaxonomySeoDescription($this->id, static::$taxonomy);
    }

    public function getUri()
    {
        return get_term_link($this->id);
    }
}
