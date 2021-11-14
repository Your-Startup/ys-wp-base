<?php
namespace YS\Core\Entity\Post;

//use YS\Core\Repository\News\CategoriesRepository;
//use YS\Core\Repository\Users\UsersRepository;
//use YS\Core\Repository\Tags\TagsRepository;
//use YS\Core\Repository\Media\MediaRepository;
//use YS\Core\Service\NewsService;
//use YS\Core\Util\DateUtil;
use YS\Core\Entity\AbstractEntity;
use YS\Core\Util\SeoUtil;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class PostEntity extends AbstractEntity
{
    protected int    $id;
    protected string $title;
    protected string $content;
    protected string $status;
    protected string $slug;
    protected string $lead;
    protected ?int   $authorId;
    //protected ?array $author;
    protected int    $publishedAt;
    protected int    $updatedAt;
    //protected ?int   $attachmentId;
    //protected ?array $attachment;
    //protected ?array $categories;
    //protected ?array $tags;
    protected int    $commentsCount;
    protected bool   $isCommentsOpen;
    //protected string $uri;

   // private CategoriesRepository $categoriesRepo;
    //private TagsRepository       $tagsRepo;
    //private UsersRepository      $authorRepo;
   // private MediaRepository      $mediaRepo;
    //private NewsService          $newsService;

   // protected $guarded  = ['author', 'attachment', 'categories', 'tags'];
   // protected $hidden   = ['authorId', 'attachmentId', 'primaryCatId'];
   // protected $lazyLoad = ['author', 'attachment', 'categories', 'tags', 'seoTitle', 'seoDescription', 'seoImage', 'isExclusive'];

    public function __construct()
    {
        parent::__construct();

        $this->setColumnsMap([
            'ID'                => 'id',
            'post_title'        => 'title',
            'post_content'      => 'content',
            'post_status'       => 'status',
            'post_name'         => 'slug',
            'post_excerpt'      => 'lead',
            'post_author'       => 'authorId',
            'comment_status'    => 'isCommentsOpen',
            'comment_count'     => 'commentsCount',
            'post_date_gmt'     => 'publishedAt',
            'post_modified_gmt' => 'updatedAt',
        ]);
/*
        // Repositories
        $this->authorRepo     = new UsersRepository();
        $this->categoriesRepo = new CategoriesRepository();
        $this->tagsRepo       = new TagsRepository();
        $this->mediaRepo      = new MediaRepository();
        $this->newsService    = new NewsService();
*/
    }

    /**
     * @inheritDoc
     */
    protected function setDefaultValues()
    {
        $this->status      = 'publish';
        $this->publishedAt = time();
        $this->updatedAt   = time();
        $this->type        = 'article';
    }

    /**
     * @inheritDoc
     */
    public static function setValidationConstraints(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\NotBlank([
            'message' => 'Поле `title` не может быть пустым!',
        ]));
    }

    public function getAttachment()
    {

    }

    public function getCategories()
    {

    }

    public function getTags()
    {

    }

    public function getAuthor()
    {

    }

    public function getType(): string
    {

    }

    public function getUri()
    {
        return $this->routeService->getNewsRoute($this->slug);
    }

    /**
     * @param bool|string $status
     */
    public function setIsCommentsOpen($status)
    {
        $status = $status === true || $status === 'open';
        $this->isCommentsOpen = $status;
    }

    public function getSeoTitle()
    {
        return SeoUtil::getPostSeoTitle($this);
    }

    public function getSeoDescription()
    {
        return SeoUtil::getPostSeoDescription($this);
    }


    public function getSeoImage()
    {
        //return SeoUtil::getSeoImage($this->id, $this->attachment['full_uri']);
    }
}
